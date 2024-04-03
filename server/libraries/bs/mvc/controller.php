<?php
namespace bs\libraries\mvc;
defined('EXE') or die('Access');

class Controller
{
	protected $app;

	public function __construct()
	{
		$this->app = \F::getApp();
	}
}