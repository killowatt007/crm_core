<?php
namespace bs\plugins\builder\entity;
defined('EXE') or die('Access');

use bs\libraries;

class PageMasters extends libraries\event\PluginModel
{
	public function onFabrikFilterField($args)
	{
		if ($args->field['Type'] == 'fabrik')
		{
			// category
			if ($args->field['Name'] == 'category')
			{
				$args->field['Params']['select'] = ['CategoryId'];
			}
		}
	}

	public function onFabrikFilterAfteWHere($field, $args)
	{
		if ($field['Name'] == 'staff')
		{
			/*B_BASE*/
			$args->where .= ($args->where ? ' AND ' : '') . 't0.BaseId='.$this->app->getCtrl('fabrik', 'pick_items')->getActiveBase();
      $args->where .= ' AND t0.RoleId=4';
		}
	}

	public function onFabrikFilterOptions($field, $data, $args)
	{
		if ($field['Name'] == 'category')
		{
      $options = \F::getHelper('arr')->sort($data, 'id', 'CategoryId', false);
      $args->options = [];

      foreach ($options as $option) 
      {
        $space = '';
        for ($i=0; $i < $option['lvl']; $i++) 
          $space .= $i ? '––' : '';

        $args->options[] = [
          'value' => $option['id'],
          'label' => $space.' '.$option['Name']
        ];
      }
		}
	}
}



