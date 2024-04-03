<?php
namespace bs\components\builder\views\addons\general;
defined('EXE') or die('Access');

include_once PATH_ROOT.'/components/builder/addon.php';
use bs\components\builder\Addon;

class Text extends Addon
{
	public function data()
	{
		$data = [];
		$model = $this->getModel();

		$data['text'] = $model->getParam('params.text');
		$data['classes'] = $model->getParam('params.classes');

		return $data;
	}

	public static function getParams()
	{
		$p = \F::getParams();
		
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
											'type' => 'text',
											'name' => 'text',
											'label' => 'Text'
										],
										1 => [
											'type' => 'field',
											'name' => 'classes',
											'label' => 'Classes'
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



