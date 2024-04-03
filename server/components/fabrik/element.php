<?php
namespace bs\components\fabrik;
defined('EXE') or die('Access');

use \bs\libraries\obj\Actor;

class Element extends Actor
{
	private $dbo;

	private $model = null;
	protected $value = [];
	protected $default = [];
	private $dbdata = [];

	private $params = null;
	private $mParams = null;
	private $mrParams = null;
	private $pParams = null;

	public function __construct($dbdata)
	{
		$this->dbdata = $dbdata;
		$this->pParams = \F::getRegistry([]);
	}

	protected function rdata()
	{
		$model = $this->getModel();

		$data = [
			'id'	=> $this->getId(),
			'group' => 'field',
			'name' => $this->getFtype()
		];

		return $data;
	}

	protected function gdata()
	{
		$model = $this->getModel();

		$data = [
			'type' => $this->getType(),
			'name' => $this->getName(),
			'label' => $this->getLabel(),
			'isedit' => $this->isEdit(),
			'display' => $this->getParam('display', true)
		];

		return $data;
	}

	public function getId() { return $this->dbdata['id']; }
	public function getName() { return $this->dbdata['Name']; }
	public function getType() { return $this->dbdata['Type']; }
	public function getLabel() { return $this->dbdata['Label']; }
	public function getModel() { return $this->model; }

	public function getFtype()
	{
		return $this->getType();
	}

	public function setModel($model)
	{
		if (!$this->model)
			$this->model = $model;
	}

	public function isEdit()
	{
		$model = $this->getModel();
		$comp = $model->getComponent();

		if ($comp->getName() == 'form')
		{
			$isedit = $model->getParam('isedit', true);

			if ($this->getParam('form_edit') !== null)
				$isedit = $this->getParam('form_edit');
		}
		else
		{
			$isedit = $this->getParam('list_edit', 0) ? true : false;
		}

		return $isedit;
	}

	public function isListLabel()
	{
		return ($this->getParam('list_label', 1) ? true : false);
	}

	public function getValue($i = 0)
	{
		if (!isset($this->value[$i]))
		{
			$model = $this->getModel();
			$model->getPluginManager()->run('elementValue', [$this, $i]);

			if (!isset($this->value[$i]))
			{
				$name = $this->getName();
				$data = $model->getData()[$i] ?? [];
				
				$this->value[$i] = $data[$name] ?? $this->getDefault($i);
			}
		}

		return $this->value[$i];
	}

	public function setValue($value, $i = 0)
	{
		$this->value[$i] = $value;
	}

	public function getDefault($i = 0)
	{
		if (!isset($this->default[$i]))
		{
			$model = $this->getModel();
			$model->getPluginManager()->run('elementDefault', [$this, $i]);

			if (!isset($this->default[$i]))
				$this->default[$i] = null;
		}

		return $this->default[$i];
	}

	public function setDefault($value, $i = 0)
	{
		$id = $this->getId();

		if (!in_array($id, $this->getModel()->isdefaultvalues))
			$this->getModel()->isdefaultvalues[] = $id;

		$this->default[$i] = $value;
	}

	public function getStoreValue()
	{
		$model = $this->getModel();
		$model->getPluginManager()->run('elementStore', [$this]);

		$formData = (array)$model->getFormData();
		$value = $formData[$this->getName()] ?? ($model->isNewRecord() ? $this->getDefault() : null);

		return $value;
	}

	public function getParam($key = null, $def = null, $mref = false)
	{
		$par = null;
		$this->initModuleParams();

		if ($mref)
			$this->initModuleParams(true);

		if (!$this->params)
		{
			$params = json_decode($this->dbdata['Params'], true);
			$this->params = \F::getRegistry($params);

			$this->getModel()->getPluginManager()->run('elementParams', [$this]);
		}

		if ($key)
		{
			$key = strpos($key, '.') === false ? 'basic.'.$key : $key;
			$par = $this->params->get($key, $def);

			if ($mref)
				$par = $this->mrParams->get($key, $par);

			$par = $this->mParams->get($key, $par);
			$par = $this->pParams->get($key, $par);
		}
		else
		{
			$par['params'] = $this->params->toArray();
			$par['mParams'] = $this->mParams->toArray();
			$par['pParams'] = $this->pParams->toArray();

			if ($mref)
				$par['mrParams'] = $this->mrParams->toArray();
		}

		return $par;
	}

	private function initModuleParams($ref = false)
	{
		if (!$ref)
			$var = &$this->mParams;
		else
			$var = &$this->mrParams;

		if (!$var)
		{
			$params = $this->getModel()->getParam('fields.'.$this->getId(), [], $ref);

			foreach ($params as $key => &$part) 
			{
				if (is_array($part) and isset($part['activeparams']))
				{
					foreach ($part['activeparams'] as $name => $isactive) 
					{
						if (!$isactive)
							unset($part[$name]);
					}

					unset($part['activeparams']);

					if (empty($part))
						unset($params[$key]);
				}
			}

			$var = \F::getRegistry($params);
		}
	}

	public function setParam($key, $val)
	{
		$key = strpos($key, '.') === false ? 'basic.'.$key : $key;
		$this->pParams->set($key, $val);
	}
}


