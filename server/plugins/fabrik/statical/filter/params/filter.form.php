<?php
namespace bs\plugins\fabrik\statical;
defined('EXE') or die('Access');

use \bs\libraries\Params;

class ParamsFormFilter extends Params
{
	public function get()
	{
		$fields = [];
		$tmpld = $this->extraArgs['tmplid'] ?? null;
		
		if ($tmpld)
		{
			$options = \F::getHelper('arr')->rebuild(
				$this->app->getCtrl('fabrik', 'module')->select('id, Name', 'TmplId='.$tmpld.' AND Module="fabrik.filter"'),
				['value'=>'id', 'label'=>'Name']
			);

			$fields[] = [
				'type' => 'fabrik/filterfields',
				'name' => 'filter',
				'label' => 'Row Id',
				'options' => $options
			];
		}

		$params = $this->fields($fields);

		return $params;
	}
}