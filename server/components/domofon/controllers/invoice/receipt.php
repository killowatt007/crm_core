<?php
namespace bs\components\domofon\controllers\invoice;
defined('EXE') or die('Access');

use \bs\libraries\mvc\Controller;

class Receipt extends Controller
{
  public function printInvoice()
  {
    $input = $this->app->input;
    $dbo = \F::getDBO();

    $date_from = $input->get('date_from');
    $date_to = $input->get('date_to');
    $clientids = $input->get('clientids');
    $contractids = $input->get('contractids');

    $time = time();
    setcookie('contracts[date_from]', $date_from, $time+(86400*30), '/', $_SERVER['HTTP_HOST']);
    setcookie('contracts[date_to]', $date_to, $time+(86400*30), '/', $_SERVER['HTTP_HOST']);

    $receipt = $this->app->getComponent('domofon', 'receipt', 'invoice')->getModel();

    if ($clientids)
      $receipt->clientids = $clientids;
    elseif ($contractids)
      $receipt->contractids = $contractids;

    $receipt->date_from = $date_from;
    $receipt->date_to = $date_to;
    $receipt->getReceipts();
    
    exit;
  }

  public function createArch() {
    $input = $this->app->input;
    $dbo = \F::getDBO();

    $data = [];
    $flag = $input->get('flag');
    $date_from = $input->get('date_from');
    $date_to = $input->get('date_to');
    $district = $input->get('district');

    $path_files = __DIR__.'/files';

    // getContracts
    if ($flag == 'getContracts') {
      $time = time();
      setcookie('contracts[date_from]', $date_from, $time+(86400*30), '/', $_SERVER['HTTP_HOST']);
      setcookie('contracts[date_to]', $date_to, $time+(86400*30), '/', $_SERVER['HTTP_HOST']);

      $data['contractIds'] = $this->getContractIds($district);
    }

    // createInvoice
    elseif ($flag == 'createInvoice') {
      $contractId = $input->get('contractId');
      $path_pdf = "$path_files/$contractId.pdf";

      $receipt = $this->app->getComponent('domofon', 'receipt', 'invoice')->getModel();
      $receipt->contractids = [$contractId];
      $receipt->date_from = $date_from;
      $receipt->date_to = $date_to;
      $receipt->output_type = 'F';
      $receipt->output_file = $path_pdf;
      $receipt->getReceipts();

      $data['size'] = $receipt->size;
    }

    // createArch
    elseif ($flag == 'createArch') {
      $size = $input->get('size');

      $districtData = \F::getDbo()->setQuery("SELECT Name FROM &__districts WHERE id=$district")->loadAssoc();
      $contractIds = $this->getContractIds($district);

      $date = new \DateTimeImmutable($date_from);
      $datef = $date->format('dmY');
      $date = new \DateTimeImmutable($date_to);
      $datet = $date->format('dmY');
      $dis_name = strtolower($this->convert_text_to_url($this->translit($districtData['Name'])));

      $receipt = $this->app->getComponent('domofon', 'receipt', 'invoice')->getModel();
      $mpdf = $receipt->getMpdfObject();
      $path_all = "$path_files/all.pdf";

      foreach ($contractIds as $cid) {
        $path_pdf = "$path_files/$cid.pdf";

        if (file_exists($path_pdf)) {
          $pagecount = $mpdf->setSourceFile($path_pdf);
    
          for ($i=0; $i<$pagecount; $i++) {
            $mpdf->AddPage();
            $tplId = $mpdf->importPage(($i+1));
            $mpdf->useTemplate($tplId);
          }

          unlink($path_pdf);
        }
      }

      $mpdf->Output($path_all, 'F');

      $name = "receipts_$datef-{$datet}_{$dis_name}_{$size}";
      $zip_name = "$name.zip";
      $zip_path = __DIR__.'/'.$zip_name;

      $zip = new \ZipArchive();
      $zip->open($zip_path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
      $zip->addFile($path_all, "$name.pdf");
      $zip->close();

      unlink($path_all);

      $data['zip_name'] = $zip_name;
    }

    // download
    elseif ($flag == 'download') {
      $zip_name = $input->get('zip_name');
      $zip_path = __DIR__.'/'.$zip_name;

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

    return $data;
  }

  private function getContractIds($district) {
    $dbo = \F::getDBO();

    $contracts = $dbo
      ->setQuery("
        SELECT 
          t0.id,
          t0.DistrictId
        FROM &__contracts t0
          LEFT JOIN &__streets t2 ON (t2.id=t0.StreetId)
        WHERE t0.DistrictId=$district
        ORDER BY 
          t2.Name,
          LENGTH(t0.HouseNumber), t0.HouseNumber,
          LENGTH(t0.BuildingNumber), t0.BuildingNumber,
          LENGTH(t0.EntranceNumber), t0.EntranceNumber
      ")
      ->loadAssocList();

    $cids = [];
    foreach ($contracts as $row)
      $cids[] = $row['id'];

    return $cids;
  }

  private function convert_text_to_url($text) {
    $specChars = [
        '!' => '',    '"' => '',    '{' => '',
        '#' => '',    '$' => '',    '%' => '',
        '&' => '',    '\'' => '',   '(' => '',
        ')' => '',    '*' => '',    '+' => '',
        ',' => '',    '~' => '',    '.' => '',
        '/' => '',    ':' => '',    ';' => '',
        '<' => '',    '=' => '',    '>' => '',
        '?' => '',    '@' => '',    '[' => '',
        '\\' => '',   ']' => '',    '^' => '',
        '`' => '',    '|' => '',    '}' => '',
        ' ' => '-'
    ];
    $text = str_replace(array_keys($specChars), array_values($specChars), $text);
    return $text;
  }

  private function translit($value)
  {
    $converter = array(
      'а' => 'a',    'б' => 'b',    'в' => 'v',    'г' => 'g',    'д' => 'd',
      'е' => 'e',    'ё' => 'e',    'ж' => 'zh',   'з' => 'z',    'и' => 'i',
      'й' => 'y',    'к' => 'k',    'л' => 'l',    'м' => 'm',    'н' => 'n',
      'о' => 'o',    'п' => 'p',    'р' => 'r',    'с' => 's',    'т' => 't',
      'у' => 'u',    'ф' => 'f',    'х' => 'h',    'ц' => 'c',    'ч' => 'ch',
      'ш' => 'sh',   'щ' => 'sch',  'ь' => '',     'ы' => 'y',    'ъ' => '',
      'э' => 'e',    'ю' => 'yu',   'я' => 'ya',
   
      'А' => 'A',    'Б' => 'B',    'В' => 'V',    'Г' => 'G',    'Д' => 'D',
      'Е' => 'E',    'Ё' => 'E',    'Ж' => 'Zh',   'З' => 'Z',    'И' => 'I',
      'Й' => 'Y',    'К' => 'K',    'Л' => 'L',    'М' => 'M',    'Н' => 'N',
      'О' => 'O',    'П' => 'P',    'Р' => 'R',    'С' => 'S',    'Т' => 'T',
      'У' => 'U',    'Ф' => 'F',    'Х' => 'H',    'Ц' => 'C',    'Ч' => 'Ch',
      'Ш' => 'Sh',   'Щ' => 'Sch',  'Ь' => '',     'Ы' => 'Y',    'Ъ' => '',
      'Э' => 'E',    'Ю' => 'Yu',   'Я' => 'Ya',
    );
   
    $value = strtr($value, $converter);
    return $value;
  }
}