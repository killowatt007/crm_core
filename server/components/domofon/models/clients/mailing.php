<?php
namespace bs\components\domofon\models\clients;
defined('EXE') or die('Access');

/**
 * $version 1.1
 */

use \bs\libraries\mvc\Model;

class Mailing extends Model
{
	private $receiptModel = null;
	public $clientsData;

	public $smpt_error = '';

	private function getReceiptModel()
	{
		$this->app = \F::getApp();

		if (!$this->receiptModel)
		{
			$client = $this->app->getCtrl('fabrik', 'clients');

			$receipt = $this->app->getComponent('domofon', 'receipt', 'invoice')->getModel();
			$receipt->clientids = \F::getHelper('arr')->single($this->clientsData, 'id');
			$receipt->date_from = date('Y-'.$client->getFirstQ().'-01 00:00:00');
			$receipt->date_to = date('Y-'.$client->getLastQ().'-01 00:00:00');
			$receipt->singledoc = false;
			$receipt->output_type = 'S';

			$this->receiptModel = $receipt;
		}

		return $this->receiptModel;
	}

	public function send($clientData)
	{
		$this->app = \F::getApp();
		$this->smpt_error = '';
		$error = '';

		$space = $this->app->getSpace();

		$config = $this->app->getCtrl('fabrik', 'config');
		$product = $this->app->getCtrl('fabrik', 'product');

		$productData = $product->select('Name', 'SpaceId='.$space->getId())[0];
		$receipts = $this->getReceiptModel()->getReceipts();

		$cid = $clientData['id'];
		$to = trim($clientData['Mail']);

		if (filter_var($to, FILTER_VALIDATE_EMAIL) !== false)
		{
			$res = $config->sendMail([
				'subject' => 'Счет за услуги домофона',
				'from_title' => $productData['Name'],
				'to' => [[
					'address' => $to,
					'title' => $clientData['FIO']
				]],
				'html' => $clientData['FIO'],
				'attachment' => [[
					'type' => 'string',
					'name' => 'invoice.pdf',
					'string' => $receipts[$cid]
				]]
			]);

			if ($res !== true)
			{
				$this->smpt_error = $res;
				$error = 'Ошибка отправки';
			}
		}
		else
		{
			$error = 'Email адрес указан не верно';
		}

		return $error;
	}
}