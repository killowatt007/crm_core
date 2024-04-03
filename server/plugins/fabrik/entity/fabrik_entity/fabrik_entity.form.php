<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

include_once PATH_ROOT .'/components/fabrik/event/pluginForm.php';
use \bs\components\fabrik\event\PluginForm;

class FormFabrik_entity extends PluginForm
{
	private static $objParams = null;

	public function onElementDefault($element)
	{
		// Params
		if ($element->getName() == 'Params')
			$element->setDefault('{}');

		// StatusId
		if ($element->getName() == 'StatusId')
			$element->setDefault(3);

		// ConnectionId
		if ($element->getName() == 'ConnectionId')
			$element->setDefault(1);
	}

	public function onBeforeStore()
	{
		$model = $this->getModel();
		$params = (object)[];

		if (!$model->isNewRecord())
		{
			$params = $this->getCV('Params');

			if (isset($params['list']['showElementids']))
				$params['list']['showElementids'] = array_values($params['list']['showElementids']);
		}

		$this->updFD('Params', json_encode($params));
	}

	public function onAfterStore()
	{		
		$model = $this->getModel();

		if ($model->isNewRecord())
		{
			$this->sql();
			$this->createFields();
		}
	}

	private function sql()
	{
		$tn = $this->getCV('Name');

		\F::getDbo()
			->setQuery(
				'CREATE TABLE &__'.$tn.' (
				  id INT (11) UNSIGNED NOT NULL AUTO_INCREMENT,
				  PRIMARY KEY (id)
				)'
			)
			->execute();
	}

	private function createFields()
	{
		$model = $this->getModel();
		$CTRLfield = $this->getCtrl('fabrik_field');

		$pkid = $CTRLfield->store([
			'Name' => 'id',
			'Label' => 'id',
			'Type' => 'internalid',
			'EntityId' => $model->getRowId(),
			'StatusId' => 3
		])->getRowId();

		\F::getDbo()->setQuery('UPDATE &__fabrik_entity SET PK='.$pkid.' WHERE id='.$model->getRowId())->execute();
	}

	public function onAfterData($view, $args)
	{
		$model = $this->getModel();

		if (!$this->getModel()->isNewRecord())
		{
			$classParams = $this->app->includeExt('plugin', 'fabrik_entity', 'fabrik.entity', ['params', 'self']);
			$objParams = new $classParams(['entityid'=>$this->getCV('id')]);

			$args->data['params'] = [
				'scheme' => $this->app->getComponent('builder', 'params')->getView($objParams->get())->getData(),
				'data' => (object)[]
			];
		}
	}
}

// ALTER TABLE test CHANGE internalid internalid INT (11) NOT NULL
// ALTER TABLE test DROP PRIMARY KEY
// ALTER TABLE test ADD PRIMARY KEY (id)
// ALTER TABLE test CHANGE id id INT (11) NOT NULL AUTO_INCREMENT