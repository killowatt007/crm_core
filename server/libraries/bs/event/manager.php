<?php
namespace bs\libraries\event;
defined('EXE') or die('Access');

class Manager
{
	public $dispatcher;
	private $modelsObserve = [];

	public function __construct($dispatcher, $model)
	{
		$this->dispatcher = $dispatcher;
		$this->setObserveForModel($model, null);
	}

	public function run($event, $args = [])
	{
		$result = [];

		foreach ($this->modelsObserve as $i => $model) 
		{
			$this->dispatcher->model = $model['object'];
			$submodel = !$i ? null : $this->modelsObserve[0]['object'];
			$prefix = !$i ? null : $model['prefix'];

			$_result = $this->dispatcher->run($event, $args, $submodel, $prefix);

			if (!$i)
				$result = $_result;
		}

		return $result;
	}

	public function setDep($type = null)
	{
		$this->dispatcher->model = $this->modelsObserve[0]['object'];
		$this->dispatcher->setDep($type);
	}

	public function setObserveForModel($model, $prefix)
	{
		$this->modelsObserve[] = [
			'object' => $model,
			'prefix' => $prefix
		];
	}
}