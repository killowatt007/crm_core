<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

use \bs\components\fabrik\Ctrl;

class CtrlContracts extends Ctrl
{
  private $info = [];

  public function validateId($id, $selfid = 0)
  {
    $error = null;

    if (is_numeric($id))
    {
      $id = (int)$id;

      if ($id)
      {
        $isset = (bool)$this->app->getCtrl('fabrik', 'contracts')->select('id', 'id='.(int)$id.' AND id!='.$selfid);

        if ($isset)
          $error = 'Поле "Номер" - данный номер уже существует в базе';
      }
      else
      {
        $error = 'Поле "Номер" - не может быть равен 0';
      }
    }
    else
    {
      $error = 'Поле "Номер" - должено содержать только цифры';
    }

    return $error;
  }
}



