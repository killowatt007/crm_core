<?php
namespace bs\components\fabrik\event;
defined('EXE') or die('Access');

class PluginList extends Plugin
{
	public function onAfterData_($view, $args)
	{
		if (method_exists($this, 'obGetActions'))
			$this->obGetActions($view, $args);
	}
}