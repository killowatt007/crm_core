<?php
namespace bs\plugins\fabrik\statical;
defined('EXE') or die('Access');

use \bs\libraries\Params;

class ParamsListFilter extends Params
{
	public function get()
	{
		$params = $this->fields([
			0 => [
				'type' => 'fabrik/sqlwhere',
				'name' => 'filter',
				'label' => 'Filter',
				'flag' => 'lst'
			]
		]);

		return $params;
	}
}