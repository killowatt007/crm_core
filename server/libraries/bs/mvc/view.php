<?php
namespace bs\libraries\mvc;
defined('EXE') or die('Access');

use \bs\libraries\obj\Actor;

class View extends Actor
{
	private $component = null;

	public function setComponent($component) { if (!$this->component) $this->component = $component; }
	public function getComponent() { return $this->component; }

	protected function rdata() 
	{ 
		$model = $this->getModel();
		$component = $this->getComponent();
		$classArr = explode('\\', get_class($this));

		return [
			'id'	=> $model->getId(),
			'group' => $component->getGroup(),
			'name' => $component->getName(),
			'branch' => $component->getBranch()
		];
	}

	public function getModel()
	{
		return $this->getComponent()->getModel();
	}
}