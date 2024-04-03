<?php
namespace bs\components\domofon\models\invoice;
defined('EXE') or die('Access');

/**
 * $version 1.1
 * --------
 * $version 1.2
 */

use \bs\libraries\mvc\Model;

class Receipt extends Model
{
  private $months_ru = [
    'январь',
    'февраль',
    'март',
    'апрель',
    'май',
    'июнь',
    'июль',
    'август',
    'сентябрь',
    'октябрь',
    'ноябрь',
    'декабрь'
  ];

  private $uc_months_ru = [
    'Январь',
    'Февраль',
    'Март',
    'Апрель',
    'Май',
    'Июнь',
    'Июль',
    'Август',
    'Сентябрь',
    'Октябрь',
    'Ноябрь',
    'Декабрь'
  ];

  public $contractids = null;
  public $clientids = null;

  public $date_from = null;
  public $date_to = null;

  public $output_type = 'I'; // I - вывести, S - строка, F - файл, D - скачать
  public $output_file = null;
  public $sourceFile = null;

  public $singledoc = true;
  private $mpdfObject = null;

  public $size = 0;

  private function getClientIds()
  {
    if ($this->clientids === null)
    {
      if ($this->contractids)
      {
        $clients = \F::getDBO()
          ->setQuery('SELECT id FROM &__clients WHERE ContractId IN ('.implode(',', $this->contractids).')')
          ->loadAssocList();

        $this->clientids = \F::getHelper('arr')->single($clients, 'id');
      }
      else
      {
        $this->clientids = false;
      }
    }

    return $this->clientids;
  }

