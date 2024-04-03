<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

include_once PATH_ROOT .'/components/fabrik/event/pluginList.php';
use \bs\components\fabrik\event\PluginList;

class ListClients extends PluginList
{
	public function onElementValue($el, $i)
	{
		$elname = $el->getName();
		$calcsAddress = [
			'ContractDistrictCalc',
			'ContractStreetCalc',
			'ContractHouseCalc',
			'ContractEntranceCalc'
		];

		if (in_array($elname, $calcsAddress))
		{
			$contractId = $this->getModel()->getData()[$i]['ContractId'];

			$data = $this->getCtrl('contracts')->selectq('
	      SELECT 
	        t1.Name 					AS ContractDistrictCalc,
	        t2.Name 					AS ContractStreetCalc,
	        t0.HouseNumber 		AS ContractHouseCalc,
	        t0.EntranceNumber AS ContractEntranceCalc
	      FROM &__contracts t0
	        LEFT JOIN &__districts t1 ON (t1.id=t0.DistrictId)
	        LEFT JOIN &__streets t2 ON (t2.id=t0.StreetId)
	      WHERE t0.id='.$contractId.'
			');

			$el->setValue($data[0][$elname], $i);
		}
	}

	public function onButtons($args)
	{
		$actPage = $this->getCtrl('builder_tmpl')->getActive();

		// manager_dashboard
		if ($actPage['Alias'] == 'manager_dashboard')
		{
			unset($args->data['buttons']['add']);
		}
	}
}