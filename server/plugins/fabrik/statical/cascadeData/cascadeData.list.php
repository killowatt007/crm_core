<?php
namespace bs\plugins\fabrik\statical;
defined('EXE') or die('Access');

include_once __DIR__ .'/cascadeData.php';
include_once PATH_ROOT .'/components/fabrik/event/pluginList.php';
use \bs\components\fabrik\event\PluginList;

class ListCascadeData extends PluginList
{
	public function onAfterData($view) 
	{
		CascadeData::cascadeData($this, 'list');
	}
}



