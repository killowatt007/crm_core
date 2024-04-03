<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

include_once PATH_ROOT .'/components/fabrik/event/pluginList.php';
use \bs\components\fabrik\event\PluginList;

class ListPayment_items extends PluginList
{
	public function onFilter()
	{
		$input = $this->app->input;
		$applyFieldName = $input->get('stream.pageData.applyFieldName') ?? 'fio';
		$clientid = 0;

		if ($applyFieldName)
		{
			$filter = $this->app->getService('fabrik', 'helper')->getFilter();
			$clientid = (int)$filter->getFieldValue($applyFieldName);
		}

		$this->getModel()->where = 't0.ClientId='.$clientid;
	}

	public function onElementValue($el, $i)
	{
		// Type
		if ($el->getName() == 'Type')
		{
			$value = '';
			$model = $this->getModel();
			$data = $model->getData()[$i];

			if ($data['Type'] == 'sell')
				$value = 'Приход';
			if ($data['Type'] == 'sellReturn')
				$value = 'Возврат';

			$el->setValue($value, $i);
		}
	}

	public function onParams()
	{
		if ($this->app->isMobile())
		{
			$model = $this->getModel();
			$showEls = $model->getParam('showElementids');
			unset($showEls[3]);
			$model->setParam('showElementids', $showEls);
		}
	}
}


