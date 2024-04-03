<?php
namespace bs\components\fabrik\event;
defined('EXE') or die('Access');

class PluginElement extends Plugin
{
	public $modelfield = null;

	public function getModelField()
	{
		return $this->modelfield;
	}
}