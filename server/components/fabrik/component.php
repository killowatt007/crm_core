<?php
namespace bs\components\fabrik;
defined('EXE') or die('Access');

use \bs\libraries\mvc;

class Component extends mvc\Component
{
	public function initModel($key, $moduleid = null, $mdata = null, $modulerefid = null)
	{
		if (!$this->model)
		{
			$table = \F::getApp()->getService('fabrik', 'helper')->getTable($key);

			$this->model = parent::getModel();
			$this->model->setId($table->getId());
			
			if ($moduleid)
				$this->model->initModule($moduleid, $mdata);
			if ($modulerefid)
				$this->model->setModuleRefId($modulerefid);

			$this->model->getPluginManager()->run('init');
		}

		return $this->model;
	}

	public function getView(...$arr)
	{
		$view = parent::getView();
		$view->getModel()->isview = true;

		return $view;
	}
}


