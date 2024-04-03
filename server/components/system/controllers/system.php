<?php
namespace bs\components\system\controllers;
defined('EXE') or die('Access');

use \bs\libraries\mvc\Controller; 

class System extends Controller
{
	public function base()
	{
		$path = $this->app->input->get('path');
		$_SESSION['baseid'] = (int)$_GET['baseid'];
		header('Location: '.$path);
	}
}
