<?php
namespace bs\plugins\fabrik\statical;
defined('EXE') or die('Access');

use \bs\libraries\Params;

class ParamsElementsYesnoBasic extends Params
{
	public function get()
	{
		$params = $this->fields([
			0 => [
				'type' => 'list',
				'name' => 'list_edit',
				'label' => 'List Edit',
				'isps' => 0,
				'default' => 0,
				'options' => [
					0 => [
						'value' => 1,
						'label' => 'Yes'
					],
					1 => [
						'value' => 0,
						'label' => 'No'
					]
				]
			],
			1 => [
				'type' => 'list',
				'name' => 'list_label',
				'label' => 'List Label',
				'isps' => 0,
				'options' => [
					0 => [
						'value' => 1,
						'label' => 'Yes'
					],
					1 => [
						'value' => 0,
						'label' => 'No'
					]
				]
			],
			2 => [
				'type' => 'list',
				'name' => 'default',
				'label' => 'Default',
				'default' => 0,
				'isps' => 0,
				'options' => [
					0 => [
						'value' => 1,
						'label' => 'Yes'
					],
					1 => [
						'value' => 0,
						'label' => 'No'
					]
				]
			]
		]);

		return $params;
	}
}