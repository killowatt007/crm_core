<?php
namespace bs\components\domofon\views\invoice;
defined('EXE') or die('Access');

use \bs\components\field\View;

class Receipt extends View
{
	protected function data()
	{
		$data = [];

		$data['date_from'] = str_replace('+', ' ', $_COOKIE['contracts']['date_from'] ?? '');
		$data['date_to'] = str_replace('+', ' ', $_COOKIE['contracts']['date_to'] ?? '');
		$data['debt_date'] = str_replace('+', ' ', $_COOKIE['contracts']['debt_date'] ?? '');

		return $data;
	}
}