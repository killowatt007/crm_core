<?php
namespace bs\components\system\service;
defined('EXE') or die('Access');

class Srvcontrol
{
	private $spaceSwc = null;

	public function getSessionBs()
	{
		$app = \F::getApp();
		$dboc = \F::getDBO('bs', 'c');
		$session = $app->getSession();
		$spaceSwc = $this->getSpaceBs();

		$sessBsData = $dboc
			->setQuery('SELECT UserId, IsCurrent FROM &__session WHERE SpaceId='.$spaceSwc['id'])
			->loadAssoc();

		return $sessBsData;
	}

	public function getSpaceBs()
	{
		if (!$this->spaceSwc)
		{
			$app = \F::getApp();
			$space = $app->getSpace();
			$allSpaces = $space->getAll();

			foreach ($allSpaces as $space) 
			{
				if ($space['Prefix'] == 'bs')
					$this->spaceSwc = $space;
			}
		}

		return $this->spaceSwc;
	}
}