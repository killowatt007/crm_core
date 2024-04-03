<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

use \bs\components\fabrik\Ctrl;

class CtrlAdvanced extends Ctrl
{
  static private $all = null;

  public function get($name)
  {
    $all = $this->getAll();
    $isactive = (int)($all[$name]['IsActive'] ?? 0);

    return $isactive;
  }

  private function getAll()
  {
    if (self::$all === null)
      self::$all = $this->select('*', null, 'Name');

    return self::$all;
  }
}



