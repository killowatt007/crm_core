<?php
namespace bs\plugins\fabrik\statical;
defined('EXE') or die('Access');

include_once PATH_ROOT .'/components/fabrik/event/pluginForm.php';
use \bs\components\fabrik\event\PluginForm;

class FormValidation extends PluginForm
{
	public function onBeforeProcess()
	{
		$model = $this->getModel();
		$fabvalid = $this->getCtrl('fabrik_validate');

		$rules = $fabvalid->select('*', 'FieldId IN (SELECT id FROM &__fabrik_field WHERE EntityId='.$model->getId().')');
		$rulesGroup = [];

		foreach ($rules as $rule) 
		{
			$fid = $rule['FieldId'];

			if (!isset($rulesGroup[$fid]))
				$rulesGroup[$fid] = [];

			$rulesGroup[$fid][] = $rule;
		}

		$validation = [];
		foreach ($rulesGroup as $fid => $rules) 
		{
			$element = $model->getElement($fid);
			$elname = $element->getName();
			$ellabel = $element->getLabel();

			foreach ($rules as $rule) 
			{
				$value = trim($this->getCV($elname));

				if ($element->getType() == 'databasejoin')
				{
					if (!(int)$value)
						$value = '';
				}

				// notempty
				if ($rule['Rule'] == 'notempty')
				{
					if ($value == '')
						$validation[] = 'Поле "'.$ellabel.'" - обязательно для заполнения';
				}
			}
		}

		$model->validation = $validation;
	}
}


