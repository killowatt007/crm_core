<?php
namespace bs\components\system\controllers;
defined('EXE') or die('Access');

use \bs\libraries\mvc\Controller; 

class Ajax extends Controller
{
	public function data()
	{
		$app = \F::getApp();
		$exttype = $app->input->get('exttype');
		$method = $app->input->get('method');
		$data = [];

		// module
		if ($exttype == 'module')
		{
			$name = $app->input->get('name');
			$path = null;
			$format = null;
			$space = null;
			$isglobal = null;
		}

		// plugin
		elseif ($exttype == 'plugin')
		{
			$name = $app->input->get('name');
			$path = $app->input->get('group').'.'.$app->input->get('type');
			$format = $app->input->get('format');
			$space = $app->input->get('space');
			$isglobal = $app->input->get('isglobal');
		}

		$className = $app->includeExt($exttype, $name, $path, $format, $space, $isglobal);
		$fullMethod = 'onAjax'.ucfirst($method);
		
		if (method_exists($className, $fullMethod))
			$data = $className::$fullMethod();

		return $data;
	}
}
