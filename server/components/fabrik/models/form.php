<?php
namespace bs\components\fabrik\models;
defined('EXE') or die('Access');

use \bs\components\fabrik\Model;

class Form extends Model
{
	private $editable = null;

	private $rowId = null;
	private $formData = null;
	private $origData = null;
	private $insertId = null;

	private $isnewrecord = true;

	public $viewelementids = [];
	public $isdefaultvalues = [];

	public $isroot = false;

	public $validation = [];

	public function getInsertId()
	{
		return $this->insertId;
	}

	protected function buildWhere()
	{
		return 't0.id='.$this->getRowId();
	}

	public function setEditable($editable)
	{
		$this->editable = $editable;
	}

	public function isEditable()
	{
		if ($this->editable === null)
			$this->editable = $this->getParam('isedit', true);

		return $this->editable;
	}

	public function setFormData($formData)
	{
		$this->formData = $formData;
	}

	public function getFormData()
	{
		return $this->formData;
	}

	public function updFormData($key, $value)
	{
		$name = $this->getElement($key)->getName();
		$this->formData[$name] = $value;
	}

	public function isNewrecord()
	{
		return $this->isnewrecord;
	}

	public function setRowId($rowId)
	{
		if ($this->rowId === null)
		{
			$this->rowId = (int)$rowId;
			$this->isnewrecord = false;
		}
	}

	public function _setRowId($rowId)
	{
		$this->rowId = (int)$rowId;
		$this->isnewrecord = false;
	}

	public function getOrigData()
	{
		if (!$this->origData)
			$this->origData = $this->getData();

		return $this->origData[0] ?? [];
	}

	public function getRowId()
	{
		if ($this->rowId === null)
		{
			$rowId = 0;
			$filter = $this->getParam('filter.filter');

			if ($filter)
			{
				$module = $this->app->getComponent('module', 'filter', 'fabrik')->getModel($filter['moduleid']);
				$value = $module->getFieldValue($filter['fieldid']);
				if ($value !== null)
					$rowId = $value;
			}

			$this->rowId = (int)$rowId;
		}
		
		return $this->rowId;
	}

	public function store()
	{
		$this->getOrigData();

		$res = $this->getPluginManager()->run('beforeProcess');
		if (in_array(false, $res))
			return false;

		if (!$this->isValid())
			return false;

		$res = $this->getPluginManager()->run('beforeStore');
		if (in_array(false, $res))
			return false;

		$query = '';
		$set = '';
		$tn = $this->getTable()->getData()['Name'];
		$elements = $this->getElements();
		$isnew = $this->isNewRecord();

		foreach ($elements as $element) 
		{
			$name = $element->getName();
			$type = $element->getType();

			if ($type == 'calc')
				continue;
			elseif ($type == 'internalid' and $element->getParam('auto_increment'))
				continue;
			elseif ($type == 'databasejoin' and $element->getParam('basic.type') == 'multilist')
				continue;

			$value = $element->getStoreValue();

			if ($value !== null)
			{
				$set .= $set ? ', ' : '';
				$set .= $name.'='.$this->getDBO()->q($value);	
			}
		}

		$query .= ($isnew ? 'INSERT INTO ' : 'UPDATE ').'&__'.$tn;
		$query .= ' SET '.$set;
		$query .= (!$isnew) ? ' WHERE id='.$this->getRowId() : '';

		$this->getDBO()->setQuery($query)->execute();

		if ($isnew)
		{
			$this->insertId = $this->getDBO()->insertid();
			$this->rowId = $this->insertId;
		}

		foreach ($elements as $element) 
		{
			$name = $element->getName();
			$type = $element->getType();

			if ($element->getParam('basic.type') == 'multilist')
			{
				$values = $this->getFormData()[$name];
				$tnr = $tn.'_repeat_'.$name;

				if (!$isnew)
					$this->getDBO()->setQuery('DELETE FROM &__'.$tnr.' WHERE `left`='.$this->rowId)->execute();

				foreach ($values as $value)
					$this->getDBO()->setQuery('INSERT INTO &__'.$tnr.' SET `left`='.$this->rowId.', `right`='.$value)->execute();
			}
		}

		$this->getPluginManager()->run('afterStore');
	}

	public function isValid()
	{
		return empty($this->validation);
	}

	public function getStatus()
	{
		$status = false;

		if ($this->getElement('StatusId'))
		{
			if ($this->isNewrecord())
			{
				$status = 'new';
			}
			else
			{
				$statusid = (int)$this->getData()[0]['StatusId'];

				if ($statusid)
				{
					$statuses = $this->app->getCtrl('fabrik', 'status')->select('id, Name', null, 'id');
					$status = $statuses[$statusid]['Name'];
				}
			}
		}

		return $status;
	}

	public function getSubmit()
	{
		return $this->isroot ? $this->app->input->get('submit') : false;
	}
}