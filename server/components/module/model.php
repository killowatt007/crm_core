<?php
namespace bs\components\module;
defined('EXE') or die('Access');

use \bs\libraries\mvc;

class Model extends mvc\Model
{
	protected $app;

	public function __construct()
	{
		$this->app = \F::getApp();
	}

	public function getTable()
	{
		return $this->app->getCtrl('fabrik', 'module')->select('*', $this->getId());
	}

	public function getParam($key = null, $def = null)
	{
		$par = null;

		if (!$this->params)
		{
			$data = $this->app->getCtrl('fabrik', 'module')->select('*', $this->getId());
			
			$tParams = json_decode($data['Params'], true);
			$this->setParam($tParams);
		}

		if ($key)
			$par = $this->params->get($key, $def);
		else
			$par = $this->params->toArray();

		return $par;
	}
}