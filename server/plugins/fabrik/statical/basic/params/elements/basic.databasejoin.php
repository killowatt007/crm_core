<?php
namespace bs\plugins\fabrik\statical;
defined('EXE') or die('Access');

use \bs\libraries\Params;

class ParamsElementsDatabasejoinBasic extends Params
{
	public function get()
	{
		$CTRLentity = $this->app->getCtrl('fabrik', 'fabrik_entity');
		$entities = \F::getHelper('arr')->rebuild($CTRLentity->select('id, Name'), ['value'=>'id', 'label'=>'Name']);

		$params = $this->fields([
			0 => [
				'type' => 'list',
				'name' => 'type',
				'label' => 'Type',
				'isps' => 0,
				'options' => [
					0 => [
						'value' => 'dropdown',
						'label' => 'Dropdown'
					],
					1 => [
						'value' => 'multilist',
						'label' => 'Multilist'
					]
				]
			],
			1 => [
				'type' => 'field',
				'name' => 'space',
				'label' => 'Space',
				'default' => 2
			],
			2 => [
				'type' => 'field',
				'name' => 'connection',
				'label' => 'Connection',
				'default' => 1
			],
			3 => [
				'type' => 'list',
				'name' => 'join_entity',
				'label' => 'Entity',
				'options' => $entities
			],
			4 => [
				'type' => 'fabrik/elements',
				'name' => 'join_key',
				'label' => 'Value',
				'table' => 'join_entity',
				'options' => []
			],
			5 => [
				'type' => 'fabrik/elements',
				'name' => 'join_val',
				'label' => 'Label',
				'table' => 'join_entity',
				'options' => []
			],
			6 => [
				'type' => 'list',
				'name' => 'search',
				'label' => 'Search',
				'options' => [
					['value' => '=', 'label' => '='],
					['value' => '!=', 'label' => '!='],
					['value' => 'regexp', 'label' => 'regexp'],
					['value' => 'regexp_w', 'label' => 'regexp_w'],
					['value' => 'regexp_wf', 'label' => 'regexp_wf']
				]
			],
		]);

		return $params;
	}
}