<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

use \bs\components\fabrik\Ctrl;

class CtrlEquipment_items extends Ctrl
{
  public function getGroupItems()
  {
    $group = [];

  	$category = $this->dbo->setQuery('SELECT id, Name, CategoryId FROM &__equipment_category')->loadAssocList();
  	$items = $this->dbo->setQuery(
  		'SELECT 
        t0.id,
        t0.CategoryId,
        t0.Name
  		 FROM &__equipment_items t0
      '
  	)->loadAssocList();
    

    $group = \F::getHelper('arr')->sort($category, 'id', 'CategoryId', false);

    foreach ($group as &$category) 
    {
      $category['items'] = [];

      foreach ($items as $item) 
      {
        if ($item['CategoryId'] == $category['id'])
          $category['items'][] = $item;
      }
    }
    
    return array_values($group);
  }

  public function getPrice($id)
  {
    $price = 0;
    $row = $this->select('Price', $id);

    if ($row)
    {
      $price = $row['Price'];
    }
    
    return $price;
  }

  // public function chengeQuantity($id, $quantity, $reqId)
  // {
  //   $row = $this->select('ItemId', $id);

  //   if ($row)
  //   {
  //     $catItemCtrl = $this->app->getCtrl('fabrik', 'sklad_catalog_items');
  //     $catItem = $catItemCtrl->select('id, Quantity', $row['ItemId']);

  //     if ($catItem)
  //     {
  //       $newQ = $catItem['Quantity'] - $quantity;

  //       $catItemCtrl->store([
  //         'Quantity' => $newQ
  //       ], $catItem['id']);

  //       // log
  //       $slog = $this->app->getCtrl('fabrik', 'super_logs');
  //       $logid = $slog->init('sklad.chenge_quantity.master');

  //       $slog->add($logid, [
  //         'oldQ' => $catItem['Quantity'],
  //         'newQ' => $newQ,
  //         'reqId' => $reqId
  //       ]);
  //     }
  //   }
  // }
}



