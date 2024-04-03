<?php
namespace bs\components\module\controllers\fabrik;
defined('EXE') or die('Access');

use \bs\libraries\mvc\Controller;

class History extends Controller
{
	public function getModule()
	{
		$dbo = \F::getDBO();
		$data = [];
		$moduleid = $this->app->input->get('moduleid');

		$moduleData = $this->app->getCtrl('fabrik', 'module')->select('Params', $moduleid);
		$moduleParams = json_decode($moduleData['Params'], true);
		
		$entityid = $moduleParams['entityid'];
		$this->app->getCtrl('fabrik', $entityid);

		$listComp = $this->app->getComponent('fabrik', 'list');
		$model = $listComp->initModel($entityid, $moduleid);
		$view = $listComp->getView();

		$tn = $model->getTable()->getData()['Name'];
		$query = $model->buildQuery();

		$whrBase = '';
		if ($model->getElement('BaseId'))
			$whrBase = 't0.BaseId='.$this->app->getCtrl('fabrik', 'pick_items')->getActiveBase();

    $rows = $dbo
			->setQuery('
				('.$query['select'].', t0.id AS s, "" AS HRowId, "" AS HIsDelete, "" AS HDateCreate FROM &__'.$tn.' t0 '.$query['join'] . ($whrBase ? ' WHERE '.$whrBase : '').')
				UNION
				('.$query['select'].', t0.HRowId AS s, HRowId, HIsDelete, HDateCreate FROM &__his_'.$tn.' t0 '.$query['join'].' WHERE HIsDelete=1'.($whrBase ? ' AND '.$whrBase : '').')
				ORDER BY s DESC
				LIMIT 0, 10
			')
			->loadAssocList();

		$model->setData($rows);
		$data['module']['rows'] = $view->getData();

		return $data;
	}
}