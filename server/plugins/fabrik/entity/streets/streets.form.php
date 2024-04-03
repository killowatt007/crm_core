<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

include_once PATH_ROOT .'/components/fabrik/event/pluginForm.php';
use \bs\components\fabrik\event\PluginForm;

class FormStreets extends PluginForm
{
  public function onBeforeStore()
  {
    $model = $this->getModel();

    if ($model->isNewRecord())
    {
      $pickCtrl = $this->getCtrl('pick_items');
      $base =  $pickCtrl->getActiveBase();

      $this->updFD('BaseId', $base);
    }
  }
}