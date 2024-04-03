<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

use \bs\libraries\Params;

class ParamsSelfFabrik_field extends Params
{
	public function get()
	{
		$params = null;
		$basicClass = $this->app->includeExt('plugin', 'basic', 'fabrik.statical', ['params.elements', $this->extraArgs['type']]);

		if ($basicClass)
		{
			$basicParams = new $basicClass($this->extraArgs);

			$params = [
				'type' => 'tabs',
				'items' => [
					0 => [
						'label' => 'Basic',
						'name' => 'basic',
						'data' => [
							'type' => 'sections',
							'items' => [
								0 => [
									'size' => 12,
									'data' => [
										'type' => 'fields',
										'view' => 'inline',
										'items' => $basicParams->get()
									]
								]
							]
						]
					]
				]
			];

			if ($this->extraArgs['type'] == 'databasejoin')
			{
				$filterClass = $this->app->includeExt('plugin', 'filter', 'fabrik.statical', ['params.elements', $this->extraArgs['type']]);
				$filterParams = new $filterClass();

				$params['items'][] = [
					'label' => 'Filter',
					'name' => 'filter',
					'data' => [
						'type' => 'sections',
						'items' => [
							0 => [
								'size' => 24,
								'data' => [
									'type' => 'fields',
									'view' => 'inline',
									'items' => $filterParams->get()
								]
							]
						]
					]
				];
			}
		}

		return $params;
	}
}