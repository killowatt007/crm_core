<?php
namespace bs\components\fabrik\controllers;
defined('EXE') or die('Access');

use \bs\libraries\mvc\Controller;

class Lst extends Controller
{
	public function data()
	{
		$entityid = $this->app->input->get('id');
		$moduleid = $this->app->input->get('moduleid', null);

		$component = $this->app->getComponent('fabrik', 'list');
		$model = $component->initModel($entityid, $moduleid);
		
		$view = $component->getView();
		$data = $view->getData();

		return $data;
	}
}