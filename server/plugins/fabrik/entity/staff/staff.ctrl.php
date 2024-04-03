<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

use \bs\components\fabrik\Ctrl;

class CtrlStaff extends Ctrl
{
  public function getActive()
  {
    $user = $this->app->getUser();
    return $this->select('*', 'UserId='.$user->data['id'])[0];
  }
}



