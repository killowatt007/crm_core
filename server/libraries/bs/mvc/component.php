<?php
namespace bs\libraries\mvc;
defined('EXE') or die('Access');

class Component
{
	protected $group;
	protected $name;
	protected $branch;

	protected $model = null;
	protected $view = null;

	public function __construct($group, $name, $branch)
	{
		$this->group = $group;
		$this->name = $name;
		$this->branch = $branch;
	}

	public function getGroup() { return $this->group; }
	public function getName() { return $this->name; }
	public function getBranch() { return $this->branch; }

	public function getModel($id = null)
	{
		if (!$this->model)
		{
			$className = null;

			$path  = PATH_ROOT.'/components/'.$this->group.'/models/';
			$path .= $this->branch ? str_replace('.', '/', $this->branch).'/' : '';
			$path .= $this->name;

			if (file_exists($path.'/'.$this->name.'.php'))
				$path .= '/'.$this->name.'.php';
			else
				$path .= '.php';

			if (file_exists($path))
			{
				include_once $path;

				$className  = '\bs\components\\'.$this->group.'\models\\';
				$className .= $this->branch ? str_replace('.', '\\', $this->branch).'\\' : '';
				$className .= ucfirst(($this->name == 'list' ? 'lst' : $this->name));
			}

			if (!$className)
			{
				$path = PATH_ROOT.'/components/'.$this->group.'/model.php';
				$classMain = '\bs\components\\'.$this->group.'\Model';

				if (file_exists($path))
				{
					include_once $path;
					$className = $classMain;
				}
			}

			if (!$className)
				$className = '\bs\libraries\mvc\Model';

			$this->model = new $className();

			if ($id)
				$this->model->setId($id);

			$this->model->setComponent($this);
		}

		return $this->model;
	}

	public function getView(...$arr)
	{
		if (!$this->view)
		{
			$app = \F::getApp();

			$path  = 'components/'.$this->group.'/views/';
			$path .= $this->branch ? str_replace('.', '/', $this->branch).'/' : '';
			$path .= $this->name;

			$className  = '\bs\components\\'.$this->group.'\views\\';
			$className .= $this->branch ? str_replace('.', '\\', $this->branch).'\\' : '';
			$className .= $this->name;
			$className = str_replace(['list', 'List'], ['lst', 'Lst'], $className);

			$app->setDep(str_replace('/views/', '/actors/', $path));
			$app->pluginManager()->setDep($this->group);

			$path = PATH_ROOT.'/'.$path.'.php';

			include_once $path;
			$this->view = new $className(...$arr);
			$this->view->setComponent($this);
		}

		return $this->view;
	}
}