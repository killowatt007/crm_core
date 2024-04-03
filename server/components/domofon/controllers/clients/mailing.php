<?php
namespace bs\components\domofon\controllers\clients;
defined('EXE') or die('Access');

/**
 * $version 1.1
 * $link bs\components\domofon\models\Invalid_debt v1.1 super_logs 
 * $deps s:bs\plugins\fabrik\entity\CtrlConfig v1.1
 *			 s:bs\components\domofon\models\invoice\Receipt v1.1
 * 			 s:bs\libraries\helper\Arr v1.1
 * $db new f clients___Mail
 * --------
 * $version 1.2
 * $deps s:bs\components\domofon\models\clients\Mailing v1.1
 */

use \bs\libraries\mvc\Controller;

class Mailing extends Controller
{
	public function send()
	{
		$result = ['clients'=>[], 'length'=>0];

		$client = $this->app->getCtrl('fabrik', 'clients');
		$clientData = $client->select('id, Mail, FIO', 'Mail!=""');

		$slog = $this->app->getCtrl('fabrik', 'super_logs');
		$logid = $slog->init('quarter.mailing');

		if (!empty($clientData))
		{
			$mailing = $this->app->getComponent('domofon', 'mailing', 'clients')->getModel();
			$mailing->clientsData = $clientData;

			foreach ($clientData as $client) 
			{
				// $error = $mailing->send($client);
				$error = false;
				
				$info = [
					'label' => $client['FIO'].' ('.$client['id'].')',
					'error' => $error
				];

      	// slog
      	$slog->add($logid, [
      		'clientid' => $client['id'],
      		'mail' => $client['Mail'],
      		'error' => $error,
      		// 'smpt_error' => $mailing->smpt_error
					'smpt_error' => 0
      	]);

				$result['clients'][] = $info;

				if (!$error)
					$result['length']++;
			}
		}

		return $result;
	}
}