<?php
namespace bs\components\field\models\fabrik\sqlwhere;
defined('EXE') or die('Access');

use \bs\libraries\mvc;

class Lst extends mvc\Model
{
	public function getAdminLabel($cel)
	{
		$label = '';

		// filter
		if ($cel['value'] == 'filter')
		{
			$ffieldName = \F::getApp()->getCtrl('fabrik', 'filter_fields')->select('Name', $cel['filter_fieldid'])['Name'];
			$fieldName = '';

			if ($cel['entity_fieldid'])
			{
				$field = \F::getApp()->getCtrl('fabrik', 'fabrik_field')->select('Name', $cel['entity_fieldid']);
				$fieldName = '.'.$field['Name'];
			}

			$label = $ffieldName.$fieldName;
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
		if ($opt['value'] == 'filter')
		{
			if ($opt['filter_fieldid'])
			{
				$filter = $app->getComponent('module', 'filter', 'fabrik')->getModel($opt['filterid']);
				$value = $filter->getFieldValue($opt['filter_fieldid']);
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