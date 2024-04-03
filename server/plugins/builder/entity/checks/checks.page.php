<?php
namespace bs\plugins\builder\entity;
defined('EXE') or die('Access');

use bs\libraries;

class PageChecks extends libraries\event\PluginModel
{
	public function onAfterData($view, $args)
	{
		$formatOpts = \F::getHelper('arr')->rebuild(
			\F::getApp()->getCtrl('fabrik', 'registry_formats')->select('id, Name'), 
			['value'=>'id', 'label'=>'Name']
		);

		$args->data['formatOpts'] = $formatOpts;
	}

	// public function onData($args)
	// {
	// 	$input = $this->app->input;

	// 	if ($input->get('name') == 'fabrikFilter' and $input->get('method') == 'apply')
	// 	{
	// 		$filter = $this->app->getService('fabrik', 'helper')->getFilter();
	// 		$applyFieldName = $input->get('stream.pageData.applyFieldName');
	// 		$clientid = $filter->getFieldValue($applyFieldName);

	// 		if ($clientid)
	// 		{
	// 			$clientCtrl = $this->app->getCtrl('fabrik', 'clients');
	// 		  $client = $clientCtrl->getInfo($clientid);

	// 	  	if ($client)
	// 	  	{
	// 	  		$amountSum = $clientCtrl->getAmountSum($clientid);
	// 	  		$data = [
	// 					'contractid' => $client['ContractId'],
	// 					'rate' => $client['Rate'],
	// 					'status' => $client['Status'],
	// 					'address' => $client['address'],
	// 					'inv' => $amountSum['inv'],
	// 					'pay' => $amountSum['pay'],
	// 					'left' => $amountSum['left']
	// 	  		];

	// 				$args->data['data']['currentpage']['plugin']['clients'] = [
	// 					'data' => $data,
	// 					'note' => $client['Note'],
	// 					'fio' => $client['FIO'],
	// 					'id' => $client['id']
	// 				];
	// 	  	}
	// 		}
	// 	}
	// }

	// public function onFabrikFilterField($args)
	// {
	// 	if ($args->field['Type'] == 'fabrik')
	// 	{
	// 		if (in_array($args->field['Name'], ['id', 'fio']))
	// 		{
	// 			$key = $args->field['Name'] == 'id' ? 'id' : 'FIO';
	// 			// 'LOWER(t0.'.$key.') regexp '.$this->dbo->q('(^|[[:space:]])'.str_replace(['[',']'], ['',''], $value));

	// 			$args->field['search'] = [
	// 				'where' => 'LOWER('.$key.') regexp "(^|[[:space:]]){{value}}"'
	// 			];
	// 		}
	// 	}
	// }
}



