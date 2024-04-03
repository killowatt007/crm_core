<?php
namespace bs\components\fabrik;
defined('EXE') or die('Access');

class ElementCalc extends Element
{
	public function getValue($i = 0)
	{
		$this->getModel()->getPluginManager()->run('elementCalcGetValue', [$this, $i]);
		return parent::getValue($i);
	}

	// protected function data()
	// {
	// 	$value = '';
	// 	$model = $this->getModel();
		
	// 	if ($model->getName() == 'Number')
	// 	{
	// 		$value = 'N000000';
			
	// 		// $model = $this->getModel();
	// 		// $row = $model->getData();

	// 		// if ($model->isNewRecord())
	// 		// 	$value = 'N000000';
	// 		// else
	// 		// 	$value = 'N00000'.$row['id'];
	// 	}

	// 	$data['value'] = $value;

	// 	return $data;
	// }
}