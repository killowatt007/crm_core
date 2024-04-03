<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

include_once PATH_ROOT .'/components/fabrik/event/pluginForm.php';
use \bs\components\fabrik\event\PluginForm;

class FormShifts extends PluginForm
{
	public static function onAjaxOpenShift()
	{
		$result = [];
		$app = \F::getApp();
		$kkt = $app->getCtrl('fabrik', 'kkt');
		$shiftData = $kkt->open_shift();

		$st_open = 9;

		if (!$kkt->error)
		{
			if (!$shiftData)
				$shiftNumber = $kkt->get_shift_status()['number'];
			else
				$shiftNumber = $shiftData['shiftNumber'];

			$activeStaff = $app->getCtrl('fabrik', 'staff')->getActive();

			$app->getCtrl('fabrik', 'shifts')->store([
		    'id' => $shiftNumber,
		    'StaffId' => $activeStaff['id'],
		    'StatusId' => $st_open,
		    'DateOpen' => date('Y-m-d H:i:s')
			]);
		}
		else
		{
			$result = ['error' => $kkt->error];
		}

		return $result;
	}

	public static function onAjaxCloseShift()
	{
		$result = [];
		$app = \F::getApp();
		$kkt = $app->getCtrl('fabrik', 'kkt');
		$shiftData = $kkt->close_shift();

		$st_close = 10;

		if (!$kkt->error)
		{
			if (!$shiftData)
				$shiftNumber = $kkt->get_shift_status()['number'];
			else
				$shiftNumber = $shiftData['shiftNumber'];

			$activeStaff = $app->getCtrl('fabrik', 'staff')->getActive();

			$app->getCtrl('fabrik', 'shifts')->store([
		    'StaffId' => $activeStaff['id'],
		    'StatusId' => $st_close,
		    'DateClose' => date('Y-m-d H:i:s')
			], $shiftNumber);
		}
		else
		{
			$result = ['error' => $kkt->error];
		}

		return $result;
	}
}