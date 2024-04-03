<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

include_once PATH_ROOT .'/components/fabrik/event/pluginForm.php';
use \bs\components\fabrik\event\PluginForm;

class FormCatalog_category extends PluginForm
{
	// public function onBeforeStore()
	// {
	// 	$model = $this->getModel();
	// 	$params = $this->getCV('Params');

	// 	// component (page)
	// 	if ($this->getCV('Type') == 'component')
	// 		$this->updFD('Link', 'option=builder&task=page&id='.$params['pageid']);

	// 	$this->display();
	// 	$this->updFD('Params', json_encode($params));
	// }

	// private function getLastItem()
	// {
	// 	$data = \F::getDbo()->setQuery('
	// 		SELECT *
	// 		FROM &__menu_item
	// 		WHERE MenuId='.$this->getCV('MenuId').' AND ParentId='.$this->getCV('ParentId').'
	// 		ORDER BY Display DESC
	// 		LIMIT 1'
	// 	)->loadAssoc();

	// 	return $data;
	// }

	// private function getById($id)
	// {
	// 	$data = \F::getDbo()->setQuery('
	// 		SELECT *
	// 		FROM &__menu_item
	// 		WHERE id='.$id
	// 	)->loadAssoc();

	// 	return $data;
	// }

	// private function updDisplay($display, $oper)
	// {
	// 	$data = \F::getDbo()->setQuery('
	// 		UPDATE &__menu_item
	// 		SET Display=Display'.$oper.'1
	// 		WHERE Display='.$display.' AND MenuId='.$this->getCV('MenuId').' AND ParentId='.$this->getCV('ParentId')
	// 	)->execute();
	// }

	// private function display()
	// {
	// 	$model = $this->getModel();
	// 	$newdisplay = null;
		
	// 	if ($model->isNewRecord())
	// 	{
	// 		$lastdisplay = ($this->getLastItem() ?? [])['Display'] ?? 0;
	// 		$newdisplay = $lastdisplay+1;
	// 	}
	// 	else
	// 	{
	// 		$orderingid = $model->getFormData()['ordering'];

	// 		if ($this->getCV('id') != $orderingid)
	// 		{
	// 			$origdisplay = $this->getCV('Display');
	// 			$stack = [];

	// 			if (!$orderingid)
	// 			{
	// 				$newdisplay = 1;
	// 				$oper = '+';
	// 				$from = 1;
	// 				$to = $this->getCV('Display')-1;
	// 			}
	// 			else
	// 			{
	// 				$orditem = $this->getById($orderingid);
	// 				$newdisplay = $orditem['Display']+1;

	// 				if ($origdisplay > $orditem['Display'])
	// 				{
	// 					$oper = '+';
	// 					$from = $orditem['Display']+1;
	// 					$to = $origdisplay-1;
	// 				}
	// 				else
	// 				{
	// 					$oper = '-';
	// 					$from = $origdisplay+1;
	// 					$to = $orditem['Display'];
	// 				}
	// 			}

	// 			for ($i=$from; $i<$to+1; $i++) 
	// 				$stack[] = $i;

	// 			if ($oper == '+')
	// 				$stack = array_reverse($stack);

	// 			foreach ($stack as $i) 
	// 				$this->updDisplay($i, $oper);
	// 		}
	// 	}

	// 	if ($newdisplay)
	// 		$this->updFD('Display', $newdisplay);
	// }

	// public function onElementCalcGetValue($element, $i)
	// {
	// 	$model = $this->getModel();

	// 	// Ordering
	// 	if ($element->getName() == 'Ordering')
	// 	{
	// 		$rows = [];

	// 		if (!$model->isNewRecord())
	// 		{
	// 			$rows = \F::getDbo()->setQuery('
	// 				SELECT id AS value, Name AS label, ParentId, Display
	// 				FROM &__menu_item
	// 				WHERE MenuId='.$this->getCV('MenuId').' AND ParentId='.$this->getCV('ParentId').'
	// 				ORDER BY Display ASC'
	// 			)->loadAssocList();

	// 			array_unshift($rows, ['value'=>0, 'label'=>'- First -']);
	// 		}

	// 		$element->setValue($rows, $i);
	// 	}
	// }

	// public static function onAjaxGetComponentParams()
	// {
	// 	$result = ['params'=>null];
	// 	$app = \F::getApp();
	// 	$type = $app->input->get('ftype');

	// 	$classParams = $app->includeExt('plugin', 'menu_item', 'fabrik.entity', ['params', 'self']);
	// 	$objParams = new $classParams(['type'=>$type]);

	// 	if ($params = $objParams->get())
	// 		$result['params'] = $app->getComponent('builder', 'params')->getView($params)->getData();

	// 	return $result;
	// }
}