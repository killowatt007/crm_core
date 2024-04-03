<?php
namespace bs\libraries\event;
defined('EXE') or die('Access');

class PluginModel extends Plugin
{
	private $model = null;
	private $submodel = null;

	public function setSubmodel($submodel)
	{
		$this->submodel = $submodel;
	}

	public function getSubmodel()
	{
		return $this->submodel;
	}

	public function setModel($model)
	{
		$this->model = $model;
	}

	public function getModel($key = null, $name = null)
	{
		return $this->model;
	}
}