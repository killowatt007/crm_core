<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

include_once PATH_ROOT .'/components/fabrik/event/pluginList.php';
use \bs\components\fabrik\event\PluginList;

class ListShifts extends PluginList
{
	public function onFilter()
	{
		$where = [];
		$filter = $this->app->getService('fabrik', 'helper')->getFilter();

		$from = $filter->getFieldValue('from');
		$to = $filter->getFieldValue('to');

		if ($from)
			$where[] = 'date(t0.DateOpen) >= "'.$from.'"';

		if ($to)
			$where[] = 'date(t0.DateOpen) <= "'.$to.'"';


		if (!empty($where))
			$where = implode(' AND ', $where);
		else
			$where =  null;

		$this->getModel()->where = $where;
	}

	public function onButtons($args)
	{
		// $actPage = $this->getCtrl('builder_tmpl')->getActive();
		// $actPage['Alias']

		unset($args->data['buttons']['add']);
	}
}