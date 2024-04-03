<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

include_once PATH_ROOT .'/components/fabrik/event/pluginList.php';
use \bs\components\fabrik\event\PluginList;

class ListInvoice_items extends PluginList
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

	public function onElementParams($el)
	{
		if ($el->getName() == 'CatalogItemId')
			$el->setParam('col_width', '180px');
	}
}