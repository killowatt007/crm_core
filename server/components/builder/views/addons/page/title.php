<?php
namespace bs\components\builder\views\addons\page;
defined('EXE') or die('Access');

include_once PATH_ROOT.'/components/builder/addon.php';
use bs\components\builder\Addon;

class Title extends Addon
{
	public function data()
	{
		$data = [];
		$data['title'] = $this->app->getMenu()->getActive()['Name'];

		return $data;
	}

	public static function getParams()
	{
		return null;
	}
}