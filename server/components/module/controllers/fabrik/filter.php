<?php
namespace bs\components\module\controllers\fabrik;
defined('EXE') or die('Access');

use \bs\libraries\mvc\Controller;

class Filter extends Controller
{
	public function getOptions()
	{
		$filterid = $this->app->input->get('filterid');
		$fieldid = $this->app->input->get('fieldid');

		$filter = $this->app->getComponent('module', 'filter', 'fabrik')->getModel($filterid);
		$options = $filter->getFabrikOptions($fieldid);

		return ['options'=>$options];
	}

	public function apply()
	{
		$data = [];
		$relatedModules = $this->app->input->get('relatedModules', []);

		$moduleid = $this->app->input->get('moduleid');
		foreach ($this->app->input->get('stream.modulesData.'.$moduleid.'.fields', []) as $fid => $val) 
		{
			$val = str_replace('\\', '', $val);
			$this->app->input->set('stream.modulesData.'.$moduleid.'.fields.'.$fid, $val);
			
			setcookie('_ffilter['.$moduleid.']['.$fid.']', $val, time()+(86400*30), '/', $_SERVER['HTTP_HOST']);
		}

		$this->app->getPagePluginManager()->run('fabrikFilterAfteSetCookie');

		$isclear = $this->app->input->get('stream.modulesData.'.$moduleid.'.isclear', 0);
		if (!$isclear)
		{
			foreach ($relatedModules as $moduleId) 
			{
				$comp = $this->app->getService('module', 'helper')->getModule($moduleId)->getComponent();
				$moduleView = $comp->getView();
				
				if ($comp->getBranch() == 'fabrik.list')
					$moduleView->getFabComp()->getModel()->setDisplay(50);

				$this->app->updateModules[] = [
					'id' => $moduleId,
					'data'	=> $moduleView->getData()
				];
			}
		}
		else
		{
			foreach ($relatedModules as $moduleId) 
				$this->app->setUpdateModule($moduleId);
		}

		return $data;
	}

	public function search()
	{
		$value = $this->app->input->get('value');
		$filterid = $this->app->input->get('filterid');
		$fieldid = $this->app->input->get('fieldid');

		$filter = $this->app->getComponent('module', 'filter', 'fabrik')->getModel($filterid);
		$field = $filter->getField($fieldid);

		$entityid = $field['Params']['entityid'];
		$labelid = $field['Params']['labelid'];

		$value = str_replace (['[', '('], '', $value);
		$where = str_replace('{{value}}', $value, $field['search']['where']);

		$flabel = $this->app->getCtrl('fabrik', 'fabrik_field')->select('Name', $labelid);
		$data = $this->app->getCtrl('fabrik', $entityid)->select('id,'.$flabel['Name'], $where);

		$options = $filter->_getFabrikOptions($field, $data);
		
		return ['options'=>$options];
	}
}