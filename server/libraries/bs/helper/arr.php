<?php
namespace bs\libraries\helper;
defined('EXE') or die('Access');

/**
 * $version 1.1
 */

class Arr
{
	public function single($arr, $key)
	{
		$single = [];

		foreach ($arr as $row) 
			$single[] = $row[$key];

		return $single;
	}
	
	public function rebuild($arr, $keys)
	{
		$newarr = [];

		if (is_array($keys))
		{
			$old = array_values($keys);
			$new = array_keys($keys);

			foreach ($arr as $i => $row) 
			{
				foreach ($old as $n => $k) 
				{
					if (isset($row[$k]))
						$newarr[$i][$new[$n]] = $row[$k];
				}
			}
		}
		else
		{
			foreach ($arr as $i => $row) 
				$newarr[] = $row[$keys];
		}

		return $newarr;
	}

	public function sort($data, $id_key = 'id', $parent_key = 'ParentId', $order_key = 'Display')
	{
		$dataGroups = [];
		$result = [];

		foreach ($data as $row) 
		{
			$parentId = (int)$row[$parent_key];

			if (!isset($dataGroups[$parentId]))
				$dataGroups[$parentId] = [];

			if ($order_key !== false)
				$row['_order_key'] = $row[$order_key];
			
			$dataGroups[$parentId][] = $row;
		}

		if ($order_key !== false)
		{
			foreach($dataGroups as $i => $row)
			{
				usort($dataGroups[$i], function($a, $b) 
					{
						if ($a['_order_key'] == $b['_order_key']) {
							return 0;
						}
						return ($a['_order_key'] < $b['_order_key']) ? -1 : 1;
					});
			}
		}

		return $this->sortEach($dataGroups[0], $dataGroups, $id_key);
	}
	
	private function sortEach($group, $groups, $id_key, $lvl = 1, &$data = [])
	{
		foreach ($group as $row) 
		{
			$row['lvl'] = $lvl;
			$data[$row[$id_key]] = $row;

			if (isset($groups[$row[$id_key]]))
				$this->sortEach($groups[$row[$id_key]], $groups, $id_key, $lvl+1, $data);
		}

		return $data;
	}
}