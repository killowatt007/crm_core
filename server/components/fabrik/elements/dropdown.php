<?php
namespace bs\components\fabrik;
defined('EXE') or die('Access');

class ElementDropdown extends Element
{
	public function data()
	{
		$data['options'] = $this->getParam('options');

		return $data;
	}

	public function getFtype()
	{
		return 'list';
	}
}