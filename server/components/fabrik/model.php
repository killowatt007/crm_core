<?php
namespace bs\components\fabrik;
defined('EXE') or die('Access');

use \bs\libraries\mvc;

class Model extends mvc\Model
{
	private $dbo;
	protected $app;

	protected $data = null;

	private $elements = null;

	private $joinData = [];
	private $joinI = 0;

	private static $qSelect = [];
	private static $qJoin = [];

	public $isview = false;

	private $moduleId = null;
	private $moduleRefId = null;

	private $mParams = null;
	private $mrParams = null;
	private $pParams = null;

	private $mdata = null;
	private $mrdata = null;

	public $where;
	public $tn = null;
	public $query = null;

	public function __construct()
	{
		$this->dbo = \F::getDBO();
		$this->app = \F::getApp();

		$this->pParams = \F::getRegistry([]);
	}

	public function getDBO()
	{
		if (!$this->dbo)
			$this->dbo = \F::getDBO();

		return $this->dbo;
	}

	public function closeDBO()
	{
		if ($this->dbo)
		{
			$this->dbo->close();
			$this->dbo = null;
		}
	}

	public function getTable()
	{
		return $this->app->getService('fabrik', 'helper')->getTable($this->getId());
	}

	public function getTableName()
	{
		if (!$this->tn)
			$this->tn = $this->getTable()->getData()['Name'];

		return $this->tn;
	}

	public function setTableName($tn)
	{
		return $this->tn = $tn;
	}

	public function getElements()
	{
		if (!$this->elements)
		{
			$this->elements = [];

			foreach ($this->getTable()->getElements() as $data) 
			{
				$class = $this->app->getService('fabrik', 'helper')->initElement($data['Type']);

				$element = new $class($data);
				$element->setModel($this);

				$this->elements[$data['id']] = $element;
			}
		}

		return $this->elements;
	}

	public function getElement($key)
	{
		$id = $key;
		$elements = $this->getElements();

		if (!is_numeric($key))
		{
			if (!isset($this->regkeys['element']))
			{
				$this->regkeys['element'] = [];
				foreach ($elements as $id => $e)
					$this->regkeys['element'][$e->getName()] = $id;
			}

			$id = $this->regkeys['element'][$key] ?? 0;
		}

		return $elements[$id] ?? false;
	}

	public function setData($data)
	{
		$this->data = $data;
	}

	public function buildQuery()
	{
		if (!$this->query)
		{
			$query = [];

			$where = $this->buildWhere();
			$tn = $this->getTableName();

			$query['select'] = 'SELECT '.$this->buildSelect(true);
			$query['from'] = 'FROM &__'.$tn.' t0';
			$query['join'] = $this->buildJoin();
			$query['where'] = $where ? ' WHERE '.$where : '';
			$query['group'] = 'GROUP BY t0.id';
			$query['order'] = $this->order();
			$query['limit'] = $this->buildLimit();

			$this->query = $query;
		}

		return $this->query;
	}

	public function getData()
	{
		if (!$this->data)
		{
			$this->getPluginManager()->run('beforeGetData');

			$this->data = [];

			$query = $this->buildQuery();
			$query = implode(' ', $query);

			$this->data = $this->getDBO()
				->setQuery($query)
				->loadAssocList();
		}

		return $this->data;
	}

	private function order()
	{
		$order = '';
		$elid = $this->getParam('orderElementid');
		$dir = $this->getParam('orderDir');

		if ($elid and $dir)
		{
			$element = $this->getElement($elid);
			$order = ' ORDER BY t0.'.$element->getName().' '.$dir;
		}

		return $order;
	}

	protected function buildLimit()
	{
		return '';
	}

	private function buildSelect()
	{
		$select = '';
		$elements = $this->getElements();

		foreach ($elements as $element) 
		{
			$type = $element->getType();
			$name = $element->getName();

			if ($type != 'calc')
			{
				if ($type == 'databasejoin' and $element->getParam('basic.type') == 'multilist')
				{
					$jd = $this->getJoinData($element);
					$select .= ', GROUP_CONCAT(t'.$jd['i'].'r.right SEPARATOR "//:://") '.$name;
				}
				else
				{
					$select .= $select ? ', ' : '';
					$select .= 't0.'.$name;
				}

				if ($this->isview)
				{
					if ($type == 'databasejoin')
					{
						$jd = $this->getJoinData($element);

						if ($element->getParam('basic.type') == 'multilist')
							$select .= ', GROUP_CONCAT(t'.$jd['i'].'.'.$jd['elVal'].' SEPARATOR "//:://") '.$name.'_join';
						else
							$select .= ', t'.$jd['i'].'.'.$jd['elVal'].' AS '.$name.'_join';
					}
				}
			}
		}

		return $select;
	}

