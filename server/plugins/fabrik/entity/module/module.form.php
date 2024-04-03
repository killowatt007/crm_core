<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

include_once PATH_ROOT .'/components/fabrik/event/pluginForm.php';
use \bs\components\fabrik\event\PluginForm;

class FormModule extends PluginForm
{
	public function onElementDefault($element, $i)
	{
		// TmplId
		if ($element->getName() == 'TmplId')
		{
			if ($filter = $this->getFilter())
			{
				$tmplid = $filter->getFieldValue('tmplid');
				$element->setDefault($tmplid, $i);
			}
		}
	}

	public function onBeforeStore()
	{
		$model = $this->getModel();
		$params = $this->getCV('Params');

		$this->updFD('Params', json_encode($params));
	}

	public static function onAjaxGetComponentParams()
	{
		$result = ['params'=>null];
		$app = \F::getApp();
		$tmodule = $app->input->get('tmodule');

		$classParams = $app->includeExt('plugin', 'module', 'fabrik.entity', ['params.module', $tmodule]);

		if ($classParams)
		{
			$objParams = new $classParams();

			if ($params = $objParams->get())
			{
				$result['params'] = $app->getComponent('builder', 'params')->getView([
					'type' => 'sections',
					'items' => [
						0 => [
							'size' => 24,
							'label' => 'Params',
							'name' => 'Params',
							'data' => $params
						]
					]
				])->getData();
			}
		}

		return $result;
	}
}