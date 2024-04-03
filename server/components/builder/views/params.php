<?php
namespace bs\components\builder\views;
defined('EXE') or die('Access');

use \bs\libraries\mvc\View;

class Params extends View
{
	protected $app;
	private $scheme;

	public function __construct($scheme)
	{
		$this->app = \F::getApp();
		$this->scheme = $scheme;
	}

	protected function data()
	{
		$this->each($this->scheme);
		return ['scheme'=>$this->scheme];
	} 

	private function each($data)
	{
		if ($data['type'] != 'fields')
		{
			if (isset($data['items']))
			{
				foreach ($data['items'] as $item) 
				{
					if (isset($item['data']))
						$this->each($item['data']);
				}
			}
		}
		else
		{
			if (isset($data['items']))
			{
				foreach ($data['items'] as $field) 
				{
					$this->app->setDep('components/field/actors/'.$field['type']);

					if (isset($field['fields']))
					{
						foreach ($field['fields'] as $field2) 
							$this->app->setDep('components/field/actors/'.$field2['type']);
					}
				}
			}
		}
	}
}