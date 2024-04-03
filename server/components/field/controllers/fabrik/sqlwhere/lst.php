<?php
namespace bs\components\field\controllers\fabrik\sqlwhere;
defined('EXE') or die('Access');

use \bs\libraries\mvc\Controller;

class Lst extends Controller
{
	public function params()
	{
		$params = $this->app->getComponent('builder', 'params')->getView([
			'type' => 'fields',
			'items' => [
				0 => [
					'type' => 'fabrik/sqlwhere/lst',
					'name' => 'value',
					'label' => 'Value',
					'options' => [
						0 => ['value'=>'filter', 'label'=>'Filter'],
						1 => ['value'=>'text', 'label'=>'Text'],
					]
				]
			]
		])->getData();

		return ['params'=>$params];
	}


	public function valueParams()
	{
		$type = $this->app->input->get('ptype');

		// filter
		if ($type == 'filter')
		{
			$tmplid = $this->app->input->get('extradata.tmplid');

			$moduleFilters = \F::getHelper('arr')->rebuild(
				$this->app->getCtrl('fabrik', 'module')->select('id, Name', 'TmplId='.$tmplid.' AND Module="fabrik.filter"'), 
				['value'=>'id', 'label'=>'Name']
			);

			$params = [
				'type' => 'fields',
				'items' => [
					0 => [
						'type' => 'list',
						'name' => 'filterid',
						'label' => 'Filter',
						'options' => $moduleFilters
					],
					1 => [
						'type' => 'list',
						'name' => 'filter_fieldid',
						'label' => 'Filter Field',
						'options' => []
					],
					2 => [
						'type' => 'list',
						'name' => 'entity_fieldid',
						'label' => 'Entity Field',
						'options' => []
					]
				]
			];
		}

		// text
		if ($type == 'text')
		{
			$params = [
				'type' => 'fields',
				'items' => [
					0 => [
						'type' => 'text',
						'name' => 'text',
						'label' => 'Text'
					]
				]
			];
		}

		$params = $this->app->getComponent('builder', 'params')->getView($params)->getData();

		return ['params'=>$params];
	}

	public function getFilterFields()
	{
		$filterid = $this->app->input->get('filterid');

		$module = $this->app->getCtrl('fabrik', 'module')->select('Params', $filterid);
		$moduleParams = json_decode($module['Params'], true);

		$options = \F::getHelper('arr')->rebuild(
			$this->app->getCtrl('fabrik', 'filter_fields')->select('id, Label', 'FilterId='.$moduleParams['filterid']), 
			['value'=>'id', 'label'=>'Label']
		);

		return ['options'=>$options];
	}

	public function getEntityFields()
	{
		$options = [];
		$filter_fieldid = $this->app->input->get('filter_fieldid');

		$field = $this->app->getCtrl('fabrik', 'filter_fields')->select('Params, Type', $filter_fieldid);
		
		if ($field['Type'] == 'fabrik')
		{
			$fieldParams = json_decode($field['Params'], true);

			$options = \F::getHelper('arr')->rebuild(
				$this->app->getCtrl('fabrik', 'fabrik_field')->select('id, Label', 'EntityId='.$fieldParams['entityid']), 
				['value'=>'id', 'label'=>'Label']
			);
		}

		return ['options'=>$options];
	}
}