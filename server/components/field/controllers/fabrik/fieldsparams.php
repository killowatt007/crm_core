<?php
namespace bs\components\field\controllers\fabrik;
defined('EXE') or die('Access');

use \bs\libraries\mvc\Controller;

class Fieldsparams extends Controller
{
	public function getParams()
	{
		$params = null;
		$fieldid = $this->app->input->get('fieldid');
		$field = $this->app->getCtrl('fabrik', 'fabrik_field')->select('id, Type, Label', $fieldid);

		$fieldClass = $this->app->includeExt('plugin', 'fabrik_field', 'fabrik.entity', ['params', 'self']);
		$objParams = new $fieldClass(['type'=>$field['Type']]);

		if ($scheme = $objParams->get())
			$params = $this->app->getComponent('builder', 'params')->getView($scheme)->getData();

		return [
			'params' => $params, 
			'field' => [
				'id' => $field['id'],
				'label' => $field['Label'],
			]
		];
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