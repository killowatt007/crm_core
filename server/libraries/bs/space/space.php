<?php
namespace bs\libraries\space;
defined('EXE') or die('Access');

class Space
{
	private $dboc;

	private $id = null;
	private $prefix = null;

	private $spaces = null;

	public function __construct()
	{
		$this->dboc = \F::getDBO('bs', 'c');
		$this->init();
	}

	private function init()
	{
		$session = \F::getApp()->getSession();

		$space = $this->dboc
			->setQuery('SELECT id, Prefix FROM &__spaces WHERE id='.$session->spaceId)
			->loadAssoc();

		$this->id = $space['id'];
		$this->prefix = $space['Prefix'];
	}

	public function getPrefix()
	{
		return $this->prefix;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getAll()
	{
		if (!$this->spaces)
		{
			$this->spaces = $this->dboc
				->setQuery('SELECT id, Prefix, Name FROM &__spaces')
				->loadAssocList('id');
		}

		return $this->spaces;
	}
}