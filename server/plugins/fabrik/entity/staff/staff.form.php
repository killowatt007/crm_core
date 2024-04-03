<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

include_once PATH_ROOT .'/components/fabrik/event/pluginForm.php';
use \bs\components\fabrik\event\PluginForm;

class FormStaff extends PluginForm
{
	public function onBeforeStore()
	{
		$model = $this->getModel();

		$user = $this->getCtrl('user');
		$userid = $user->store([
			'Name' => $this->getCV('Login'),
			'Password' => $this->getCV('Password'),
			'RoleId' => $this->getCV('RoleId')
		], ($model->isNewrecord() ? null : $this->getCV('UserId')))->getRowId();

		if ($model->isNewrecord())
		{
			$this->updFD('UserId', $userid);

      $pickCtrl = $this->getCtrl('pick_items');
      $base =  $pickCtrl->getActiveBase();
      $this->updFD('BaseId', $base);
		}
	}
}