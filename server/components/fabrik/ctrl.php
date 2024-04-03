<?php
namespace bs\components\fabrik;
defined('EXE') or die('Access');

use \bs\libraries;

class Ctrl extends libraries\Ctrl
{
	protected $dbo;
	protected $app;

	protected $id;

	private $selectCache = [];

	public function __construct($id)
	{
		$this->dbo = \F::getDBO();
		$this->app = \F::getApp();
		$this->id = $id;
	}

	public function getTable()
	{
		return $this->app->getService('fabrik', 'helper')->getTable($this->id);
	}

	public function select($select, $where = null, $key = false)
	{
		$cacheKey = $select.$where.$key;

		if (!isset($this->selectCache[$cacheKey]))
		{
			$tn = $this->getTable()->getData()['Name'];

			$method = 'loadAssocList';
			$query  = 'SELECT '.$select.' FROM &__'.$tn;

			if ($where)
			{
				if (is_numeric($where))
				{
					$where = 'id='.$where;
					$method = 'loadAssoc';
				}

				$where = 'WHERE '.$where;
			}

			$data = $this->dbo
				->setQuery('SELECT '.$select.' FROM &__'.$tn.' '.$where)
				->$method();

			if ($key)
			{
				$_data = [];
				foreach ($data as $row)
					$_data[$row[$key]] = $row;

				$data = $_data;
			}

			$this->selectCache[$cacheKey] = $data;
		}

		return $this->selectCache[$cacheKey];
	}

	public function selectq($q, $key = null)
	{
		if (!isset($this->selectCache[$q]))
			$this->selectCache[$q] = $this->dbo->setQuery($q)->loadAssocList($key);

		return $this->selectCache[$q];
	}

	public function store($data, $rowid = null, $model = null)
	{
		if (!$model)
			$model = $this->getModel();

		if ($rowid)
			$model->setRowId($rowid);

		$model->setFormData($data);
		$model->store();
		$rowid = $model->getRowId();

		return $model;
	}

	public function getModel($name = 'form')
	{
		return $this->app->getComponent('fabrik', $name)->initModel($this->id);
	}
}


