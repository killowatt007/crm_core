<?php
namespace bs\components\domofon\controllers\invoice;
defined('EXE') or die('Access');

use \bs\libraries\mvc\Controller;

class Quarter extends Controller
{
	public function invoice()
	{
		$dbo = \F::getDBO();

		$baseid = (int)$this->app->input->get('baseid');

		$invoice_items = $this->app->getCtrl('fabrik', 'invoice_items');
		$clients = $this->app->getCtrl('fabrik', 'clients');
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

		$dateInv = date('Y-m-d H:i:s');
		$c = 0;
		foreach ($clientsData as $client) 
		{
			if ($client['Rate'])
			{
				if (!isset($invs[$client['id']]))
				{
					$c++;
					$amount = $clients->getPriceForCurrentQ($client['Rate'], $client['DateActivate']);

				  $invoice_items->store([
						'ClientId' => $client['id'],
						'CatalogCategoryId' => 2,
						'CatalogItemId' => $cid,
						'Amount' => $amount,
						'Published' => 1,
						'DateInvoice' => $dateInv
				  ], null, $invoice_items_m);

				  $invoice_items_m->closeDBO();
				}
			}
		}

		return ['length'=>$c];
	}
}