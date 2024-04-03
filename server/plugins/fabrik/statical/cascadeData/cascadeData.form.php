<?php
namespace bs\plugins\fabrik\statical;
defined('EXE') or die('Access');

include_once __DIR__ .'/cascadeData.php';
include_once PATH_ROOT .'/components/fabrik/event/pluginForm.php';
use \bs\components\fabrik\event\PluginForm;

class FormCascadeData extends PluginForm
{
	public function onAfterData($view) 
	{
		CascadeData::cascadeData($this, 'form');
	}
}



