<?php
namespace bs\plugins\builder\entity;
defined('EXE') or die('Access');

/**
 * $version 1.1
 * $deps domofon.invoice.receipt v1.1
 */

use bs\libraries;

class PageContracts extends libraries\event\PluginModel
{
	public function onAfterData($view, $args)
	{
		$baseid = $this->app->getCtrl('fabrik', 'pick_items')->getActiveBase();
		$component = $this->app->getComponent('domofon', 'receipt', 'invoice');

		$data = \F::getDbo()->setQuery('
			SELECT id AS value, Name AS label
			FROM &__districts
			WHERE BaseId='.$baseid.'
			ORDER BY Name ASC
		')->loadAssocList();

		$args->data['invoice']['receipt'] = $component->getView()->getData();
		$args->data['invoice']['districts_options'] = $data;
	}
}