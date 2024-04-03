<?php
namespace bs\components\field\controllers\fabrik;
defined('EXE') or die('Access');

use \bs\libraries\mvc\Controller;

class Filterfields extends Controller
{
	public function getFields()
	{
		$moduleid = $this->app->input->get('moduleid');

		$module = $this->app->getCtrl('fabrik', 'module')->select('Params', $moduleid);
		$params = json_decode($module['Params'], true);
		
		$options = \F::getHelper('arr')->rebuild(
			$this->app->getCtrl('fabrik', 'filter_fields')->select('id, Label', 'FilterId='.$params['filterid']), 
			['value'=>'id', 'label'=>'Label']
		);

		return ['options'=>$options];
	}
}