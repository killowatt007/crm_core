<?php
namespace bs\components\builder\views\addons\page;
defined('EXE') or die('Access');

include_once PATH_ROOT.'/components/builder/addon.php';
use bs\components\builder\Addon;

class Module extends Addon
{
	public function data()
	{
		$data = [];
		$model = $this->getModel();

		$moduleView = $this->app->getService('module', 'helper')
			->getModule($model->getParam('params.moduleid'))
			->getComponent()
			->getView();

		$data = $moduleView->getData();

		return $data;
	}

	public static function getParams()
	{
		$app = \F::getApp();
		$p = \F::getParams();
		$CTRLmodule = $app->getCtrl('fabrik', 'module');
		$modules = [];

		foreach ($CTRLmodule->select('id, Name') as $row) 
		{
			$modules[] = [
				'value' => $row['id'],
				'label' => $row['Name']
			];
		}
		
		$params = [
			'type' => 'tabs',
			'items' => [
				0 => [
					'label' => 'Basic',
					'data' => [
						'type' => 'sections',
						'items' => [
							0 => [
								'size' => 24,
								'data' => [
									'type' => 'fields',
									'items' => $p->fields([
										0 => [
											'type' => 'list',
											'name' => 'moduleid',
											'label' => 'Module',
											'options' => $modules
										]
									])
								]
							]
						]
					]
				]
			]
		];

		return $params;
	}
}