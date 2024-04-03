<?php
namespace bs\components\field\models\fabrik\sqlwhere;
defined('EXE') or die('Access');

use \bs\libraries\mvc;

class Databasejoin extends mvc\Model
{
	public function getAdminLabel($cel)
	{
		$label = '';

		// filter
		if ($cel['value'] == 'filter')
		{
			$filterField = \F::getApp()->getCtrl('fabrik', 'filter_fields')->select('Name', $cel['filter_fieldid']);
			$entityField = \F::getApp()->getCtrl('fabrik', 'fabrik_field')->select('Name', $cel['entity_fieldid']);

			$label = $filterField['Name'].'.'.$entityField['Name'];
		}

		// entity_field
		if ($cel['value'] == 'entity_field')
		{
			$field = \F::getApp()->getCtrl('fabrik', 'fabrik_field')->select('Name', $cel['fieldid']);
			$label = 'this.'.$field['Name'];
		}

		// text
		elseif ($cel['value'] == 'text')
		{
			$label = "'".$cel['text']."'";
		}

		return $label;
	}

	public function buildeValue($opt, $plg = null)
	{
		$value = '';
		$app = \F::getApp();

		// filter
		if ($opt['value'] == 'filter')
		{
			$filter = $app->getComponent('module', 'filter', 'fabrik')->getModel($opt['filterid']);
			$value = $filter->getFieldValue($opt['filter_fieldid']);
		}

		// entity_field
		elseif ($opt['value'] == 'entity_field')
		{
			if (!in_array($opt['fieldid'], $plg->currentControlFields))
				$plg->currentControlFields[] = $opt['fieldid'];

			$value = $plg->getCV($opt['fieldid']);
		}

		// text
		elseif ($opt['value'] == 'text')
		{
			$value = $opt['text'];
		}

		return $value;
	}

	public function getRowId($filter)
	{
		$rowid = '';

		foreach ($filter as $part) 
		{
			if ($part['type'] == 'value' and $part['value'] == 'filter')
			{
				$rowid = $this->buildeValue($part);
				break;
			}
		}

		return $rowid;
	}
}