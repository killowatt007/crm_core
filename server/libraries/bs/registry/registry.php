<?php
namespace bs\libraries\registry;
defined('EXE') or die('Access');

class Registry
{
	private $data = null;

	public function __construct($data = null)
	{
		if ($data)
			$this->loadArray($data);
	}

	public function loadArray($array)
	{
		$this->data = $array;
	}

	public function get($key, $def = null)
	{
		$data = $this->data;
		$keyArr = explode('.', $key);

		foreach ($keyArr as $key) 
		{
			if (isset($data[$key]))
			{
				$data = $data[$key];
			}
			else
			{
				$data = $def;
				break;
			}
		}

		return $data;
	}

	public function set($key, $val)
	{
		$keyArr = explode('.', $key);
		$arr = &$this->data;

		$test = $key;

		foreach ($keyArr as $i => $key) 
		{
			if (count($keyArr) != ($i+1))
			{
				if (!isset($arr[$key]))
					$arr[$key] = [];

				$arr = &$arr[$key];
			}
			else
			{
				$arr[$key] = $val;
			}
		}
	}

	public function toArray()
	{
		return $this->data;
	}
}