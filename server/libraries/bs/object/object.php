<?php
namespace bs\libraries\obj;
defined('EXE') or die('Access');

class Obj
{
	protected $app;

	public function __construct()
	{
		$this->app = \F::getApp();
	}
}