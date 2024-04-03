<?php
namespace bs\components\builder;
defined('EXE') or die('Access');

class Table
{
	private $data;

	public function __construct($data)
	{
		$this->data = $data;
	}

	public function getData()
	{
		return $this->data;
	}
}


