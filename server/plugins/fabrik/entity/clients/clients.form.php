<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

include_once PATH_ROOT .'/components/fabrik/event/pluginForm.php';
use \bs\components\fabrik\event\PluginForm;

class FormClients extends PluginForm
{
	public function onContrAfterProcess($args)
	{
		$model = $this->getModel();
		$formData = $model->getFormData();

		$args->data['clientid'] = $formData['id'];
	}

	public function onBeforeStore()
	{
		$submit = $this->app->input->get('submit');
		$model = $this->getModel();

		$this->updFD('BaseId', $this->getCtrl('pick_items')->getActiveBase());

		if ($submit == 'invalid')
			$this->updFD('StatusId', 6);
		elseif ($submit == 'approve')
			$this->updFD('StatusId', 2);
	}

	public function onBeforeProcess()
	{
		$model = $this->getModel();

		if ($validateId = $this->getCtrl()->validateId($this->getCV('id'), $model->getRowId()))
			$model->validation[] = $validateId;
	}

	public static function onAjaxCheckId()
	{
		$app = \F::getApp();
		$value = $app->input->get('value');
		$selfid = $app->input->get('selfid');

		$isset = (bool)$app->getCtrl('fabrik', 'clients')->validateId($value, $selfid);

		return ['isset'=>$isset];
	}

	public static function onAjaxQuarterInvoice()
	{
		$dbo = \F::getDBO();
		$app = \F::getApp();

		$baseid = (int)$app->input->get('baseid');
		$invoice_items = $app->getCtrl('fabrik', 'invoice_items');
		$clients = $app->getCtrl('fabrik', 'clients');
		$invoice_items_m = $invoice_items->getModel();
		$st_approve = 2;

		$cid = $clients->getCurrentQ();
		$fqm = $clients->getFirstQ();

		$date_inv = date('Y-'.$fqm.'-01 00:00:00');

		$wBase = $baseid == -1 ? '' : ' AND t2.BaseId='.$baseid;

		$clientsData = $dbo->setQuery('
			SELECT t0.id, t0.DateActivate, t1.IntValue AS Rate
			FROM &__clients t0
				LEFT JOIN &__pick_items t1 ON (t1.id=t0.RateId)
				LEFT JOIN &__contracts t2 ON (t2.id=t0.ContractId)
			WHERE t0.StatusId='.$st_approve.$wBase
		)->loadAssocList();

		$invs = $dbo->setQuery('
			SELECT t0.id, t0.ClientId
			FROM &__invoice_items t0
				LEFT JOIN &__clients t1 ON (t1.id=t0.ClientId)
					LEFT JOIN &__contracts t2 ON (t2.id=t1.ContractId)
			WHERE t0.Published=1 AND 
						t0.CatalogItemId='.$cid.' AND date(t0.DateInvoice) >= '.$dbo->q($date_inv).' AND 
						t1.StatusId='.$st_approve.$wBase
		)->loadAssocList('ClientId');

		$c = 0;
		foreach ($clientsData as $client) 
		{
			if ($client['Rate'])
			{
				if (!isset($invs[$client['id']]))
				{
					$amount = $clients->getPriceForCurrentQ($client['Rate'], $client['DateActivate']);

				  $invoice_items->store([
						'ClientId' => $client['id'],
						'CatalogCategoryId' => 2,
						'CatalogItemId' => $cid,
						'Amount' => $amount,
						'Published' => 1,
						'DateInvoice' => date('Y-m-d H:i:s'),
				  ], null, $invoice_items_m);

				  $invoice_items_m->closeDBO();
				}
			}
		}

		return [];
	}

	public static function onAjaxSaveNote()
	{
		$app = \F::getApp();
		$value = $app->input->get('value');
		$clientid = $app->input->get('clientid');

		$app->getCtrl('fabrik', 'clients')->store([
			'Note' => $value
	  ], $clientid);

		return [];
	}

	public function onActions($args)
	{
		$model = $this->getModel();
		$status = $model->getStatus();
		$actPage = $this->getCtrl('builder_tmpl')->getActive();

		// manager_dashboard
		if ($actPage['Alias'] == 'manager_dashboard')
		{
			$close = $args->data['actions']['close'];
			$args->data['actions'] = [];
			$args->data['actions']['close'] = $close;
		}

		// __other
		else
		{
			if ($status == 'new' or $status == 'invalid')
			{
				$args->data['actions']['approve'] = [
					'name' => 'approve',
					'position' => 'left',
					'label' => 'Включить',
					'color' => 'primary',
					'order' => 10
				];
			}
			else
			{
				$args->data['actions']['invalid'] = [
					'name' => 'invalid',
					'position' => 'left',
					'label' => 'Выключить',
					'color' => 'primary',
					'order' => 11
				];
			}

			if ($status == 'repayment') 
			{
				$args->data['actions']['approve'] = [
					'name' => 'approve',
					'position' => 'left',
					'label' => 'Включить',
					'color' => 'primary',
					'order' => 10
				];
				$args->data['actions']['invalid'] = [
					'name' => 'invalid',
					'position' => 'left',
					'label' => 'Выключить',
					'color' => 'primary',
					'order' => 11
				];
			}
		}
	}

	public function onElementParams($el)
	{
		$actPage = $this->getCtrl('builder_tmpl')->getActive();

		// manager_dashboard
		if ($actPage['Alias'] == 'manager_dashboard')
		{
			$el->setParam('form_edit', false);
		}
	}
}
