<?php
namespace bs\components\module\views\verticalmenu;
defined('EXE') or die('Access');

use \bs\libraries\mvc\View;

class Verticalmenu extends View
{
	public function data()
	{
		$data = [];
		$dboCws = \F::getDBO();
		$app = \F::getApp();
		$menu = $app->getMenu();
		$space = $app->getSpace();
		$user = $app->getUser();

		$product = $dboCws
			->setQuery('SELECT Name, Description FROM &__product WHERE SpaceId='.$space->getId())
			->loadAssoc();

		$data['productName'] = $product['Name'];
		$data['userName'] = $user->data['Name'];
		$data['activeItem'] = $menu->getActive();
		$data['itemsGroup'] = $menu->getItemsGroup($menu->getActiveMenu()['id']);

		return $data;
	}

}