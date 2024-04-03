<?php
namespace bs\spaces\bs\globals\plugins\system;
defined('EXE') or die('Access');

class Scontrol
{
	public function onBeforeAuthorise()
	{
		$app = \F::getApp();
		$srvcontrol = $app->getService('system', 'srvcontrol');
		$sessBs = $srvcontrol->getSessionBs();

		if ($sessBs and !$sessBs['UserId'] and !$sessBs['IsCurrent'])
		{
			$session = $app->getSession();
			$spaceBs = $srvcontrol->getSpaceBs();

			$session->setCurrent($spaceBs['id']);
			$app->redirect('/');
		}
	}
}


