<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

include_once PATH_ROOT .'/components/fabrik/event/pluginForm.php';
use \bs\components\fabrik\event\PluginForm;

class FormFilter_fields extends PluginForm
{
	public function onBeforeStore()
	{
		// qqq($this->getCV('Params'));
		$this->updFD('Params', json_encode($this->getCV('Params')));
	}

	public function onAfterData($view, $args)
	{
		// $params = json_decode($args->data['rows'][0]['Params'], true);
		// qqq($params['filter']);
	}

	public static function onAjaxGetTypeParams()
	{
		$result = ['params'=>null];
		$app = \F::getApp();
		$type = $app->input->get('ftype');

		$classParams = $app->includeExt('plugin', 'filter_fields', 'fabrik.entity', ['params', 'self']);
		$objParams = new $classParams(['type'=>$type]);

		if ($params = $objParams->get())
			$result['params'] = $params;

		return $result;
	}
}