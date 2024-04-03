<?php
namespace bs\components\fabrik\views;
defined('EXE') or die('Access');

use \bs\libraries\mvc\View;

class Form extends View
{
	protected function data()
	{
		$data = [];
		$model = $this->getModel();
		$plgManager = $model->getPluginManager();
		$elements = $model->getElements();
		$rows = $model->getData();
		$tmplData = $this->getTmpData();
		$isdefaultvalues = [];
		$fields = [];

		foreach ($elements as $id => $element) 
		{
			$name = $element->getName();
			$type = $element->getType();
			$value = $element->getValue();

			if ($value !== null)
				$rows[0][$name] = $value;

			if ($type == 'databasejoin')
				$rows[0][$name.'_join'] = $element->getJoinValue(0);

			if (in_array($id, $model->viewelementids))
			{
				$eldata = $element->getData();
				$eldata['opts']['i'] = 0;

				$fields[$id] = [
					'data' => $eldata
				];

				$this->app->setDep('components/field/actors/'.$eldata['name']);
			}
		}

		foreach ($model->isdefaultvalues as $id) 
		{
			$element = $model->getElement($id);

			$isdefaultvalues[] = [
				'name' => $element->getName(),
				'value' => $element->getValue()
			];
		}

		$data['isdefaultvalues'] = $isdefaultvalues;
		$data['tmpl'] = $tmplData;
		$data['rows'] = $rows;
		$data['tablename'] = $model->getTable()->getData()['Name'];
		$data['isEditable'] = $model->isEditable();
		$data['isNewRecord'] = $model->isNewRecord();
		$data['editElementid'] = $model->getParam('editElementid');
		$data['rowId'] = $model->getRowId();
		$data['fields'] = $fields;

		$data['modulerefid'] = $model->getModuleRefId() ?? '';
		$data['moduleid'] = $model->getModuleId() ?? '';

		$plgManager->run('actions', [\F::std(['data'=>&$data])]);
		$plgManager->run('afterData', [$this, \F::std(['data'=>&$data])]);
		$plgManager->setDep();

		$data['actions'] = $this->sortActions($data['actions']);

		return $data;
	}

	private function sortActions($actions)
	{
		usort($actions, function($a, $b) 
			{
				if ($a['order'] == $b['order'])
					return 0;
				return ($a['order'] < $b['order']) ? -1 : 1;
			});

		return $actions;
	}

	private function getTmpData()
	{
		$model = $this->getModel();
		$CTRLbuilder = $this->app->getCtrl('fabrik', 'builder_tmpl');

		$tmpl = $CTRLbuilder->select('id', 'EntityId='.$model->getId().' AND EntityTypeId=2')[0];
		$tableComp = $this->app->getComponent('builder', 'table');
		$tableModel = $tableComp->getModel();
		$tableModel->setParentObject($model);
		$tableModel->setId($tmpl['id']);
		$tableView = $tableComp->getView();
		$tmplData = $tableView->getData();

		return $tmplData;
	}
}


