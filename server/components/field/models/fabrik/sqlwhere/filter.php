<?php
namespace bs\components\field\models\fabrik\sqlwhere;
defined('EXE') or die('Access');

use \bs\libraries\mvc;

class Filter extends mvc\Model
{
	public function getAdminLabel($cel)
	{
		$label = '';

		// filterfield
		if ($cel['value'] == 'filterfield')
		{
			$filterField = \F::getApp()->getCtrl('fabrik', 'filter_fields')->select('Name', $cel['filter_fieldid']);
			$entityField = \F::getApp()->getCtrl('fabrik', 'fabrik_field')->select('Name', $cel['entity_fieldid']);

			$label = $filterField['Name'].'.'.$entityField['Name'];
		}

		// text
		elseif ($cel['value'] == 'text')
		{
			$label = "'".$cel['text']."'";
		}

		return $label;
	}

	public function buildeValue($opt)
	{
		$value = '';
		$app = \F::getApp();

		// filter
		if ($opt['value'] == 'filterfield')
		{
			if ($opt['filter_fieldid'])
			{
				// $filterField = $app->getCtrl('fabrik', 'filter_fields')->getData('byId', $opt['filter_fieldid']);
				// $module = $app->getModule($filterField['FilterId']);

				$module = $app->getService('fabrik', 'helper')->getFilter('main');
				$value = $module->getFieldValue($opt['filter_fieldid']);
			}
		}

		// text
		if ($opt['value'] == 'text')
		{
			$value .= $opt['text'];
		}

		return $value;
	}
}