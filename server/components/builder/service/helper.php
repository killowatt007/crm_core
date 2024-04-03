<?php
namespace bs\components\builder\service;
defined('EXE') or die('Access');

class Helper
{
	public function includeAddon($path, $group)
	{
		include_once PATH_ROOT.'/components/builder/addon.php';

		$pathArr = explode('.', $path);

		$group = isset($pathArr[1]) ? $pathArr[0] : $group;
		$name = isset($pathArr[1]) ? $pathArr[1] : $pathArr[0];

		$pathf = PATH_ROOT.'/components/builder/views/addons/'.$group.'/'.$name.'.php';
		$class = '\bs\components\builder\views\addons\\'.$group.'\\'.ucfirst($name);
		include_once $pathf;

		return $class;
	}
}


