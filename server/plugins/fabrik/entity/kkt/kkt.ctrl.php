<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

use \bs\components\fabrik\Ctrl;

class CtrlKkt extends Ctrl
{
  private $timeout = 30;
  public $last_uuid = null;
  public $error = null;

  public $err_code = [
    '401'       => 'Авторизация не пройдена',
    '403'       => 'ККТ не активирована',
    '404post'   => 'ККТ по заданному идентификатору не найдена или ККТ по умолчанию не выбрана',
    '404get'    => 'ККТ по заданному идентификатору не найдена или ККТ по умолчанию не выбрана или задание с указанным UUID не найдено',
    '408'       => 'За 30 секунд не удалось захватить управление драйвером (занят фоновыми непрерываемыми задачами). Повторите запрос позже.',
    '409'       => 'Задание с таким же uuid уже существует',
    '420'       => 'Произошла ошибка во время проверки формата задания',

    'expired'   => 'Смена истекла',
    'opened'    => 'Смена уже открыта',
    'closed'    => 'Смена уже закрыта',

    'notPrinted' => 'Закончилась бумага'
  ];

  private function getActiveData()
  {
    return $this->select('*', 'IsCurrent=1')[0];
  }

  private function code_text($code, $method)
  {
    $key = $code.($code == '404' ? $method : '');
    return $code.'. '.($this->err_code[$key] ?? 'нет соединения');
  }

  private function CallAPI($method, $data, $_url = '/requests') 
  {
    $cd = $this->getActiveData();

    $headers = ['Content-Type: application/json'];
    $url = 'http://'.$cd['Host'].':'.$cd['Port'].$_url; // 127.0.0.1::16732

    $curl = curl_init($url);

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

    $resp = json_decode(curl_exec($curl), true);
    $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    return  [$code, $resp];
  }

  // получает результат задания
  public function get_res($uuid)
  {
    $ready = false;
    $cnt = 0;
    $res_url = '/requests/'.$uuid;

    while (!$ready && ++$cnt<30) 
    {
      usleep(500000); // подождем чуть, прежде чем просить ответ

      list($code, $resp) = $this->CallAPI('GET', [], $res_url);

      if ($code == '200')
      {
        $ready = ($resp['results'][0]['status'] == 'ready');

        if ($resp['results'][0]['status'] == 'ready') 
        {
          return $resp;
        }
        elseif ($resp['results'][0]['status'] == 'inProgress')
        {

        }
        else
        {
          $this->error = $resp['results'][0]['errorDescription'];
          return;
        }
      }
      else
      {
        $this->error = $this->code_text($code, 'get');
        return;
      }
    }

    $this->error = 'Не удалось получить результата';
  }

  // генерирует уникальный id для задания
  private function gen_uuid()
  {
    $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
      // 32 bits for "time_low"
      mt_rand(0, 0xffff), mt_rand(0, 0xffff),

      // 16 bits for "time_mid"
      mt_rand(0, 0xffff),

      // 16 bits for "time_hi_and_version",
      // four most significant bits holds version number 4
      mt_rand(0, 0x0fff) | 0x4000,

      // 16 bits, 8 bits for "clk_seq_hi_res",
      // 8 bits for "clk_seq_low",
      // two most significant bits holds zero and one for variant DCE1.1
      mt_rand(0, 0x3fff) | 0x8000,

      // 48 bits for "node"
      mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
    
    return $uuid;
  }

  // выполняет задание и возвращает его результат
  private function atol_task($type, $req = [], $callback = null)
  {
    $req['type'] = $type;
    $uuid =  $this->gen_uuid();
    $this->last_uuid = $uuid;
    $data = ['uuid'=>$uuid, 'request'=>$req];

    if ($callback)
      $data['callbacks']['resultUrl'] = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/kkt_task_ready.php';

    list($code, $resp) = $this->CallAPI('POST', $data);

    if ($code == '201') 
    {
      if (!$callback)
        return $this->get_res($uuid);
    }
    else
      $this->error = $this->code_text($code, 'post');
  }

  private function items_prepare($items)
  {
    $res_items = [];
    $summ = 0;

    while ($item = array_shift($items))
    {
      $res_item = $item;

      if (!isset($item['type'])) 
        $res_item['type'] = 'position';

      if (isset($item['price']) and isset($item['quantity']))
      {
        $res_item['amount'] = $item['price']*$item['quantity'];
        $res_item['tax'] = ['type'=>'none'];
        $res_item['paymentObject'] = 'service';
        
        $summ += $res_item['amount'];
      }

      $res_items[] = $res_item;
    }

    return [$res_items, $summ];
  }



  // статус смены
  public function get_shift_status()
  {
    $res = null;
    $resp = $this->atol_task('getShiftStatus');

    if ($resp)
      $res = $resp['results'][0]['result']['shiftStatus'];

    return $res;
  }

  // открытие смены
  public function open_shift()
  {
    $res = null;
    $cd = $this->getActiveData();
    $shift = $this->get_shift_status();

    if (!$this->error)
    {
      if ($shift['state'] == 'expired') 
        $this->error = $this->err_code['expired'];

      if ($shift['state'] == 'opened') 
        $this->error = $this->err_code['opened'];

      if (!$this->error)
      {
        $resp = $this->atol_task('openShift', ['operator' => ['name' => $cd['Operator']], 'electronically' => true]);
        
        $notprinted = $resp['results'][0]['warnings']['notPrinted'] ?? false;
        if ($notprinted)
          $this->error = $this->err_code['notPrinted'];

        $res = $resp['results'][0]['result']['fiscalParams'] ?? null;
      }
    }

    return $res;
  }

  // закрытие смены
  public function close_shift()
  {
    $res = null;
    $cd = $this->getActiveData();
    $shift = $this->get_shift_status();

    if (!$this->error)
    {
      if ($shift['state'] == 'closed') 
        $this->error = $this->err_code['closed'];

      if (!$this->error)
      {
        $resp = $this->atol_task('closeShift', ['operator' => ['name' => $cd['Operator']], 'electronically' => true]);

        $notprinted = $resp['results'][0]['warnings']['notPrinted'] ?? false;
        if ($notprinted)
          $this->error = $this->err_code['notPrinted'];

        $res = $resp['results'][0]['result']['fiscalParams'] ?? null;
      }
    }

    return $res;
  }

  // продажа sell, возврат sellReturn
  public function fiskal($type_op, $items, $pay_type = 'electronically', $electronically = true)
  {
    $cd = $this->getActiveData();

    $data = [];
    $data['operator'] = ['name' => $cd['Operator']];
    $data['payments'] = [];
    $data['taxationType'] = 'patent';
    $data['clientInfo'] = ['emailOrPhone'=>$cd['EmailArchive']];
    $data['electronically'] = $electronically;

    list($data['items'], $summ) = $this->items_prepare($items);
    $data['payments'][] = ['type'=>$pay_type, 'sum'=>$summ];

    return $this->atol_task($type_op, $data);
  }

  // public function status() 
  // {
  //   return $this->CallAPI('GET', [], '/stat/request');
  // }
}



