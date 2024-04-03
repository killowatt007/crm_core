<?php
namespace bs\components\builder\models;
defined('EXE') or die('Access');

use \bs\libraries\mvc\Model;

class Table extends Model
{
	protected $dbo;
	protected $app;

	private $tmpl = [];

	public function __construct()
	{
		$this->dbo = \F::getDBO();
		$this->app = \F::getApp();
	}

	public function getData()
	{
		return $this->parse();
	}

	public function getTmpl($id = null)
	{
		if (!isset($this->tmpl[$id]))
		{
			$_id = !$id ? $this->id : $id;

			$this->tmpl[$id] = $this->dbo
				->setQuery('SELECT id, Data, ParentId, EntityTypeId FROM &__builder_tmpl WHERE id='.$_id)
				->loadAssoc();
		}

		return $this->tmpl[$id];
	}

	public function getGroup()
	{
		$tmpl = $this->getTmpl();
		return $tmpl['EntityTypeId'] == 1 ? 'page' : 'fabrikform';
	}

	private function parse($id = null, $childId = 0, &$result = [])
	{
		$tmpl = $this->getTmpl($id);
		$id = $tmpl['id'];
		$data = json_decode($tmpl['Data'], true);

		$render = $this->render($id);
		$data = $render ? $this->parseData($data) : null;

		$result[] = [
			'id' => $id,
			'childId' => $childId,
			'parentId' => (int)$tmpl['ParentId'],
			'render' => $render,
			'data' => $data
		];

		if ($tmpl['ParentId'])
			$this->parse($tmpl['ParentId'], (int)$id, $result);

		return $result;
	}

	private function render($id)
	{
		$render = true;
		$referrerItemId = $this->app->input->get('referrerItemId');

		if ($referrerItemId)
		{
			$tmpl = $this->dbo
				->setQuery('SELECT ParentId FROM &__builder_tmpl WHERE id='.$id)
				->loadAssoc();

			$render = (bool)$tmpl['ParentId'];
		}

		return $render;
	}

	public function parseData($data)
	{
		foreach ($data as &$row) 
		{
			if ($row['type'] == 'content')			
			{ 
				$row['content'] = null; 
			}
			elseif ($row['type'] == 'header') 	
			{ 
				$rdata = $row['data'];
				$row['data'] = [];

				foreach ($rdata as $position => $addons) 
				{
					$cdata = [];
					foreach ($addons as $addon)
						$cdata[] = $this->getAddonData($addon);

					$row['data'][] = [
						'position' => $position,
						'data' => $cdata
					];
				}
			}
			elseif ($row['type'] == 'row')
			{
				foreach ($row['columns'] as &$column) 
				{
					foreach ($column['data'] as &$cdata) 
					{
						if ($cdata['type'] == 'addon')
						{
							$cdata = $this->getAddonData($cdata);
						}
						elseif ($cdata['type'] == 'row')
						{
							$cdata = $this->parseData([$cdata])[0];
						}
					}
				}
			}
		}

		return $data;
	}

	public function getAddonData($params)
	{
		$data = [];
		$nameArr = explode('.', $params['name']);

		$params['branch'] = 'addons.'.(isset($nameArr[1]) ? $nameArr[0] : $this->getGroup());
		$params['name'] = isset($nameArr[1]) ? $nameArr[1] : $params['name'];
	
		$addonComp = $this->app->getComponent('builder', $params['name'], $params['branch']);
		$aModel = $addonComp->getModel();
		$aModel->setParam($params);
		$addonView = $addonComp->getView();
		$addonView->setPageModel($this);
		
		$data = $addonView->getData();

		$this->getPluginManager()->run('afterAddonData', [$addonView, \F::std(['data'=>&$data])]);

		return $data;
	}
}