<?php
namespace bs\plugins\fabrik\statical;
defined('EXE') or die('Access');

use \bs\libraries\Params;

class ParamsFormBasic extends Params
{
	public function get()
	{
		$entityid = $this->extraArgs['entityid'];
		$CTRLtmpl = $this->app->getCtrl('fabrik', 'builder_tmpl');
		$CTRLfield = $this->app->getCtrl('fabrik', 'fabrik_field');

		$tmpls = \F::getHelper('arr')->rebuild($CTRLtmpl->select('id, Name', 'EntityId='.$entityid.' AND EntityTypeId=2'), ['value'=>'id', 'label'=>'Name']);
		$elements = \F::getHelper('arr')->rebuild($CTRLfield->select('id, Label', 'EntityId='.$entityid), ['value'=>'id', 'label'=>'Label']);

		$params = $this->fields([
			[
				'type' => 'list',
				'name' => 'tmplid',
				'label' => 'Template',
				'options' => $tmpls
			],
			[
				'type' => 'yesno',
				'name' => 'isedit',
				'label' => 'Editable'
			],
			[
				'type' => 'yesno',
				'name' => 'history',
				'label' => 'History'
			],
			[
				'type' => 'list',
				'name' => 'editElementid',
				'label' => 'Edit Element',
				'options' => $elements
			],
		]);

		return $params;
	}
}