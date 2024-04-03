<?php
namespace bs\plugins\builder\entity;
defined('EXE') or die('Access');

use bs\libraries;

class PageRequest_items extends libraries\event\PluginModel
{
	public function onFabrikFilterAfteWHere($field, $args)
	{
		if ($field['Name'] == 'staff')
		{
			/*B_BASE*/
			$args->where .= ($args->where ? ' AND ' : '') . 't0.BaseId='.$this->app->getCtrl('fabrik', 'pick_items')->getActiveBase();
		}
	}

	public function onFabrikFilterOptions($field, $data, $args)
	{
		if ($field['Name'] == 'client')
		{
			$args->options = [];
			foreach ($data as $row) 
			{
				$label  = $row['FIO'];
				$label .= ' <span class="test">('.$this->app->getCtrl('fabrik', 'clients')->renderClientId($row['id']).')</span>';

				$args->options[] = [
					'value' => $row['id'],
					'label' => $label
				];
			}
		}
	}

	public function onFabrikFilterField($args)
	{
		$user = $this->app->getUser();
		$roleid = $user->data['RoleId'];
		$r_master = 4;
		$r_operator = 3;

		if ($args->field['Type'] == 'fabrik')
		{
			// staff
			if ($args->field['Name'] == 'staff')
			{
				// r_master
				if ($roleid == $r_master)
				{
					$args->field['Params']['display'] = 0;
				}
			}

			// client
			if ($args->field['Name'] == 'client')
			{
				$where = '(LOWER(FIO) regexp "(^|[[:space:]]){{value}}" OR id regexp "(^|[[:space:]]){{value}}")';

				/*B_BASE*/
				$where .= ' AND BaseId='.$this->app->getCtrl('fabrik', 'pick_items')->getActiveBase();
						
				$args->field['search'] = [
					'where' => $where
				];
			}

			// status
			if ($args->field['Name'] == 'status')
			{
				$ids = 0;

				if ($roleid == $r_operator)
					$ids = '1,15,17,18';
				elseif ($roleid == $r_master)
					$ids = '15,17,18';

				$args->field['Params']['where'] = 'id IN ('.$ids.')';
			}
		}
	}
}



