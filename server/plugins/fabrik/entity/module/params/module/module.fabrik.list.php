<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

use \bs\libraries\Params;

class ParamsModuleFabrikListModule extends Params
{
	public function get()
	{
		$CTRLentity = \F::getApp()->getCtrl('fabrik', 'fabrik_entity');
		$entities = \F::getHelper('arr')->rebuild($CTRLentity->select('id, Name'), ['value'=>'id', 'label'=>'Name']);

		$params = [
			'type' => 'fields',
			'view' => 'inline',
			'items' => [
				0 => [
					'type' => 'fabrik/entity',
					'name' => 'entityid',
					'label' => 'Entity',
					'view' => 'list',
					'options' => $entities
				]
			]
		];

		return $params;
	}
}