<?php
namespace bs\components\fabrik;
defined('EXE') or die('Access');

class Attr
{
	public $data;
	private $params = null;

	private $dbn = null;

	public function __construct($data)
	{
		$this->data = $data;
	}

	public function getParam($key = null, $def = null)
	{
		$par = null;

		if (!$this->params)
		{
			$params = json_decode($this->data['Params'], true);
			$this->params = \F::getRegistry($params);
		}

		if ($key)
			$par = $this->params->get($key, $def);
		else
			$par = $this->params->toArray();

		return $par;
	}
}


