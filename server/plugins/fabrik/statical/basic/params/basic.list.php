<?php
namespace bs\plugins\fabrik\statical;
defined('EXE') or die('Access');

use \bs\libraries\Params;

class ParamsListBasic extends Params
{
	public function get()
	{
		$entityid = $this->extraArgs['entityid'];
		$CTRLfield = $this->app->getCtrl('fabrik', 'fabrik_field');

		$elements = \F::getHelper('arr')->rebuild($CTRLfield->select('id, Label', 'EntityId='.$entityid), ['value'=>'id', 'label'=>'Label']);

		$params = $this->fields([
			[
				'type' => 'field',
				'name' => 'label',
				'label' => 'Label'
			],
			[
				'type' => 'list',
				'name' => 'editElementid',
				'label' => 'Edit Element',
				'options' => $elements
			],
			[
				'type' => 'list',
				'name' => 'orderElementid',
				'label' => 'Ordering By',
				'options' => $elements
			],
			[
				'type' => 'list',
				'name' => 'orderDir',
				'label' => 'Ordering Dir',
				'options' => [0=>['value'=>'asc', 'label'=>'ASC'], 1=>['value'=>'desc', 'label'=>'DESC']]
			],
			[
				'type' => 'field',
				'name' => 'display',
				'label' => 'Display'
			],
			[
				'type' => 'items',
				'name' => 'showElementids',
				'label' => 'Show Elements',
				'isopen' => 1,
				'fields' => $this->fields([
					0 => [
						'type' => 'list',
						'name' => 'id',
						'options' => $elements
					]
				])
			]
		]);

		return $params;
	}
}