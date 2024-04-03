<?php
namespace bs\components\fabrik\service;
defined('EXE') or die('Access');

class Cascade
{
	private $app;
	private $tmplModules = null;

	public function __construct()
	{
		$this->app = \F::getApp();
	}

	public function getRelatedModules($findId, $findType, $onlyDirect = false, &$rModules = [], $direct = true)
	{
		$modules = $this->getTmplModules();

		foreach ($modules as $module) 
		{
			$params = json_decode($module['Params'], true);
			$rModuleId = null;

			if ($module['Module'] == 'fabrik.form')
			{
				if ($findType == 'fabrik.filter')
					$rModuleId = $params['filter']['filter']['moduleid'] == $findId ? $module['id'] : null;
			}
			elseif ($module['Module'] == 'fabrik.list')
			{
				if ($params['filter']['activeparams']['filter'] and isset($params['filter']['filter']))
				{
					foreach ($params['filter']['filter'] as $part) 
					{
						if ($part['type'] == 'value' and $part['value'] == 'filter')
						{
							$rModuleId = $part['filterid'] == $findId ? $module['id'] : null;
							break;
						}
					}
				}
			}

			if ($rModuleId)
			{
				$rModules[] = [
					'id' => $rModuleId,
					'direct' => $direct
				];

				if (!$onlyDirect)
					$this->getRelatedModules($module['id'], $module['Module'], $onlyDirect, $rModules, false);
			}
		}

		return $rModules;
	}

	public function getTmplModules()
	{
		if (!$this->tmplModules)
		{
			$activeItem = $this->app->getMenu()->getActive();
			parse_str($activeItem['Link'], $vars);

			$tmplData = $this->app->getCtrl('fabrik', 'builder_tmpl')->select('Data', $vars['id']);

			$data = json_decode($tmplData['Data'], true);
			$moduleIds = $this->getModuleIds($data);
		
			$this->tmplModules = $this->app->getCtrl('fabrik', 'module')->select(
				'id, Module, Params',
				'id IN('.implode(',', $moduleIds).') AND Module IN ("fabrik.form", "fabrik.list")',
				'id'
			);
		}

		return $this->tmplModules;
	}

	private function getModuleIds($data, &$modules = [])
	{
		preg_match_all('/"moduleid":([0-9]*)/', json_encode($data), $matches);

		// foreach ($data as $row) 
		// {
		// 	if ($row['type'] == 'row')
		// 	{
		// 		foreach ($row['columns'] as $column) 
		// 		{
		// 			foreach ($column['data'] as $cdata) 
		// 			{
		// 				if ($cdata['type'] == 'addon')
		// 				{
		// 					if ($cdata['name'] == 'module')
		// 					{
		// 						$modules[] = $cdata['params']['moduleid'];
		// 					}
		// 					elseif ($cdata['name'] == 'tabs')
		// 					{
		// 						foreach ($cdata['params']['items'] as $item) 
		// 							$modules[] = $item['moduleid'];
		// 					}
		// 				}
		// 				elseif ($cdata['type'] == 'row')
		// 				{
		// 					$this->getModuleIds([$cdata], $modules);
		// 				}
		// 			}
		// 		}
		// 	}
		// }

		return $matches[1];
	}
}