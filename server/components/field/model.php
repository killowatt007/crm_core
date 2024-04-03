<?php
namespace bs\components\field;
defined('EXE') or die('Access');

use \bs\libraries\mvc;

class Model extends mvc\Model
{
	// 01.03.2022
	// private $dbo;

	// public $isedit = null;
	// public $value = [];

	// static private $data = [];

	// public function __construct()
	// {
	// 	$this->dbo = \F::getDBO();
	// }

	// public function getDBData()
	// {
	// 	if (!isset(self::$data[$this->id]))
	// 	{
	// 		self::$data[$this->id] = $this->dbo
	// 			->setQuery('SELECT id, Type, Name, Label, Params FROM &__fabrik_field WHERE id='.$this->id)
	// 			->loadAssoc();
	// 	}

	// 	return self::$data[$this->id];
	// }

	// public function getName()
	// {
	// 	return $this->getDBData()['Name'];
	// }

	// public function getType()
	// {
	// 	return $this->getDBData()['Type'];
	// }

	// public function getLabel()
	// {
	// 	return $this->getDBData()['Label'];
	// }

	// public function isEdit()
	// {
	// 	if ($this->isedit === null)
	// 	{
	// 		$this->getPluginManager()->run('isEdit');
	// 		$this->isedit = $this->isedit === null ? true : $this->isedit;
	// 	}

	// 	return $this->isedit;
	// }

	// public function getValue($i = 0, $opt = [])
	// {
	// 	$value = null;
	// 	$key = $i.serialize($opt);

	// 	if (!isset($this->value[$key]))
	// 		$this->getPluginManager()->run('getValue', [$i, $opt]);
		
	// 	return $this->value[$key];
	// }
}