<?php
namespace bs\plugins\fabrik\statical;
defined('EXE') or die('Access');

use \bs\libraries\Params;

class ParamsElementsCalcBasic extends Params
{
	public function get()
	{
		$rowid = $this->extraArgs['rowid'] ?? null;
		$fieldData = null;

		if ($rowid)
			$fieldData = $this->app->getCtrl('fabrik', 'fabrik_field')->select('Name', $this->extraArgs['rowid']);
		
		$params = [
			0 => [
				'type' => 'list',
				'name' => 'list_label',
				'label' => 'List Label',
				'isps' => 0,
				'options' => [
					0 => [
						'value' => 1,
						'label' => 'Yes'
					],
					1 => [
						'value' => 0,
						'label' => 'No'
					]
				]
			]
		];

		if ($fieldData)
		{
			$configData = $this->app->getCtrl('fabrik', 'config')->select('*', 'IsActive=1')[0];
			
			// Number
			if ($fieldData['Name'] == 'Number')
			{
				$numbersOptions = $this->app->getCtrl('fabrik', 'pick_items')->select('id, Name', 'PickTypeId='.$configData['TypeNumber']);

				$params[] = [
					'type' => 'list',
					'name' => 'number_prefix',
					'label' => 'Prefix',
					'isps' => 1,
					'options' => \F::getHelper('arr')->rebuild($numbersOptions, ['value'=>'id', 'label'=>'Name'])
				];
			}
		}

		return $this->fields($params);
	}
}