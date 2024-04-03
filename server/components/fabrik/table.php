<?php
namespace bs\components\fabrik;
defined('EXE') or die('Access');

class Table
{
	private $dbo;
	private $id;

	private $elements = null;

	static private $data = [];
	static private $dbelements = [];
	static private $connectionData = [];

	public function __construct($id)
	{
		$this->dbo = \F::getDBO();
		$this->id = $id;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getData()
	{
		if (!isset(self::$data[$this->id]))
		{
			self::$data[$this->id] = $this->dbo
				->setQuery('SELECT Name, PK, ConnectionId, Params FROM &__fabrik_entity WHERE id='.$this->id)
				->loadAssoc();
		}

		return self::$data[$this->id];
	}

	public function getElements()
	{
		if (!isset(self::$dbelements[$this->id]))
		{
			self::$dbelements[$this->id] = $this->dbo
				->setQuery('SELECT * FROM &__fabrik_field WHERE EntityId='.$this->id)
				->loadAssocList();
		}

		return self::$dbelements[$this->id];
	}
}


