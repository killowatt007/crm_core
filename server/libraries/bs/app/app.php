<?php
declare(strict_types = 1);
namespace bs\libraries\app;
defined('EXE') or die('Access');

/**
 * $version 1.1
 * $upd c:libraries/bs/object/object v1.1
 */

include PATH_ROOT .'/libraries/bs/object/object.php';
include PATH_ROOT .'/libraries/bs/object/actor.php';

include PATH_ROOT .'/libraries/bs/mvc/component.php';
include PATH_ROOT .'/libraries/bs/mvc/controller.php';
include PATH_ROOT .'/libraries/bs/mvc/model.php';
include PATH_ROOT .'/libraries/bs/mvc/view.php';

include PATH_ROOT.'/libraries/bs/event/dispatcher.php';
include PATH_ROOT.'/libraries/bs/event/dispatcherModel.php';
include PATH_ROOT.'/libraries/bs/event/manager.php';
include PATH_ROOT.'/libraries/bs/event/plugin.php';
include PATH_ROOT.'/libraries/bs/event/pluginModel.php';


include PATH_ROOT.'/libraries/bs/ctrl.php';

include PATH_ROOT .'/libraries/bs/module/module.php';

include PATH_ROOT .'/libraries/bs/user/user.php';
include PATH_ROOT .'/libraries/bs/session/session.php';
include PATH_ROOT .'/libraries/bs/input/input.php';
include PATH_ROOT .'/libraries/bs/menu/menu.php';
include PATH_ROOT .'/libraries/bs/space/space.php';

class App
{
	private $isdev = true;

	private $dep = [0=>'components/builder/actors/page'];
	private $clientPath;

	public $input;
	private $users = [];
	private $menu = null;
	private $plgManager = null;

	private $session = null;
	private $space = null;

	private $modules = [];
	public $updateModules = [];
	private $data = [];

	private $service = [];
	private $ctrl = [];

	private $pagePluginManager = null;

	private $initializedComponents = ['basic'=>[], 'plugin'=>[]];

	private $errorMsg = [
		404 => 'Page Not Found',
		500 => 'Internal Server Error'
	];

	public function __construct() {}

	public function clear()
	{
		$this->menu = null;
		$this->space = null;
		$this->plgManager = null;
	}

	public function exe()
	{
		// $dbo = \F::getDbo();
		// $data = $dbo->setQuery('SELECT * FROM fabrik_entity')->loadAssocList();

		// foreach ($data as $key => $row) 
		// {
		// 	$params = [];
		// 	$_params = json_decode($row['Params'], true);

		// 	if (!empty($_params))
		// 	{
		// 		$params['form']['basic'] = $_params['form']['basic']['basic']['basic']['basic'];
		// 		$params['list']['basic'] = $_params['list']['basic']['basic']['basic']['basic'];

		// 		$dbo->setQuery('UPDATE fabrik_entity SET Params='.$dbo->q(json_encode($params)).' WHERE id='.$row['id'])->execute();
		// 	}
		// }

		// qqq('ok');

		$result = [];
		$this->input = new \bs\libraries\input\Input();
		$this->route();

		$taskArr = explode('.', $this->input->get('task'));
		$method = (isset($taskArr[1])) ? $taskArr[1] : 'data';

		$controller = $this->getController();

		$data = $controller->$method();

		$result['data'] = array_merge($this->data, $data);
		$result['dep'] = $this->dep;
		$result['clientPath'] = $this->clientPath;
		$result['updateModules'] = $this->updateModules;
		$result['ismobile'] = $this->isMobile();
		$result['ip'] = $_SERVER['REMOTE_ADDR'];

		$this->getPagePluginManager()->run('data', [\F::std(['data'=>&$result['data']])]);

		if ($active = $this->getMenu()->getActive())
		{
			$pageactive = $this->getCtrl('fabrik', 'builder_tmpl')->getActive();

			$result['item'] = [
				'id' => $active['id'],
				'builderid' => $pageactive['id'],
				'builderalias' => $pageactive['Alias'],
				'path' => $active['path']
			];
		}

		echo json_encode($result);
	}

	private function route()
	{
		$dbo = \F::getDBO();
		$menu = $this->getMenu();
		$activeItem = null;

		if ($itemId = $this->input->get('itemId'))
		{
			$activeItem = $dbo
				->setQuery('SELECT id, Link FROM &__menu_item WHERE id='.$itemId)
				->loadAssoc();

			if (!$activeItem)
				$this->error(404, ['type'=>'http', 'msg'=>'Page Not Found']);
		}
		elseif ($path = $this->input->get('path'))
		{
			$pathArr = explode('/', $path);
			$items = [];
			$parentId = null;

			foreach ($pathArr as $i => $alias) 
			{
				if ($i > 0)
				{
					if ($alias == '' and $i == 1)
						$alias = 'home';

					// if ($alias == '' and $i == 1)
					// {
					// 	$item = $menu->getHome();
					// }
					// else
					// {
						$where = 'Alias='.$dbo->q($alias);
						if ($parentId)
							$where .= ' AND ParentId='.$parentId;

						$item = $dbo
							->setQuery('SELECT id, Link FROM &__menu_item WHERE '.$where)
							->loadAssoc();

						if (!$item)
							$this->error(404, ['type'=>'http', 'msg'=>'Page Not Found']);

						$parentId = $item['id'];
					// }

					$items[] = $item;
				}
			}

			$activeItem = end($items);
		}

		if ($activeItem)
			$menu->setActive($activeItem['id']);
		
		if (!$this->input->get('option', null))
		{
			if ($activeItem['Link'])
				parse_str($activeItem['Link'], $vars);

			foreach ($vars as $key => $value)
				$this->input->set($key, $value);
		}

		$this->authorise();
	}

