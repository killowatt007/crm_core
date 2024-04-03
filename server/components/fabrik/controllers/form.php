<?php
namespace bs\components\fabrik\controllers;
defined('EXE') or die('Access');

use \bs\libraries\mvc\Controller;

class Form extends Controller
{
	public function data()
	{
		$entityid = $this->app->input->get('id');
		$rowId = $this->app->input->get('rowId', null);
		$stream = $this->app->input->get('stream', []);

		$moduleid = $this->app->input->get('moduleid', null);
		$modulerefid = $stream['modulerefid'] ?? null;

		$component = $this->app->getComponent('fabrik', 'form');
		$model = $component->initModel($entityid, $moduleid, null, $modulerefid);
		
		if ($rowId)
			$model->setRowId($rowId);

		$view = $component->getView();
		$data = $view->getData();

		return $data;
	}

	public function process()
	{
		$data = [];
		$tableId = $this->app->input->get('id');
		$rowId = $this->app->input->get('rowId', null);
		$formData = $this->app->input->get('formData');
		$stream = $this->app->input->get('stream', []);
		$modulerefid = $stream['modulerefid'] ?? null;

		$model = $this->app->getComponent('fabrik', 'form')->initModel($tableId, null, null, $modulerefid);
		$model->isroot = true;

		if ($rowId)
			$model->setRowId($rowId);
		$model->setFormData($formData);

		$model->store();

		$data['modulerefid'] = $moduleid ?? '';
		$data['validation'] = $model->validation;

		if ($model->isNewRecord())
			$data['rowId'] = $model->getInsertId();

		$model->getPluginManager()->run('contrAfterProcess', [\F::std(['data'=>&$data])]);

		return $data;
	}

	public function databasejoinSearch()
	{
		$value = $this->app->input->get('value');
		$entityid = $this->app->input->get('entityid');
		$fieldid = $this->app->input->get('fieldid');

		$model = $this->app->getComponent('fabrik', 'form')->initModel($entityid, null, null, null);
		$field = $model->getElement($fieldid);

		$condition = $field->getParam('search');
		$jentityid = $field->getParam('join_entity');
		$jkeyElid = $field->getParam('join_key');
		$jvalElid = $field->getParam('join_val');

		$jkeyElname = $this->app->getCtrl('fabrik', 'fabrik_field')->select('Name', $jkeyElid)['Name'];
		$jvalElname = $this->app->getCtrl('fabrik', 'fabrik_field')->select('Name', $jvalElid)['Name'];

		if (in_array($condition, ['regexp','regexp_w','regexp_wf']))
		{
			if ($value == '')
			{
				$value = '.';
			}
			else
			{
				if ($condition == 'regexp_w')
					$value = '(^|[[:space:]])'.$value;
				elseif ($condition == 'regexp_wf')
					$value = '^'.$value;
			}

			$condition = 'regexp';
		}

		$where = 'LOWER('.$jvalElname.') '.$condition.' "'.$value.'"';

		/*B_BASE*/
		$isbase = $this->app->getCtrl('fabrik', 'fabrik_field')->select('id', 'EntityId='.$entityid.' AND Name="BaseId"')[0] ?? null;
		if ($isbase)
			$where .= ($where ? ' AND ' : '') . 'BaseId='.$this->app->getCtrl('fabrik', 'pick_items')->getActiveBase();

		$data = $this->app->getCtrl('fabrik', $jentityid)->select($jkeyElname.', '.$jvalElname, $where);
		$options = \F::getHelper('arr')->rebuild($data, ['value'=>$jkeyElname, 'label'=>$jvalElname]);

		return ['options'=>$options];
	}
}