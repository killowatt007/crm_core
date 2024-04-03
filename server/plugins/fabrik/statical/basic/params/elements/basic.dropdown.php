<?php
namespace bs\plugins\fabrik\statical;
defined('EXE') or die('Access');

use \bs\libraries\Params;

class ParamsElementsDropdownBasic extends Params
{
	public function get()
	{
		$params =	 $this->fields([
			0 => [
				'type' => 'items',
				'name' => 'options',
				'label' => 'Options',
				'labelfield' => 0,
				'fields' => $this->fields([
					0 => [
						'type' => 'field',
						'name' => 'value',
						'label' => 'Value'
					],
					1 => [
						'type' => 'field',
						'name' => 'label',
						'label' => 'Label'
					],
					2 => [
						'type' => 'yesno',
						'name' => 'isdef',
						'label' => 'Default'
					]
				])
			]
		]);

		return $params;
	}
}