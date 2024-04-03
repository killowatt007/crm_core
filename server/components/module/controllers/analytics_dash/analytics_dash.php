<?php
namespace bs\components\module\controllers\analytics_dash;
defined('EXE') or die('Access');

/**
 * $version 1.1
 * $deps s:bs\components\module\models\analytics_dash\Analytics_dash v1.1
 */

use \bs\libraries\mvc\Controller;

class Analytics_dash extends Controller
{
	public function get_list_data()
	{
		$data = [];

		$input = $this->app->input;
		$streamData = $input->get('stream.analytics', []);

		$anModel = $this->app->getComponent('module', 'analytics_dash', 'analytics_dash')->getModel();

		$anModel->debt = $streamData['debt'];
		$anModel->status = $streamData['status'];
		$anModel->base = $streamData['base'];

		$listData = $anModel->getListData();
		$allData = $anModel->allData;

		$allData['debtSum'] = number_format($allData['debtSum'], 0, ',', ' ');
		$allData['debt_l'] = number_format($allData['debt_l'], 0, ',', ' ');

		$data['list'] = $listData;
		$data['allData'] = $allData;

		return $data;
	}
}