	private function authorise()
	{
		$this->pluginManager()->run('system', 'beforeAuthorise');

		$item = $this->getMenu()->getActive();

		if ($item)
		{
			$user = $this->getUser();

			if (!in_array($item['LevelId'], $user->data['LevelsId']))
			{
				$this->redirect('/?red='.urlencode($_SERVER['HTTP_REFERER']));
			}
		}
	}

	public function getSession()
	{
		if (!$this->session)
		{
			if (!isset($this->session))
				$this->session = new \bs\libraries\session\Session();
		}

		return $this->session;
	}

	public function redirect($key)
	{
		// if ($key == '/')
		// {
		// 	// $menu = $this->getMenu();
		// 	// $home = $menu->getHome();

		// 	// $itemId = $home['id'];
		// 	$itemId = $key;
		// }
		// else
		// {
		// 	$itemId = '/test';
		// }

		exit(json_encode(['redirect'=>$key]));
	}

	public function getUser($id = 0)
	{
		if (!isset($this->users[$id]))
			$this->users[$id] = new \bs\libraries\user\User($id);

		return $this->users[$id];
	}

	public function clearUsers()
	{
		$this->users = [];
	}

	public function getMenu()
	{
		if (!$this->menu)
			$this->menu = new \bs\libraries\menu\Menu();

		return $this->menu;
	}

	private function getController()
	{
		$cgroup = $this->input->get('option'); 
		$branch = $this->input->get('branch');
		$component = explode('.', $this->input->get('task'))[0];

		$this->clientPath = 'components/'.$cgroup.'/actors/'.$component;
		$this->setDep($this->clientPath);

		$path  = PATH_ROOT .'/components/'.$cgroup.'/controllers/';
		$path .= $branch ? str_replace('.', '/', $branch).'/' : '';
		$path .= $component.'.php';

		$className  = '\bs\components\\'.$cgroup.'\controllers\\';
		$className .= $branch ? str_replace('.', '\\', $branch).'\\' : '';
		$className .= ucfirst(($component=='list'?'lst':$component));

		$this->includeDepsComponent('basic', $cgroup);
		include_once $path;
		$object = new $className();

		return $object;
	}

	public function getComponent($group, $name, $branch = null)
	{
		$pathMain = PATH_ROOT.'/components/'.$group.'/component.php';
		
		if (file_exists($pathMain))
		{
			include_once $pathMain;
			$class = '\bs\components\\'.$group.'\Component';
		}
		else
		{
			$class = '\bs\libraries\mvc\Component';
		}

		$this->includeDepsComponent('basic', $group);
		return new $class($group, $name, $branch);
	}

	public function includeExt($type, $name, $path = null, $format = null, $space = null, $isglobal = false)
	{
		$class = false;
		$fpath = null;

		if (is_array($format))
		{
			$fpath = $format[0];
			$format = $format[1] ?? null;
		}

		$pathf  = PATH_ROOT;
		$pathf .= $space ? '/spaces/'.$space : '';
		$pathf .= $isglobal ? '/globals' : '';
		$pathf .= '/'.$type.'s';
		$pathf .= $path ? '/'.str_replace('.', '/', $path) : '';
		$pathf .= '/'.$name;
		$pathf .= $fpath ? '/'.str_replace('.', '/', $fpath) : '';
		$pathf .= '/'.$name;
		$pathf .= $format ? '.'.$format : '';
		$pathf .= '.php';

		if (file_exists($pathf))
		{
			if ($type == 'plugin')
			{
				$group = explode('.', $path)[0];
				$this->includeDepsComponent($type, $group);
			}

			if (($fpath and strpos($fpath, 'params') !== false) or $format == 'params')
				include_once PATH_ROOT.'/libraries/bs/params.php';
			
			include_once $pathf;

			$class  = $space ? 'spaces\\'.$space.'\\' : '';
			$class .= $isglobal ? 'globals\\' : '';
			$class .= $type.'s';
			$class .= $path ? '\\'.str_replace('.', '\\', $path) : '';
			$class .= $fpath ? '\\'.str_replace('.', '', $fpath) : '\\';
			$class .= $format ? ucfirst(str_replace('.', '', $format)) : '';
			$class .= ucfirst($name);
			$class  = 'bs\\'.$class;
		}

		return $class;
	}

