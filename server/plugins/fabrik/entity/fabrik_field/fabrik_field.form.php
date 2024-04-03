<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

include_once PATH_ROOT .'/components/fabrik/event/pluginForm.php';
use \bs\components\fabrik\event\PluginForm;

class FormFabrik_field extends PluginForm
{
	public function onElementDefault($element)
	{
		// StatusId
		if ($element->getName() == 'StatusId')
			$element->setDefault(3);

		// EntityId
		if ($element->getName() == 'EntityId')
		{
			if ($filter = $this->getFilter('main'))
			{
				$entityid = $filter->getFieldValue('entity');
				$element->setDefault($entityid);
			}
		}
	}

	public function onBeforeStore()
	{
		$model = $this->getModel();
		$params = $this->getCV('Params');

		// temp for dropdown
		if (isset($params['basic']['options']))
			$params['basic']['options'] = array_values($params['basic']['options']);

		$this->updFD('Params', json_encode($params));
	}

	public function onAfterStore()
	{		
		$this->sql();
	}

	private function sql()
	{
		$model = $this->getModel();
		$type = $this->getCV('Type');
		$name = $this->getCV('Name');

		$CTRLentity = $this->getCtrl('fabrik_entity');
		$entity = $CTRLentity->select('Name', $this->getCV('EntityId'));

		$params = json_decode($this->getCV('Params'), true);
		$ismultilist = ($type == 'databasejoin' and $params['basic']['type'] == 'multilist');

		if ($type != 'calc' and !$ismultilist and ($model->isNewRecord() and $type != 'internalid'))
		{
			$sql = null;
			$tn = $entity['Name'];
			
			// internalid
			if ($type == 'internalid')
			{
				$ai = $params['basic']['auto_increment'] ?? 1;
				$datatype = 'INT (11) UNSIGNED NOT NULL' . ($ai ? ' AUTO_INCREMENT' : '');
			}
			// field
			if ($type == 'field' or $type == 'dropdown')
			{
				$datatype = 'VARCHAR (255) NULL';
			}
			// databasejoin
			elseif ($type == 'databasejoin')
			{
				$datatype = 'INT (11) NOT NULL DEFAULT 0';
			}
			// text
			elseif ($type == 'text')
			{
				$datatype = 'TEXT NULL';
			}
			// date
			elseif ($type == 'date')
			{
				$datatype = 'DATETIME NULL';
			}
			// yesno
			elseif ($type == 'yesno')
			{
				$datatype = 'INT (1) NOT NULL DEFAULT 0';
			}

			if ($model->isNewrecord())
			{
				if ($type != 'internalid')
					$sql = 'ALTER TABLE `&__'.$tn.'` ADD `'.$name.'` '.$datatype;
			}
			else
			{
				$origName = $model->getOrigData()['Name'];
				$sql = 'ALTER TABLE `&__'.$tn.'` CHANGE `'.$origName.'` `'.$name.'` '.$datatype;
			}

			if ($sql)
				\F::getDbo()->setQuery($sql)->execute();
		}

		if ($ismultilist)
		{
			if ($model->isNewrecord())
			{
				\F::getDbo()
					->setQuery(
						'CREATE TABLE &__'.$entity['Name'].'_repeat_'.$name.' (
						  `left` int(11) NOT NULL,
						  `right` int(11) NOT NULL
						)'
					)
					->execute();
				}
		}
	}

	public static function onAjaxGetFieldParams()
	{
		$result = ['params'=>null];
		$app = \F::getApp();
		$type = $app->input->get('ftype');
		$rowid = $app->input->get('rowid');

		$fieldClass = $app->includeExt('plugin', 'fabrik_field', 'fabrik.entity', ['params', 'self']);
		$objParams = new $fieldClass(['type'=>$type, 'rowid'=>$rowid]);

		if ($scheme = $objParams->get())
			$result['params'] = $app->getComponent('builder', 'params')->getView($scheme)->getData();

		return $result;
	}
}