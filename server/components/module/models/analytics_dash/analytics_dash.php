<?php
namespace bs\components\module\models\analytics_dash;
defined('EXE') or die('Access');

/**
 * $version 1.1
 * $deps s:bs\components\domofon\models\invoice\Receipt v1.2
 */

use \bs\components\module\Model;

class Analytics_dash extends Model
{
	public $clientData = [];
	public $allData = [
		'debtSum' => 0
	];

	public $debt = null;
	public $status = -1;
	public $base = -1;

	public $model;

	public function getListData()
	{
		$this->app = \F::getApp();

		$listComp = $this->app->getComponent('fabrik', 'list');
		$this->model = $listComp->initModel(32, 76);
		$this->model->setWhere($this->getListWhere());
		$view = $listComp->getView();

		return $view->getData();
	}

	public function getListWhere()
	{
		if ($this->debt)
		{
			$status = $streamData['status'] ?? -1;

			$client = $this->app->getCtrl('fabrik', 'clients');

			$where = [];
			if ($this->status != -1)
				$where[] = 't0.StatusId='.$this->status;
			if ($this->base != -1)
				$where[] = 't0.BaseId='.$this->base;

			if (!empty($where))
				$where = 'WHERE '.implode(' AND ', $where);
			else
				$where = '';

	    $clients = $client->selectq('
	      SELECT 
	        t0.id,
	        t0.StatusId,
	        t1.IntValue AS Rate
	      FROM &__clients t0
	        LEFT JOIN &__pick_items t1 ON (t1.id=t0.RateId)
	      '.$where.'
	    ');
	    $cids = \F::getHelper('arr')->single($clients, 'id');

			$receipt = $this->app->getComponent('domofon', 'receipt', 'invoice')->getModel();
			$receipt->clientids = $cids;
			$receipt->date_from = date('Y-'.$client->getFirstQ().'-01 00:00:00');
			$receipt->date_to = date('Y-'.$client->getLastQ().'-01 00:00:00');

			$amounts = $receipt->getAmounts();

			$debts = [];
			$ids = [];
			foreach ($clients as $client) 
			{
	      $cid = $client['id'];
	      $amount = $amounts[$cid] ?? [];

	      $cinvs_sum = $amount['c_invs']['sum'] ?? 0;
	      $oinvs_sum = $amount['a_invs']['sum'] ?? 0;
	      $pays_sum = $amount['a_pays']['sum'] ?? 0;

	      $left = $pays_sum - $oinvs_sum; // учитывать или нет текущий квартал !!!!!

	      if ($left < 0)
	      {
	      	$left = abs($left);

		      if ($left > $this->debt)
		      {
		      	$debts[] = $cid;

			      $this->model->clientData[$cid] = [
			      	'left' => abs($left)
			      ];
			      $this->allData['debtSum'] += abs($left);
		      }
	      }
			}

			$this->allData['debt_l'] = count($debts);

			if (empty($debts))
				$debts[] = 0;

			$where = 't0.id IN ('.implode(',', $debts).')';
		}
		else
		{
			$where = 't0.id=-1';
		}

		return $where;
	}
}