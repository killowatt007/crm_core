<?php
namespace bs\libraries\event;
defined('EXE') or die('Access');

class DispatcherModel
{
	private $statical_plugins = [];
	private $entity_plugins = [];
	private $sg_statical_plugins = [];
	private $s_entity_plugins = [];
	
	public $model;

	public function run($event, $args, $submodel, $prefix)
	{
		$result = [];

		$model = $this->model;
		$cgroup = $model->getComponent()->getGroup();
		$cname = $submodel ? $prefix : $model->getComponent()->getName();
		$key = $cgroup.$cname.$model->getId();

		$this->import($cgroup, $cname, $key, $model);

		$sg_s_p = $this->sg_statical_plugins[$cgroup.$cname] ?? [];
		$s_e_p = $this->s_entity_plugins[$key] ?? [];
		$s_p = $this->statical_plugins[$cgroup.$cname] ?? [];
		$e_p = $this->entity_plugins[$key] ?? [];

		foreach ([$sg_s_p, $s_e_p, $s_p, $e_p] as $k => $group) 
		{
			foreach ($group as $plugin) 
			{
				$method = 'on'.ucfirst($event);

				if (method_exists($plugin, $method))
				{
					$plugin->setModel($model);
					$plugin->setSubmodel($submodel);

					$ok = $plugin->$method(...$args);

					if ($ok === false)
						$result[] = false;
					else
						$result[] = true;
				}
			}
		}

		return array_unique($result);
	}

	private function import($cgroup, $cname, $key, $model)
	{
		$app = \F::getApp();

		// spaces global entity
		if (!isset($this->s_entity_plugins[$key]))
		{
			$this->s_entity_plugins[$key] = [];

			if (method_exists($model, 'getTable'))
			{
				$prefix = $app->getSpace()->getPrefix();
				$name = $model->getTable()->getData()['Name'];

				if ($class = $app->includeExt('plugin', $name, $cgroup.'.entity', $cname, $prefix))
				{
					$plugin = new $class();
					$this->s_entity_plugins[$key][] = $plugin;
				}
			}
		}

		// spaces global statical 
		if (!isset($this->sg_statical_plugins[$cgroup.$cname]))
		{
			$pathRoot = PATH_ROOT.'/spaces';

			foreach ($app->getSpace()->getAll() as $space) 
			{
				$spacePath = $pathRoot.'/'.$space['Prefix'].'/globals/plugins/'.$cgroup.'/statical';

				if (file_exists($spacePath))
				{
					foreach (scandir($spacePath) as $name) 
					{
						if ($name[0] != '.')
						{
							if ($class = $app->includeExt('plugin', $name, $cgroup.'.statical', $cname, $space['Prefix'], true))
							{
								$plugin = new $class();
								$this->sg_statical_plugins[$cgroup.$cname][] = $plugin;
							}

							$app->setDep('spaces/'.$space['Prefix'].'/globals/plugins/'.$cgroup.'/statical/'.$name.'/'.$name.'.'.$cname);
						}
					}
				}
			}
		}

		// statical 
		if (!isset($this->statical_plugins[$cgroup.$cname]))
		{
			$this->statical_plugins[$cgroup.$cname] = [];
			$pathRoot = PATH_ROOT.'/plugins/'.$cgroup.'/statical';

			if (file_exists($pathRoot))
			{
				foreach (scandir($pathRoot) as $name) 
				{
					if ($name[0] != '.')
					{
						if ($class = $app->includeExt('plugin', $name, $cgroup.'.statical', $cname))
						{
							$plugin = new $class();
							$this->statical_plugins[$cgroup.$cname][] = $plugin;
						}
					}
				}
			}
		}
		
		// entity
		if (!isset($this->entity_plugins[$key]))
		{
			$this->entity_plugins[$key] = [];

			if (method_exists($model, 'getTable'))
			{
				$tname = $model->getTable()->getData()['Name'];
				
				if ($class = $app->includeExt('plugin', $tname, $cgroup.'.entity', $cname))
				{
					$plugin = new $class();
					$this->entity_plugins[$key][] = $plugin;
				}
			}
		}

		// include
	}

	public function setDep($cname)
	{
		$app = \F::getApp();
		$cgroup = $this->model->getComponent()->getGroup();
		$cname = $cname ? $cname : $this->model->getComponent()->getName();

		// statical 
		$pathRoot = PATH_CLIENT.'/plugins/'.$cgroup.'/statical';

		if (file_exists($pathRoot))
		{
			foreach (scandir($pathRoot) as $name) 
			{
				if ($name[0] != '.')
				{
					$pathf = $pathRoot.'/'.$name.'/'.$name.'.'.$cname.'.js';

					if (file_exists($pathf))
						$app->setDep('plugins/'.$cgroup.'/statical/'.$name.'/'.$name.'.'.$cname);
				}
			}
		}

		// entity 
		if (method_exists($this->model, 'getTable'))
		{
			$pathRoot = PATH_CLIENT.'/plugins/'.$cgroup.'/entity';
			$tname = $this->model->getTable()->getData()['Name'];

			$pathf = PATH_CLIENT.'/plugins/'.$cgroup.'/entity/'.$tname.'/'.$tname.'.'.$cname.'.js';

			if (file_exists($pathf))
				$app->setDep('plugins/'.$cgroup.'/entity/'.$tname.'/'.$tname.'.'.$cname);
		}

		// spaces global entity
		$prefix = $app->getSpace()->getPrefix();
		$pathRoot = PATH_CLIENT.'/spaces/'.$prefix.'/plugins/'.$cgroup.'/entity';

		if (file_exists($pathRoot))
		{
			foreach (scandir($pathRoot) as $name) 
			{
				if ($name[0] != '.')
				{
					$pathf = $pathRoot.'/'.$name.'/'.$name.'.'.$cname.'.js';

					if (file_exists($pathf))
						$app->setDep('spaces/'.$prefix.'/plugins/'.$cgroup.'/entity/'.$name.'/'.$name.'.'.$cname);
				}
			}
		}
	}
}


