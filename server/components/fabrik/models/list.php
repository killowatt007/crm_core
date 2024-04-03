<?php
namespace bs\components\fabrik\models;
defined('EXE') or die('Access');

use \bs\components\fabrik\Model;

class Lst extends Model
{
	private $display = null;

	public function getLengthRows()
	{
		$tn = $this->getTableName();
		$where = $this->buildWhere();

		$result = $this->getDBO()
			->setQuery('SELECT COUNT(*) FROM &__'.$tn.' t0'.($where ? ' WHERE '.$where : ''))
			->loadAssoc();

		return (int)$result['COUNT(*)'];
	}

	protected function buildWhere()
	{
		$this->getPluginManager()->run('filter');
		return $this->where;
	}

	public function setWhere($where)
	{
		$this->where = $where;
	}

	protected function buildLimit()
	{
		$display = $this->getDisplay();
		$pagination = $this->getPagination() * $display;

		return 'LIMIT '.$pagination.', '.$display;
	}

	public function getFgroupAndRows()
	{
		$rows = $this->getData();
		$fieldsgroup = [];

		foreach ($rows as $i => &$row) 
		{
			$fields = [];

			foreach ($this->getParam('showElementids') as $showel) 
			{
				$element = $this->getElement($showel['id']);

				$name = $element->getName();
				$type = $element->getType();

				$row[$name] = $element->getValue($i) ?? '';
				if ($type == 'databasejoin')
					$row[$name.'_join'] = $element->getJoinValue($i) ?? '';

				$eldata = $element->getData();
				$eldata['opts']['i'] = $i;

				$fields[] = [
					'data' => $eldata
				];

				$this->app->setDep('components/field/actors/'.$eldata['name']);
			}

			$fieldsgroup[] = $fields;
		}

		return [
			'rows' => $rows,
			'fgroup' => $fieldsgroup
		];
	}

	public function getHeaders()
	{
		$headers = [];

		foreach ($this->getParam('showElementids') as $showel)
		{
			$element = $this->getElement($showel['id']);

			$headers[] = [
				'id' => $showel['id'],
				'isshow' => $element->isListLabel(),
				'label' => $element->getLabel(),
				'width' => $element->getParam('col_width', '')
			];
		}

		return $headers;
	}

	public function setDisplay($display)
	{
		$this->display = $display;
	}

	public function getDisplay()
	{
		if ($this->display === null)
		{
			$def = $this->getParam('display', 10);

			if (!(int)$def)
				$def = 10;

			$this->display = $this->app->input->get('stream.modulesData.'.$this->getModuleId().'.display', $def);
		}

		return $this->display;
	}

	public function getPagination()
	{
		return $this->app->input->get('stream.modulesData.'.$this->getModuleId().'.pagination', 0);
	}
}