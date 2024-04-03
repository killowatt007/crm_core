<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

include_once PATH_ROOT .'/components/fabrik/event/pluginList.php';
use \bs\components\fabrik\event\PluginList;

class ListStaff extends PluginList
{
	public function onFilter()
	{
		/*B_BASE*/
		$where = 't0.BaseId='.$this->getCtrl('pick_items')->getActiveBase();

		$this->getModel()->where = $where;
	}
}