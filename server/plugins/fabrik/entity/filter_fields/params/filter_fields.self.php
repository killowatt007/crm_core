<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

use \bs\libraries\Params;

class ParamsSelfFilter_fields extends Params
{
	public function get()
	{
		$params = null;
		$type = $this->extraArgs['type'];

		// fabrik
		if ($type == 'fabrik')
		{
			$CTRLentity = $this->app->getCtrl('fabrik', 'fabrik_entity');
			$entities = \F::getHelper('arr')->rebuild($CTRLentity->select('id, Name'), ['value'=>'id', 'label'=>'Name']);

			$params = $this->app->getComponent('builder', 'params')->getView([
				'type' => 'sections',
				'name' => 'Params',
				'items' => [
					0 => [
						'size' => 24,
						'label' => 'Params',
						'data' => [
							'type' => 'fields',
							'view' => 'inline',
							'items' => [
								0 => [
									'type' => 'list',
									'name' => 'entityid',
									'label' => 'Entity',
									'options' => $entities
								],
								1 => [
									'type' => 'fabrik/elements',
									'name' => 'labelid',
									'label' => 'Label',
									'table' => 'entityid',
									'options' => []
								],
								2 => [
									'type' => 'yesno',
									'name' => 'isapply',
									'label' => 'Apply'
								],
								3 => [
									'type' => 'fabrik/sqlwhere',
									'name' => 'filter',
									'label' => 'Filter',
									'flag' => 'filter'
								]
							]
						]
					]
				]
			])->getData();
		}

		// input
		elseif ($type == 'input')
		{
			$params = $this->app->getComponent('builder', 'params')->getView([
				'type' => 'sections',
				'name' => 'Params',
				'items' => [
					0 => [
						'size' => 24,
						'label' => 'Params',
						'data' => [
							'type' => 'fields',
							'view' => 'inline',
							'items' => [
								0 => [
									'type' => 'yesno',
									'name' => 'isapply',
									'label' => 'Apply'
								]
							]
						]
					]
				]
			])->getData();
		}

		elseif ($type == 'add')
		{
			$CTRLmodule = $this->app->getCtrl('fabrik', 'module');
			$modules = \F::getHelper('arr')->rebuild($CTRLmodule->select('id, Name'), ['value'=>'id', 'label'=>'Name']);

			$params = $this->app->getComponent('builder', 'params')->getView([
				'type' => 'sections',
				'name' => 'Params',
				'items' => [
					0 => [
						'size' => 24,
						'label' => 'Params',
						'data' => [
							'type' => 'fields',
							'view' => 'inline',
							'items' => [
								0 => [
									'type' => 'list',
									'name' => 'moduleid',
									'label' => 'Module',
									'options' => $modules
								]
							]
						]
					]
				]
			])->getData();
		}

		return $params;
	}
}