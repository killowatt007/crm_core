<?php
namespace bs\plugins\fabrik\statical;
defined('EXE') or die('Access');

include_once PATH_ROOT .'/components/fabrik/event/pluginForm.php';
use \bs\components\fabrik\event\PluginForm;

class FormHistory extends PluginForm
{
	public function onBeforeGetData()
	{
		$model = $this->getModel();

		if ($model->getModuleAlias() == 'history')
		{
			$isdelete = $this->app->input->get('stream.history.isdeleted');

			if ($isdelete)
			{
				$tn = $model->getTableName();
				$model->setTableName('his_'.$tn);	
			}
		}
	}

	public function onElementParams($el)
	{
		$model = $this->getMOdel();

		if ($model->getModuleAlias() == 'history')
		{
			$el->setParam('form_edit', false);
		}
	}

	public function onActions($args)
	{
		$model = $this->getMOdel();

		if ($model->getModuleAlias() == 'history')
		{
			unset($args->data['actions']['save']);
		}
	}

  public function onAfterStore()
  {
  	$model = $this->getModel();

  	if ((int)$model->getParam('history'))
    	$this->history();
  }

  public function history()
  {
    $model = $this->getModel();

    if (!$model->isNewRecord())
    {
      $rowid = $this->getCV('id');

      $arr1 = $model->getOrigData();
      $arr2 = $this->getCtrl()->select('*', $rowid);

      if (isset($arr1['DateUpdate']))
      {
	      unset($arr1['DateUpdate']);
	      unset($arr2['DateUpdate']);
      }

      if ($arr1 != $arr2)
      {
        $dbo = $model->getDBO();
        $elements = $model->getElements();
        $tn = $model->getTable()->getData()['Name'];
        $origData = $model->getOrigData();
        $query = '';
        $set = '';

        $jdata = [];
				$jquery = '';
				foreach ($elements as $element) 
				{
					if ($element->getType() == 'databasejoin')
					{
						$jd = $model->getJoinData($element);
						$elname = $element->getName();
						$value = (int)$origData[$elname];

						if ($value)
						{
							$jquery .= $jquery ? ' UNION ' : '';
							$jquery .= '(SELECT '.$jd['elVal'].' AS value, "'.$elname.'" AS element FROM &__'.$jd['tn'].' WHERE '.$jd['elKey'].'='.$value.')';
						}
					}
				}

				if ($jquery)
    			$jdata = $dbo->setQuery($jquery)->loadAssocList('element');

        foreach ($origData as $name => $value) 
        {
          if ($name != 'id')
          {
            $set .= $set ? ', ' : '';
            $set .= $name.'='.$dbo->q($value); 
          }
        }

        foreach ($jdata as $name => $row) 
          $set .= ', '.$name.'_j='.$dbo->q($row['value']); 

        $set .= ', HIsDelete=0, HRowId='.$this->getCV('id').', HDateCreate='.$dbo->q(date('Y-m-d H:i:s'));

        $query .= 'INSERT INTO &__his_'.$tn;
        $query .= ' SET '.$set;

        $dbo->setQuery($query)->execute();
      }
    }
  }
}


