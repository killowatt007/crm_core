<?php
namespace bs\plugins\fabrik\statical;
defined('EXE') or die('Access');

class CascadeData
{
	static public function cascadeData($plg, $blockType)
	{
		// $app = \F::getApp();
		// $model = $plg->getModel();
		// $moduleId = $model->getModuleId();

		// if ($app->input->get('option') == 'fabrik' and $moduleId and $app->input->get('moduleid') == $moduleId)
		// {
		// 	$hCascade = $app->getService('fabrik', 'cascade');

		// 	// form
		// 	if ($blockType == 'form')
		// 	{
		// 		$filterId = $model->getParam('rowId.moduleId');
		// 	}
		// 	// list
		// 	elseif ($blockType == 'list')
		// 	{
		// 		foreach ($model->getParam('filter') as $filter) 
		// 		{
		// 			if ($filter['value']['type'] == 'fabrikFilter')
		// 			{
		// 				$filterId = $filter['value']['moduleId'];
		// 				break;
		// 			}
		// 		}
		// 	}

		// 	$relatedModules = $hCascade->getRelatedModules($filterId, 'fabrikFilter', true);

		// 	foreach ($relatedModules as $rModule) 
		// 	{
		// 		if ($rModule['id'] != $moduleId)
		// 			$app->setUpdateModule($rModule['id']);
		// 	}
		// }
	}
}



