<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

use \bs\libraries\Params;

class ParamsSelfFabrik_entity extends Params
{
	public function get()
	{
		$params = [
			'type' => 'tabs',
			'name' => 'Params',
			'items' => [
				0 => [
					'label' => 'Form',
					'name' => 'form',
					'data' => [
						'type' => 'sections',
						'items' => $this->getBlockParams('form')
					]
				],
				1 => [
					'label' => 'List',
					'name' => 'list',
					'data' => [
						'type' => 'sections',
						'items' => $this->getBlockParams('list')
					]
				]
			]
		];

		return $params;
	}

	public function getBlockParams($type)
	{
		$tmpldid = $this->extraArgs['tmplid'] ?? null;

		$classBasic = $this->app->includeExt('plugin', 'basic', 'fabrik.statical', ['params', $type]);
		$objBasic = new $classBasic(['entityid'=>$this->extraArgs['entityid']]);

		$classFilter = $this->app->includeExt('plugin', 'filter', 'fabrik.statical', ['params', $type]);
		$objFilter = new $classFilter(['tmplid'=>$tmpldid]);

		return [
			0 => [
				'size' => 12,
				'label' => 'Basic',
				'name' => 'basic',
				'data' => [
					'type' => 'fields',
					'view' => 'inline',
					'items' => $objBasic->get()
				]
			],
			1 => [
				'size' => 12,
				'label' => 'Filter',
				'name' => 'filter',
				'data' => [
					'type' => 'fields',
					'view' => 'inline',
					'items' => $objFilter->get()
				]
			]
		];
	}
}