<?php
namespace bs\plugins\fabrik\statical;
defined('EXE') or die('Access');

use \bs\libraries\Params;

class ParamsElementsDateBasic extends Params
{
	public function get()
	{
		$params = $this->fields([
			0 => [
				'type' => 'list',
				'name' => 'form_edit',
				'label' => 'Form Edit',
				'isps' => 0,
				'options' => [
					0 => ['value' => 1, 'label' => 'Yes'],
					1 => ['value' => 0, 'label' => 'No']
				]
			],
			1 => [
				'type' => 'list',
				'name' => 'alwaystoday',
				'label' => 'Always Today',
				'isps' => 0,
				'default' => 0,
				'options' => [
					0 => ['value' => 1, 'label' => 'Yes'],
					1 => ['value' => 0, 'label' => 'No']
				]
			],
			2 => [
				'type' => 'list',
				'name' => 'defaulttotoday',
				'label' => 'Default Totoday',
				'isps' => 0,
				'default' => 0,
				'options' => [
					0 => ['value' => 1, 'label' => 'Yes'],
					1 => ['value' => 0, 'label' => 'No']
				]
			]
		]);

		return $params;
	}
}