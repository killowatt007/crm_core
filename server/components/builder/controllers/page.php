<?php
namespace bs\components\builder\controllers;
defined('EXE') or die('Access');

use \bs\libraries\mvc\Controller; 

class Page extends Controller
{
	public function data()
	{
		$id = $this->app->input->get('id');

		$component = $this->app->getComponent('builder', 'page');
		$model = $component->getModel();
		$model->setId($id);
		$view = $component->getView();
		$data = $view->getData();

		return $data;
	}
}