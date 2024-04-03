<?php
namespace bs\components\module\views\sklad\tree_catalog;
defined('EXE') or die('Access');

use \bs\libraries\mvc\View;

class Tree_catalog extends View
{
	protected function data()
	{
		$data = [];
		$model = $this->getModel();

		$this->app->getPagePluginManager()->run('treeCatalogViewData', [$model, \F::std(['data'=>&$data])]);

		return $data;
	}
}