<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

/**
 * $version 1.1
 */

use \bs\components\fabrik\Ctrl;

class CtrlSuper_logs extends Ctrl
{
	private $models = [];

	public function init($name)
	{
		$model = $this->store([
			'Name' => $name
		]);

		return $model->getInsertId();
	}

	public function add($logid, $data)
	{
		$ctrl = $this->app->getCtrl('fabrik', 'super_logs_items');

		if (!isset($this->models[$logid]))
			$this->models[$logid] = $ctrl->getModel();

		$model = $this->models[$logid];

		$this->app->getCtrl('fabrik', 'super_logs_items')->store([
			'Data' => json_encode($data, JSON_UNESCAPED_UNICODE),
			'LogId' => $logid
		], null, $model);

		$model->closeDBO();
	}
}



