<?php
namespace bs\plugins\fabrik\statical;
defined('EXE') or die('Access');

include_once PATH_ROOT .'/components/fabrik/event/pluginForm.php';
use \bs\components\fabrik\event\PluginForm;

class FormFilter extends PluginForm
{
	public $currentControlFields = [];
	public $controlFields = [];

	public function onElementDatabasejoinWhere($element, $args)
	{
		$filter = $element->getParam('filter.filter', null, true);

		if ($filter)
		{
			$this->currentControlFields = [];
			$args->where = $this->app->getComponent('field', 'sqlwhere', 'fabrik')->getModel()->buildeWhere($filter, 'databasejoin', $this);
		
			foreach ($this->currentControlFields as $fieldid) 
			{
				if (!isset($this->controlFields[$fieldid]))
					$this->controlFields[$fieldid] = [];

				if (!in_array($element->getId(), $this->controlFields[$fieldid]))
					$this->controlFields[$fieldid][] = $element->getId();
			}
		}
	}

	public function onElementDefault($element, $i)
	{
		$filter = $element->getParam('filter.filter', null, true);

		if ($filter)
		{
			$dbjModel = $this->app->getComponent('field', 'databasejoin', 'fabrik.sqlwhere')->getModel();

			if ($rowid = $dbjModel->getRowId($filter))
				$element->setDefault($rowid, $i);
		}
	}

	public function onAfterData($view, $args)
	{
		$app = \F::getApp();
		$model = $this->getModel();
		$filter = $model->getParam('filter.filter');

		if ($filter)
		{
			$moduleId = $filter['moduleid'];

			$view->setData('plugin.filter', [
				'moduleId' => $moduleId,
				'fieldId' => $filter['fieldid'],
			]);
		}

		// controlFields
		$controlFields = [];
		foreach ($this->controlFields as $parentid => $childids) 
		{
			$controlFields[] = [
				'parentid' => $parentid,
				'childids' => $childids
			];
		}

		$view->setData('plugin.filter.controlFields', $controlFields);
	}

	public function onAfterStore()
	{
		$model = $this->getModel();
		$moduleid = $model->getModuleId() ?? $model->getModuleRefId();

		if ($moduleid)
		{
			$module = $this->app->getService('module', 'helper')->getModule($moduleid);
			$moduleC = $module->getComponent();
			$fieldid = $module->getParam('filter.filter.fieldid');

			if ($moduleC->getBranch() == 'fabrik.form' and $fieldid)
			{
				if ($model->isNewRecord())
				{
					$filterid = $module->getParam('filter.filter.moduleid');
					$filter = $this->app->getComponent('module', 'filter', 'fabrik')->getModel($filterid);

					$field = $filter->getField($fieldid);
					$fParams = $field['Params'];

					$newModel = $this->app->getComponent('fabrik', 'form')->initModel($model->getId(), $moduleid, $moduleC->getModel()->getTable());
					$newModel->setRowId($model->getInsertId());

					$eValue = $newModel->getElement('id');
					$eLabel = $newModel->getElement($fParams['labelid']);

					$this->app->setData('plugin.filter.newOption', [
						'moduleId' => $filterid,
						'fieldid' => $fieldid,
						'option' => [
							'value' => $eValue->getValue(),
							'label' => $eLabel->getValue()
						]
					]);
				}
			}
		}
	}

	public static function onAjaxControlFields()
	{
		$app = \F::getApp();
		$fields = [];

		$value = $app->input->get('value');
		$entityid = $app->input->get('entityid');
		$parentid = $app->input->get('parentid');
		$childids = $app->input->get('childids');
		$modulerefid = $app->input->get('modulerefid');

		$form = $app->getComponent('fabrik', 'form')->initModel($entityid, $modulerefid);
		$form->updFormData($parentid, $value);

		foreach ($childids as $id) 
		{
			$fields[] = [
				'id' => $id,
				'options' => $form->getElement($id)->getOptions()
			];
		}

		return ['fields'=>$fields];
	}
}


