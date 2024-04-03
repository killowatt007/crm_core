<?php
namespace bs\components\module\views\acquiring;
defined('EXE') or die('Access');

use \bs\libraries\mvc\View;

class Acquiring extends View
{
	protected function data()
	{
		$data = [];

		// $data['options'] = \F::getHelper('arr')->rebuild(
		// 	$this->app->getCtrl('fabrik', 'module')->select('id, Name', 'Alias="history"'),
		// 	['value'=>'id', 'label'=>'Name']
		// );

		return $data;
	}
}