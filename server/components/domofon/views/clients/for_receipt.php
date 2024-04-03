<?php
namespace bs\components\domofon\views\clients;
defined('EXE') or die('Access');

use \bs\components\field\View;

class For_receipt extends View
{
	protected function data()
	{
		$data = [];

		$data['debt_date'] = str_replace('+', ' ', $_COOKIE['contracts']['debt_date'] ?? '');

		return $data;
	}
}