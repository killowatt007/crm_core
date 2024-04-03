<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

include_once PATH_ROOT .'/components/fabrik/event/pluginForm.php';
use \bs\components\fabrik\event\PluginForm;

class FormChecks extends PluginForm
{
	public function onBeforeStore()
	{
		$model = $this->getModel();
		$model->kkt_error = null;
		$ctrl = $this->getCtrl();
		$submit = $model->getSubmit();
		$openShift = $this->getCtrl('shifts')->getOpen();

    if ($openShift)
    {
    	if ($submit)
    	{
    		$type = $submit == 'save' ? 'sell' : 'sellReturn';
    		$this->updFD('Type', $type);
    		$this->updFD('DatePay', date('Y-m-d H:i:s'));
    	}

    	if ($ctrl->kkt)
    	{
	    	$kkt = $this->getCtrl('kkt');

	    	$st_error = 14;
	    	$st_ready = 12;

	  		$amount = str_replace(',', '.', $this->getCV('Amount'));
	  		$amount = (float)number_format($amount, 2, '.', '');

				$kkt->fiskal($this->getCV('Type'), [0 => [
					'name' => $this->getCV('ItemName'),
					'price' => $amount,
					'quantity' => 1
				]], 'electronically', !(bool)$this->getCV('IsPrintCheck'));

				if ($kkt->error)
				{
					$model->kkt_error = $kkt->error;
					$this->updFD('Error', $kkt->error);
					$status = $st_error;
				}
				else
				{
					$status = $st_ready;
				}

				$this->updFD('UUID', $kkt->last_uuid);
				$this->updFD('StatusId', $status);
    	}

    	$this->updFD('ShiftId', $openShift['id']);
		}
		else
		{
			$model->validation[] = 'Новая смена не открыта';
		}
	}

	private static function getDataOnParce($row, $keys) {
		$value = '';

		if ($keys) {
			$value = [];
			$keysArr = explode(',', $keys);

			foreach ($keysArr as $key) {
				$value[] = $row[($key-1)];
			}

			$value = implode(' ', $value);
		}

		return $value;
	}

