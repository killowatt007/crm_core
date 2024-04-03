<?php
namespace bs\plugins\fabrik\statical;
defined('EXE') or die('Access');

include_once PATH_ROOT .'/components/fabrik/event/pluginList.php';
use \bs\components\fabrik\event\PluginList;

class ListHistory extends PluginList
{
  public function obGetActions($view, $args)
  {
    $model = $this->getModel();

    if ($model->getModuleAlias() == 'history')
    {
      $args->data['actions']['history'] = [
        'name' => 'history',
        'icon' => 'fal fa-list-alt'
      ];

      // $args->data['actions']['isdelete'] = [
      //   'name' => 'isdelete',
      //   'icon' => 'far fa-circle'
      // ];
    }
  }

  static public function onAjaxGetHistoryItems()
  {
    $data = [];

    $app = \F::getApp();
    $dbo = \F::getDBO();
    $rowid = $app->input->get('rowid');
    $moduleid = $app->input->get('moduleid');

    $moduleData = $app->getCtrl('fabrik', 'module')->select('Params', $moduleid);
    $moduleParams = json_decode($moduleData['Params'], true);
    
    $entityid = $moduleParams['entityid'];
    $app->getCtrl('fabrik', $entityid);

    $listComp = $app->getComponent('fabrik', 'list');
    $model = $listComp->initModel($entityid, $moduleid);
    $view = $listComp->getView();

    $tn = $model->getTableName();
    $model->setTableName('his_'.$tn);
    $model->setWhere('t0.HIsDelete=0 AND t0.HRowId='.$rowid);
    $model->buildQuery();

    foreach ($model->getElements() as $element) 
    {
      if ($element->getType() == 'databasejoin')
      {
        $elname = $element->getName();
        $model->query['select'] .= ', '.$elname.'_j';
      }
    }

    $fgroupAndRows = $model->getFgroupAndRows();

    $data['headers'] = $model->getHeaders();
    $data['rows'] = $fgroupAndRows['rows'];
    $data['fgroup'] = $fgroupAndRows['fgroup'];

    return $data;
  }
}


