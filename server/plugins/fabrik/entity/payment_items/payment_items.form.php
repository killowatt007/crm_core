<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

include_once PATH_ROOT .'/components/fabrik/event/pluginForm.php';
use \bs\components\fabrik\event\PluginForm;

class FormPayment_items extends PluginForm
{
  private $checkid;

  public function onBeforeProcess()
  {
    $model = $this->getModel();
    $ctrl = $this->getCtrl();
    $isFiscal = $this->getCV('IsFiscal');

    if ($isFiscal and $ctrl->ischeck)
    {
      $openShift = $this->getCtrl('shifts')->getOpen();

      if (!$openShift)
        $model->validation[] = 'Новая смена не открыта';
    }
  }

	public function onBeforeStore()
	{
		$model = $this->getModel();
		$ctrl = $this->getCtrl();
    $isFiscal = $this->getCV('IsFiscal');

  	$st_ready = 12;
  	$status = '';

    if ($isFiscal and $ctrl->ischeck)
    {
    	$checks = $this->getCtrl('checks');

    	if ($submit = $model->getSubmit())
    	{
	      $type = $submit == 'sellReturn' ? 'sellReturn' : 'sell';
	      $this->updFD('Type', $type);
    	}

      $checkModel = $checks->store([
      	'ItemName' => 'Техническое обслуживание домофонных систем',
        'ClientId' => $this->getCV('ClientId'),
        'Amount' => $this->getCV('Amount'),
        'Type' => $this->getCV('Type'),
        'MethodId' => $this->getCV('MethodId'),
        'DatePay' => $this->getCV('DatePay'),
        'IsPrintCheck' => $this->getCV('IsPrintCheck')
      ]);

      $this->checkid = $checkModel->getRowId();

    	if ($checkModel->kkt_error)
    	{
    		$this->validation[] = $checkModel->kkt_error;
    	}
    	else
    	{
    		$status = $st_ready;
    	}
    }

    $this->updFD('StatusId', $status);
	}

	public function onAfterStore()
	{
		$model = $this->getModel();
		$ctrl = $this->getCtrl();
    $isFiscal = $this->getCV('IsFiscal');

    if ($isFiscal and $ctrl->ischeck)
    {
    	$checks = $this->getCtrl('checks');

    	$checks->kkt = false;
      $checks->store([
        'PaymentItemId' => $model->getRowId(),
      ], $this->checkid);
    }
	}

	public function onActions($args)
	{
		$model = $this->getModel();

		if ($model->isNewRecord())
		{
			$args->data['actions']['save']['label'] = 'Создать платеж';
		}
		else
		{
			unset($args->data['actions']['save']);

			if ($this->getCV('Type') == 'sell')
			{
				$args->data['actions']['sellReturn'] = [
					'name' => 'sellReturn',
					'position' => 'left',
					'label' => 'Возврат',
					'color' => 'success',
					'order' => 10
				];
			}
		}
	}
}