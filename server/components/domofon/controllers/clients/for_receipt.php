<?php
namespace bs\components\domofon\controllers\clients;
defined('EXE') or die('Access');

use \bs\libraries\mvc\Controller;

class For_receipt extends Controller
{
  public function find()
  {
    $input = $this->app->input;
    $dbo = \F::getDBO();

    $ctids = $input->get('contractids');
    $isplus_balance = $input->get('isplus_balance');
    $debt_date = $input->get('debt_date');
    $date_from = $input->get('date_from');
    $date_to = $input->get('date_to');

    $time = time();
    setcookie('contracts[debt_date]', $debt_date, $time+(86400*30), '/', $_SERVER['HTTP_HOST']);

    $clients = $dbo->setQuery('
      SELECT 
        t0.id,
        t0.FIO,
        t0.StatusId,
        t0.ContractId,
        t1.HouseNumber,
        t1.BuildingNumber,
        t1.EntranceNumber,
        t2.Name AS Street,
        t3.Name AS District
      FROM &__clients t0
        LEFT JOIN &__contracts t1 ON (t1.id=t0.ContractId)
          LEFT JOIN &__streets t2 ON (t2.id=t1.StreetId)
          LEFT JOIN &__districts t3 ON (t3.id=t1.DistrictId)
      WHERE t0.ContractId IN ('.implode(',', $ctids).') AND t0.StatusId IN (2,6)
    ')->loadAssocList();

    $cids = [];
    foreach ($clients as $key => $row)
      $cids[] = $row['id'];

    $receipt = $this->app->getComponent('domofon', 'receipt', 'invoice')->getModel();
    $receipt->clientids = $cids;
		$receipt->date_from = $date_from;
		$receipt->date_to = $date_to;

    $amounts = $receipt->getAmounts($cids, $date_from, $date_to);

    $clients_group = [];
    foreach ($clients as $row) 
    {
      $ctid = $row['ContractId'];

      if (!isset($clients_group[$ctid]))
        $clients_group[$ctid] = [];

      $clients_group[$ctid][] = $row;
    }
    unset($clients);

    $clientsData = [];
    foreach ($clients_group as $ctid => &$clients) 
    {
      $address  = $clients[0]['District'];
      $address .= ', '.$clients[0]['Street'];
      $address .= ($clients[0]['HouseNumber'] ? ' '.$clients[0]['HouseNumber'] : '');
      $address .= ($clients[0]['BuildingNumber'] ? ', кор '.$clients[0]['BuildingNumber'] : '');
      $address .= ', под '.$clients[0]['EntranceNumber'];

      $clientsPos = [
        'address' => $address,
        'contractid' => $ctid,
        'valid' => [],
        'notvalid' => []
      ];

      foreach ($clients as &$client) 
      {
        $cid = $client['id'];
        $amount = $amounts[$cid] ?? [];

        $c_invs_sum = $amount['c_invs']['sum'] ?? 0;
        $a_invs_sum = $amount['a_invs']['sum'] ?? 0;
        $a_pays_sum = $amount['a_pays']['sum'] ?? 0;

        $invs_sum = $c_invs_sum+$a_invs_sum;
        $pays_sum = $a_pays_sum;

        $last_inv_date = $amount['last_inv']['DateInvoice'] ?? '';
        $last_pay_date = $amount['last_pay']['DatePay'] ?? '';

        // $client['debt_s'] = $invs_sum - $pays_sum;
        // $client['quart_s'] = $c_invs_sum;
        // $client['left_s'] = $c_invs_sum + $client['debt_s'];

        $client['balance'] = $pays_sum - $invs_sum;
        $client['last_pay_date'] = $last_pay_date ? \DateTime::createFromFormat('Y-m-d H:i:s', $last_pay_date)->format('d.m.Y') : '—';

        $position = 'valid';

        if (!$last_pay_date and ($debt_date and $last_inv_date) and (strtotime($last_inv_date) <= strtotime($debt_date)))
          $position = 'notvalid';

        if (!$isplus_balance and $client['balance'] >= 0)
          $position = 'notvalid';

        if ((int)$client['StatusId'] != 2 and $debt_date and $last_pay_date)
        {
          if (strtotime($last_pay_date) <= strtotime($debt_date))
            $position = 'notvalid';
        }

        $clientsPos[$position][] = $client;
      }

      usort($clientsPos['notvalid'], function($a, $b) 
        {
          if ($a['balance'] == $b['balance']) {
            return 0;
          }
          return ($a['balance'] > $b['balance']) ? 1 : -1;
        });

      $clientsData[] = $clientsPos;
    }

    return ['clientsData'=>$clientsData];
  }
}