  public static function onAjaxLoadPayments()
  {
  	$error = '';
    $return = [];
		$app = \F::getApp();
		$input = $app->input;

		$flag = $input->get('flag');
		$paymentType = $input->get('paymentType');

    // storeData
    if ($flag == 'storeData')
    {
      $return['checks'] = [];
      $alert = [];

      $shift = $app->getCtrl('fabrik', 'shifts')->getOpen();
      
      if ($shift)
      {
				$paymentRows = [];
				$callback = null;

				// resend
      	if ($paymentType == 'resend')
				{
					$checks = $app->getCtrl('fabrik', 'checks')->select('id, ClientId, PaymentItemId', 'StatusId!=12 AND DateCreate > "2022-09-06 00:00:00"');

					foreach ($checks as $key => $row) 
					{
						if ($row['PaymentItemId'])
						{
						  $return['checks'][] = [
						    'checkid' => $row['id'],
						    'clientid' => $row['ClientId'],
						    'clientid_l' => $row['ClientId']
						  ];	
						}
					}

					if (empty($return['checks']))
					{
						$error = 'Все чеки отправлены';
					}
				}

				// registry
				elseif ($paymentType == 'registry')
				{
	      	$file = $input->get('file');
	      	$date_pay = $input->get('datepay');

	        $fileName = $file['name'];
	        $fileData = $file['data'];
	        $fileExt = pathinfo($fileName)['extension'];

	        $pathf = PATH_ROOT.'/files/registry/'.$fileName;
	        
	        if (file_exists($pathf))
	          $error = 'Файл с таким именем уже существует';

	        if (!$error)
	        {
	        	$registry_formats = $app->getCtrl('fabrik', 'registry_formats');
	        	$rformat = $registry_formats->select('*', $input->get('formatid'));

	          // validate
	          if (!$rformat['ColDatePay'] and !$input->get('datepay'))
	            $error = 'Для этого шаблона нужно выбрать дату оплаты';

	          if (!$error)
	          {
		          // create file
		          $posComma = (strpos($fileData, ',')+1);
		          $fileData = substr_replace($fileData, '', 0, $posComma);
		          $fileData = str_replace(' ', '+', $fileData);
		          $fileData = base64_decode($fileData);
		          file_put_contents($pathf, $fileData);

		          // parser
							self::error_report(false);
		          include PATH_ROOT.'/libraries/parse_xls.php';

		          $parser = new \ParseXls($pathf, false);
	            $rows = $parser->parse(['coordRow' => $rformat['Row']], $fileExt);
							self::error_report();

		          // create history
		          if (!$error)
		          {
		            $regid = $app->getCtrl('fabrik', 'register_history')->store([
		              'File' => $fileName,
		              'Length' => count($rows),
		              'TmplId' => $rformat['id']
		            ])->getRowId();
		          }

		          // create checks
		          if (!$error)
		          {
						    $date_format = $rformat['FormatDatePay'];
						    $isfiscal = $rformat['IsFiscal'];

						    $st_not_send = 13;

						    foreach ($rows as $key => $row) 
						    {
									$date_pay = $rformat['ColDatePay'] ? self::getDataOnParce($row, $rformat['ColDatePay']) : $date_pay;

						      if ($date_format)
						        $date_pay = \DateTime::createFromFormat($date_format, $date_pay)->format('Y-m-d H:i:s');

						    	$paymentRows[] = [
						    		'ClientId' => (int)self::getDataOnParce($row, $rformat['ColLic']),
						    		'Amount' => self::getDataOnParce($row, $rformat['ColSumma']),
						    		'MethodId' => $rformat['MethodId'],
						    		'StatusId' => ($isfiscal ? $st_not_send : 0),
						    		'IsFiscal' => $isfiscal,
						    		'Type' => ($isfiscal ? 'sell' : ''),
						    		'IsPrintCheck' => 0,
						    		'DatePay' => $date_pay,
						    		'regid' => $regid
						    	];
						    }
		          }
	          }
	        }
				}

				// acquiring
				elseif ($paymentType == 'acquiring')
				{
					$st_not_send = 13;
					$acqpData = $app->getCtrl('fabrik', 'acquiring_payment')->select('*', 'StatusId='.$st_not_send);

					if (!empty($acqpData))
					{
						foreach ($acqpData as $row) 
						{
				    	$paymentRows[] = [
				    		'acqp_id' => $row['id'],
				    		'ClientId' => $row['Lic'],
				    		'Amount' => $row['Total'],
				    		'MethodId' => 69,
				    		'StatusId' => $st_not_send,
				    		'IsFiscal' => 1,
				    		'Type' => 'sell',
				    		'IsPrintCheck' => 0,
				    		'DatePay' => $row['DatePay'],
				    		'regid' => 0
				    	];
						}
					}
					else
					{
						$alert[] = 'Все платежи загружены';
					}

					$callback = function($row, $checkid)
					{
						$app = \F::getApp();
						$acqp = $app->getCtrl('fabrik', 'acquiring_payment');
						$st_not_send = 13;

	          $acqp->store([
	          	'CheckId' => $checkid,
	          	'StatusId' => $st_not_send
	          ], $row['acqp_id']);
					};
				}

				self::registryStoreData($paymentRows, $error, $alert, $return, $callback);
      }
      else
      {
        $error = 'Новая смена не открыта';
      }

      $_SESSION['checks_reg_alert'] = $alert;
    }

    // kkt
    elseif ($flag == 'kkt')
    {
    	$st_ready = 12;
    	$st_error = 14;
    	$checkid = $input->get('checkid');

      $check = $app->getCtrl('fabrik', 'checks');
      $payment_items = $app->getCtrl('fabrik', 'payment_items');
      $payment_items->ischeck = false;
      $checkData = $check->select('ClientId, PaymentItemId', $checkid);

      $checkModel = $check->store([], $checkid);

      if ($checkModel->kkt_error)
        $_SESSION['checks_reg_alert'][] = 'Ошибка чека '.$checkData['ClientId'].': '.$checkModel->kkt_error;

      $payment_items->store([
        'StatusId' => ($checkModel->kkt_error ? $st_error : $st_ready),
      ], $checkData['PaymentItemId']);

      // acquiring
      if ($paymentType == 'acquiring')
      {
				$acqp = $app->getCtrl('fabrik', 'acquiring_payment');
				$acqpData = $acqp->select('id', 'CheckId='.$checkid)[0];

        $acqp->store([
        	'StatusId' => ($checkModel->kkt_error ? $st_error : $st_ready)
        ], $acqpData['id']);
      }
    }

    $return['alert'] = $_SESSION['checks_reg_alert'] ?? [];

    if ($error)
    	return ['error'=>$error];
    else
    	return $return;
  }

