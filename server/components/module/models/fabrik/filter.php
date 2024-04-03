<?php
namespace bs\components\module\models\fabrik;
defined('EXE') or die('Access');

use \bs\components\module\Model;

class Filter extends Model
{
	private $fields = null;

	public function getField($key)
	{
		$field = null;
		$this->getFields();

		if (!is_numeric($key))
		{
			foreach ($this->fields as $item) 
			{
				if ($item['Name'] == $key)
				{
					$field = $item;
					break;
				}
			}
		}
		else
		{
			$field = $this->fields[$key];
		}

		return $field;
	}

	public function getFields()
	{
		if (!$this->fields)
		{
			$fields = $this->app->getCtrl('fabrik', 'filter_fields')->select('*', 'FilterId='.$this->getParam('filterid'));

			foreach ($fields as $i => $opt) 
			{
				$opt['Params'] = json_decode($opt['Params'], true);
				$this->app->getPagePluginManager()->run('fabrikFilterField', [\F::std(['field'=>&$opt])]);

				$this->fields[$opt['id']] = $opt;
			}	
		}

		return $this->fields;
	}

	public function getFieldValue($key)
	{
		$id = !is_numeric($key) ? $this->getField($key)['id'] : $key;

		$stream = $this->app->input->get('stream', []);
		$value = $stream['modulesData'][$this->getId()]['fields'][$id] ?? null;

		if ($value === null)
		{
			$getData = $this->app->input->get('_ffilter', []);
			$value = $getData[$this->getId()][$id] ?? null;
		}

		return $value;
	}

	public function getFabrikOptions($fieldid)
	{
		$options = [];
		$field = $this->getField($fieldid);

		$selectArr = $field['Params']['select'] ?? [];
		$select0 = '';
		$select = '';

		foreach ($selectArr as $s)
		{
			$select0 .= ', t0.'.$s;
			$select .= ', '.$s;
		}

		if (!isset($field['search']))
		{
			$flabel = $this->app->getCtrl('fabrik', 'fabrik_field')->select('Name', $field['Params']['labelid']);
			$entity = $this->app->getCtrl('fabrik', 'fabrik_entity')->select('Name', $field['Params']['entityid']);

			$where = $field['Params']['where'] ?? null;

			if (!$where)
			{
				$filter = $field['Params']['filter'] ?? null;

				if ($filter)
					$where = $this->app->getComponent('field', 'sqlwhere', 'fabrik')->getModel()->buildeWhere($field['Params']['filter'], 'filter');
			}

			$this->app->getPagePluginManager()->run('fabrikFilterAfteWhere', [$field, \F::std(['where'=>&$where])]);

			$data = \F::getDbo()
				->setQuery('
					SELECT t0.id, t0.'.$flabel['Name'].$select0.'
					FROM &__'.$entity['Name'].' t0
					'.($where ? 'WHERE '.$where : '')
				)
				->loadAssocList();

			$options = $this->_getFabrikOptions($field, $data);
		}
		else
		{
			$value = $this->getFieldValue($fieldid);

			if ($value)
			{
				$entityid = $field['Params']['entityid'];
				$labelid = $field['Params']['labelid'];

				$flabel = $this->app->getCtrl('fabrik', 'fabrik_field')->select('Name', $labelid);
				$data = [$this->app->getCtrl('fabrik', $entityid)->select('id, '.$flabel['Name'].$select, $value)];

				$options = $this->_getFabrikOptions($field, $data);
			}
		}

		return $options;
	}

	public function _getFabrikOptions($field, $data)
	{
		$options = null;
		$this->app->getPagePluginManager()->run('fabrikFilterOptions', [$field, $data, \F::std(['options'=>&$options])]);

		if (!$options)
		{
			$flabel = $this->app->getCtrl('fabrik', 'fabrik_field')->select('Name', $field['Params']['labelid']);
			$options = \F::getHelper('arr')->rebuild($data, ['value'=>'id', 'label'=>$flabel['Name']]);
		}

		return $options;
	}
}