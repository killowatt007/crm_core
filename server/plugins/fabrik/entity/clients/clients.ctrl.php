<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

use \bs\components\fabrik\Ctrl;

class CtrlClients extends Ctrl
{
  private $info = [];

  private $monthFQM = [1=>'01', 2=>'01', 3=>'01',  4=>'04', 5=>'04', 6=>'04',  7=>'07', 8=>'07', 9=>'07',  10=>'10', 11=>'10', 12=>'10'];
  private $monthLQM = [1=>'03', 2=>'03', 3=>'03',  4=>'06', 5=>'06', 6=>'06',  7=>'09', 8=>'09', 9=>'09',  10=>'12', 11=>'12', 12=>'12'];
  private $monthItems = [1=>1, 2=>1, 3=>1,  4=>2, 5=>2, 6=>2,  7=>3, 8=>3, 9=>3,  10=>4, 11=>4, 12=>4];

  private $amountSum = [];

  public function getPriceForCurrentQ($rate, $dateactive)
  {
    $fqm = $this->monthFQM[date('n')]; // первый месяц квартала
    $lqm = $this->monthLQM[date('n')]; // последний месяц квартала

    $dateInstall = date('Y-m-d', strtotime($dateactive));

    $dInstall = date_create($dateInstall);
    $dLast = date_create(date('Y-'.$lqm.'-t'));
    $intInstall = date_diff($dInstall, $dLast);

    if ($intInstall->y > 0 or $intInstall->m > 2)
    {
      $amount = $rate;
    }
    else
    {
      $dFirst = date_create(date('Y-'.$fqm.'-01'));
      $intQuart = date_diff($dFirst, $dLast);

      $allDays = $intQuart->days;
      $leftDays = $intInstall->days;

      $amount = ceil(($rate/$allDays)*$leftDays);
    }

    return $amount;
  }

  public function getPriceForYear($clientid, $rate, $dateactive)
  {
    $cid = $this->getCurrentQ();
    $date_inv = date('Y-'.$this->getFirstQ().'-01 00:00:00');

    $inv = $this->dbo->setQuery('
      SELECT id
      FROM &__invoice_items
      WHERE ClientId='.$clientid.' AND CatalogItemId='.$cid.' AND date(DateInvoice) >= '.$this->dbo->q($date_inv).'
    ')->loadAssoc();

    $currentQAmount = $inv ? 0 : $this->getPriceForCurrentQ($rate, $dateactive);
    $leftQ = 4-$this->getCurrentQ();
    $left = $this->getAmountSum($clientid)['left'];

    $leftForYear = (($leftQ * $rate) - $left) + $currentQAmount;

    if ($leftForYear < 0)
      $leftForYear = 0; 

    return $leftForYear;
  }

  public function getCurrentQ()
  {
    return $this->monthItems[date('n')];
  }

  public function getFirstQ()
  {
    return $this->monthFQM[date('n')];
  }

  public function getLastQ()
  {
    return $this->monthLQM[date('n')];
  }

  public function renderClientId($id)
  {
    return str_pad((int)$id, 6, 0, STR_PAD_LEFT);
  }

  public function validateId($id, $selfid = 0)
  {
    $error = null;

    if (is_numeric($id))
    {
      $id = (int)$id;

      if ($id)
      {
        $isset = (bool)$this->app->getCtrl('fabrik', 'clients')->select('id', 'id='.(int)$id.' AND id!='.$selfid);

        if ($isset)
          $error = 'Поле "Счет" - данный номер уже существует в базе';
      }
      else
      {
        $error = 'Поле "Счет" - не может быть равен 0';
      }
    }
    else
    {
      $error = 'Поле "Счет" - должено содержать только цифры';
    }

    return $error;
  }

	public function getDataInfo($id)
	{
  	return $this->dbo->setQuery(
  		'SELECT t0.id, 
  						t0.FIO, 
  						t0.ContractId, 
  						t0.Note, 
  						t0.Debt,
  						t0.FlatNumber,
              t0.Mobile,
              t0.Phone,
              t0.DateActivate,

  						t4.Name AS District,
  						t5.Name AS Street,
  						t1.HouseNumber,
  						t1.BuildingNumber,
  						t1.EntranceNumber,

  						t2.Name AS Rate,
              t2.IntValue AS Rate_int,
              t3.id AS Status_raw,
  						t3.Label AS Status
  		 FROM &__clients t0
  		 	 LEFT JOIN &__contracts t1 ON (t1.id=t0.ContractId)
  		 	 	 LEFT JOIN &__districts t4 ON (t4.id=t1.DistrictId)
  		 	 	 LEFT JOIN &__streets t5 ON (t5.id=t1.StreetId)
  		 	 LEFT JOIN &__pick_items t2 ON (t2.id=t0.RateId)
  		 	 LEFT JOIN &__status t3 ON (t3.id=t0.StatusId)
  		 WHERE t0.id='.$id
  	)->loadAssoc();
	}

	public function getInfo($id)
	{
    if (!isset($this->info[$id]))
    {
      $data = $this->getDataInfo($id);

      if ($data)
      {
        $data['address']  = $data['District'];
        $data['address'] .= ', '.$data['Street'];
        $data['address'] .= ($data['HouseNumber'] ? ' '.$data['HouseNumber'] : '');
        $data['address'] .= ($data['BuildingNumber'] ? ', кор '.$data['BuildingNumber'] : '');
        $data['address'] .= ', под '.$data['EntranceNumber'];
        $data['address'] .= ', кв '.$data['FlatNumber'];
      } 

      $this->info[$id] = $data;
    }

		return $this->info[$id];
	}

	public function getAmountSum($clientid)
	{
    if (!isset($this->amountSum[$clientid]))
    {
      $data = [];

      $invoice = $this->app->getCtrl('fabrik', 'invoice_items')->select('Amount', 'ClientId='.$clientid.' ORDER BY id DESC');
      $payment = $this->app->getCtrl('fabrik', 'payment_items')->select('Amount, Type', 'ClientId='.$clientid.' ORDER BY id DESC');

      $inv = 0;
      foreach ($invoice as $row) 
        $inv += $row['Amount'];

      $pay = 0;
      foreach ($payment as $row) 
      {
        if (!$row['Type'] or $row['Type'] == 'sell')
          $pay += $row['Amount'];
      }

      $data['inv'] = $inv;
      $data['pay'] = $pay;
      $data['left'] = $pay - $inv;

      $this->amountSum[$clientid] = $data;
    }


  	return $this->amountSum[$clientid];
	}
}