	private function buildJoin()
	{
		$key = $this->getId();
		$tn = $this->getTableName();

		if (!isset(self::$qJoin[$key]))
		{
			$joins = '';
			$elements = $this->getElements();

			foreach ($elements as $element) 
			{
				if ($element->getType() == 'databasejoin')
				{
					$jd = $this->getJoinData($element);
					$elname = $element->getName();

					if ($element->getParam('basic.type') == 'multilist')
					{
						$joins .= ' LEFT JOIN &__'.$tn.'_repeat_'.$elname.' t'.$jd['i'].'r ON t'.$jd['i'].'r.left=t0.id';
						
						if ($this->isview)
							$joins .= ' LEFT JOIN &__'.$jd['tn'].' t'.$jd['i'].' ON t'.$jd['i'].'.'.$jd['elKey'].'=t'.$jd['i'].'r.right';
					}
					else
					{
						if ($this->isview)
							$joins .= ' LEFT JOIN &__'.$jd['tn'].' t'.$jd['i'].' ON t'.$jd['i'].'.'.$jd['elKey'].'=t0.'.$elname;
					}
				}
			}

			self::$qJoin[$key] = $joins;
		}

		return self::$qJoin[$key];
	}

	public function getJoinData($element)
	{
		$id = $element->getId();

		if (!isset($this->joinData[$id]))
		{
			$this->joinI++;

			$this->joinData[$id] = $this->getDBO()
				->setQuery(
					'SELECT t0.Name AS tn, t1.Name AS elKey, t2.Name AS elVal
					 FROM &__fabrik_entity t0
					 LEFT JOIN &__fabrik_field t1 ON t1.id='.$element->getParam('join_key').'
					 LEFT JOIN &__fabrik_field t2 ON t2.id='.$element->getParam('join_val').'
					 WHERE t0.id='.$element->getParam('join_entity')
				)
				->loadAssoc();

			$this->joinData[$id]['i'] = $this->joinI;
		}

		return $this->joinData[$id];	
	}

	public function getParam($key = null, $def = null, $mref = false)
	{
		$par = null;

		if ($mref)
			$this->initModuleRef();

		if (!$this->params)
		{
			$cname = $this->getComponent()->getName();
			$tParams = json_decode($this->getTable()->getData()['Params'], true);
			$this->params = \F::getRegistry(($tParams[$cname] ?? []));

			$this->getPluginManager()->run('params');
		}

		if ($key)
		{
			$test = false;
			if ($key == 'filter.filter')
				$test = true;

			$key = strpos($key, '.') === false ? 'basic.'.$key : $key;
			$par = $this->params->get($key, $def);

			if ($mref)
				$par = $this->mrParams->get($key, $par);

			if ($this->mParams)
				$par = $this->mParams->get($key, $par);

			$par = $this->pParams->get($key, $par);
		}
		else
		{
			$par['params'] = $this->params->toArray();

			if ($this->mParams)
				$par['mParams'] = $this->mParams->toArray();

			if ($mref)
				$par['mrParams'] = $this->mrParams->toArray();

			$par['pParams'] = $this->pParams->toArray();
		}

		return $par;
	}

	public function setParam($key, $val = null)
	{
		if (!$val)
		{
			parent::setParam($key);
		}
		{
			$key = strpos($key, '.') === false ? 'basic.'.$key : $key;
			$this->pParams->set($key, $val);
		}
	}

	public function getModuleAlias($isref = true)
	{
		$alias = null;
		$data = $this->getModuleData();

		if (!$data and $isref)
			$data = $this->getModuleData(true);

		if ($data)
			$alias = $data['Alias'];

		return $alias;
	}

	public function getModuleData($ref = false)
	{
		$mid = $ref ? $this->moduleRefId : $this->moduleId;
		$var = $ref ? 'mrdata' : 'mdata';

		if ($mid and $this->$var === null)
		{
			$this->$var = $this->getDBO()
				->setQuery('SELECT * FROM &__module WHERE id='.$mid)
				->loadAssoc();
		}

		return $this->$var;
	}

	public function initModule($id, $mdata = null)
	{
		if (!$this->mParams)
		{
			$this->moduleId = $id;

			if ($mdata)
				$this->mdata = $mdata;
			else
				$this->mdata = $this->getModuleData();

			$params = json_decode($this->mdata['Params'], true);
			$this->mParams = $this->initModuleParams($params);
		}
	}

	public function initModuleRef()
	{
		if (!$this->mrParams)
		{
			$params = [];
			
			if ($mdata = $this->getModuleData(true))
				$params = json_decode($mdata['Params'], true);

			$this->mrParams = $this->initModuleParams($params);
		}
	}

	private function initModuleParams($params)
	{
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
				
		return \F::getRegistry($params);
	}

	public function getModuleId()
	{
		return $this->moduleId;
	}

	public function setModuleRefId($modulerefid)
	{
		if (!$this->moduleRefId)
			$this->moduleRefId = $modulerefid;
	}

	public function getModuleRefId()
	{
		return $this->moduleRefId;
	}
}