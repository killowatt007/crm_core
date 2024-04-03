<?php
namespace bs\components\module\controllers\acquiring;
defined('EXE') or die('Access');

use \bs\libraries\mvc\Controller;

class Acquiring extends Controller
{
	public function test()
	{
		$date = $this->app->input->get('date');
		$datetimeArr = explode(' ', $date);
		$dateArr = explode('-', $datetimeArr[0]);

		$dt = new \DateTime($date);
		$dt->modify('+6 day');
		$date7 = $dt->format('Y-m-d');
		$date7Arr = explode('-', $date7);

		$sPostFields = [
			'Shop_ID'						=> '00029184',
			'Login' 						=> '7254',
			'Password' 					=> 'yVBCSEpC8uJImdZ3N0BM5rHBIC96ImHzNmgXJZgfRy9gUkZ6CWHCYg2HqbVx10UWROAV0SVOpoHnw8El',
			'Format' 						=> 1,
			// 'ShopOrderNumber' 	=> '20220428172718'
			// 'Success' => 1


			'StartYear' 	=> $dateArr[0],
			'StartMonth' 	=> $dateArr[1],
			'StartDay' 		=> $dateArr[2],
			'StartHour'		=> '00',
			'StartMin'		=> '00',

			'EndYear' 		=> $date7Arr[0],
			'EndMonth' 		=> $date7Arr[1],
			'EndDay' 			=> $date7Arr[2],
		];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://wpay.uniteller.ru/results/');
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $sPostFields);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
		curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
		$curl_response = curl_exec($ch);
		$curl_error = curl_error($ch);

		$data['headers'] = [
			'Номер',
			'Счет',
			'ФИО',
			'Адрес',
			'Сумма',
			'Карта',
			'Держатель карты',
			'Почта',
			'Дата Платежа',
			'Статус'
		];
		$data['rows'] = [];

		if ($curl_response)
		{
			$parseData = [];
			foreach (explode(PHP_EOL, $curl_response) as $row)
			  $parseData[] = explode(';', $row);

			$orders = [];
			foreach ($parseData as $key => $row) 
			{
				if ($row[0] != 20220428153659 and $row[0] and $row[4])
				{
					$datetimeArr = explode(' ', $row[5]);
					$dateArr = explode('.', $datetimeArr[0]);
					$datepay = $dateArr[2].'-'.$dateArr[1].'-'.$dateArr[0].' '.$datetimeArr[1];

					$infoArr = explode(':::', $row[4]);

					$status = '';
					$acqData = $this->app->getCtrl('fabrik', 'acquiring_payment')->select('StatusId', 'AcqId='.$row[0]);
					$statusid = $acqData[0]['StatusId'] ?? 0;

					if ($statusid)
						$status = $statusid == 13 ? 'Не отправлено' : 'Готово';

					$orders[] = [
						$row[0],
						$infoArr[0],
						$infoArr[1],
						$infoArr[2],
						$row[6],
						$row[9],
						$row[20],
						$row[14],
						$datepay,
						$status
					];
				}
			}

			$data['rows'] = $orders;
		}

		return $data;
	}
}