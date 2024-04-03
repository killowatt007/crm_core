<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

include_once PATH_ROOT .'/components/fabrik/event/pluginList.php';
use \bs\components\fabrik\event\PluginList;

class ListContracts extends PluginList
{
	public function onFilter()
	{
		$filter = $this->app->getService('fabrik', 'helper')->getFilter();

		if ($filter)
		{
			$where = [];

			$id = (int)$filter->getFieldValue('id');
			$district = $this->getFV('district');
			$street = $this->getFV('street');
			$housenumber = $this->getFV('housenumber');

			if ($id)
				$where[] = 't0.id='.$id;
			if ($housenumber)
				$where[] = 'LOWER(t0.HouseNumber) = LOWER("'.$filter->getFieldValue('housenumber').'")';
			if ($district)
				$where[] = 't0.DistrictId IN (SELECT id FROM &__districts WHERE LOWER(Name) regexp LOWER("'.$district.'"))';
			if ($street)
				$where[] = 't0.StreetId IN (SELECT id FROM &__streets WHERE LOWER(Name) regexp LOWER("'.$street.'"))';

			if (!empty($where))
				$where = implode(' AND ', $where);
			else
				$where =  null;

			/*B_BASE*/
			$where .= ($where ? ' AND ' : '') . 't0.BaseId='.$this->getCtrl('pick_items')->getActiveBase();

			$this->getModel()->where = $where;
		}
	}

	private function getFV($name)
	{
		$filter = $this->app->getService('fabrik', 'helper')->getFilter();
		$value = $filter->getFieldValue($name);

		if ($value)
			$value = '(^|[[:space:]])'.str_replace(['[',']'], ['',''], $value);

		return $value;
	}

	public function onParams()
	{
		if ($this->app->isMobile())
		{
			$model = $this->getModel();
			$showEls = $model->getParam('showElementids');
			$pod = $showEls[6];
			$actions = $showEls[8];

			unset($showEls[0], $showEls[2], $showEls[3], $showEls[4], $showEls[5], $showEls[6], $showEls[7], $showEls[8]);
			$showEls[] = ['id' => 334];
			$showEls[] = $pod;
			$showEls[] = $actions;

			$model->setParam('showElementids', array_values($showEls));		
		}
	}

	public function onAfterData($view, $args)
	{
		if ($this->app->isMobile())
		{
			// $args->data['headers'][4]['label'] = 'Дом';
			$args->data['headers'][0]['label'] = '№';
			$args->data['headers'][2]['label'] = 'Под.';
		}
	}

	public function onElementValue($el, $i)
	{
		if ($el->getName() == 'AddressCalc')
		{
			if ($this->app->isMobile())
			{
				$model = $this->getModel();
				$data = $model->getData()[$i] ?? null;

				if ($data)
				{
					$val  = 
						'<div class="tb-address">
							<span class="street">'.$data['StreetId_join'].' '.($data['HouseNumber'] ? $data['HouseNumber'] : '').'</span>'.($data['BuildingNumber'] ? ', кор. '. $data['BuildingNumber'] : '').'
	        	</div>';

	        // $data['DistrictId_join']

					$el->setValue($val, $i);
				}
			}
		}
	}
}