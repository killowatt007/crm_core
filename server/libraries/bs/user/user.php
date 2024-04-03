<?php
namespace bs\libraries\user;
defined('EXE') or die('Access');

class User
{
	private $id;
	private $db;

	public $guest = false;
	public $data;

	public function __construct($id)
	{
		$this->dbo = \F::getDBO();
		$this->id = $id;

		if (!$this->id)
			$this->setCurrentUser();

		$this->load();
	}

	private function load()
	{
		if (!$this->guest)
		{
			$data = $this->dbo
				->setQuery(
					'SELECT 
						t0.*, 
						GROUP_CONCAT(t1.right SEPARATOR ",") GroupsId,
						GROUP_CONCAT(t2.left SEPARATOR ",") LevelsId
					FROM &__user t0
					LEFT JOIN &__user_role_repeat_GroupId t1 ON (t1.left=t0.RoleId)
					LEFT JOIN &__access_level_repeat_GroupId t2 ON (t2.right=t1.right)
					WHERE t0.id='.$this->id.'
					GROUP BY t0.id
				')
				->loadAssoc();

			$data['GroupsId'] = explode(',', $data['GroupsId']);
		}
		else
		{
			$data = $this->dbo
				->setQuery('SELECT GROUP_CONCAT(`left` SEPARATOR ",") LevelsId FROM &__access_level_repeat_GroupId WHERE `right`=1')
				->loadAssoc();
			$data['GroupsId'] = [1];
		}

		$data['LevelsId'] = explode(',', $data['LevelsId']);
		$this->data = $data;
	}

	private function setCurrentUser()
	{
		$session = \F::getApp()->getSession();

		if ($session->guest)
		{
			$this->guest = true;
		}
		else
		{
			$this->id = $session->userId;
		}
	}
}