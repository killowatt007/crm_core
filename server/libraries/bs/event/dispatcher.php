<?php
namespace bs\libraries\event;
defined('EXE') or die('Access');

class Dispatcher
{
	protected $plugins = [];

	public function run($type, $event, $args = [])
	{
		$this->import($type);

		foreach ($this->plugins[$type] as $plugin) 
		{
			$method = 'on'.ucfirst($event);

			if (method_exists($plugin, $method))
				$plugin->$method(...$args);
		}
	}

	private function import($type)
	{
		$app = \F::getApp();
		
		if (!isset($this->plugins[$type]))
		{
			$this->plugins[$type] = [];

			// main
			$mainPath = PATH_ROOT.'/plugins/'.$type;
			
			if (file_exists($mainPath))
			{
				foreach (scandir($mainPath) as $name) 
				{
					if ($name[0] != '.')
					{
						$pathf = $mainPath.'/'.$name.'/'.$name.'.php';

						if (file_exists($pathf))
						{
							$className = '\bs\plugins\\'.$type.'\\'.ucfirst($name);

							include $pathf;
							$this->plugins[$type][] = new $className();
						}
					}
				}
			}

			// spaces global
			$spaceRoot = PATH_ROOT.'/spaces';

			foreach ($app->getSpace()->getAll() as $space) 
			{
				$spacePath = $spaceRoot.'/'.$space['Prefix'].'/globals/plugins/'.$type;

				if (file_exists($spacePath))
				{
					foreach (scandir($spacePath) as $name) 
					{
						if ($name[0] != '.')
						{
							$pathf = $spacePath.'/'.$name.'/'.$name.'.php';

							if (file_exists($pathf))
							{
								$className = '\bs\spaces\\'.$space['Prefix'].'\globals\plugins\\'.$type.'\\'.ucfirst($name);

								include $pathf;
								$this->plugins[$type][] = new $className();

								$app->setDep('spaces/'.$space['Prefix'].'/globals/plugins/'.$type.'/'.$name.'/'.$name);
							}
						}
					}
				}
			}
		}
	}

	public function setDep($group)
	{
		$app = \F::getApp();
		
		// main
		$mainPath = PATH_CLIENT.'/plugins/'.$group;

		if (file_exists($mainPath))
		{
			foreach (scandir($mainPath) as $name) 
			{
				if ($name[0] != '.')
				{
					$pathf = $mainPath.'/'.$name.'/'.$name.'.js';

					if (file_exists($pathf))
						$app->setDep('plugins/'.$group.'/'.$name.'/'.$name);
				}
			}
		}

		// spaces global
		$spaceRoot = PATH_CLIENT.'/spaces';

		foreach ($app->getSpace()->getAll() as $space) 
		{
			$spacePath = $spaceRoot.'/'.$space['Prefix'].'/globals/plugins/'.$group;

			if (file_exists($spacePath))
			{
				foreach (scandir($spacePath) as $name) 
				{
					if ($name[0] != '.')
					{
						$pathf = $spacePath.'/'.$name.'/'.$name.'.js';

						if (file_exists($pathf))
							$app->setDep('spaces/'.$space['Prefix'].'/globals/plugins/'.$group.'/'.$name.'/'.$name);
					}
				}
			}
		}
	}
}