<?php
namespace bs\plugins\fabrik\statical;
defined('EXE') or die('Access');

include_once PATH_ROOT .'/components/fabrik/event/pluginList.php';
use \bs\components\fabrik\event\PluginList;

class ListBasic extends PluginList
{
	public function onElementValue($el, $i)
	{
		$model = $this->getModel();

		// Number
		if ($el->getName() == 'Number')
		{
			$data = $model->getData()[$i];
			$prefixid = $el->getParam('number_prefix');

			if ($prefixid)
			{
				$numberData = $this->getCtrl('pick_items')->select('StrValue', $prefixid);
				$value = $numberData['StrValue'].str_pad($data['id'], 6, 0, STR_PAD_LEFT);
				$el->setValue($value, $i);
			}
		}
	}

	public function onButtons($args)
	{
		if (isset($_GET['kkt']))
		{
			$kkt = $this->getCtrl('kkt');

			// qqq($kkt->get_res('03e8fb37-649f-4a00-b391-80d50e040d95'));

			qqq([$kkt->get_shift_status(), $kkt->error]);	

		  // Изготовление металлоизделий
		  // $res = $kkt->fiskal('sell', [0 => [
		  //   'name' =>'Техническое обслуживание домофонных систем',
		  //   'price' => 1,
		  //   'quantity' => 1
		  // ]], 'cash', true);

		  // echo '<pre>';
		  // print_r($kkt->error);
		  // echo '</pre>';

		  // echo '<pre>';
		  // print_r($res);
		  // echo '</pre>';

		  // echo '<pre>';
		  // print_r($kkt->last_uuid);
		  // echo '</pre>';
		  // exit;

		  // 
		}


		$args->data['buttons']['add'] = [
			'name' => 'add',
			'label' => 'Добавить',
			'color' => 'success',
			'icon' => 'far fa-plus',
			'order' => 0
		];
	}
}


