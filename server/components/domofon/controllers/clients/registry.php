<?php
namespace bs\components\domofon\controllers\clients;
defined('EXE') or die('Access');

use \bs\libraries\mvc\Controller;

class Registry extends Controller
{
	public function downloadForRnkb()
	{
    $files_path = $this->createFilesTxtRnkb();
    
    $period = date('my');
    $name = "register_for_rnkb_$period";
    $zip_name = "$name.zip";
    $zip_path = __DIR__.'/'.$zip_name;

    $zip = new \ZipArchive();
    $zip->open($zip_path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

    foreach ($files_path as $file_path) {
      $zip->addFile($file_path, basename($file_path));
    }

    $zip->close();

    foreach ($files_path as $file_path) {
      unlink($file_path);  
    }

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . $zip_name);
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($zip_path));
    readfile($zip_path);
    unlink($zip_path);  

    exit;
  }

  private function createFilesTxtRnkb() {
    $dbo = \F::getDBO();

    $clients = $dbo
      ->setQuery('
        SELECT 
          t0.id,
          t0.FIO,
          t0.FlatNumber,
          t0.BaseId,
          t1.HouseNumber,
          t1.BuildingNumber,
          t1.EntranceNumber,
          t4.Name AS District,
          t5.Name AS Street
        FROM &__clients t0
          LEFT JOIN &__contracts t1 ON (t1.id=t0.ContractId)
            LEFT JOIN &__districts t4 ON (t4.id=t1.DistrictId)
            LEFT JOIN &__streets t5 ON (t5.id=t1.StreetId)
      ')
      ->loadAssocList();

    // начисление
    $inv = $dbo->setQuery('
        SELECT ClientId, Amount
        FROM &__invoice_items
    ')->loadAssocList();

    $invGroup = [];
    foreach ($inv as $row) 
    {
      $cid = $row['ClientId'];
      if (!isset($invGroup[$cid]))
        $invGroup[$cid] = [];
      $invGroup[$cid][] = $row['Amount'];
    }
    unset($inv);

    // платежи
    $pay = $dbo->setQuery('
        SELECT ClientId, Amount
        FROM &__payment_items
        WHERE (Type="sell" OR Type="")
    ')->loadAssocList();

    $payGroup = [];
    foreach ($pay as $row) 
    {
      $cid = $row['ClientId'];
      if (!isset($payGroup[$cid]))
        $payGroup[$cid] = [];
      $payGroup[$cid][] = $row['Amount'];
    }
    unset($pay);

    $inn = '920353654830';
    $date = date('my');

    $clientsGroup = [
      47 => [ // simf
        'clients' => [],
        'id' => '11174418',
        'rs' => '40802810312280023699'
      ],
      48 => [ // sevas
        'clients' => [],
        'id' => '11174419',
        'rs' =>  '40802810112280003699'
      ]
    ];
    foreach ($clients as $row) {
      $base = $row['BaseId'];
      $clientsGroup[$base]['clients'][] = $row;
    }
    unset($clients);

    $files_path = [
      47 => '',
      48 => ''
    ];
    foreach ($clientsGroup as $baseid => $group) {
      $rs = $group['rs'];
      $id = $group['id'];

      $filename = "{$inn}_{$rs}_{$id}_{$date}.txt";
      $files_path[$baseid] = __DIR__.'/'.$filename;
  
      $myfile = fopen($files_path[$baseid], 'w');
      $period = date('my');
  
      foreach ($group['clients'] as $row) 
      {
        $cid = $row['id'];
        $inv = $invGroup[$cid] ?? [];
        $pay = $payGroup[$cid] ?? [];
  
        $invSumm = 0;
        $paySumm = 0;
        foreach ($inv as $amount)
          $invSumm += $amount;
        foreach ($pay as $amount)
          $paySumm += $amount;
  
        $fio = str_replace(';', '', $row['FIO']);
        $balance = number_format($invSumm - $paySumm, 2, ',', '');
        $lic = str_pad((int)$cid, 6, 0, STR_PAD_LEFT);
  
        $address  = $row['District'];
        $address .= ', '.$row['Street'];
        $address .= ($row['HouseNumber'] ? ' '.$row['HouseNumber'] : '');
        $address .= ($row['BuildingNumber'] ? ', кор '.$row['BuildingNumber'] : '');
        $address .= ', под '.$row['EntranceNumber'];
        $address .= ', кв '.$row['FlatNumber'];
        $address  = str_replace(';', ',', $address);
  
        $text = "$lic;;;$fio;$address;$period;$balance\n";
        fwrite($myfile, $text);
      }
  
      fclose($myfile);
  
      $f = file_get_contents($files_path[$baseid]);
      $f = iconv("UTF-8", "WINDOWS-1251", $f);
      file_put_contents($files_path[$baseid], $f);
    }

    return $files_path;


    




    // header('Content-Description: File Transfer');
    // header('Content-Type: application/octet-stream');
    // header('Content-Disposition: attachment; filename=' . $filename);
    // header('Content-Transfer-Encoding: binary');
    // header('Expires: 0');
    // header('Cache-Control: must-revalidate');
    // header('Pragma: public');
    // header('Content-Length: ' . filesize($file_path));
    // readfile($file_path);
    // unlink($file_path);  
  }
}