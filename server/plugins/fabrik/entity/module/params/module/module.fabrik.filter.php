<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

use \bs\libraries\Params;

class ParamsModuleFabrikFilterModule extends Params
{
	public function get()
	{
		$CTRLfilter = \F::getApp()->getCtrl('fabrik', 'filters');
		$filters = \F::getHelper('arr')->rebuild($CTRLfilter->select('id, Name'), ['value'=>'id', 'label'=>'Name']);

		$params = [
			'type' => 'fields',
			'view' => 'inline',
			'items' => [
				0 => [
					'type' => 'list',
					'name' => 'filterid',
					'label' => 'Filter',
					'options' => $filters
				]
			]
		];

		return $params;
	}
}