<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

include_once PATH_ROOT .'/components/fabrik/event/pluginList.php';
use \bs\components\fabrik\event\PluginList;

class ListChecks extends PluginList
{
	public function onFilter()
	{
		$where = [];
		$filter = $this->app->getService('fabrik', 'helper')->getFilter();

		$from = $filter->getFieldValue('from');
		$to = $filter->getFieldValue('to');

		if ($from)
			$where[] = 'date(t0.DatePay) >= "'.$from.'"';

		if ($to)
			$where[] = 'date(t0.DatePay) <= "'.$to.'"';


		if (!empty($where))
			$where = implode(' AND ', $where);
		else
			$where =  null;

		$this->getModel()->where = $where;
	}

	public function onParams()
	{
		if ($this->app->isMobile())
		{
			$model = $this->getModel();
			$showEls = $model->getParam('showElementids');
			unset($showEls[2], $showEls[3]);
			$model->setParam('showElementids', array_values($showEls));		
		}
	}

	public function onButtons($args)
	{
		$args->data['buttons']['resend'] = [
			'name' => 'resend',
			'label' => 'Обновить',
			'color' => 'primary',
			'icon' => 'fa fa-regular fa-repeat',
			'order' => -1
		];
	}
}