<?php
namespace bs\components\module\views\custom;
defined('EXE') or die('Access');

use \bs\libraries\mvc\View;

class Custom extends View
{
	public function data()
	{
		$data = [];
		$app = \F::getApp();
		$model = $this->getModel();

		// slider
		if ($model->getParam('flag') == 'slider')
		{
			$dboC = \F::getDBO();
			$space = $app->getSpace();
			$prefix = $space->getPrefix();
			$sId = $space->getId();

			$pathLogo = '/spaces/'.$prefix.'/filecentral/logo.png';
			$pathSlide = '/spaces/'.$prefix.'/filecentral/slide.jpg';

			if (file_exists(PATH_ROOT.'/'.$pathLogo))
				$data['logo'] = $pathLogo;

			if (file_exists(PATH_ROOT.'/'.$pathSlide))
				$data['slide'] = $pathSlide;

			$data['product'] = $dboC
				->setQuery('SELECT Name, Description FROM &__product WHERE SpaceId='.$sId)
				->loadAssoc();
		}

		// dashboard
		elseif ($model->getParam('flag') == 'dashboard')
		{
			$user = $app->getUser();
			$data['guest'] = $user->guest;

			if (!$user->guest)
			{
				$menu = $app->getMenu();
				$start = $menu->getStart();
				$data['path'] = $start['path'];
			}
		}

		// login
		elseif ($model->getParam('flag') == 'login')
		{
			$user = $app->getUser();
			$bell = false;
			$base = false;

			if (!$user->guest)
			{
				$r_operator = 3;
				$r_director = 5;
				$r_master = 4;

				if (in_array($user->data['RoleId'], [$r_operator, $r_director]))
				{
					$pickitemCtrl = $app->getCtrl('fabrik', 'pick_items');
					$bases = $pickitemCtrl->select('id, Name', 'PickTypeId=4');
					$bases = \F::getHelper('arr')->rebuild($bases, ['value'=>'id', 'label'=>'Name']);
					$base = [
						'active' => $pickitemCtrl->getActiveBase(),
						'items' => $bases
					];
				}

				if (in_array($user->data['RoleId'], [$r_operator, $r_director, $r_master]))
					$bell = true;
			}

			$data['base'] = $base;
			$data['bell'] = $bell;
			$data['guest'] = $user->guest;
		}

		// developer
		elseif ($model->getParam('flag') == 'developer')
		{
		}

		$data['flag'] = $model->getParam('flag');

		return $data;
	}
}