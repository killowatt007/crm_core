<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

include_once PATH_ROOT .'/components/fabrik/event/pluginForm.php';
use \bs\components\fabrik\event\PluginForm;

class FormBuilder_tmpl extends PluginForm
{
	public function onAfterData($view)
	{
		$activetmpl = $this->getCtrl('builder_tmpl')->getActive();

		if ($activetmpl['Alias'] == 'template_form')
		{
			$group = 'fabrikform';
			$type = 'table';
			$entitytypeid = 2;
			$parentid = 0;

			$filter = $this->getFilter();
			$entityid = $filter->getFieldValue('entity');
		}
		else
		{
			$group = 'page';
			$type = 'page';
			$entitytypeid = 1;
			$parentid = 5;
			$entityid = 0;
		}

		$view->setData('builder', [
			'type' => $type,
			'group' => $group,
			'popupparams' => $this->getPopupParams($group),
			'entityid' => $entityid,
			'entitytypeid' => $entitytypeid,
			'parentid' => $parentid
		]);
	}

	public function getPopupParams($group)
	{
		$addonList = $this->getAddonList($group);
		$p = \F::getParams();

		return [
			// addon
			'addon' => [
				'type' => 'sections',
				'items' => [
					0 => [
						'size' => 24,
						'data' => [
							'type' => 'fields',
							'items' => $p->fields([
								0 => [
									'type' => 'field',
									'name' => 'adminlabel',
									'label' => 'Admin Label'
								],
								1 => [
									'type' => 'list',
									'name' => 'name',
									'label' => 'Addon',
									'group' => 1,
									'options' => $addonList
								]
							])
						]
					]
				]
			]
		];
	}

	public function getAddonList($group)
	{
		$addons = [];

		// general
		$pathGeneral = PATH_ROOT.'/components/builder/views/addons/general';
		$addons[0]['label'] = 'general';

		foreach (scandir($pathGeneral) as $name) 
		{
			if ($name[0] != '.')
			{
				$info = pathinfo($name);
				$addons[0]['options'][] = [
					'value' => 'general.'.$info['filename'],
					'label' => $info['filename']
				];
			}
		}

		// group
		$pathGroup = PATH_ROOT.'/components/builder/views/addons/'.$group;
		$addons[1]['label'] = 'self';

		foreach (scandir($pathGroup) as $name) 
		{
			if ($name[0] != '.')
			{
				$info = pathinfo($name);
				$addons[1]['options'][] = [
					'value' => $info['filename'],
					'label' => $info['filename']
				];
			}
		}

		return $addons;
	}

	public static function onAjaxGetAddonParams()
	{
		$app = \F::getApp();
		$addon = $app->input->get('addon');
		$group = $app->input->get('addongroup');

		$class = $app->getService('builder', 'helper')->includeAddon($addon, $group);
		$params = $class::getParams();

		return ['params'=>$params];
	}
}



