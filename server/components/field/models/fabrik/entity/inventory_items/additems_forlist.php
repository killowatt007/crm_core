<?php
namespace bs\components\field\models\fabrik\entity\inventory_items;
defined('EXE') or die('Access');

use \bs\libraries\mvc;

class Additems_forlist extends mvc\Model
{
	public function getRequestInventory($reqId)
	{
		$data = [
			'm' => [],
			'd' => []
		];

		$inventoryItems = \F::getApp()->getCtrl('fabrik', 'request_inventory')->selectq('
			SELECT 
				t0.Quantity,
				t0.IsDemount,
				t1.id,
				t1.Name,
				t1.CategoryId
			FROM &__request_inventory t0
				LEFT JOIN &__catalog_items t1 ON (t1.id=t0.CatalogItemId)
			WHERE t0.RequestItemId='.$reqId.'
		');

		foreach ([0, 1] as $type) 
		{
			$typel = !$type ? 'm' : 'd';
			$items = [];

			foreach ($inventoryItems as $item) 
			{
				if ($item['IsDemount'] == $type)
					$items[] = $item;
			}

			$data[$typel] = $this->sort($items);
		}

		return $data;
	}

	public function sort($items)
	{
		$cats = \F::getApp()->getCtrl('fabrik', 'catalog_category')->select('id, Name', null, 'id');

		foreach ($items as $row) 
		{
			$catid = $row['CategoryId'];

			if (!isset($data[$catid]))
			{
				$data[$catid] = [
					'id' => $catid,
					'name' => $cats[$catid]['Name'],
					'items' => []
				];
			}

			$data[$catid]['items'][] = [
				'id' => $row['id'],
				'name' => $row['Name'],
				'q' => ($row['Quantity'] ?? null)
			];
		}

		$data = array_values($data);

		return $data;
	}
}