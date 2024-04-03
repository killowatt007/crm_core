<?php
namespace bs\plugins\builder\entity;
defined('EXE') or die('Access');

use bs\libraries;

class PageEquipment extends libraries\event\PluginModel
{
  public function onTreeCatalogViewData($model, $args)
  {
		$category = $this->app->getCtrl('fabrik', 'equipment_category')->select('id, Name, CategoryId');
		$categoryGroup = [];

		foreach ($category as $row) 
		{
			$pid = $row['CategoryId'];

			if (!isset($categoryGroup[$pid]))
			{
				$categoryGroup[$pid] = [
					'data' => []
				];
			}

			$categoryGroup[$pid]['data'][] = $row;
		}

    $args->data = [
			'itemsGroup' => $categoryGroup,
      'treeEntityId' => 64
		];
  }
}