  private static function registryStoreData($rows, &$error, &$alert, &$return, $callback = null)
  {
		$app = \F::getApp();

  	$payment_items = $app->getCtrl('fabrik', 'payment_items');
  	$payment_items_m = $payment_items->getModel();
    $checks = $app->getCtrl('fabrik', 'checks');
    $checks_m = $checks->getModel();
    $clients = $app->getCtrl('fabrik', 'clients');
    
    $payment_items->ischeck = false;
    $checks->kkt = false;

    $debts = 0;
    $nolic = 0;

    $st_not_send = 13;
    $st_invalid = 6;
    $st_repayment = 8;

    foreach ($rows as $row) 
    {
    	$cid = $row['ClientId'];

      if (!$cid)
      {
        $nolic++;
        continue;
      }

      $client = $clients->select('id, StatusId, Debt', $cid);

      if ($client)
      {
        $payitemid = $payment_items->store([
          'ClientId' => $cid,
          'Amount' => $row['Amount'],
          'MethodId' => $row['MethodId'],
          'DatePay' => $row['DatePay'],
          'StatusId' => $row['StatusId'],
          'IsFiscal' => $row['IsFiscal'],
          'Type' => $row['Type'],
          'IsPrintCheck' => $row['IsPrintCheck']
        ], null, $payment_items_m)->getRowId();
        $payment_items_m->closeDBO();

        if ($row['IsFiscal'])
        {
          $checkid = $checks->store([
          	'ItemName' => 'Техническое обслуживание домофонных систем',
            'ClientId' => $cid,
            'Amount' => $row['Amount'],
            'Type' => 'sell',
            'MethodId' => $row['MethodId'],
            'RegistryId' => $row['regid'],
            'StatusId' => $st_not_send,
            'DatePay' => $row['DatePay'],
            'PaymentItemId' => $payitemid,
            'IsPrintCheck' => 0
          ], null, $checks_m)->getRowId();
          $checks_m->closeDBO();

          $return['checks'][] = [
            'checkid' => $checkid,
            'clientid' => $cid,
            'clientid_l' => $clients->renderClientId($cid)
          ];
        }

        if ($callback)
        	$callback($row, $checkid);

        if (!$error)
        {
          if ($client['StatusId'] == $st_invalid)
          {
            $clients->store([
              'StatusId' => $st_repayment
            ], $client['id']);

            $debts++;
          }
        }
      }
      else
      {
        $alert[] = 'Абонент не найден: '.$cid;
      }
    }

    if ($debts)
      $alert[] = 'Абоненты оплатившие задолженность: '.$debts;

    if ($nolic)
      $alert[] = 'Не найден лицевой счет: '.$nolic;
  }

	public function onActions($args)
	{
		$model = $this->getModel();

		if ($model->isNewRecord())
		{
			$args->data['actions']['save']['label'] = 'Приход';
			$args->data['actions']['sellReturn'] = [
				'name' => 'sellReturn',
				'position' => 'left',
				'label' => 'Возврат',
				'color' => 'primary',
				'order' => 10
			];
		}
		else
		{
			unset($args->data['actions']['save']);
		}
	}

	private static function error_report($on = true)
	{
		if ($on)
		{
			error_reporting(E_ALL);
			ini_set('display_errors', '1');
		}
		else
		{
			error_reporting(0);
			ini_set('display_errors', '0');
		}
	}

	public function onElementParams($el)
	{
		if (!$this->getModel()->isNewRecord())
		{
			$el->setParam('form_edit', false);
		}
	}
}