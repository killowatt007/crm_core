<?php
namespace bs\plugins\fabrik\statical;
defined('EXE') or die('Access');

use \bs\libraries\Params;

class ParamsElementsDatabasejoinFilter extends Params
{
	public function get()
	{
		$params = $this->fields([
			0 => [
				'type' => 'fabrik/sqlwhere',
				'name' => 'filter',
				'label' => 'Filter',
				'flag' => 'databasejoin'
			]
		]);

		return $params;
	}
}