<?php
namespace bs\libraries\menu;
defined('EXE') or die('Access');

class Menu
{
	private $dbo;
	private $app;

	private $items = [];
	private $active = null;
	private $activeMenu = null;
	private $start = null;
	private $home = null;
	private $itemsGroup = [];

	public function __construct()
	{
		$this->dbo = \F::getDBO();
		$this->app = \F::getApp();
	}

	public function setActive($id)
	{
		if (!$this->active)
		{
			$item = $this->dbo
				->setQuery('SELECT id, MenuId FROM &__menu_item WHERE id='.$id)
				->loadAssoc();

			$this->getItemsGroup($item['MenuId']);
			$this->active = $this->items[$item['id']];
		}
	}

	public function getActive()
	{
		return $this->active;
	}

	public function getActiveMenu()
	{
		if (!$this->activeMenu)
		{
			$ctrlmenu = $this->app->getCtrl('fabrik', 'menu');
			$user = $this->app->getUser();

			foreach (array_reverse($user->data['GroupsId']) as $key => $groupid) 
			{
				$data = $ctrlmenu->select('id', 'GroupId='.$groupid);

				if (!empty($data))
				{
					$this->activeMenu = $data[0];
					break;
				}
			}
		}

		return $this->activeMenu;
	}

	public function getHome()
	{
		if (!$this->home)
		{
			$amenu = $this->getActiveMenu();

			$item = $this->dbo
				->setQuery('SELECT id, MenuId FROM &__menu_item WHERE IsHome=1 AND MenuId='.$amenu['id'])
				->loadAssoc();

			$this->getItemsGroup($item['MenuId']);
			$this->home = $this->items[$item['id']];
		}

		return $this->home;
	}

	public function getStart()
	{
		if (!$this->start)
		{
			$amenu = $this->getActiveMenu();

			$item = $this->dbo
				->setQuery('SELECT id, MenuId FROM &__menu_item WHERE IsStart=1 AND MenuId='.$amenu['id'])
				->loadAssoc();

			$this->getItemsGroup($item['MenuId']);
			$this->start = $this->items[$item['id']];
		}

		return $this->start;
	}

	public function getItemsGroup($menuId)
	{
		if (!isset($this->itemsGroup[$menuId]))
		{
			$itemsGroup = [];

			// get items
			$items = $this->dbo
				->setQuery(
				 'SELECT t1.*, GROUP_CONCAT(t2.right SEPARATOR ",") GroupsId
					FROM &__menu t0
					LEFT JOIN &__menu_item t1 ON (t1.MenuId=t0.id)
						LEFT JOIN &__access_level_repeat_GroupId t2 ON (t2.left=t1.LevelId)
					WHERE t0.id='.$menuId.'
					GROUP BY t1.id'
				)
				->loadAssocList('id');

			$user = $this->app->getUser();

			// to group
			foreach ($items as $id => $row) 
			{
				// if (@$user->data['id'] != 50 and $id == 107)
				// 	continue;

					// $access = false;

					// foreach ($user->data['GroupsId'] as $ugroupid) 
					// {
					// 	$groupsId = explode(',', $row['GroupsId']);

					// 	if (in_array($ugroupid, $groupsId))
					// 	{
					// 		$access = true;
					// 		break;
					// 	}
					// }

					// if (!$access)
					// 	continue;



				$parentId = $row['ParentId'];

				if (!isset($itemsGroup[$parentId]))
					$itemsGroup[$parentId] = ['data' => []];

				$itemsGroup[$parentId]['data'][] = $row;
			}

			$this->formingTree(null, $itemsGroup, $this);
			$this->itemsGroup[$menuId] = $itemsGroup;
		}

		return $this->itemsGroup[$menuId];
	}

	private function formingTree($parent, &$groups)
	{
		$groupId = $parent ? $parent['id'] : 0;
		$group = &$groups[$groupId];

		usort($group['data'], function($a, $b) 
		{
			if ($a['Display'] == $b['Display']) {
				return 0;
			}
			return ($a['Display'] < $b['Display']) ? -1 : 1;
		});

		if ($groupId == 0)
		{
			$path = '';
			$level = 1;
		}
		else
		{
			$pGroup = $groups[$parent['ParentId']];

			$level = $pGroup['level']+1;
			$path = $pGroup['path'].'/'.$parent['Alias'];
		}

		$group['path'] = $path;
		$group['level'] = $level;

		foreach ($group['data'] as &$item)
		{
			$id = $item['id'];

			$item['path'] = $path.'/'.$item['Alias'];
			$this->items[$id] = $item;

			if (isset($groups[$id]))
				$this->formingTree($item, $groups);
		}
	}
}