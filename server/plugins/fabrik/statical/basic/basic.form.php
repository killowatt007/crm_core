<?php
namespace bs\plugins\fabrik\statical;
defined('EXE') or die('Access');

include_once PATH_ROOT .'/components/fabrik/event/pluginForm.php';
use \bs\components\fabrik\event\PluginForm;

class FormBasic extends PluginForm
{
	public function onBeforeStore()
	{
		$this->defaultStatus();
	}

	private function defaultStatus()
	{
		$model = $this->getModel();
		$element = $model->getElement('StatusId');

		if ($element)
		{
			if (!$this->getCV('StatusId'))
				$this->updFD('StatusId', 1);
		}
	}

	public function onElementValue($el, $i)
	{
		$model = $this->getModel();

		// Number
		if ($el->getName() == 'Number')
		{
			if ($model->getRowId())
			{
				$data = $model->getData()[$i];
				$prefixid = $el->getParam('number_prefix');

				if ($prefixid)
				{
					$numberData = $this->getCtrl('pick_items')->select('StrValue', $prefixid);
					$value = $numberData['StrValue'].str_pad($data['id'], 6, 0, STR_PAD_LEFT);
					$el->setValue($value, $i);
				}
			}
		}
	}

	public function onElementStore($element)
	{
		$model = $element->getModel();

		// DateCreate
		if ($element->getName() == 'DateCreate')
		{
			if ($model->isNewRecord())
				$model->updFormData('DateCreate', date('Y-m-d H:i:s'));
		}

		// DateUpdate
		if ($element->getName() == 'DateUpdate')
		{
			$model->updFormData('DateUpdate', date('Y-m-d H:i:s'));
		}
	}

	public function onActions($args)
	{
		$args->data['actions']['save'] = [
			'name' => 'save',
			'position' => 'left',
			'label' => 'Сохранить',
			'color' => 'success',
			'order' => 0
		];

		$args->data['actions']['close'] = [
			'name' => 'close',
			'position' => 'right',
			'type' => 'button',
			'class' => 'gb',
			'label' => 'Закрыть',
			'color' => 'default',
			'order' => 20
		];
	}
}