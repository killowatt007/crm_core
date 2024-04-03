<?php
namespace bs\components\domofon\views\invoice;
defined('EXE') or die('Access');

use \bs\libraries\mvc\View;

class Quarter extends View
{
	protected function data()
	{
		$data = [];

		$data['baseOpts'] = \F::getHelper('arr')->rebuild(
			\F::getApp()->getCtrl('fabrik', 'pick_items')->select('id, Name', 'PickTypeId=4'), 
			['value'=>'id', 'label'=>'Name']
		);

		$advanced = $this->app->getCtrl('fabrik', 'advanced');

		$data['advanced']['invalid_debt'] = $advanced->get('quarter.invalid_debt');
		$data['advanced']['mailing'] = $advanced->get('quarter.mailing');

		return $data;
	}
}