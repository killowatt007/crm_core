<?php
namespace bs\components\module\views\sklad;
defined('EXE') or die('Access');

use \bs\libraries\mvc\View;

class Tree_catalog extends View
{
	protected function data()
	{
		$model = $this->getModel();

		return [];
	}
}