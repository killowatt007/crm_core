<?php
namespace bs\components\module\views\fabrik\lst;
defined('EXE') or die('Access');

use \bs\libraries\mvc\View;

class Lst extends View
{
	private $fabcomp = null;

	protected function data()
	{
		$comp = $this->getFabComp();
		$view = $comp->getView();

		$data['view'] = $view->getData();

		return $data;
	}

	public function getFabComp()
	{
		if (!$this->fabcomp)
		{
			$model = $this->getModel();
			$entityid = $model->getParam('entityid');

			$this->fabcomp = $this->app->getComponent('fabrik', 'list');
			$this->fabcomp->initModel($entityid, $model->getId(), $model->getTable());
		}

		return $this->fabcomp;
	}
}