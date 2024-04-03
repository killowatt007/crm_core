<?php
namespace bs\components\fabrik\views;
defined('EXE') or die('Access');

use \bs\libraries\mvc\View;

class Lst extends View
{
	protected function data()
	{
		$data = [];
		$app = \F::getApp();
		$model = $this->getModel();
		$plgManager = $model->getPluginManager();

		$groupAndRows = $model->getFgroupAndRows();

		$data['rows'] = $groupAndRows['rows'];
		$data['fieldsgroup'] = $groupAndRows['fgroup'];
		$data['tablename'] = $model->getTable()->getData()['Name'];
		$data['headers'] = $model->getHeaders();
		$data['editElementid'] = $model->getParam('editElementid');
		$data['label'] = $model->getParam('label');

		$data['modulerefid'] = $model->getModuleRefId() ?? '';
		$data['moduleid'] = $model->getModuleId() ?? '';

		$data['lenghtrows'] = $model->getLengthRows();
		$data['display'] = $model->getDisplay();
		$data['pagination'] = $model->getPagination();
		$data['modulealias'] = $model->getModuleAlias();

		$plgManager->run('buttons', [\F::std(['data'=>&$data])]);
		$plgManager->run('afterData', [$this, \F::std(['data'=>&$data])]);
		$plgManager->run('afterData_', [$this, \F::std(['data'=>&$data])]);
		$plgManager->setDep();

		$data['buttons'] = $this->sortButtons($data['buttons']);
		$data['actions'] = isset($data['actions']) ? array_values($data['actions']) : [];

		$app->setDep('components/field/actors/list');
		$app->setDep('components/fabrik/pluginmanager/manager');

		return $data;
	}

	private function sortButtons($actions)
	{
		usort($actions, function($a, $b) 
			{
				if ($a['order'] == $b['order'])
					return 0;
				return ($a['order'] < $b['order']) ? -1 : 1;
			});

		return $actions;
	}
}