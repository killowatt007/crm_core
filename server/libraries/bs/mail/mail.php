<?php
namespace bs\libraries\mail;
defined('EXE') or die('Access');

/**
 * $version 1.1
 * $deps s:lib\PHPMailer
 */

require_once PATH_ROOT.'/libraries/PHPMailer/Exception.php';
require_once PATH_ROOT.'/libraries/PHPMailer/PHPMailer.php';
require_once PATH_ROOT.'/libraries/PHPMailer/SMTP.php';

use \PHPMailer\PHPMailer\PHPMailer;
use \PHPMailer\PHPMailer\Exception;

class Mail
{
	public function send($data)
	{
		$result = true;

		$mail = new PHPMailer;
		$mail->CharSet = 'UTF-8';
		 
		$mail->isSMTP();
		$mail->SMTPAuth = true;
		$mail->SMTPDebug = 0;
		// $mail->SMTPDebug = 2;

		$mail->Host = $data['host'];
		$mail->Port = $data['port'];
		$mail->Username = $data['username'];
		$mail->Password = $data['pass'];

		$mail->setFrom($data['from'], $data['from_title']);		
		 
		foreach ($data['to'] as $to) 
			$mail->addAddress($to['address'], $to['title']);

		$mail->Subject = $data['subject'];

		$mail->msgHTML($data['html']);

		if (isset($data['attachment']))
		{
			foreach ($data['attachment'] as $att)
			{
				$type = $att['type'] ?? 'file';

				if ($type == 'string')
					$mail->addStringAttachment($att['string'], $att['name']);
				else
					$mail->addAttachment($att['path']);
			}
		}
		 
		$issend = $mail->send();

		if (!$issend)
			$result = $mail->ErrorInfo;

		return $result;
	}
}