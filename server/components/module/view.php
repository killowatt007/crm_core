<?php
namespace bs\components\module;
defined('EXE') or die('Access');

use \bs\libraries\mvc;

class View extends mvc\View
{
	protected function getTable()
	{
		return $this->app->getCtrl('fabrik', 'module')->select('*', $this->getId());
	}
}