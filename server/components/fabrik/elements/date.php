<?php
namespace bs\components\fabrik;
defined('EXE') or die('Access');

class ElementDate extends Element
{
	public function getDefault($i = 0)
	{
		if (!isset($this->default[$i]))
		{
			if (parent::getDefault($i) === null)
			{
				if ($this->getParam('defaulttotoday') and $this->isEdit())
					$this->setDefault(date('Y-m-d H:i:s'), $i);
			}
		}
		
		return $this->default[$i];
	}
}