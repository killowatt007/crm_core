<?php
namespace bs\plugins\builder\entity;
defined('EXE') or die('Access');

/**
 * $version 1.1
 * $deps domofon.invoice.quarter v1.1
 */

use bs\libraries;

class PageClients extends libraries\event\PluginModel
{
	public function onAfterData($view, $args)
	{
		$invoice_quarter_c = $this->app->getComponent('domofon', 'quarter', 'invoice');
		$args->data['invoice']['quarter'] = $invoice_quarter_c->getView()->getData();
	}

	public function onData($args)
	{
		$input = $this->app->input;

		if ($input->get('branch') == 'fabrik' and $input->get('task') == 'filter.apply')
		{
			$filter = $this->app->getService('fabrik', 'helper')->getFilter();
			$applyFieldName = $input->get('stream.pageData.applyFieldName');
			$clientid = $filter->getFieldValue($applyFieldName);

			if ($clientid)
			{
				$clientCtrl = $this->app->getCtrl('fabrik', 'clients');
			  $client = $clientCtrl->getInfo($clientid);

		  	if ($client)
		  	{
		  		$amountSum = $clientCtrl->getAmountSum($clientid);
		  		$left = number_format($amountSum['left'], 2, ',', ' ');
		  		$status  = $client['Status'];
		  		$status .= ($client['Debt'] == 1 and $client['Status_raw'] == 6) ? ' (задолженность)' : '';

					$leftForYear = $clientCtrl->getPriceForYear($clientid, $client['Rate_int'], $client['DateActivate']);
					$leftForYear = number_format($leftForYear, 2, ',', ' ');

		  		if ($left > 0)
		  		{
		  			$left = '<span style="color:green;">+'.$left.'</span>';
		  		}
		  		elseif ($left < 0)
		  		{
		  			$left = '<span style="color:#ef3535;">'.$left.'</span>';
		  		}
		  		else
		  		{
		  			$left = '<span style="color:green;">'.$left.'</span>';
		  		}


		  		$data = [
						'contractid' => $client['ContractId'],
						'rate' => $client['Rate'],
						'status' => $status,
						'address' => $client['address'],
						'inv' => number_format($amountSum['inv'], 2, ',', ' '),
						'pay' => number_format($amountSum['pay'], 2, ',', ' '),
						'left' => $left,
						'leftForYear' => $leftForYear
		  		];

					$args->data['data']['currentpage']['plugin']['clients'] = [
						'data' => $data,
						'note' => $client['Note'],
						'fio' => $client['FIO'],
						'id' => $client['id'],
						'id_l' => $clientCtrl->renderClientId($client['id'])
					];
		  	}
			}
		}
	}

	public function onFabrikFilterField($args)
	{
		if ($args->field['Type'] == 'fabrik')
		{
			if (in_array($args->field['Name'], ['id', 'fio']))
			{
				$key = $args->field['Name'] == 'id' ? 'id' : 'FIO';
				$where = 'LOWER('.$key.') regexp LOWER("(^|[[:space:]]){{value}}")';

				/*B_BASE*/
				$where .= ' AND BaseId='.$this->app->getCtrl('fabrik', 'pick_items')->getActiveBase();

				$args->field['search'] = [
					'where' => $where
				];
			}
		}
	}

	public function onFabrikFilterOptions($field, $data, $args)
	{
		if ($field['Name'] == 'id')
		{
			$args->options = [];
			foreach ($data as $row) 
			{
				$args->options[] = [
					'value' => $row['id'],
					'label' => $this->app->getCtrl('fabrik', 'clients')->renderClientId($row['id'])
				];
			}
		}
	}

	public function onFabrikFilterAfteSetCookie()
	{
		$moduleid = $this->app->input->get('moduleid');

		$applyFieldName = $this->app->input->get('stream.pageData.applyFieldName') ?? 'fio';
		$id = $applyFieldName == 'id' ? 13 : 12;

		$val = $this->app->input->get('stream.modulesData.'.$moduleid.'.fields.'.$id);

		setcookie('_ffilter['.$moduleid.'][12]', $val, time()+(86400*30), '/', $_SERVER['HTTP_HOST']);
		setcookie('_ffilter['.$moduleid.'][13]', $val, time()+(86400*30), '/', $_SERVER['HTTP_HOST']);
	}
}



