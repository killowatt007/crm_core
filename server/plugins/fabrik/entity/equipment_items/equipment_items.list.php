<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

include_once PATH_ROOT .'/components/fabrik/event/pluginList.php';
use \bs\components\fabrik\event\PluginList;

class ListEquipment_items extends PluginList
{
	public function onFilter()
	{
		$model = $this->getModel();
		$categoryId = $this->app->input->get('stream.tree_catalog.active_category_id');

		$where = $categoryId ? 't0.CategoryId='.$categoryId : 't0.id=0';
		$model->where = $where;
	}

	public function onButtons($args)
	{
		unset($args->data['buttons']['add']);
	}
}