<?php
namespace bs\components\module\views\fabrik\filter;
defined('EXE') or die('Access');

use \bs\libraries\mvc\View;

class Filter extends View
{
	protected function data()
	{
		$data = [];

		$model = $this->getModel();

		$params = $model->getParam();
		$data['relatedModules'] = $this->app->getService('fabrik', 'cascade')->getRelatedModules($model->getId(), 'fabrik.filter');
		
		return $data;
	}
}