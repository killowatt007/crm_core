<?php
namespace bs\libraries;
defined('EXE') or die('Access');

class Params
{
	protected $app;
	protected $extraArgs;

	public function __construct($extraArgs = [])
	{
		$this->app = \F::getApp();
		$this->extraArgs = $extraArgs;
	}

	public function fields($fields)
	{
		foreach ($fields as $field)
			\F::getApp()->setDep('components/field/actors/'.$field['type']);

		return $fields;
	}

	public function get()
	{
		return null;
	}
}


