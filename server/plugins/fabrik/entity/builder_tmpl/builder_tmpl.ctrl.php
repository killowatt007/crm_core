<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

use \bs\components\fabrik\Ctrl;

class CtrlBuilder_tmpl extends Ctrl
{
	public function getActive()
	{
		$active = $this->app->getMenu()->getActive();
		parse_str($active['Link'], $vars);
		$tn = $this->getTable()->getData()['Name'];

		return $this->select('*', $vars['id']);
	}
}



