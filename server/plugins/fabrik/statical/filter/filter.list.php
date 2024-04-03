<?php
namespace bs\plugins\fabrik\statical;
defined('EXE') or die('Access');

include_once PATH_ROOT .'/components/fabrik/event/pluginList.php';
use \bs\components\fabrik\event\PluginList;

class ListFilter extends PluginList
{
	public function onFilter()
	{
		$model = $this->getModel();

		if ($filter = $model->getParam('filter.filter'))
			$model->where = $this->app->getComponent('field', 'sqlwhere', 'fabrik')->getModel()->buildeWhere($filter, 'lst');
	}

	public function onAfterData($view)
	{
		$model = $this->getModel();

		if ($filters = $model->getParam('filter.filter'))
		{
			foreach ($filters as $filter) 
			{
				if ($filter['type'] == 'value' and $filter['value'] == 'filter')
				{
					$view->setData('plugin.filter', [
						'moduleId' => $filter['filterid'],
						'fieldId' => $filter['filter_fieldid']
					]);

					break;
				}
			}
		}
	}
}


