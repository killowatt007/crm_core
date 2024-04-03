<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

use \bs\libraries\Params;

class ParamsSelfMenu_item extends Params
{
	public function get()
	{
		$params = null;
		$type = $this->extraArgs['type'];

		if ($type == 'component')
		{
			$CTRLtmpl = $this->app->getCtrl('fabrik', 'builder_tmpl');
			$pages = \F::getHelper('arr')->rebuild($CTRLtmpl->select('id, Name', 'EntityTypeId=1'), ['value'=>'id', 'label'=>'Name']);

			$params = [
				'type' => 'sections',
				'name' => 'Params',
				'items' => [
					0 => [
						'size' => 24,
						'label' => 'Params',
						'data' => [
							'type' => 'fields',
							'view' => 'inline',
							'items' => $this->fields([
								0 => [
									'type' => 'list',
									'name' => 'pageid',
									'label' => 'Page',
									'options' => $pages
								]
							])
						]
					]
				]
			];
		}

		return $params;
	}
}