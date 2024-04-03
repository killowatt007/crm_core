<?php
namespace bs\libraries\event;
defined('EXE') or die('Access');

use \bs\libraries\obj\Obj; 

class Plugin extends Obj
{
	protected $app;
	
	public function __construct()
	{
		$this->app = \F::getApp();
	}
}