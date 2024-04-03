<?php
namespace bs\components\fabrik\event;
defined('EXE') or die('Access');

class PluginForm extends Plugin
{
	// get current value
	public function getCV($name)
	{
		$value = null;
		$model = $this->getModel();
		$element = $model->getElement($name);

		if ($model->getFormData())
			$value = $element->getStoreValue();
			
		if ($value === null)
			$value = $element->getValue();

		return $value;
	}

	public function updFD($name, $value)
	{
		$this->getModel()->updFormData($name, $value);
	}
}