<?php
namespace bs\components\domofon\controllers\clients;
defined('EXE') or die('Access');

use \bs\libraries\mvc\Controller;

class Invalid_debt extends Controller
{
  public function invalid()
  {
    $dbo = \F::getDBO();
    $return = [
      'debts' => []
    ];

    $slog = $this->app->getCtrl('fabrik', 'super_logs');
    $logid = $slog->init('quarter.invalid_debt');

    $baseid = (int)$this->app->input->get('baseid');

    $st_approve = 2;
    $st_invalid = 6;

    $where  = 't0.StatusId='.$st_approve;
    $where .= $baseid ? ' AND t0.BaseId='.$baseid : '';

    $clients = $dbo->setQuery('
      SELECT t0.id, t0.FIO, t1.IntValue AS Rate
      FROM &__clients t0
        LEFT JOIN &__pick_items t1 ON (t1.id=t0.RateId)
        LEFT JOIN &__contracts t2 ON (t2.id=t0.ContractId)
      WHERE '.$where.'
    ')->loadAssocList();

    $cids = [];
    foreach ($clients as $row)
      $cids[] = $row['id'];

    // начисление
    $inv = $dbo->setQuery(
      'SELECT ClientId, Amount
       FROM &__invoice_items
       WHERE ClientId IN ('.implode(',', $cids).')'
    )->loadAssocList();

    $invGroup = [];
    foreach ($inv as $row) 
    {
      $cid = $row['ClientId'];
      if (!isset($invGroup[$cid]))
        $invGroup[$cid] = [];
      $invGroup[$cid][] = $row;
    }
    unset($inv);

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
    unset($pay);

    $debts = [];
    foreach ($clients as $client) 
    {
      $cid = $client['id'];
      $rate = (int)$client['Rate'];

      $inv = 0;
      $pay = 0;

      if (isset($invGroup[$cid]))
      {
        foreach ($invGroup[$cid] as $row) 
          $inv += $row['Amount'];
      }

      if (isset($payGroup[$cid]))
      {
        foreach ($payGroup[$cid] as $row) 
          $pay += $row['Amount'];
      }

      if ($rate)
      {
        $left = $inv - $pay;
        $rate5 = $rate * 6;

        if ($left > $rate5)
        {
          $debts[] = [
            'id' => $cid,
            'left' => $left,
            'FIO' => $client['FIO']
          ];
        }
      }
    }

    if (!empty($debts))
    {
      $ids = [];
      foreach ($debts as $debt) 
      {
        $ids[] = $debt['id'];

        $return['debts'][] = [
          'label' => $debt['FIO'].' ('.$debt['id'].') - '.$debt['left']
        ];

        // slog
        $slog->add($logid, [
          'clientid' => $debt['id'],
          'left' => $debt['left']
        ]);
      } 

      $dbo
        ->setQuery('UPDATE &__clients SET StatusId='.$st_invalid.', DateQuarterDebt='.$dbo->q(date('Y-m-d H:i:s')).' WHERE id IN ('.implode(',', $ids).')')
        ->execute();
    }

    return $return;
  }
}