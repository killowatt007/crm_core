<?php
namespace bs\plugins\fabrik\statical;
defined('EXE') or die('Access');

use \bs\libraries\Params;

class ParamsElementsInternalidBasic extends Params
{
	public function get()
	{
		$params = $this->fields([
			0 => [
				'type' => 'list',
				'name' => 'auto_increment',
				'label' => 'Auto Increment',
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