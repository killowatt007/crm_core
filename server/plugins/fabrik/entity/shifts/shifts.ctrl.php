<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

use \bs\components\fabrik\Ctrl;

class CtrlShifts extends Ctrl
{
  private $activeShift = null;

  public function getOpen()
  {
    if ($this->activeShift === null)
    {
      $this->activeShift = false;
      $st_open = 9;

      $shift = $this->dbo->setQuery('
        SELECT id, DateOpen 
        FROM &__shifts 
        WHERE StatusId='.$st_open.'
        ORDER BY id DESC
        LIMIT 1'
      )->loadAssoc();

      if ($shift)
      {
        $origin = date_create($shift['DateOpen']);
        $target = date_create(date('Y-m-d H:i:s'));
        $interval = date_diff($origin, $target);

        if ((int)$interval->format('%d') === 0 and (int)$interval->format('%h') <= 20)
          $this->activeShift = $shift;
      }
    }

    return $this->activeShift;
  }
}



