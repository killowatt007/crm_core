<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

include_once PATH_ROOT .'/components/fabrik/event/pluginForm.php';
use \bs\components\fabrik\event\PluginForm;

class FormEquipment_category extends PluginForm
{
  public function onContrAfterProcess($args)
  {
    $model = $this->getModel();
    $rowid = $model->isNewRecord() ? $args->data['rowId'] : $model->getRowId();

    $args->data['isnewrecord'] = $model->isNewRecord();
    $args->data['data'] = $this->getCtrl()->select('*', $rowid);
  }
   
  public function onElementValue($el, $i)
  {
    $model = $this->getModel();
    
    if ($el->getName() == 'CategoryId')
    {
      if ($model->isNewRecord())
      {
        $categoryId = $this->app->input->get('stream.tree_catalog.active_category_id');
        $el->setValue($categoryId);
      }
    }
  }

  public function onElementDatabasejoinBeforeQuery($el, $args)
  {
    $model = $this->getModel();
    
    if ($el->getName() == 'CategoryId')
    {
      $args->select .= ', t0.CategoryId';
    }
  }

  public function onElementDatabasejoinAfterQuery($el, $args)
  {
    $model = $this->getModel();
    
    if ($el->getName() == 'CategoryId')
    {
      if (!empty($args->options))
      {
        $options = array_values(\F::getHelper('arr')->sort($args->options, 'value', 'CategoryId', false));

        foreach ($options as &$row) 
        {
          $space = '';
          for ($i=0; $i < $row['lvl']; $i++) 
            $space .= $i ? '––' : '';
  
          $row['label'] = $space.' '.$row['label'];
        }
  
        $args->options = $options;
      }
    }
  }
}