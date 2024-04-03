<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

include_once PATH_ROOT .'/components/fabrik/event/pluginForm.php';
use \bs\components\fabrik\event\PluginForm;

class FormTelegram_chat extends PluginForm
{
	public function onElementParams($el)
	{
		// StaffId
		if ($el->getName() == 'StaffId')
		{
			$el->setParam('ignoreBase', true);
		}
	}

	public function onBeforeStore()
	{
		$this->updFD('TypeId', 64);
	}

	public static function onAjaxFindChatId()
	{
		$status = 'ok';
		$key = \F::getApp()->input->get('key');

		$botData = \F::getApp()->getCtrl('fabrik', 'telegram_bot')->select('BotId, Token')[0];
		$bot_token = $botData['BotId'].':'.$botData['Token'];
		$params = [];

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'https://api.telegram.org/bot' . $bot_token . '/getUpdates');
		curl_setopt($curl, CURLOPT_POST, true); // отправка данных методом POST
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
		$result = curl_exec($curl);
		curl_close($curl);

		$res = json_decode($result, true);
		$id = null;
		$name = null;

		foreach ($res['result'] as $row) 
		{
			$text = $row['message']['text'] ?? '';

			if ($text == $key)
			{
				$id = $row['message']['chat']['id'];
				$fname = $row['message']['chat']['first_name'];
				$lname = $row['message']['chat']['last_name'] ?? '';
				$name = $fname . ($lname ? ' '.$lname : '');

				$chats = \F::getApp()->getCtrl('fabrik', 'telegram_chat')->select('id', 'ChatId='.$id);

				if (!empty($chats))
					$status = 'exist';
				else
					$status = 'ok';

				break;
			}
			else
			{
				$status = 'notfound';
			}
		}

		return ['id'=>$id, 'name'=>$name, 'status'=>$status];
	}
}