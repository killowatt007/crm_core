<?php
namespace bs\components\builder\views;
defined('EXE') or die('Access');

use \bs\libraries\mvc\View;

class Page extends View
{
	public function data()
	{
		$data = [];
		$app = \F::getApp();
		$model = $this->getModel();
		$plgManager = $model->getPluginManager();
		
		$data['tmpls'] = $model->getData();
		$data['group'] = $model->getGroup();
		$data['tablename'] = $model->getTable()->getData()['Name'];

		$plgManager->run('afterData', [$this, \F::std(['data'=>&$data])]);
		$app->pluginManager()->run('builder', 'tmplAfterData', [$this]);

		$model->getPluginManager()->setDep();

		return $data;
	}
}