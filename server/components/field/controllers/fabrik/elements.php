<?php
namespace bs\components\field\controllers\fabrik;
defined('EXE') or die('Access');

use \bs\libraries\mvc\Controller;

class Elements extends Controller
{
	public function getFields()
	{
		$entityid = $this->app->input->get('entityid');

		$CTRLfields = $this->app->getCtrl('fabrik', 'fabrik_field');
		$hArr = \F::getHelper('arr');
		
		$entities = $hArr->rebuild($CTRLfields->select('id, Label', 'EntityId='.$entityid), ['value'=>'id', 'label'=>'Label']);

		return ['options'=>$entities];
	}
}