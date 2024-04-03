<?php
namespace bs\components\domofon\models\clients;
defined('EXE') or die('Access');

/**
 * $version 1.1
 *
 ***< super_logs
 * $deps s:bs\plugins\fabrik\entity\CtrlSuper_logs v1.1
 * $db new t super_logs
 *            - Name
 *            - DateCreate
 * $db new t super_logs_items
 *            - Data
 *            - LogId
 *            - DateCreate
 ***>
 */

use \bs\libraries\mvc\Model;

class Invalid_debt extends Model
{
	public function invalid()
	{

	}
}