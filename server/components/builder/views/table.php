<?php
namespace bs\components\builder\views;
defined('EXE') or die('Access');

use \bs\libraries\mvc\View;

class Table extends View
{
	public function data()
	{
		$data = [];
		$app = \F::getApp();
		$model = $this->getModel();
		
		$data['tmpls'] = $model->getData();
		$data['group'] = $model->getGroup();

		// $app->pluginManager()->run('builder', 'tmplAfterData', [$this]);

		return $data;
	}
}