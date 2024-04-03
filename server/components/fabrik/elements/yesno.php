<?php
namespace bs\components\fabrik;
defined('EXE') or die('Access');

class ElementYesno extends Element
{
	public function getDefault($i = 0)
	{
		if (!isset($this->default[$i]))
		{
			if (parent::getDefault($i) === null)
				$this->setDefault($this->getParam('default', 0), $i);
		}
		
		return $this->default[$i];
	}

	public function getValue($i = 0)
	{
		return (int)parent::getValue($i);
	}
}