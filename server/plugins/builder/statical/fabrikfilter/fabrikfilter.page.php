<?php
namespace bs\plugins\builder\statical;
defined('EXE') or die('Access');

use bs\libraries;

class PageFabrikfilter extends libraries\event\PluginModel
{
	public function onAfterAddonData($addon, $args)
	{
		if ($addon->getModel()->getParam('name') == 'module')
		{
			$data = $args->data['opts'];

			if ($data['branch'] == 'fabrik.filter')
			{
				$result = [
					'fields' => [],
					'relatedModules' => $data['opts']['relatedModules']
				];

				$filter = $this->app->getComponent('module', 'filter', 'fabrik')->getModel($data['id']);

				foreach ($filter->getFields() as $field) 
				{
					$fieldData = [
						'id' => $field['id'],
						'label' => $field['Label'],
						'type' => $field['Type'],
						'name' => $field['Name'],
						'display' => ($field['Params']['display'] ?? 1)
					];

					// fabrik
					if ($field['Type'] == 'fabrik')
					{
						$options = [];
						$parentsField = false;
						$this->app->setDep('components/field/actors/list');

						if (!isset($field['search']))
						{
							$sqlwhere = $field['Params']['filter'] ?? false;

							if ($sqlwhere)
							{
								$parentsField = [];
								foreach ($sqlwhere as $row) 
								{
									if ($row['type'] == 'value' and $row['value'] == 'filterfield')
										$parentsField[] = $row['filter_fieldid'];
								}
							}
						}

						$options = $filter->getFabrikOptions($field['id']);

						$fieldData['bname'] = 'list';
						$fieldData['parentsField'] = $parentsField;
						$fieldData['isapply'] = ($field['Params']['isapply'] ?? 1);
						$fieldData['options'] = $options;
						$fieldData['issearch'] = isset($field['search']);
					}

					// input
					elseif ($field['Type'] == 'input')
					{
						$this->app->setDep('components/field/actors/field');
					}

					// date
					elseif ($field['Type'] == 'date')
					{
						$this->app->setDep('components/field/actors/date');
					}

					// add
					elseif ($field['Type'] == 'add')
					{
						$fieldData['moduleid'] = $field['Params']['moduleid'];
					}

					$fieldData['value'] = $filter->getFieldValue($field['id']);
					$result['fields'][] = $fieldData;
				}

				$args->data['opts']['opts'] = $result;
			}
		}
	}
}



