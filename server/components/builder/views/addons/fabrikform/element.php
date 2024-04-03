<?php
namespace bs\components\builder\views\addons\fabrikform;
defined('EXE') or die('Access');

include_once PATH_ROOT.'/components/builder/addon.php';
use bs\components\builder\Addon;

class Element extends Addon
{
	public function data()
	{
		$data = [];
		$model = $this->getModel();
		$formModel = $this->getPageModel()->getParentObject();
		
		$elid = $model->getParam('params.elementid');
		$view = $model->getParam('params.view');
		$element = $formModel->getElement($elid);

		$data['elementid'] = $element->getId();
		$data['view'] = $view;

		if ($view == 'input')
		{
			$formModel->viewelementids[] = $element->getId();
		}
		elseif ($view == 'label')
		{
			$data['label'] = $element->getLabel();
		}

		return $data;
	}

	public static function getParams()
	{
		$app = \F::getApp();
		$extra = $app->input->get('extra');
		$p = \F::getParams();

		$app = \F::getApp();
		$CTRLfield = $app->getCtrl('fabrik', 'fabrik_field');
		$elements = [];

		foreach ($CTRLfield->select('id, Label', 'EntityId='.$extra['entityid']) as $row) 
		{
			$elements[] = [
				'value' => $row['id'],
				'label' => $row['Label']
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
											'name' => 'elementid',
											'label' => 'Element',
											'options' => $elements
										],
										1 => [
											'type' => 'list',
											'name' => 'view',
											'label' => 'View',
											'options' => [
												0 => ['value'=>'input', 'label'=>'Input'],
												1 => ['value'=>'label', 'label'=>'Label']
											]
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



