<?php
namespace bs\libraries\obj;
defined('EXE') or die('Access');

use \bs\libraries\obj\Obj; 

class Actor extends Obj
{
	private $data = [];

	protected function data() { return []; }
	protected function rdata() { return []; }
	protected function gdata() { return []; }

	public function getData()
	{
		$data = $this->rdata();
		$vdata = $this->data();
		$gdata = $this->gdata();

		$data['opts'] = array_merge($this->data, $vdata, $gdata);

		return $data;
	}

	public function setData($key, $value)
	{
		$keyArr = explode('.', $key);
		$data = &$this->data;

		foreach ($keyArr as $i => $key) 
		{
			if (!isset($data[$key]))
				$data[$key] = [];

			$data = &$data[$key];
		}

		$data = $value;
	}
}