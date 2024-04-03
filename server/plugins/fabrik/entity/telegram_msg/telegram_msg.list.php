<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

include_once PATH_ROOT .'/components/fabrik/event/pluginList.php';
use \bs\components\fabrik\event\PluginList;

class ListTelegram_msg extends PluginList
{
	public function onButtons($args)
	{
		// $actPage = $this->getCtrl('builder_tmpl')->getActive();
		// $actPage['Alias']

		unset($args->data['buttons']['add']);
	}
}