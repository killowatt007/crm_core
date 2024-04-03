<?php
namespace bs\libraries\input;
defined('EXE') or die('Access');

class Input
{
	private $get = null;
	private $post = null;
	private $cookie = null;

	private $data = null;
	
	public function __construct()
	{
		$get = $this->clear($_GET);
		$post = $this->clear($_POST);
		$cookie = $this->clear($_COOKIE);

		$this->get = \F::getRegistry($get);
	 	$this->post = \F::getRegistry($post);
		$this->cookie = \F::getRegistry($cookie);

		$this->data = \F::getRegistry(array_merge($cookie, $post, $get));
	}

	public function get($key, $def = null)
	{
		$val = $this->data->get($key, $def);

		// if (is_numeric($val))
		// 	$val = (float)$val;

		return $val;
	}

	public function set($key, $val)
	{
		$this->data->set($key, $val);
	}

	private function clear($arr)
	{
		foreach ($arr as $key => &$val) 
		{
			if (is_array($val))
			{
				$val = $this->clear($val);
			}
			else
			{
				if (is_numeric($val))
					$val = (float)$val;
			}
		}

		return $arr;
	}
}

