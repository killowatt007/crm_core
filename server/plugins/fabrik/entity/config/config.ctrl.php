<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

/**
 * $version 1.1
 * $deps s:F v1.1
 * $db new f config___Mail
 * 		 new f config___MailPass
 * 		 new f config___MailHost
 * 		 new f config___MailPort
 */

use \bs\components\fabrik\Ctrl;

class CtrlConfig extends Ctrl
{
	public function sendMail($data)
	{
		$mail = \F::getMail();
		$config = $this->select('*', 1);

		$data['username'] = $config['Mail'];
		$data['from'] = $config['Mail'];
		$data['pass'] = $config['MailPass'];
		$data['host'] = $config['MailHost'];
		$data['port'] = $config['MailPort'];

		$res = $mail->send($data);

		return $res;
	}
}



