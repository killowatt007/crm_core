<?php
namespace bs\spaces\cws\domain\entity;
defined('EXE') or die('Access');

use bs\components\fabrik\domain\Entity;

class Product extends Entity
{
	public function afterInit()
	{
		if ($this->isNew())
		{
		}


		// \F::getDBO()->setQuery('DROP DATABASE ad_main')->execute();
		// \F::getDBO()->setQuery('DROP DATABASE test_main')->execute();
		// \F::getDBO()->setQuery('DROP DATABASE test4_main')->execute();
		// \F::getDBO()->setQuery('DROP DATABASE test5_main')->execute();
		// \F::getDBO()->setQuery('DROP DATABASE test6_main')->execute();

		// \F::getDBO()->setQuery('DROP DATABASE ad_main')->execute();
		// \F::getDBO()->setQuery('DROP DATABASE np_main')->execute();

		
	}
}


