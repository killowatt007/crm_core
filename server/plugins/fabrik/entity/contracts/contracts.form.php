<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

/**
 * $version 1.1
 */

include_once PATH_ROOT .'/components/fabrik/event/pluginForm.php';
use \bs\components\fabrik\event\PluginForm;

class FormContracts extends PluginForm
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

  public function onBeforeProcess()
  {
    $model = $this->getModel();

    if ($validateId = $this->getCtrl()->validateId($this->getCV('id'), $model->getRowId()))
      $model->validation[] = $validateId;
  }

  public static function onAjaxCheckId()
  {
    $app = \F::getApp();
    $value = $app->input->get('value');
    $selfid = $app->input->get('selfid');

    $isset = (bool)$app->getCtrl('fabrik', 'contracts')->validateId($value, $selfid);

    return ['isset'=>$isset];
  }

  public static function onAjaxGetClients()
  {
    $dbo = \F::getDBO();
    $app = \F::getApp();
    $contractid = $app->input->get('contractid');

    // клиенты
    $clients = $dbo
      ->setQuery('
        SELECT t0.id, t0.FIO, t0.Phone, t0.Mobile, t0.FlatNumber, t0.StatusId, t1.Label AS StatusId_j
        FROM &__clients t0
          LEFT JOIN &__status t1 ON (t1.id=t0.StatusId)
        WHERE t0.ContractId='.$contractid.'
        ORDER BY t0.id ASC'
      )
      ->loadAssocList();

    $cids = [];
    foreach ($clients as $row)
      $cids[] = $row['id'];

    // начисление
    $inv = $dbo->setQuery(
      'SELECT ClientId, Amount
       FROM &__invoice_items
       WHERE ClientId IN ('.implode(',', $cids).') AND Published=1'
    )->loadAssocList();

    $invGroup = [];
    foreach ($inv as $row) 
    {
      $cid = $row['ClientId'];
      if (!isset($invGroup[$cid]))
        $invGroup[$cid] = [];
      $invGroup[$cid][] = $row;
    }

    // платежи
    $pay = $dbo->setQuery(
      'SELECT ClientId, Amount
       FROM &__payment_items
       WHERE ClientId IN ('.implode(',', $cids).') AND (Type="sell" OR Type="")'
    )->loadAssocList();

    $payGroup = [];
    foreach ($pay as $row) 
    {
      $cid = $row['ClientId'];
      if (!isset($payGroup[$cid]))
        $payGroup[$cid] = [];
      $payGroup[$cid][] = $row;
    }

    foreach ($clients as &$row) 
    {
      $cid = $row['id'];
      $inv = $invGroup[$cid] ?? [];
      $pay = $payGroup[$cid] ?? [];

      $invSumm = 0;
      $paySumm = 0;
      foreach ($inv as $row2)
        $invSumm += $row2['Amount'];
      foreach ($pay as $row2)
        $paySumm += $row2['Amount'];

      $row['balance'] = number_format($paySumm - $invSumm, 2, ',', ' ');
    }
    
    return ['clients'=>$clients];
  }
}