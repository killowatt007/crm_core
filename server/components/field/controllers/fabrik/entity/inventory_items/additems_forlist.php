<?php
namespace bs\components\field\controllers\fabrik\entity\inventory_items;
defined('EXE') or die('Access');

use \bs\libraries\mvc\Controller;

class Additems_forlist extends Controller
{
	public function test()
	{
		$staffid = $this->app->input->get('staffid');
		$itemsgroup = $this->getItems($staffid);

		return ['itemsgroup' => $itemsgroup];
	}

	private function getItems($staffid = null)
	{
		$model = $this->app->getComponent('field', 'additems_forlist', 'fabrik.entity.inventory_items')->getModel();
  	$data = [];

		// $itemsMount = \F::getDBO()
		// 	->setQuery('
		// 			SELECT 
		// 				t0.Quantity,
		// 				t0.id,
		// 				t1.Name,
		// 				t1.CategoryId
		// 			FROM &__inventory_items t0
		// 				LEFT JOIN &__catalog_items t1 ON (t1.id=t0.CatalogItemId)
		// 			WHERE t0.StaffId='.$staffid.'
		// 		')
		// 	->loadAssocList();

		$itemsDemount = \F::getDBO()
			->setQuery('
				SELECT 
					t0.id,
					t0.Name,
					t0.CategoryId
				FROM &__catalog_items t0
				WHERE CategoryId not in (2,3)
				')
			->loadAssocList();

		$data['d'] = $model->sort($itemsDemount);
		$data['m'] = $data['d'];

		return $data;
	}
}