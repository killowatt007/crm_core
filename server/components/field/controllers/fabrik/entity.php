<?php
namespace bs\components\field\controllers\fabrik;
defined('EXE') or die('Access');

use \bs\libraries\mvc\Controller;

class Entity extends Controller
{
	public function getParams()
	{
		$entityid = $this->app->input->get('entityid');
		$tmplid = $this->app->input->get('tmplid');
		$eview = $this->app->input->get('eview');

		$classParams = $this->app->includeExt('plugin', 'fabrik_entity', 'fabrik.entity', ['params', 'self']);
		$objParams = new $classParams(['entityid'=>$entityid, 'tmplid'=>$tmplid]);

		$fields = \F::getHelper('arr')->rebuild(
			$this->app->getCtrl('fabrik', 'fabrik_field')->select('id, Label', 'EntityId='.$entityid),
			['value'=>'id', 'label'=>'Label']
		);

		$params = $this->app->getComponent('builder', 'params')->getView([
			'type' => 'tabs',
			'items' => [
				0 => [
					'label' => ucfirst($eview),
					'data' => [
						'type' => 'sections',
						'items' => $objParams->getBlockParams($eview)
					]
				],
				1 => [
					'label' => 'Fields',
					'data' => [
						'type' => 'fields',
						'name' => 'fields',
						'view' => 'inline',
						'items' => [
							0 => [
								'type' => 'fabrik/fieldsparams',
								'label' => 'Field',
								'name' => 'fields',
								'options' => $fields,
								'activeparams' => false
							]
						]
					]
				]
			]
		])->getData();

		return ['params'=>$params];
	}

	// public function getFields()
	// {
	// 	$entityid = $this->app->input->get('entityid');

	// 	$CTRLfields = $this->app->getCtrl('fabrik', 'fabrik_field');
	// 	$hArr = \F::getHelper('arr');
		
	// 	$entities = $hArr->rebuild($CTRLfields->getData('byCond', 'EntityId='.$entityid), ['id'=>'value', 'Label'=>'label']);

	// 	return ['options'=>$entities];
	// }
}