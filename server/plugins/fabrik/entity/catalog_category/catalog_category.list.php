<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

include_once PATH_ROOT .'/components/fabrik/event/pluginList.php';
use \bs\components\fabrik\event\PluginList;

class ListCatalog_category extends PluginList
{
	public function onAfterData($view, $args)
	{
		$rows = &$args->data['rows'];

		if (!empty($rows))
		{
			$rows = array_values(\F::getHelper('arr')->sort($rows, 'id', 'ParentId', false));

			foreach ($rows as &$row) 
			{
				$space = '';
				for ($i=0; $i < $row['lvl']; $i++) 
					$space .= $i ? '– ' : '';

				$row['Name'] = $space.$row['Name'];
			}
		}
	}
}