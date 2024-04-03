<?php
namespace bs\plugins\fabrik\entity;
defined('EXE') or die('Access');

include_once PATH_ROOT .'/components/fabrik/event/pluginForm.php';
use \bs\components\fabrik\event\PluginForm;

class FormInvoice_tmpl extends PluginForm
{
	public function onElementParams($el)
	{
    if ($el->getName() == 'Name')
			$el->setParam('form_edit', false);
	}
}