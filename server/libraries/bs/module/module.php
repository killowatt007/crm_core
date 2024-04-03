<?php
namespace bs\libraries\module;
defined('EXE') or die('Access');

use \bs\libraries\obj\Actor;

class Module extends Actor
{
	protected $app;

	public $id;
	public $name;
	private $params;

	public function __construct($data)
	{
		$this->app = \F::getApp();

		$this->id = $data['id'];
		$this->name = $data['Module'];
		$this->params = \F::getRegistry($data['Params']);
	}

	protected function rdata()
	{
		$data = [
			'id' => $this->id,
			'name' => $this->name
		];

		return $data;
	}

	public function getParam($key = null, $def = null)
	{
		$par = null;

		if ($key)
			$par = $this->params->get($key, $def);
		else
			$par = $this->params->toArray();

		return $par;
	}

	public static function getParams()
	{
		return null;
	}
}