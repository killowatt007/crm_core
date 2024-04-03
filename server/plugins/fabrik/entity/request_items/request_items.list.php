<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

include_once PATH_ROOT .'/components/fabrik/event/pluginList.php';
use \bs\components\fabrik\event\PluginList;

class ListRequest_items extends PluginList
{
	public function onParams()
	{
		$model = $this->getModel();
		$user = $this->app->getUser();
		$roleid = $user->data['RoleId'];
		$actPage = $this->getCtrl('builder_tmpl')->getActive();

		$r_master = 4;

		// request_items
		if ($actPage['Alias'] == 'request_items')
		{
			// master
			if ($roleid == $r_master)
			{
				$showElementids = [];
				foreach ($model->getParam('showElementids') as $row) 
				{
					if ($row['id'] != 280)
						$showElementids[] = $row;
				}

				$model->setParam('showElementids', $showElementids);
			}
		}

		// // remove el for list
		// if ($this->app->isMobile())
		// {
		// 	$model = $this->getModel();
		// 	$showEls = $model->getParam('showElementids');

		// 	// unset($showEls[5], $showEls[6]);

		// 	$model->setParam('showElementids', array_values($showEls));		
		// }
	}

	public function onElementValue($el, $i)
	{
		if ($el->getName() == 'ClientAddress')
		{
			$info = $this->getClientInfo($i);
			$val = $info['address'];

			if (!$this->app->isMobile())
				$val = '<div class="list-address">'.$val .'</div>';

			$el->setValue($val, $i);
		}
	}

	private function getClientInfo($i)
	{
		$clientid = $this->getModel()->getData()[$i]['ClientId'];
		return $this->getCtrl('clients')->getInfo($clientid);
	}

	public function onFilter()
	{
		$where = null;
		$user = $this->app->getUser();
		$roleid = $user->data['RoleId'];
		$activemenu = $this->app->getMenu()->getActive();
		$actPage = $this->getCtrl('builder_tmpl')->getActive();

		$s_pending = 15;
		$s_ready = 17;
		$r_master = 4;

		// request_items
		if ($actPage['Alias'] == 'request_items')
		{
			$where = [];
			$filter = $this->app->getService('fabrik', 'helper')->getFilter();

			$priorityid = $filter->getFieldValue('priority');
			$staffid = $filter->getFieldValue('staff');
			$clientid = $filter->getFieldValue('client');
			$statusid = $filter->getFieldValue('status');
			$date_from = $filter->getFieldValue('date_from');
			$date_to = $filter->getFieldValue('date_to');
			$contractid = $filter->getFieldValue('contract');
			
			if ($contractid)
				$where[] = 't0.ClientId IN (SELECT id FROM &__clients WHERE ContractId='.$contractid.')';

			if ($priorityid)
				$where[] = 't0.PriorityId='.$priorityid;

			// master
			if ($roleid == $r_master)
			{
				$staff = $this->getCtrl('staff')->select('id', 'UserId='.$user->data['id'])[0];
				$where[] = 't0.AssignedToStaffId='.$staff['id'];
			}
			// other
			else
			{
				if ($staffid)
					$where[] = 't0.AssignedToStaffId='.$staffid;
			}

			if ($clientid)
				$where[] = 't0.ClientId='.$clientid;

			// // master
			// if ($roleid == $r_master)
			// {
			// 	$statusid = !$statusid ? [$s_pending, $s_ready] : [$statusid];
			// 	$where[] = 't0.StatusId IN ('.implode(',', $statusid).')';
			// }
			// // other
			// else
			// {
				if ($statusid)
					$where[] = 't0.StatusId='.$statusid;
			// }

			if ($date_from or $date_to)
			{
				$whereDate = '';

				if ($date_from)
					$whereDate = 'date(t0.DateCreate) >= "'.$date_from.'"';

				if ($date_to)
					$whereDate .= ($whereDate ? ' AND ' : '') . 'date(t0.DateCreate) <= "'.$date_to.'"';

				$where[] = '('.$whereDate.')';
			}

			if ($date_to)
				$where[] = 'date(t0.DateCreate) <= "'.$date_to.'"';

			if (!empty($where))
				$where = implode(' AND ', $where);
			else
				$where = 't0.id !=0';
		}

		// operator-clients
		elseif ($activemenu['Alias'] == 'operator-clients')
		{
			$input = $this->app->input;
			$applyFieldName = $input->get('stream.pageData.applyFieldName') ?? 'fio';
			$clientid = 0;

			if ($applyFieldName)
			{
				$filter = $this->app->getService('fabrik', 'helper')->getFilter();
				$clientid = (int)$filter->getFieldValue($applyFieldName);
			}

			$where = 't0.ClientId='.$clientid;
		}

		// master-dashboard
		elseif ($activemenu['Alias'] == 'master-dashboard')
		{
			$staffid = $this->getCtrl('staff')->select('id', 'UserId='.$user->data['id'])[0]['id'];

			$where = 't0.AssignedToStaffId='.$staffid.' AND t0.StatusId=15';
		}

		// operator-dashboard
		elseif ($activemenu['Alias'] == 'operator-dashboard')
		{
			$where = 't0.StatusId=15';
		}

		/*B_BASE*/
		$where .= ($where ? ' AND ' : '') . 't0.BaseId='.$this->getCtrl('pick_items')->getActiveBase();

		if ($where)
			$this->getModel()->where = $where;
	}

	public function onButtons($args)
	{
		$actPage = $this->getCtrl('builder_tmpl')->getActive();

		// manager_dashboard, master_dashboard
		if (in_array($actPage['Alias'], ['manager_dashboard', 'master_dashboard']))
		{
			unset($args->data['buttons']['add']);
		}
	}
}