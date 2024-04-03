<?php
namespace bs\components\domofon\controllers\clients;
defined('EXE') or die('Access');

/**
 * $version 1.1
 * $deps s:bs\components\domofon\models\clients\Mailing v1.1
 */

use \bs\libraries\mvc\Controller;

class Single_inv_mail extends Controller
{
	public function get_client_data()
	{
		$clientid = $this->app->input->get('clientid');
		$clientData = $this->app->getCtrl('fabrik', 'clients')->select('FIO, Mail', $clientid);

		return ['client'=>$clientData];
	}

	public function send()
	{
		$result = ['error' => null];
		$clientid = $this->app->input->get('clientid');
		$mail = $this->app->input->get('mail');

		$client = $this->app->getCtrl('fabrik', 'clients');
		$clientsData = $this->app->getCtrl('fabrik', 'clients')->select('id, FIO', 'id='.$clientid);
		$clientsData[0]['Mail'] = $mail;

		$slog = $this->app->getCtrl('fabrik', 'super_logs');
		$logid = $slog->init('single_inv_mail');

		$mailing = $this->app->getComponent('domofon', 'mailing', 'clients')->getModel();
		$mailing->clientsData = $clientsData;

		foreach ($clientsData as $client) 
		{
			$error = $mailing->send($client);

    	// slog
    	$slog->add($logid, [
    		'clientid' => $client['id'],
    		'mail' => $client['Mail'],
    		'error' => $error,
    		'smpt_error' => $mailing->smpt_error
    	]);

			$result['error'] = $error;
		}

		return $result;
	}
}