  public function getAmounts()
  {
    $dbo = \F::getDBO();
    $data = [];
    $cids = $this->getClientIds();
    $w = $cids ? 'ClientId IN ('.implode(',', $cids).') AND' : '';

    // c_invs
    $c_invs = $dbo->setQuery('
      SELECT ClientId, Amount, DateInvoice
      FROM &__invoice_items
      WHERE 
        '.$w.' (date(DateInvoice) >= '.$dbo->q($this->date_from).' AND date(DateInvoice) <= '.$dbo->q($this->date_to).')
      ORDER BY id DESC
    ')->loadAssocList();

    foreach ($c_invs as $row) 
    {
      $cid = $row['ClientId'];

      if (!isset($data[$cid]['c_invs']))
      {
        $data[$cid]['c_invs']['sum'] = 0;
        $data[$cid]['last_inv'] = $row;
      }

      $data[$cid]['c_invs']['sum'] += (float)$row['Amount'];
    }
    unset($c_invs);

    // a_invs
    $a_invs = $dbo->setQuery('
      SELECT ClientId, Amount, DateInvoice
      FROM &__invoice_items
      WHERE 
        '.$w.' date(DateInvoice) < '.$dbo->q($this->date_from).'
      ORDER BY id DESC
    ')->loadAssocList();

    foreach ($a_invs as $row) 
    {
      $cid = $row['ClientId'];

      if (!isset($data[$cid]['a_invs']))
      {
        $data[$cid]['a_invs']['sum'] = 0;

        if (!isset($data[$cid]['last_inv']))
          $data[$cid]['last_inv'] = $row;
      }

      $data[$cid]['a_invs']['sum'] += (float)$row['Amount'];
    }
    unset($a_invs);

    // a_pays
    $a_pays = $dbo->setQuery('
      SELECT ClientId, Amount, DatePay
      FROM &__payment_items
      WHERE 
        '.$w.' (Type="sell" OR Type="")
      ORDER BY id DESC
    ')->loadAssocList();

    foreach ($a_pays as $row) 
    {
      $cid = $row['ClientId'];

      if (!isset($data[$cid]['a_pays']))
      {
        $data[$cid]['a_pays']['sum'] = 0;
        $data[$cid]['last_pay'] = $row;
      }

      $data[$cid]['a_pays']['sum'] += (float)$row['Amount'];
    }
    unset($a_pays);

    return $data;
  }

  public function getReceipts()
  {
    $result = $this->singledoc ? null : [];

    $this->app = \F::getApp();
    $input = $this->app->input;
    $dbo = \F::getDBO();

    $pickitemCtrl = $this->app->getCtrl('fabrik', 'pick_items');
    $bid = $pickitemCtrl->getActiveBase();

    $where = $this->contractids
      ? 't0.ContractId IN ('.implode(',', $this->contractids).') AND t0.StatusId=2' 
      : 't0.id IN ('.implode(',', $this->clientids).')'
    ;

    $clients = $dbo->setQuery('
      SELECT 
        t0.id,
        t0.FIO,
        t0.FlatNumber,
        t0.StatusId,
        t0.Debt,

        t1.id AS ContractNumber,
        t1.HouseNumber,
        t1.BuildingNumber,
        t1.EntranceNumber,

        t4.IntValue AS RateId_j,
        t3.Name AS StreetId_j
      FROM &__clients t0
        LEFT JOIN &__contracts t1 ON (t1.id=t0.ContractId)
          LEFT JOIN &__streets t3 ON (t3.id=t1.StreetId)
        LEFT JOIN &__pick_items t4 ON (t4.id=t0.RateId)
      WHERE '.$where.'
      GROUP BY t0.id
    ')->loadAssocList();

    if (!empty($clients)) {
      $cids = [];
      foreach ($clients as $key => $row)
        $cids[] = $row['id'];
  
      $amounts = $this->getAmounts();
  
      $htmls = ['top'=>[], 'bottom'=>[]];
      foreach ($clients as $client) 
      {
        $mpdf = $this->_getMpdfObject();

        $cid = $client['id'];
        $amount = $amounts[$cid] ?? [];
  
        $cinvs_sum = $amount['c_invs']['sum'] ?? 0;
        $oinvs_sum = $amount['a_invs']['sum'] ?? 0;
        $pays_sum = $amount['a_pays']['sum'] ?? 0;
  
        $debt = $oinvs_sum - $pays_sum;
        $left = $cinvs_sum + $debt;
  
        if ($this->contractids and $left <= 0)
          continue;

        $this->size++;
  
        $mFrom = $this->date('m', strtotime($this->date_from));
        $mTo = $this->date('m', strtotime($this->date_to));
  
        ob_start();
        require PATH_ROOT.'/components/domofon/controllers/invoice/receipt/tmpl.php';
        $html = ob_get_clean();
  
        if (!$this->singledoc)
        {
          $mpdf->AddPage();
          $mpdf->WriteHTML($html);
  
          $result[$cid] = $mpdf->Output($this->output_file, $this->output_type);
        }
        else
        {
          $rate5 = $client['RateId_j'] * 5;
  
          if ($left >= $rate5)
            $htmls['bottom'][] = $html;
          else
            $htmls['top'][] = $html;  
        }
      }
  
      if ($this->singledoc)
      {
        // usort($htmls, function($a, $b) 
        //   {
  
        //     if ($a['_order_key'] == $b['_order_key']) {
        //       return 0;
        //     }
        //     return ($a['_order_key'] < $b['_order_key']) ? -1 : 1;
        //   });
  
        $isset = false;
        foreach (['bottom', 'top'] as $pos) 
        {
          foreach ($htmls[$pos] as $html) 
          {
            $isset = true;

            $mpdf->AddPage();
            $mpdf->WriteHTML($html);
          }
        }

        if ($isset) {
          $result = $mpdf->Output($this->output_file, $this->output_type);
        }
      }
    }

    return $result;
  }

  public function getMpdfObject() {
    spl_autoload_register(function ($class) 
    {
      $classArr = explode('\\', $class);

      if ($classArr[0] == 'Mpdf' or $classArr[0] == 'setasign')
          unset($classArr[0]);

      if ($classArr[1] == 'FpdiPdfParser')
        $classArr[1] = 'Fpdi';
      
      $path = '';
      foreach ($classArr as $p) 
        $path .= '/'.$p;

      include PATH_ROOT.'/libraries/mpdf/mpdf'.$path.'.php';
    });

    $mpdfObject = new \Mpdf\Mpdf([
      'format' => 'A5-L',
      'margin_top' => 7,
      'margin_bottom' => 7,
      'margin_left' => 5,
      'margin_right' => 5
    ]);

    return $mpdfObject;
  }

  private function _getMpdfObject()
  {
    if (!$this->singledoc or !$this->mpdfObject)
    {
      $this->mpdfObject = $this->getMpdfObject();

      if ($this->sourceFile and file_exists($this->sourceFile)) {
        $pagecount = $this->mpdfObject->setSourceFile($this->sourceFile);

        for ($i=0; $i<$pagecount; $i++) {
          $this->mpdfObject->AddPage();
          $tplId = $this->mpdfObject->importPage(($i+1));
          $this->mpdfObject->useTemplate($tplId);
        }
      }
    }

    return $this->mpdfObject;
  }

  private function date($tmpl, $date)
  {
    $result = date($tmpl, $date);
    $search = null;

    if (strpos($tmpl, 'M') !== false)
      $search = 'M';
    if (strpos($tmpl, 'm') !== false)
      $search = 'm';

    if ($search)
    {
      $num = date('n', $date)-1;
      $eng = date($search, $date);
      $arr = $search == 'M' ? $this->uc_months_ru : $this->months_ru;

      $result = str_replace($eng, $arr[$num], $result);
    }

    return $result;
  }
}