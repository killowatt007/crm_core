<?php
namespace bs\components\module\views\fabrik\form;
defined('EXE') or die('Access');

use \bs\libraries\mvc\View;

class Form extends View
{
	protected function data()
	{
		$model = $this->getModel();

		$entityid = $model->getParam('entityid');
		$model = $this->app->getComponent('fabrik', 'form')->initModel($entityid, $model->getId(), $model->getTable());

		$view = $model->getComponent()->getView();
		$data['view'] = $view->getData();

		return $data;
	}
}