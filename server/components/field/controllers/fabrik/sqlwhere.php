<?php
namespace bs\components\field\controllers\fabrik;
defined('EXE') or die('Access');

use \bs\libraries\mvc\Controller;

class Sqlwhere extends Controller
{
	private $options = [
		'join' => [
			'and' => 'AND',
			'or' 	=>'OR'
		],
		'condition' => [
			'=' 	=> '=',
			'!=' 	=> '!=',
			'regexp' 	=> 'regexp',
			'regexp_w' 	=> 'regexp_w',
			'regexp_wf' 	=> 'regexp_wf'
		],
		'brk' => [
			'(' 	=> '(',
			')' 	=> ')'
		]
	];

	private $condition = [
		'and' => 'AND',
		'or' =>'OR'
	];

	public function adminLabel()
	{
		$labels = [];
		$cels = $this->app->input->get('cels', []);
		$flag = $this->app->input->get('flag');

		foreach ($cels as $key => $cel) 
		{
			$label = '';
			$type = $cel['type'];

			if ($type != 'value')
			{
				if ($type == 'element')
				{
					$entityField = $this->app->getCtrl('fabrik', 'fabrik_field')->select('Name', $cel[$type]);
					$label = 'this.'.$entityField['Name'];
				}
				else
				{
					$label = $this->options[$type][$cel[$type]];
				}
			}
			else
			{
				$label = $this->app->getComponent('field', $flag, 'fabrik.sqlwhere')->getModel()->getAdminLabel($cel);
			}

			$labels[] = $label;
		}
		
		return ['labels'=>$labels];
	}

	public function popupParams()
	{
		$params = $this->app->getComponent('builder', 'params')->getView([
			'type' => 'fields',
			'items' => [
				0 => [
					'type' => 'list',
					'name' => 'type',
					'label' => 'Type',
					'options' => [
						0 => ['value'=>'element', 'label'=>'Element'],
						1 => ['value'=>'condition', 'label'=>'Condition'],
						2 => ['value'=>'value', 'label'=>'Value'],
						3 => ['value'=>'join', 'label'=>'Join'],
						4 => ['value'=>'brk', 'label'=>'Brk'],
					]
				]
			]
		])->getData();

		return ['params'=>$params];
	}

	public function typeParams()
	{
		$type = $this->app->input->get('ptype');
		
		// element
		if ($type == 'element')
		{
			if ($fieldid = $this->app->input->get('extradata.fieldid'))
			{
				$params = $this->app->getCtrl('fabrik', 'fabrik_field')->select('Params', $fieldid)['Params'];
				$entityid = json_decode($params, true)['basic']['join_entity'];
			}
			else
			{
				$entityid = $this->app->input->get('extradata.entityid');
			}
			
			$entity_fields = \F::getHelper('arr')->rebuild(
				$this->app->getCtrl('fabrik', 'fabrik_field')->select('id, Label', 'EntityId='.$entityid),
				['value'=>'id', 'label'=>'Label']
			);

			$params = [
				'type' => 'fields',
				'items' => [
					0 => [
						'type' => 'list',
						'name' => 'element',
						'label' => 'Element',
						'options' => $entity_fields
					]
				]
			];
		}

		// condition
		elseif ($type == 'condition')
		{
			$params = [
				'type' => 'fields',
				'items' => [
					0 => [
						'type' => 'list',
						'name' => 'condition',
						'label' => 'Condition',
						'options' => $this->getOptions('condition')
					]
				]
			];
		}

		// join
		elseif ($type == 'join')
		{
			$params = [
				'type' => 'fields',
				'items' => [
					0 => [
						'type' => 'list',
						'name' => 'join',
						'label' => 'Join',
						'options' => $this->getOptions('join')
					]
				]
			];
		}

		// brk
		elseif ($type == 'brk')
		{
			$params = [
				'type' => 'fields',
				'items' => [
					0 => [
						'type' => 'list',
						'name' => 'brk',
						'label' => 'Brk',
						'options' => $this->getOptions('brk')
					]
				]
			];
		}

		$params = $this->app->getComponent('builder', 'params')->getView($params)->getData();

		return ['params'=>$params];
	}

	private function getOptions($type)
	{
		$options = [];

		foreach ($this->options[$type] as $val => $lab)
			$options[] = ['value'=>$val, 'label'=>$lab];

		return $options;
	}
}