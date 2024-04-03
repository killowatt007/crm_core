<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

use \bs\components\fabrik\Ctrl;

class CtrlPick_items extends Ctrl
{
  public function getActiveBase()
  {
    $baseid = 0;
    $user = $this->app->getUser();

    if (!$user->guest)
    {
      $r_operator = 3;
      $r_director = 5;

      if (in_array($user->data['RoleId'], [$r_operator, $r_director]))
        $baseid = $_SESSION['baseid'] ?? null;

      if (!$baseid)
        $baseid = $this->getUserBase();
    }

    return $baseid;
  }

  public function getUserBase()
  {
    $user = $this->app->getUser();
    $userid = $user->data['id'];

    $staff = $this->app->getCtrl('fabrik', 'staff')->select('BaseId', 'UserId='.$userid)[0];
    
    return $staff['BaseId'];
  }
}



