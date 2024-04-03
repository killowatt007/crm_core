<?php
namespace bs\libraries\session;
defined('EXE') or die('Access');

class Session
{
	public $sessionId;
	public $guest;
	public $userId;
	public $spaceId;
	public $time;

	private $dboc;

	public function __construct()
	{
		$this->dboc = \F::getDBO('bs', 'c');
		$this->load();
	}

	public function clear($spaceid = null)
	{
		$sessid = $this->sessionId;

		if ($spaceid)
		{
			$usersKeys = array_keys($_COOKIE['user']);

			foreach ($usersKeys as $key)
			{
				$session = $this->dboc
					->setQuery('SELECT SessionId FROM &__session WHERE SessionId='.$this->dboc->q($key).' AND SpaceId='.$spaceid)
					->loadAssoc();

				if ($session)
					$sessid = $session['SessionId'];
			}
		}

		$this->dboc
			->setQuery('DELETE FROM &__session WHERE SessionId='.$this->dboc->q($sessid))
			->execute();

		setcookie('user['.$sessid.']', 1, time()-100, '/', $_SERVER['HTTP_HOST']);
		unset($_COOKIE['user'][$sessid]);
	}

	private function add($spaceid, $userid = 0)
	{
		$time = time();
		$sess_id = md5($time);
		$guest = $userid ? 0 : 1;

		$this->dboc
			->setQuery(
				'INSERT INTO &__session 
				 SET SessionId='.$this->dboc->q($sess_id).', 
						 IsGuest='.$guest.', 
						 UserId='.$userid.', 
						 SpaceId='.$spaceid.', 
						 Time='.$this->dboc->q($time).',
						 IsCurrent=1'
			)
			->execute();

		setcookie('user['.$sess_id.']', 1, $time+(86400*3), '/', $_SERVER['HTTP_HOST']);
		$_COOKIE['user'][$sess_id] = 1;

		return [
			'SessionId' => $sess_id,
			'Time' => $time,
			'IsGuest' => $guest
		];
	}

	public function setCurrent($spaceid)
	{
		$sessData = $this->dboc->setQuery('SELECT SessionId, UserId FROM &__session WHERE SpaceId='.$spaceid)->loadAssoc();
		$this->dboc->setQuery('UPDATE &__session SET IsCurrent=0 WHERE IsCurrent=1')->execute();

		if ($sessData)
		{
			$this->dboc->setQuery('UPDATE &__session SET IsCurrent=1 WHERE SpaceId='.$spaceid)->execute();
		}
		else
		{
			$this->add($spaceid);
		}

		$this->load();
		\F::getApp()->clear();
	}

	public function logout()
	{
		$this->dboc
			->setQuery('UPDATE &__session SET IsGuest=1, UserId=0 WHERE SessionId='.$this->dboc->q($this->sessionId))
			->execute();
	}

	public function load($userid = null)
	{
		$data = [];
		$clear = !isset($_COOKIE['user']);

		if ($clear or $userid !== null)
		{
			$userid = $userid ?? 0;

			if (!$userid)
			{
				$space = $this->dboc
					->setQuery('SELECT id FROM &__spaces WHERE IsCurrent=1')
					->loadAssoc();

				$spaceid = $space['id'];
			}
			else
			{
				$spaceid = $this->spaceId;
			}

			if (!$clear)
				$this->clear($spaceid);

			$sessData = $this->add($spaceid, $userid);

			$data = [
				'SessionId' => $sessData['SessionId'],
				'IsGuest' => $sessData['IsGuest'],
				'UserId' => $userid,
				'SpaceId' => $spaceid,
				'Time' => $sessData['Time'],
			];
		}
		else
		{
			$usersKeys = array_keys($_COOKIE['user']);
			$deleteKeys = [];

			foreach ($usersKeys as $key)
			{
				$_data = $this->dboc
					->setQuery('SELECT * FROM &__session WHERE SessionId='.$this->dboc->q($key).' AND IsCurrent=1')
					->loadAssoc();

				if ($_data)
				{
					$data = $_data;
					setcookie('user['.$data['SessionId'].']', 1, time()+(86400*3), '/', $_SERVER['HTTP_HOST']);
				}
				else
				{
					$deleteKeys[] = $this->dboc->q($key);
					setcookie('user['.$key.']', 1, time()-100, '/', $_SERVER['HTTP_HOST']);
					unset($_COOKIE['user'][$key]);
				}
			}

			if (!empty($deleteKeys))
			{
				$this->dboc
					->setQuery('DELETE FROM &__session WHERE SessionId IN('.implode(',', $deleteKeys).')')
					->execute();
			}
		}

		if ($data)
		{
			$this->sessionId = $data['SessionId'];
			$this->guest = $data['IsGuest'];
			$this->userId = $data['UserId'];
			$this->spaceId = $data['SpaceId'];
			$this->time = $data['Time'];
		}
		else
		{
			$this->load(0);
		}
	}
}