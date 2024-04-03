<?php
namespace bs\components\fabrik\event;
defined('EXE') or die('Access');

use bs\libraries;

class Plugin extends libraries\event\PluginModel
{
	public function getCtrl($key = null)
	{
		$key = $key ? $key : $this->getModel()->getId();
		return \F::getApp()->getCtrl('fabrik', $key);
	}

	public function getFilter($alias = 'main')
	{
		$filter = null;
		$model = $this->getModel();
		$input = $this->app->input;

		if ($model->getModuleRefId() and $input->get('task') != 'form.process')
			$filter = $this->app->getService('fabrik', 'helper')->getFilter($alias);

		return $filter;
	}
}


