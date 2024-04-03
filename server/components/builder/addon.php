<?php
namespace bs\components\builder;
defined('EXE') or die('Access');

use \bs\libraries\mvc\View;

class Addon extends View
{
	private $pmodel = null;

	protected function rdata()
	{
		$model = $this->getModel();

		$data = [
			'type' => 'addon',
			'group' => 'builder',
			'branch' => $model->getParam('branch'),
			'name' => $model->getParam('name')
		];

		return $data;
	}

	public function setPageModel($pmodel)
	{
		if (!$this->pmodel)
			$this->pmodel = $pmodel;
	}

	public function getPageModel()
	{
		return $this->pmodel;
	}
}


