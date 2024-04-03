<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

include_once PATH_ROOT .'/components/fabrik/event/pluginForm.php';
use \bs\components\fabrik\event\PluginForm;

class FormInventory_items extends PluginForm
{
	public function onBeforeStore()
	{
		$model = $this->getModel();
		$alias = $model->getModuleAlias();

    if ($alias)
    {
      if ($alias == 'demount')
        $this->updFD('IsDemount', 1);

      if ($model->isNewRecord())
      {
        // if isset
        $itemid = $this->getCV('ItemId');
        $where = 'ItemId='.$itemid.' AND IsDemount='.($alias == 'demount' ? 1 : 0);
        $isset = $this->getCtrl()->select('id, Quantity', $where)[0] ?? null;

        if ($isset)
        {
          $this->updFD('Quantity', $this->getCV('Quantity') + $isset['Quantity']);
          $this->getModel()->_setRowId($isset['id']);
        }
      }
    }
	}

  public function onElementDefault($el, $i)
  {
    $filter = $this->app->getService('fabrik', 'helper')->getFilter();

    if ($el->getName() == 'StaffId')
    {
      $staffid = $filter->getFieldValue('staff');
      $el->setDefault($staffid);
    }

    if ($el->getName() == 'CategoryId')
    {
      $categoryid = $filter->getFieldValue('category');
      $el->setDefault($categoryid);
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
    
		// // ItemId
    // if ($el->getName() == 'ItemId')
    // {
    //   if (!empty($args->options))
    //   {
		// 		$itemIds = [];

		// 		foreach ($args->options as $item)
		// 			$itemIds[] = $item['label'];

		// 		$sItems = \F::getDBO()
		// 			->setQuery('
		// 				SELECT id, Name 
		// 				FROM &__sklad_catalog_items
		// 				WHERE id IN ('.implode(',', $itemIds).')
		// 			')
		// 			->loadAssocList('id');

		// 		foreach ($args->options as &$option) 
		// 		{
		// 			$id = $option['label'];
		// 			$option['label'] = $sItems[$id]['Name'];
		// 		}
    //   }
    // }

		// CategoryId
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