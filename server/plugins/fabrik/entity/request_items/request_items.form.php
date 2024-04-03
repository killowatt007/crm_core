<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

include_once PATH_ROOT .'/components/fabrik/event/pluginForm.php';
use \bs\components\fabrik\event\PluginForm;

class FormRequest_items extends PluginForm
{
	public function onAfterData($view, $args)
	{
		// equipment
		$roleid = $this->app->getUser()->data['RoleId'];
		$status = $this->getCV('StatusId');

		$r_master = 4;
		$s_pending = 15;
		$s_ready = 17;

		$args->data['status'] = $this->getCV('StatusId');
		$args->data['role'] = $roleid;

		if ($status == $s_pending)
		{
			if ($roleid == $r_master)
			{
				$equipmentGroupItems = $this->getCtrl('equipment_items')->getGroupItems();
				$args->data['equipmentGroupItems'] = $equipmentGroupItems;
			}
		}
		elseif ($status == $s_ready)
		{
			$requestEquipment = $this->getCtrl('request_equipment')->selectq('
				SELECT 
					t0.Quantity,
					t0.IsDemount,
					t1.Name,
					t2.Name AS CategoryName
				FROM &__request_equipment t0
				LEFT JOIN &__equipment_items t1 ON (t1.id = t0.EquipmentId)
					LEFT JOIN &__equipment_category t2 ON (t2.id = t1.CategoryId)
				WHERE t0.RequestId='.$this->getCV('id').'
			');

			$args->data['requestEquipment'] = $requestEquipment;
		}
	}

	public function onBeforeStore()
	{
		$model = $this->getModel();
		$submit = $model->getSubmit();
		$s_pending = 15;
		$s_ready = 17;
		$s_cancel = 18;

		$this->updFD('BaseId', $this->getCtrl('pick_items')->getActiveBase());

		// send
		if ($submit == 'send')
		{
			$this->updFD('StatusId', $s_pending);
			$this->updFD('DateInprocess', date('Y-m-d H:i:s'));
		}

		// ready
		if ($submit == 'ready')
		{
			$this->updFD('StatusId', $s_ready);
			$this->updFD('DateCompleted', date('Y-m-d H:i:s'));

			$this->equipmentStore();
		}

		// cancel
		if ($submit == 'cancel')
		{
			$this->updFD('StatusId', $s_cancel);
			$this->updFD('DateCompleted', date('Y-m-d H:i:s'));
		}
	}

	private function equipmentStore()
	{
		$equipment = $this->app->input->get('formData.equipment', null);

		if ($equipment)
		{
			$aStaffId = $this->getCV('AssignedToStaffId');
			$inventoryCtrl = $this->getCtrl('inventory_items');

			foreach ($equipment as $item) 
			{
				$dbo = \F::getDBO();
				$eqCtrl = $this->getCtrl('equipment_items');
				$eq = $eqCtrl->select('*', $item['ItemId']);

				// остаток мастеров
				$inventory = $inventoryCtrl->select('id, Quantity', 'ItemId='.$item['ItemId'].' AND IsDemount='.$item['IsDemount'].' AND StaffId='.$aStaffId)[0] ?? null;

				if ($inventory)
				{
					if (!(int)$item['IsDemount'])
						$newQ = $inventory['Quantity'] - $item['Quantity'];
					else
						$newQ = $inventory['Quantity'] + $item['Quantity'];

					$inventoryCtrl->store([
						'Quantity' => $newQ
					], $inventory['id']);
				}
				else
				{
					if (!(int)$item['IsDemount'])
						$newQ = -1*$item['Quantity'];
					else
						$newQ = $item['Quantity'];

					$inventoryCtrl->store([
						'StaffId' 		=> $aStaffId,
						'CategoryId' 	=> $eq['CategoryId'],
						'ItemId' 			=> $item['ItemId'],
						'IsDemount' 	=> $item['IsDemount'],
						'Quantity' 		=> $newQ
					]);
				}

				// // остаток склад
				// if (!$item['IsDemount'])
				// 	$eqCtrl->chengeQuantity($eq['id'], $item['Quantity'], $this->getCV('id'));

				// заявка
				$this->getCtrl('request_equipment')->store([
					'EquipmentId' 	=> $eq['id'],
					'Data' 					=> json_encode($eq),
					'Price' 				=> $eqCtrl->getPrice($eq['id']),
					'Quantity' 			=> $item['Quantity'],
					'IsDemount'			=> $item['IsDemount'],
					'RequestId'			=> $this->getCV('id')
				]);
			}
		}
	}

	public function onAfterStore()
	{
		$model = $this->getModel();
		$submit = $model->getSubmit();

		// send
		if ($submit == 'send')
		{
			$this->sendTelegramMaster();
		}
	}

	private function sendTelegramMaster()
	{
		$model = $this->getModel();

		$masterid = $this->getCV('AssignedToStaffId');
		$chatData = $this->getCtrl('telegram_chat')->select('id', 'StaffId='.$masterid)[0] ?? null;

		if ($chatData)
		{
			$rowId = $model->getRowId();
			$number = 'ЗК'.str_pad($rowId, 6, 0, STR_PAD_LEFT);

			$info = $this->getClientInfo();

			$text = '
<a href="'.$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/master-requests?fabrik[entity]=37&fabrik[rowid]='.$rowId.'"><u><b>Заявка #'.$number.'</b></u></a>
ФИО: <b>'.$info['FIO'].'</b>
Счет: <b>'.$info['id'].'</b>
			';

			$this->getCtrl('telegram_msg')->store([
				'ChatId' => $chatData['id'],
				'Text' => $text
			]);
		}
	}

	private function sendTelegramOperator()
	{
		$chatData = $this->getCtrl('telegram_chat')->select('id', 'GroupTypeId=67')[0];
		$info = $this->getClientInfo();

		$text = '
<a href="https://mirdomofon.burnsoft.ru/clients"><u><b>Заявка #'.$this->getCV('Number').'</b></u></a>
Статус: <b>Выполнено</b>
ФИО: <b>'.$info['FIO'].'</b>
Счет: <b>'.$info['id'].'</b>
Описание: <b>'.$this->getCV('Description').'</b>
		';

		$this->getCtrl('telegram_msg')->store([
			'ChatId' => $chatData['id'],
			'Text' => $text
		]);
	}

	public function onElementDefault($el, $i)
	{
		// AssignedFromStaffId
		if ($el->getName() == 'AssignedFromStaffId')
		{
			$staff = $this->getCtrl('staff')->select('id', 'UserId='.$this->app->getUser()->data['id'])[0];
			$el->setValue($staff['id'], $i);
		}
	}

	public function onElementValue($el, $i)
	{
		if ($el->getName() == 'ContractCalc')
		{
			$info = $this->getClientInfo();

			if ($info)
			{
				$el->setValue($info['ContractId'], $i);
			}
		}

		if ($el->getName() == 'ClientAddress')
		{
			$info = $this->getClientInfo();

			if ($info)
			{
				$html = 
					'<a href="#" class="show-address" style="color:#237888;cursor:pointer;">'.$info['address'].'</a>
					<div id="map"></div>';

				$el->setValue($html, $i);	
			}
		}
	}

	private function getClientInfo()
	{
		$data = null;
		$clientid = $this->getCV('ClientId');

		if ($clientid)
			$data = $this->getCtrl('clients')->getInfo($clientid);

		return $data;
	}

	public static function onAjaxGetClientInfo()
	{
		$result = [];
		$app = \F::getApp();
		$clientid = $app->input->get('clientid');
		$data = $app->getCtrl('fabrik', 'clients')->getInfo($clientid);

		$phones = '';
		if ($data['Mobile'])
			$phones .= $data['Mobile'];
		if ($data['Phone'])
			$phones .= ($phones ? ', ' : '').$data['Phone'];

		$result[] = [
			'name' => 'ContractCalc',
			'data' => $data['ContractId']
		];
		$result[] = [
			'name' => 'ClientAddress',
			'data' => $data['address']
		];

		return $result;
	}

	public function onElementParams($el)
	{
		$actPage = $this->getCtrl('builder_tmpl')->getActive();
		$roleid = $this->app->getUser()->data['RoleId'];
		$r_master = 4;

		// master
		if ($roleid == $r_master)
		{
			$el->setParam('form_edit', false);

			// AssignedToStaffId
			if ($el->getName() == 'AssignedToStaffId')
			{
				$el->setParam('display', false);
			}
		}

		// manager_dashboard
		if (in_array($actPage['Alias'], ['manager_dashboard']))
		{
			$el->setParam('form_edit', false);
		}
	}

	public function onActions($args)
	{
		$model = $this->getModel();
		$status = $model->getStatus();
		$actPage = $this->getCtrl('builder_tmpl')->getActive();
		$roleid = $this->app->getUser()->data['RoleId'];
		$r_master = 4;
		$r_operator = 3;

		// operator
		if ($roleid == $r_operator)
		{
			if (in_array($status, ['new', 'draft']))
			{
				$args->data['actions']['send'] = [
					'name' => 'send',
					'position' => 'left',
					'label' => 'Отправить мастеру',
					'color' => 'primary',
					'order' => 10
				];
			}
		}

		// pending
		if (in_array($status, ['pending']))
		{
			$args->data['actions']['ready'] = [
				'name' => 'ready',
				'position' => 'left',
				'label' => 'Выполнено',
				'color' => 'primary',
				'order' => 11
			];

			$args->data['actions']['cancel'] = [
				'name' => 'cancel',
				'position' => 'left',
				'label' => 'Отмена',
				'color' => 'primary',
				'order' => 12
			];
		}

		// master
		if ($roleid == $r_master or $status == 'ready')
		{
			unset($args->data['actions']['save']);
		}

		// manager_dashboard
		if (in_array($actPage['Alias'], ['manager_dashboard']))
		{
			unset($args->data['actions']['save']);
		}
	}
}