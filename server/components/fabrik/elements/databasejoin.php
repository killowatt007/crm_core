<?php
namespace bs\components\fabrik;
defined('EXE') or die('Access');

class ElementDatabasejoin extends Element
{
	private $joinOpt = null;
	private $jvalue = [];

	public function data()
	{
		$data = [];
		
		if ($this->isEdit())
		{
			$data['options'] = $this->getOptions();
			$data['search'] = (bool)$this->getParam('search');
		}

		if ($this->getParam('basic.type') == 'multilist')
			$data['multilist'] = 1;

		return $data;
	}

	public function getOptions()
	{
		$options = [];
		$search = (bool)$this->getParam('search');

		if (!$search)
		{
			$jOpt = $this->getJoinOpt();
			$where = $this->getWhere();

			$query  = 'SELECT t0.'.$jOpt['kName'].' AS value, t0.'.$jOpt['vName'].' AS label FROM &__'.$jOpt['tName'].' t0';
			$query .= $where ? ' WHERE '.$where : '';

			$options = \F::getDBO()->setQuery($query)
				->loadAssocList();
		}
		else
		{
			$option = null;

			if ($this->getModel()->isNewRecord())
			{
				if ($value = $this->getValue())
				{
					$jkey = \F::getApp()->getCtrl('fabrik', 'fabrik_field')->select('Name', $this->getParam('join_key'))['Name'];
					$jval = \F::getApp()->getCtrl('fabrik', 'fabrik_field')->select('Name', $this->getParam('join_val'))['Name'];

					$data = \F::getApp()->getCtrl('fabrik', $this->getParam('join_entity'))->select($jkey.','.$jval, $jkey.'='.$value)[0];

					$option = [
						'value' => $data[$jkey],
						'label' => $data[$jval]
					];
				}
			}
			else
			{
				$data = $this->getModel()->getData()[0] ?? null;
				
				if ($data)
				{
					$name = $this->getName();

					$option = [
						'value' => $data[$name],
						'label' => $data[$name.'_join']
					];
				}
			}

			if ($option)
				$options[] = $option;
		}

		return $options;
		// if (!isset($options[$value])) notice...
	}

	public function getStoreValue()
	{
		$value = parent::getStoreValue();

		if ($value !== null)
			$value = (int)$value;

		return $value;
	}
	
	public function getJoinValue($i)
	{
		if (!isset($this->jvalue[$i]))
		{
			$name = $this->getName();
			$data = $this->getModel()->getData()[$i] ?? [];

			$this->jvalue[$i] = $data[$name.'_join'] ?? '';
		}

		return $this->jvalue[$i];
	}

	public function getFtype()
	{
		return 'list';
	}

	public function getWhere()
	{
		$where = '';

		if ($this->isEdit())
		{
			$this->getModel()->getPluginManager()->run('elementDatabasejoinWhere', [$this, \F::std(['where'=>&$where])]);

			/*B_BASE*/
			$isbase = \F::getApp()->getCtrl('fabrik', 'fabrik_field')->select('id', 'EntityId='.$this->getParam('join_entity').' AND Name="BaseId"')[0] ?? null;
			if ($isbase and !$this->getParam('ignoreBase'))
				$where .= ($where ? ' AND ' : '') . 't0.BaseId='.\F::getApp()->getCtrl('fabrik', 'pick_items')->getActiveBase();
		}
		else
		{
			$value = $this->getValue();

			if ($value)
			{
				$jOpt = $this->getJoinOpt();
				$where = $jOpt['kName'].'='.$value;
			}
		}

		return $where;
	}

	public function getJoinOpt()
	{
		if (!$this->joinOpt)
		{
			$jTable = $this->getParam('join_entity');
			$jElKey = $this->getParam('join_key');
			$jElVal = $this->getParam('join_val');

			$this->joinOpt = \F::getDBO()
				->setQuery(
					'SELECT e.Name AS tName, k.Name AS kName, v.Name AS vName
					 FROM &__fabrik_entity e
				 	 LEFT JOIN &__fabrik_field k ON k.id='.$jElKey.'
				 	 LEFT JOIN &__fabrik_field v ON v.id='.$jElVal.'
					 WHERE e.id='.$jTable
				)
				->loadAssoc();
		}

		return $this->joinOpt;
	}
}