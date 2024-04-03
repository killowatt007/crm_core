<?php
namespace bs\spaces\md\plugins\fabrik\entity;
defined('EXE') or die('Access');

// create field DebtSumCalc

include_once PATH_ROOT .'/components/fabrik/event/pluginList.php';
use \bs\components\fabrik\event\PluginList;

class ListClients extends PluginList
{
	private $clientData = [];

	// public function onAfterData($view, $args)
	// {
	// 	$model = $this->getModel();
	// 	$actPage = $this->getCtrl('builder_tmpl')->getActive();

	// 	// analytics_dash
	// 	if ($actPage['Alias'] == 'analytics_dash')
	// 	{
	// 		$args->data['analytics']['test'] = 1;
	// 	}
	// }

	public function onFilter()
	{
		$model = $this->getModel();
		$actPage = $this->getCtrl('builder_tmpl')->getActive();

		// analytics_dash
		if ($actPage['Alias'] == 'analytics_dash')
		{
			$streamData = $this->app->input->get('stream.analytics', []);
			$islist = (int)($streamData['islist'] ?? 0);

			if ($islist)
			{
				$anModel = $this->app->getComponent('module', 'analytics_dash', 'analytics_dash')->getModel();

				$anModel->model = $model;
				$anModel->debt = $streamData['debt'];
				$anModel->status = $streamData['status'];
				$anModel->base = $streamData['base'];

				$where = $anModel->getListWhere();

				$this->getModel()->where = $where;
			}
		}
	}

	public function onParams()
	{
		$model = $this->getModel();
		$actPage = $this->getCtrl('builder_tmpl')->getActive();

		// analytics_dash
		if ($actPage['Alias'] == 'analytics_dash')
		{
			if ($this->app->isMobile())
			{
				$model = $this->getModel();
				$showEls = $model->getParam('showElementids');
				unset($showEls[2], $showEls[3], $showEls[4], $showEls[5]);
				$model->setParam('showElementids', $showEls);
			}
		}
	}

	public function onElementValue($el, $i)
	{
		if ($el->getName() == 'DebtSumCalc')
		{
			$model = $this->getModel();
			$data = $model->getData()[$i];
			$cid = $data['id'];

			$left = number_format($model->clientData[$cid]['left'], 0, ',', ' ');

			$el->setValue($left, $i);
		}

		// AddressCalc
		if ($el->getName() == 'AddressCalc')
		{
			$model = $this->getModel();
			$data = $model->getData()[$i] ?? null;

			if ($data)
			{
				$info = $this->getClientInfo($i);
				$val = $info['address'];

				// if (!$this->app->isMobile())
				// 	$val = '<div class="list-address">'.$val .'</div>';

				$el->setValue($val, $i);
			}
		}
	}

	private function getClientInfo($i)
	{
		$id = $this->getModel()->getData()[$i]['id'];
		return $this->getCtrl()->getInfo($id);
	}
}