<?php
namespace bs\components\field\models\fabrik;
defined('EXE') or die('Access');

use \bs\libraries\mvc;

class Sqlwhere extends mvc\Model
{
	public $rowid = null;

	public function buildeWhere($opt, $flag, $plg = null)
	{
		$where = '';
		$app = \F::getApp();
		$regexp = false;

		foreach ($opt as $part) 
		{
			// element
			if ($part['type'] == 'element')
			{
				$element = $app->getCtrl('fabrik', 'fabrik_field')->select('Name', $part['element']);
				$where .= 't0.'.$element['Name'];
			}

			// condition
			elseif ($part['type'] == 'condition')
			{
				$condition = $part['condition'];

				if (in_array($part['condition'], ['regexp','regexp_w','regexp_wf']))
				{
					$condition = 'regexp';
					$regexp = $part['condition'];
				}

				$where .= ' '.$condition.' ';
			}

			// value
			elseif ($part['type'] == 'value')
			{
				$model = $app->getComponent('field', $flag, 'fabrik.sqlwhere')->getModel();
				$value = $model->buildeValue($part, $plg);

				if ($regexp)
				{
					if ($value == '')
					{
						$value = '.';
					}
					else
					{
						if ($regexp == 'regexp_w')
							$value = '(^|[[:space:]])'.$value;
						elseif ($regexp == 'regexp_wf')
							$value = '^'.$value;
					}
					
					$regexp = false;
				}

				$where .= '"'.$value.'"';
			}

			// join
			elseif ($part['type'] == 'join')
			{
				$where .= ' '.$part['join'].' ';
			}
		}

		return $where;
	}
}