<?php
namespace bs\plugins\builder\entity;
defined('EXE') or die('Access');

use bs\libraries;

class PageCatalog extends libraries\event\PluginModel
{
	public function onFabrikFilterField($args)
	{
		if ($args->field['Name'] == 'category')
		{
			$select = $args->field['Params']['select'] ?? [];
			$select[] = 'ParentId';

			$args->field['Params']['select'] = $select;
		}
	}

	public function onFabrikFilterOptions($field, $data, $args)
	{
		if ($field['Name'] == 'category')
		{
			$data = array_values(\F::getHelper('arr')->sort($data, 'id', 'ParentId', false));

			$args->options = [];
			foreach ($data as $row) 
			{
				$space = '';
				for ($i=0; $i < $row['lvl']; $i++) 
					$space .= $i ? '– ' : '';

				$args->options[] = [
					'value' => $row['id'],
					'label' => $space.$row['Name']
				];
			}
		}
	}
}



