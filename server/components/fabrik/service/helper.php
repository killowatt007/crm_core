<?php
namespace bs\components\fabrik\service;
defined('EXE') or die('Access');

class Helper
{
	private $tIds = [];
	private $tables = [];

	private $filter = null;

	public function initElement($type)
	{
		include_once PATH_ROOT .'/components/fabrik/element.php';
		include_once PATH_ROOT .'/components/fabrik/elements/'.$type.'.php';
		$class = '\bs\components\fabrik\Element'.ucfirst($type);

		return $class;
	}

	public function getTable($key)
	{
		include_once PATH_ROOT .'/components/fabrik/table.php';

		if (!is_numeric($key))
		{
			if (!isset($this->tIds[$key]))
			{
				$dbo = \F::getDBO();
				$table = $dbo
					->setQuery('SELECT id FROM &__fabrik_entity WHERE Name='.$dbo->q($key))
					->loadAssoc();

				if ($table)
					$id = $this->tIds[$key] = $table['id'];
				else
					qqq('Table Not Found ('.$key.')', 0, 1);
			}
			else
			{
				$id = $this->tIds[$key];
			}
		}
		else
		{
			$id = $key;
		}

		if (!isset($this->tables[$id]))
			$this->tables[$id] = new \bs\components\fabrik\Table($id);

		return $this->tables[$id];
	}

	public function getFilter($alias = 'main')
	{
		if (!$this->filter)
		{
			$app = \F::getApp();

			$CTRLtmpl = $app->getCtrl('fabrik', 'builder_tmpl');
			$CTRLmodule = $app->getCtrl('fabrik', 'module');

			$activetmpl = $CTRLtmpl->getActive();
			$filterData = $CTRLmodule->select('id', 'TmplId='.$activetmpl['id'].' AND Module="fabrik.filter" AND Alias="'.$alias.'"');

			if (!empty($filterData[0]))
				$this->filter = $app->getComponent('module', 'filter', 'fabrik')->getModel($filterData[0]['id']);
		}

		return $this->filter;
	}
}


