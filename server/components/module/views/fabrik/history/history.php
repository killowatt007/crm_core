<?php
namespace bs\components\module\views\fabrik\history;
defined('EXE') or die('Access');

use \bs\libraries\mvc\View;

class History extends View
{
	protected function data()
	{
		$data = [];

		$data['options'] = \F::getHelper('arr')->rebuild(
			$this->app->getCtrl('fabrik', 'module')->select('id, Name', 'Alias="history"'),
			['value'=>'id', 'label'=>'Name']
		);

		return $data;
	}
}