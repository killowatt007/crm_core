<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

include_once PATH_ROOT .'/components/fabrik/event/pluginForm.php';
use \bs\components\fabrik\event\PluginForm;

class FormTelegram_msg extends PluginForm
{
	public function onBeforeStore()
	{
		$botData = $this->getCtrl('telegram_bot')->select('BotId, Token')[0];
		$chatData = $this->getCtrl('telegram_chat')->select('ChatId', $this->getCV('ChatId'));

		$tg_user = $chatData['ChatId'];
		$bot_token = $botData['BotId'].':'.$botData['Token'];

		$params = array(
	    'chat_id' => $tg_user,
	    'text' => $this->getCV('Text'),
	    'parse_mode' => 'HTML',
		);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'https://api.telegram.org/bot' . $bot_token . '/sendMessage'); // getUpdates
		curl_setopt($curl, CURLOPT_POST, true); // отправка данных методом POST
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
		$result = curl_exec($curl);
		curl_close($curl);

		$res = json_decode($result, true);

		if ($res['ok'])
		{
			$this->updFD('MsgId', $res['result']['message_id']);
		}

		// $res['ok']
		// $res['error_code']
		// $res['description']
	}

	public function onElementParams($el)
	{
		if (!$this->getModel()->isNewRecord())
		{
			$el->setParam('form_edit', false);
		}
	}
}