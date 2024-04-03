<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

include_once PATH_ROOT .'/components/fabrik/event/pluginList.php';
use \bs\components\fabrik\event\PluginList;

class ListInventory_items extends PluginList
{
	public function onFilter()
	{
		$model = $this->getModel();
		$alias = $model->getModuleAlias();
		$where = [];
		$filter = $this->app->getService('fabrik', 'helper')->getFilter();

		$staffid = $filter->getFieldValue('staff');
		$categoryid = $filter->getFieldValue('category');

		if ($staffid)
			$where[] = 't0.StaffId='.$staffid;

		if ($categoryid)
			$where[] = 't0.CategoryId='.$categoryid;

		if (!empty($where))
		{
			$where[] = 't0.IsDemount='.($alias == 'demount' ? 1 : 0);
			$where = implode(' AND ', $where);
		}
		else
		{
			$where = 't0.id=0';
		}
			
		$this->getModel()->where = $where;
	}

	public function onAfterData($view, $args)
	{
		// $rows = $args->data['rows'];

		// $itemIds = [0];
		// foreach ($rows as $row)
		// 	$itemIds[] = $row['ItemId_join'];

		// $sItems = \F::getDBO()
		// 	->setQuery('
		// 		SELECT id, Name 
		// 		FROM &__sklad_catalog_items
		// 		WHERE id IN ('.implode(',', $itemIds).')
		// 	')
		// 	->loadAssocList('id');

		// foreach ($rows as &$row) 
		// {
		// 	$id = $row['ItemId_join'];
		// 	$row['ItemId_join'] = $sItems[$id]['Name'];
		// }

		// $args->data['rows'] = $rows;
	}
}