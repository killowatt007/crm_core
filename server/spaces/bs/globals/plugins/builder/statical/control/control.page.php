<?php
namespace bs\spaces\bs\globals\plugins\builder\statical;
defined('EXE') or die('Access');

use bs\libraries;

class PageControl extends libraries\event\PluginModel
{
	public function onAfterData($view, $args)
	{
		$data = [];
		$app = \F::getApp();
		$srvcontrol = $app->getService('system', 'srvcontrol');
		$sessBs = $srvcontrol->getSessionBs();

		// if ($sessBs and $sessBs['UserId'])
		// {
			$space = $app->getSpace();
			$allSpaces = $space->getAll();

			$data = [
				'spaces' => array_values($allSpaces),
				'active' => $space->getId()
			];
		// }

		$view->setData('plugin.control', $data);
	}

	public static function onAjaxChangeSpace()
	{
		$app = \F::getApp();
		$session = $app->getSession();
		$spaceid = $app->input->get('spaceid');

		$session->setCurrent($spaceid);

		if (!$session->userId)
		{
			$redirect = '/';
		}
		else
		{
			$menu = $app->getMenu();
			$start = $menu->getStart();

			$redirect = $start['path'];
		}

		return ['redirect' => $redirect];
	}
}


