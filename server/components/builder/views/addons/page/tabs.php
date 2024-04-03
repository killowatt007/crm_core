<?php
namespace bs\components\builder\views\addons\page;
defined('EXE') or die('Access');

include_once PATH_ROOT.'/components/builder/addon.php';
use bs\components\builder\Addon;

class Tabs extends Addon
{
	public function data()
	{
		$model = $this->getModel();
		$type = $model->getParam('params.type', 'simple');
		$data = ['type' => $type, 'tabs' => []];

		if ($type == 'advanced')
		{
			$pmodel = $this->getPageModel();

			foreach ($model->getParam('params.items') as $item) 
			{
				$data['tabs'][] = [
					'label' => $item['params']['label'],
					'data' => $pmodel->parseData($item['data'])
				];
			}	
		}
		else
		{
			foreach ($model->getParam('params.items') as $item) 
			{
				$moduleView = $this->app->getService('module', 'helper')->getModule($item['moduleid'])->getComponent()->getView();

				$data['tabs'][] = [
					'label' => $item['label'],
					'data' => $moduleView->getData()
				];
			}	
		}

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
											'name' => 'type',
											'label' => 'Type',
											'isps' => false,
											'options' => [
												0 => ['value'=>'simple', 'label'=>'Simple'],
												1 => ['value'=>'advanced', 'label'=>'Advanced']
											]
										],
										1 => [
											'type' => 'items',
											'name' => 'items',
											'label' => 'Items',
											'fields' => $p->fields([
												0 => [
													'type' => 'list',
													'name' => 'moduleid',
													'label' => 'Module',
													'options' => $modules
												]
											])
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