	private function includeDepsComponent($type, $group)
	{
		if (!isset($this->initializedComponents[$type][$group]))
		{
			$this->initializedComponents[$type][$group] = true;
			$root = PATH_ROOT.'/components/'.$group;

			if ($type == 'basic')
			{
				$arr = [
					0 => $group.'.php',
					1 => 'model.php',
					2 => 'view.php'
				];
			}
			elseif ($type == 'plugin')
			{
				$arr = [
					0 => 'event/plugin.php'
				];
			}

			foreach ($arr as $item) 
			{
				$pathf = $root.'/'.$item;
				if (file_exists($pathf))
					include $pathf;
			}
		}
	}

	public function getService($cgroup, $name)
	{
		$key = $cgroup.'.'.$name;

		if (!isset($this->service[$key]))
		{
			$path = PATH_ROOT .'/components/'.$cgroup.'/service/'.$name.'.php';
			$className = '\bs\components\\'.$cgroup.'\service\\'.ucfirst($name);

			include $path;
			$this->service[$key] = new $className();
			$this->setDep('components/'.$cgroup.'/service/'.$name);
		}

		return $this->service[$key];
	}

	public function getCtrl($group, $key)
	{
		$table = $this->getService($group, 'helper')->getTable($key);
		$id = $table->getId();

		if (!isset($this->ctrl[$group][$id]))
		{
			$tname = $table->getData()['Name'];

			// main
			$pathM = PATH_ROOT.'/components/'.$group.'/ctrl.php';
			$class = '\bs\components\\'.$group.'\Ctrl';

			if (file_exists($pathM))
				include_once $pathM;

			// entity
			$pathE = PATH_ROOT.'/plugins/'.$group.'/entity/'.$tname.'/'.$tname.'.ctrl.php';

			if (file_exists($pathE))
			{
				include_once $pathE;
				$class = '\bs\plugins\\'.$group.'\entity\Ctrl'.ucfirst($tname);
			}

			$ctrl = new $class($id);
			$this->ctrl[$group][$id] = $ctrl;
		}

		return $this->ctrl[$group][$id];
	}

	public function getPagePluginManager()
	{
		if (!$this->pagePluginManager)
		{
			$activetmpl = $this->getCtrl('fabrik', 'builder_tmpl')->getActive();
			$model = $this->getComponent('builder', 'page')->getModel();
			$model->setId($activetmpl['id']);

			$this->pagePluginManager = $model->getPluginManager();
		}

		return $this->pagePluginManager;
	}

	public function setUpdateModule($id)
	{
		$moduleView = $this->getService('module', 'helper')->getModule($id)->getComponent()->getView();

		$this->updateModules[] = [
			'id' => $id,
			'data'	=> $moduleView->getData()
		];
	}

	public function setData($key, $value)
	{
		$keyArr = explode('.', $key);
		$data = &$this->data;

		foreach ($keyArr as $i => $key) 
		{
			if (!isset($data[$key]))
				$data[$key] = [];

			$data = &$data[$key];
		}

		$data = $value;
	}

	public function setDep($path)
	{
		if (file_exists(PATH_CLIENT.'/'.$path.'.js'))
		{
			if (!in_array($path, $this->dep))
				$this->dep[] = $path;
		}
	}

	public function pluginManager()
	{
		if (!$this->plgManager)
			$this->plgManager = new \bs\libraries\event\Dispatcher();

		return $this->plgManager;
	}

	public function getSpace()
	{
		if (!$this->space)
			$this->space = new \bs\libraries\space\Space();

		return $this->space;
	}

	public function error($code, $data)
	{
		$result = [
			'error' => 1, 
			'code' => $code, 
			'msg' => $this->errorMsg[$code],
			'isdev' => $this->isdev
		];

		if ($this->isdev)
		{
			$sys_msg = $data['msg'];
			if ($data['type'] == 'sql')
				$sys_msg .= '<br>'.$data['query'];

			$result['system_msg'] = $sys_msg;
			$result['backtrace'] = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		}

		// write log
		if ($data['type'] == 'sql')
		{
			$pathf = PATH_ROOT .'/logs/sql/'.date('y.m.d').'.txt';

			$log = date('h:i:s'). "\n" . $data['msg'] ."\n". $data['query'];
			$log = file_exists($pathf) ? "\n\n".$log : $log;

			$handle = fopen($pathf, 'a');
			fwrite($handle, $log);
		}

		exit(json_encode($result));
	}

	public function isMobile()
	{
		$ismobile = false;


		// if ($_SERVER['REMOTE_ADDR'] == '91.237.182.241')
		// {
		// 	$ismobile = true;
		// }
		
		$useragent = $_SERVER['HTTP_USER_AGENT'];

		if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))
			$ismobile = true;

		return $ismobile;
	}
}