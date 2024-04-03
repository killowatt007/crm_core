<?php
namespace bs\libraries\mvc;
defined('EXE') or die('Access');

class Model
{
	protected $id = null;
	protected $params = null;
	public $i;

	protected $regkeys = [];

	private $component = null;
	private $parentObject = null;
	private $plgmanager = null;

	static private $dispatcher = null;

	public function setId($id) { if (!$this->id) $this->id = $id; }
	public function getId() { return $this->id; }
	public function setComponent($component) { if (!$this->component) $this->component = $component; }
	public function getComponent() { return $this->component; }
	public function setParentObject($object) { if (!$this->parentObject) $this->parentObject = $object; }
	public function getParentObject() { return $this->parentObject; }

	public function setParam($params, $val = null)
	{
		$this->params = \F::getRegistry($params);
	}

	public function getParam($key = null, $def = null)
	{
		$par = null;

		if (!$this->params)
		{
			$tParams = json_decode($this->getTable()->getData()['Params'], true);
			$this->setParam($tParams);
		}

		if ($key)
			$par = $this->params->get($key, $def);
		else
			$par = $this->params->toArray();

		return $par;
	}

	protected function getModelData()
	{
		return [];
	}

	public function getPluginManager()
	{
		if (!$this->plgmanager)
		{
			if (!self::$dispatcher)
				self::$dispatcher = new \bs\libraries\event\DispatcherModel();

			$this->plgmanager = new \bs\libraries\event\Manager(self::$dispatcher, $this);
		}

		return $this->plgmanager;
	}
}