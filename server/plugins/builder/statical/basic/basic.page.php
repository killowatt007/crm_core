<?php
namespace bs\plugins\builder\statical;
defined('EXE') or die('Access');

use bs\libraries;

class PageBasic extends libraries\event\PluginModel
{
	public function onAfterData($view, $data)
	{
		// redirect
		$fabrik = $this->app->input->get('fabrik', null);

		if ($fabrik)
			$data->data['fabrik'] = $fabrik;
	}
}



