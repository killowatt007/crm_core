<?php
namespace bs\components\system\controllers;
defined('EXE') or die('Access');

use \bs\libraries\mvc\Controller; 

class User extends Controller
{
	public function logout()
	{
		$app = \F::getApp();

		$userId = $app->getUser()->data['id'];
		$session = $app->getSession();
		$session->logout();

		$app->redirect('/');
	}

	public function login()
	{
		sleep(1);
		
		$error = null;
		$result = [];
		$app = \F::getApp();

		$dbo = \F::getDBO();
		$productId = $app->input->get('productId');
		$user = $app->input->get('user');
		$password = $app->input->get('password');

		$user = $dbo->escape($user);
		$password = $dbo->escape($password);
		
		$user = $dbo
			->setQuery('SELECT id FROM &__user WHERE Name='.$dbo->q($user).' AND Password='.$dbo->q($password))
			->loadAssoc();

		if ($user)
		{
			$session = $app->getSession();
			$session->load($user['id']);
			$app->clearUsers();

			$menu = $app->getMenu();
			$start = $menu->getStart();

			$result['redirect'] = $start['path'];
		}
		else
		{
			$error = 'Incorrect Username or Password';
		}

		if ($error)
		{
			$result['error'] = $error;
		}

		return $result;
	}
}
