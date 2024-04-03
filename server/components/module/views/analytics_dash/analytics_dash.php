<?php
namespace bs\components\module\views\analytics_dash;
defined('EXE') or die('Access');

/**
 * $version 1.1
 * $deps s:bs\components\domofon\models\invoice\Receipt v1.2
 */

use \bs\libraries\mvc\View;

class Analytics_dash extends View
{
	protected function data()
	{
		$data = [];

		$client = $this->app->getCtrl('fabrik', 'clients');
		$simid = 47;
		$sevid = 48;

		//колл-во
		$l_sim = $client->selectq('SELECT COUNT(*) AS length FROM &__clients WHERE BaseId='.$simid)[0];
		$l_sev = $client->selectq('SELECT COUNT(*) AS length FROM &__clients WHERE BaseId='.$sevid)[0];

		$data['l_all'] = number_format(($l_sim['length']+$l_sev['length']), 0, ',', ' ');
		$data['l_sim'] = number_format($l_sim['length'], 0, ',', ' ');
		$data['l_sev'] = number_format($l_sev['length'], 0, ',', ' ');

		// отключенных
		$d_sim = $client->selectq('SELECT COUNT(*) AS length FROM &__clients WHERE BaseId='.$simid.' AND StatusId=6')[0];
		$d_sev = $client->selectq('SELECT COUNT(*) AS length FROM &__clients WHERE BaseId='.$sevid.' AND StatusId=6')[0];

		$data['d_all'] = number_format(($d_sim['length']+$d_sev['length']), 0, ',', ' ');
		$data['d_sim'] = number_format($d_sim['length'], 0, ',', ' ');
		$data['d_sev'] = number_format($d_sev['length'], 0, ',', ' ');
		
		// долг
    $clients = $client->selectq('SELECT id, BaseId FROM &__clients WHERE StatusId=2', 'id');
    $cids = \F::getHelper('arr')->single($clients, 'id');

		$receipt = $this->app->getComponent('domofon', 'receipt', 'invoice')->getModel();
		$receipt->clientids = $cids;
		$receipt->date_from = date('Y-'.$client->getFirstQ().'-01 00:00:00');
		$receipt->date_to = date('Y-'.$client->getLastQ().'-01 00:00:00');

		$amounts = $receipt->getAmounts();
		$all_left = [
			'sim' => 0,
			'sev' => 0
		];
		foreach ($amounts as $cid => $amount) 
		{
			$client = $clients[$cid];
			$baseid = $client['BaseId'];
			$key = $baseid == $simid ? 'sim' : 'sev';

      $cinvs_sum = $amount['c_invs']['sum'] ?? 0;
      $oinvs_sum = $amount['a_invs']['sum'] ?? 0;
      $pays_sum = $amount['a_pays']['sum'] ?? 0;

      $left = $pays_sum - $oinvs_sum; // учитывать или нет текущий квартал !!!!!

      if ($left < 0)
      	$all_left[$key] += abs($left);
		}

		$all_left['all'] = number_format(($all_left['sim'] + $all_left['sev']), 0, ',', ' ');
		$all_left['sim'] = number_format($all_left['sim'], 0, ',', ' ');
		$all_left['sev'] = number_format($all_left['sev'], 0, ',', ' ');

		$data['all_left'] = $all_left;

		// лист
		$anModel = $this->app->getComponent('module', 'analytics_dash', 'analytics_dash')->getModel();
		$data['list'] = $anModel->getListData();

		return $data;
	}
}