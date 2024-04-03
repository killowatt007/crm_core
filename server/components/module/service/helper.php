<?php
namespace bs\components\module\service;
defined('EXE') or die('Access');

class Helper
{
	public function __construct()
	{
		$this->app = \F::getApp();
	}

	public function getModule($id)
	{
		$moduleData = $this->app->getCtrl('fabrik', 'module')->select('id, Module', $id);

		$branch = $moduleData['Module'];
		$branchArr = explode('.', $branch);
		$name = end($branchArr);

		return $this->app->getComponent('module', $name, $branch)->getModel($moduleData['id']);
	}
}


