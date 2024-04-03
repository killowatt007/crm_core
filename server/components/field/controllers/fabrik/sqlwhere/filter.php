<?php
namespace bs\components\field\controllers\fabrik\sqlwhere;
defined('EXE') or die('Access');

use \bs\libraries\mvc\Controller;

class Filter extends Controller
{
	public function params()
	{
		$params = $this->app->getComponent('builder', 'params')->getView([
			'type' => 'fields',
			'items' => [
				0 => [
					'type' => 'fabrik/sqlwhere/filter',
					'name' => 'value',
					'label' => 'Value',
					'options' => [
						0 => ['value'=>'filterfield', 'label'=>'Filter Field'],
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

		// filterfield
		if ($type == 'filterfield')
		{
			$filterid = $this->app->input->get('extradata.filterid');

			$CTRLfields = $this->app->getCtrl('fabrik', 'filter_fields');
			$fields = \F::getHelper('arr')->rebuild($CTRLfields->select('id, Label', 'FilterId='.$filterid), ['value'=>'id', 'label'=>'Label']);

			$params = [
				'type' => 'fields',
				'items' => [
					0 => [
						'type' => 'list',
						'name' => 'filter_fieldid',
						'label' => 'Filter Field',
						'options' => $fields
					],
					1 => [
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

	public function getEntityFields()
	{
		$filter_fieldid = $this->app->input->get('filter_fieldid');

		$field = $this->app->getCtrl('fabrik', 'filter_fields')->select('Params', $filter_fieldid);
		$fieldParams = json_decode($field['Params'], true);

		$entity_fields = \F::getHelper('arr')->rebuild(
			$this->app->getCtrl('fabrik', 'fabrik_field')->select('id, Label', 'EntityId='.$fieldParams['entityid']), 
			['value'=>'id', 'label'=>'Label']
		);

		return ['options'=>$entity_fields];
	}
}