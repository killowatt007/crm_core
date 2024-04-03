<?php
namespace bs\spaces\cws\domain\repository;
defined('EXE') or die('Access');

use bs\components\fabrik\domain\Repository;

class Product extends Repository
{
	protected function afterStore($entity)
	{		
		if ($entity->isNew())
		{
			$prefix = $entity->getData()['Prefix'];
			$id = $entity->getData()['id'];
			$name = $entity->getData()['Name'];
			$dbn = $prefix.'_main';

			// create space
			$dboc = \F::getDBO('core');
			$dboc
				->setQuery(
					'INSERT INTO spaces 
					 SET Name='.$dboc->q($name).', 
					 		 Prefix='.$dboc->q($prefix)
				)
				->execute();
			$spaceId = $dboc->insertid();

			// create new DB
			$this->dbo->setQuery('CREATE DATABASE '.$dbn)->execute();

			// update product SpaceId
			$this->dbo->setQuery('UPDATE product SET SpaceId='.$spaceId.' WHERE id='.$id)->execute();

			// set default CHARACTER
			$dbomy = \F::getDBO($prefix);
			$dbomy->setQuery('ALTER DATABASE '.$dbn.' CHARACTER SET utf8 COLLATE utf8_general_ci')->execute();
			$dbomy->setQuery('ALTER DATABASE '.$dbn.' DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci')->execute();

			// create data
			$queryArr = [
				// access_group
				'access_group' => "
					CREATE TABLE `access_group` (
					  `id` int(11) UNSIGNED NOT NULL,
					  `Name` varchar(50) NOT NULL DEFAULT ''
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;
				",
				'access_group_i' => "
					INSERT INTO `access_group` (`id`, `Name`) VALUES
					(1, 'Public'),
					(2, 'Admin');
				",
				'access_group_pr' => "
					ALTER TABLE `access_group`
					  ADD PRIMARY KEY (`id`);
				",
				'access_group_pr_mod' => "
					ALTER TABLE `access_group`
					  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
				",

				// access_level
				'access_level' => "
					CREATE TABLE `access_level` (
					  `id` int(11) UNSIGNED NOT NULL,
					  `Name` varchar(50) NOT NULL DEFAULT ''
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;
				",
				'access_level_i' => "
					INSERT INTO `access_level` (`id`, `Name`) VALUES
					(1, 'Public'),
					(2, 'Admin');
				",
				'access_level_pr' => "
					ALTER TABLE `access_level`
					  ADD PRIMARY KEY (`id`);
				",
				'access_level_pr_mod' => "
					ALTER TABLE `access_level`
					  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
				",

				// access_level_repeat_groupid
				'access_level_repeat_GroupId' => "
					CREATE TABLE `access_level_repeat_GroupId` (
					  `left` int(11) NOT NULL,
					  `right` int(11) NOT NULL
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;
				",
				'access_level_repeat_GroupId_i' => "
					INSERT INTO `access_level_repeat_GroupId` (`left`, `right`) VALUES
					(1, 1),
					(2, 2);
				",

				// builder_tmpl
				'builder_tmpl' => "
					CREATE TABLE `builder_tmpl` (
					  `id` int(11) UNSIGNED NOT NULL,
					  `Name` varchar(100) NOT NULL DEFAULT '',
					  `Data` text NOT NULL,
					  `ParentId` int(11) NOT NULL,
					  `EntityTypeId` int(11) NOT NULL,
					  `StatusId` int(11) NOT NULL
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;
				",
				'builder_tmpl_i' => "
					INSERT INTO `builder_tmpl` (`id`, `Name`, `Data`, `ParentId`, `EntityTypeId`, `StatusId`) VALUES
					(1, 'Dashboard', '[{\"type\":\"row\",\"columns\":[{\"type\":\"column\",\"size\":24,\"data\":[{\"type\":\"addon\",\"name\":\"title\"}]}]}]', 5, 1, 3),
					(4, 'Home', '[{\"type\":\"row\",\"columns\":[{\"type\":\"column\",\"size\":24,\"data\":[{\"type\":\"addon\",\"name\":\"module\",\"moduleId\":4}]}]}]', 11, 1, 3),
					(5, 'LoggedIn Tmpl', '[{\"type\":\"header\",\"data\":{\"left\":[{\"name\":\"module\",\"moduleId\":5}],\"center\":[],\"right\":[{\"name\":\"module\",\"moduleId\":18}]}},{\"type\":\"content\",\"data\":{\"class\":\"product\"}}]\n', 0, 1, 3),
					(6, 'Menu', '[{\"type\":\"row\",\"columns\":[{\"type\":\"column\",\"size\":24,\"data\":[{\"type\":\"addon\",\"name\":\"title\"}]}]},{\"type\":\"row\",\"columns\":[{\"type\":\"column\",\"size\":24,\"data\":[{\"type\":\"addon\",\"name\":\"module\",\"moduleId\":6}]}]},{\"type\":\"row\",\"columns\":[{\"type\":\"column\",\"size\":24,\"data\":[{\"type\":\"addon\",\"name\":\"tabs\",\"addon_items\":[{\"label\":\"General\",\"data\":[{\"type\":\"row\",\"columns\":[{\"type\":\"column\",\"size\":24,\"data\":[{\"type\":\"addon\",\"name\":\"module\",\"moduleId\":7}]}]}]},{\"label\":\"Items\",\"data\":[{\"type\":\"row\",\"columns\":[{\"type\":\"column\",\"size\":24,\"data\":[{\"type\":\"addon\",\"name\":\"module\",\"moduleId\":8}]}]}]}]}]}]}]', 5, 1, 3),
					(7, 'Modules', '[{\"type\":\"row\",\"columns\":[{\"type\":\"column\",\"size\":24,\"data\":[{\"type\":\"addon\",\"name\":\"title\"}]}]},{\"type\":\"row\",\"columns\":[{\"type\":\"column\",\"size\":24,\"data\":[{\"type\":\"addon\",\"name\":\"module\",\"moduleId\":9}]}]},{\"type\":\"row\",\"columns\":[{\"type\":\"column\",\"size\":24,\"data\":[{\"type\":\"addon\",\"name\":\"tabs\",\"addon_items\":[{\"label\":\"Modules\",\"data\":[{\"type\":\"row\",\"columns\":[{\"type\":\"column\",\"size\":24,\"data\":[{\"type\":\"addon\",\"name\":\"module\",\"moduleId\":10}]}]}]}]}]}]}]', 5, 1, 3),
					(8, 'Fabrik Entity', '[{\"type\":\"row\",\"columns\":[{\"type\":\"column\",\"size\":24,\"data\":[{\"type\":\"addon\",\"name\":\"title\"}]}]},{\"type\":\"row\",\"columns\":[{\"type\":\"column\",\"size\":24,\"data\":[{\"type\":\"addon\",\"name\":\"module\",\"moduleId\":11}]}]},{\"type\":\"row\",\"columns\":[{\"type\":\"column\",\"size\":24,\"data\":[{\"type\":\"addon\",\"name\":\"tabs\",\"addon_items\":[{\"label\":\"Entity\",\"data\":[{\"type\":\"row\",\"columns\":[{\"type\":\"column\",\"size\":24,\"data\":[{\"type\":\"addon\",\"name\":\"module\",\"moduleId\":12}]}]}]}]}]}]}]', 5, 1, 3),
					(9, 'Fabrik Property', '[{\"type\":\"row\",\"columns\":[{\"type\":\"column\",\"size\":24,\"data\":[{\"type\":\"addon\",\"name\":\"title\"}]}]},{\"type\":\"row\",\"columns\":[{\"type\":\"column\",\"size\":24,\"data\":[{\"type\":\"addon\",\"name\":\"module\",\"moduleId\":13}]}]},{\"type\":\"row\",\"columns\":[{\"type\":\"column\",\"size\":24,\"data\":[{\"type\":\"addon\",\"name\":\"tabs\",\"addon_items\":[{\"label\":\"Fields\",\"data\":[{\"type\":\"row\",\"columns\":[{\"type\":\"column\",\"size\":24,\"data\":[{\"type\":\"addon\",\"name\":\"module\",\"moduleId\":14}]}]}]},{\"label\":\"Calculations\",\"data\":[{\"type\":\"row\",\"columns\":[{\"type\":\"column\",\"size\":24,\"data\":[{\"type\":\"addon\",\"name\":\"module\",\"moduleId\":17}]}]}]}]}]}]}]', 5, 1, 3),
					(10, 'Template Page', '[{\"type\":\"row\",\"columns\":[{\"type\":\"column\",\"size\":24,\"data\":[{\"type\":\"addon\",\"name\":\"title\"}]}]},{\"type\":\"row\",\"columns\":[{\"type\":\"column\",\"size\":24,\"data\":[{\"type\":\"addon\",\"name\":\"module\",\"moduleId\":15}]}]},{\"type\":\"row\",\"columns\":[{\"type\":\"column\",\"size\":24,\"data\":[{\"type\":\"addon\",\"name\":\"tabs\",\"addon_items\":[{\"label\":\"Templates\",\"data\":[{\"type\":\"row\",\"columns\":[{\"type\":\"column\",\"size\":24,\"data\":[{\"type\":\"addon\",\"name\":\"module\",\"moduleId\":16}]}]}]}]}]}]}]', 5, 1, 3),
					(11, 'LoggedOut Tmpl', '[{\"type\":\"header\",\"data\":{\"left\":[{\"name\":\"module\",\"moduleId\":19}],\"center\":[],\"right\":[{\"name\":\"module\",\"moduleId\":18}]}},{\"type\":\"content\",\"data\":{\"class\":\"public\"}}]', 0, 1, 3);
				",
				'builder_tmpl_pr' => "
					ALTER TABLE `builder_tmpl`
					  ADD PRIMARY KEY (`id`);
				",
				'builder_tmpl_pr_mod' => "
					ALTER TABLE `builder_tmpl`
					  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
				",

				// fabrik_connection
				'fabrik_connection' => "
					CREATE TABLE `fabrik_connection` (
					  `id` int(11) UNSIGNED NOT NULL,
					  `Host` varchar(100) DEFAULT '',
					  `User` varchar(100) DEFAULT '',
					  `Password` varchar(100) DEFAULT '',
					  `DB` varchar(100) DEFAULT '',
					  `Description` varchar(255) DEFAULT ''
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;
				",
				'fabrik_connection_i' => "
					INSERT INTO `fabrik_connection` (`id`, `Host`, `User`, `Password`, `DB`, `Description`) VALUES
					(1, 'localhost', 'root', '11111', 'main', '');
				",
				'fabrik_connection_pr' => "
					ALTER TABLE `fabrik_connection`
					  ADD PRIMARY KEY (`id`);
				",
				'fabrik_connection_pr_mod' => "
					ALTER TABLE `fabrik_connection`
					  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
				",

				// fabrik_entity
				'fabrik_entity' => "
					CREATE TABLE `fabrik_entity` (
					  `id` int(11) UNSIGNED NOT NULL,
					  `Name` varchar(100) DEFAULT '',
					  `PK` int(11) NOT NULL DEFAULT '0',
					  `ConnectionId` int(11) NOT NULL DEFAULT '0',
					  `StatusId` int(11) NOT NULL DEFAULT '0',
					  `Params` text NOT NULL,
					  `IsSystem` int(1) NOT NULL DEFAULT '0'
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;
				",
				'fabrik_entity_i' => "
					INSERT INTO `fabrik_entity` (`id`, `Name`, `PK`, `ConnectionId`, `StatusId`, `Params`, `IsSystem`) VALUES
					(2, 'menu', 6, 1, 3, '{}', 0),
					(3, 'menu_item', 8, 1, 3, '{}', 0),
					(4, 'access_level', 20, 1, 3, '{}', 0),
					(5, 'builder_tmpl', 22, 1, 3, '{}', 0),
					(6, 'module', 27, 1, 3, '{}', 0),
					(7, 'fabrik_entity', 31, 1, 3, '{}', 1),
					(8, 'fabrik_field', 35, 1, 3, '{}', 1),
					(9, 'status', 41, 1, 3, '{}', 0),
					(22, 'access_group', 55, 1, 3, '{}', 0),
					(23, 'access_level', 57, 1, 3, '{}', 0),
					(24, 'user', 59, 1, 3, '{}', 0),
					(25, 'user_role', 63, 1, 3, '{}', 0),
					(26, 'vertical_menu', 65, 1, 3, '{}', 0),
					(27, 'fabrik_connection', 74, 1, 3, '{}', 0);
				",
				'fabrik_entity_pr' => "
					ALTER TABLE `fabrik_entity`
					  ADD PRIMARY KEY (`id`);
				",
				'fabrik_entity_pr_mod' => "
					ALTER TABLE `fabrik_entity`
					  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
				",

				// fabrik_field
				'fabrik_field' => "
					CREATE TABLE `fabrik_field` (
					  `id` int(11) UNSIGNED NOT NULL,
					  `Label` varchar(100) NOT NULL DEFAULT '',
					  `Name` varchar(100) NOT NULL DEFAULT '',
					  `Type` varchar(100) NOT NULL DEFAULT '',
					  `EntityId` int(11) NOT NULL,
					  `StatusId` int(11) NOT NULL,
					  `Params` text NOT NULL
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;
				",
				'fabrik_field_i' => "
					INSERT INTO `fabrik_field` (`id`, `Label`, `Name`, `Type`, `EntityId`, `StatusId`, `Params`) VALUES
					(6, 'id', 'id', 'internalid', 2, 3, ''),
					(7, 'Name', 'Name', 'field', 2, 3, ''),
					(8, 'id', 'id', 'internalid', 3, 3, ''),
					(9, 'Name', 'Name', 'field', 3, 3, ''),
					(10, 'Alias', 'Alias', 'field', 3, 3, ''),
					(11, 'Type', 'Type', 'field', 3, 3, ''),
					(12, 'Link', 'Link', 'field', 3, 3, ''),
					(13, 'Parent', 'ParentId', 'databasejoin', 3, 3, '{\"space\":".$spaceId.",\"connection\":1,\"join_entity\":3,\"join_key\":8,\"join_val\":9}'),
					(14, 'Menu', 'MenuId', 'databasejoin', 3, 3, '{\"space\":".$spaceId.",\"connection\":1,\"join_entity\":2,\"join_key\":6,\"join_val\":7}'),
					(15, 'Level', 'LevelId', 'databasejoin', 3, 3, '{\"space\":".$spaceId.",\"connection\":1,\"join_entity\":4,\"join_key\":20,\"join_val\":21}'),
					(16, 'Home', 'IsHome', 'yesno', 3, 3, ''),
					(17, 'Start', 'IsStart', 'yesno', 3, 3, ''),
					(18, 'Icon', 'Icon', 'field', 3, 3, ''),
					(19, 'Display', 'Display', 'field', 3, 3, ''),
					(20, 'id', 'id', 'internalid', 4, 3, ''),
					(21, 'Name', 'Name', 'field', 4, 3, ''),
					(22, 'id', 'id', 'internalid', 5, 3, ''),
					(23, 'Name', 'Name', 'field', 5, 3, ''),
					(24, 'Data', 'Data', 'text', 5, 3, ''),
					(25, 'Parent', 'ParentId', 'databasejoin', 5, 3, '{\"space\":".$spaceId.",\"connection\":1,\"join_entity\":5,\"join_key\":22,\"join_val\":23}'),
					(26, 'Entity Type', 'EntityTypeId', 'field', 5, 3, ''),
					(27, 'id', 'id', 'internalid', 6, 3, ''),
					(28, 'Name', 'Name', 'field', 6, 3, ''),
					(29, 'Template', 'TmplId', 'databasejoin', 6, 3, '{\"space\":".$spaceId.",\"connection\":1,\"join_entity\":5,\"join_key\":22,\"join_val\":23}'),
					(30, 'Params', 'Params', 'text', 6, 3, ''),
					(31, 'id', 'id', 'internalid', 7, 3, ''),
					(32, 'Name', 'Name', 'field', 7, 3, ''),
					(33, 'PK', 'PK', 'databasejoin', 7, 3, '{\"space\":".$spaceId.",\"connection\":1,\"join_entity\":8,\"join_key\":35,\"join_val\":37}'),
					(34, 'Params', 'Params', 'text', 7, 3, ''),
					(35, 'id', 'id', 'internalid', 8, 3, ''),
					(36, 'Label', 'Label', 'field', 8, 3, ''),
					(37, 'Name', 'Name', 'field', 8, 3, ''),
					(38, 'Type', 'Type', 'field', 8, 3, ''),
					(39, 'Entity', 'EntityId', 'databasejoin', 8, 3, '{\"space\":".$spaceId.",\"connection\":1,\"join_entity\":7,\"join_key\":31,\"join_val\":32}'),
					(40, 'Params', 'Params', 'text', 8, 3, ''),
					(41, 'id', 'id', 'internalid', 9, 3, ''),
					(42, 'Name', 'Name', 'field', 9, 3, ''),
					(43, 'Status', 'StatusId', 'databasejoin', 7, 3, '{\"space\":".$spaceId.",\"connection\":1,\"join_entity\":9,\"join_key\":41,\"join_val\":42}'),
					(44, 'Status', 'StatusId', 'databasejoin', 8, 3, '{\"space\":".$spaceId.",\"connection\":1,\"join_entity\":9,\"join_key\":41,\"join_val\":42}'),
					(45, 'Module', 'Module', 'field', 6, 3, ''),
					(46, 'Status', 'StatusId', 'databasejoin', 5, 3, '{\"space\":".$spaceId.",\"connection\":1,\"join_entity\":9,\"join_key\":41,\"join_val\":42}'),
					(55, 'id', 'id', 'internalid', 22, 3, '{}'),
					(56, 'Name', 'Name', 'field', 22, 3, '{}'),
					(57, 'id', 'id', 'internalid', 23, 3, '{}'),
					(58, 'Name', 'Name', 'field', 23, 3, '{}'),
					(59, 'id', 'id', 'internalid', 24, 3, '{}'),
					(60, 'Name', 'Name', 'field', 24, 3, '{}'),
					(61, 'Password', 'Password', 'field', 24, 3, '{}'),
					(62, 'Rore', 'RoleId', 'databasejoin', 24, 3, '{\"space\":".$spaceId.",\"connection\":1,\"join_entity\":25,\"join_key\":63,\"join_val\":64}'),
					(63, 'id', 'id', 'internalid', 25, 3, '{}'),
					(64, 'Name', 'Name', 'field', 25, 3, '{}'),
					(65, 'id', 'id', 'internalid', 26, 3, '{}'),
					(66, 'Name', 'Name', 'field', 26, 3, '{}'),
					(67, 'Menu', 'MenuId', 'databasejoin', 26, 3, '{\"space\":".$spaceId.",\"connection\":1,\"join_entity\":2,\"join_key\":6,\"join_val\":7}'),
					(68, 'Role', 'RoleId', 'databasejoin', 26, 3, '{\"space\":".$spaceId.",\"connection\":1,\"join_entity\":25,\"join_key\":63,\"join_val\":64}'),
					(74, 'id', 'id', '', 27, 3, '{}'),
					(75, 'Host', 'Host', 'field', 27, 3, '{}'),
					(76, 'User', 'User', 'field', 27, 3, '{}'),
					(77, 'Password', 'Password', 'field', 27, 3, '{}'),
					(78, 'Database', 'DB', 'field', 27, 3, '{}'),
					(79, 'Description', 'Description', 'text', 27, 3, '{}'),
					(80, 'Connection', 'ConnectionId', 'databasejoin', 7, 3, '{\"space\":".$spaceId.",\"connection\":1,\"join_entity\":27,\"join_key\":74,\"join_val\":78}'),
					(81, 'System', 'IsSystem', 'yesno', 7, 3, '{}');
				",
				'fabrik_field_pr' => "
					ALTER TABLE `fabrik_field`
					  ADD PRIMARY KEY (`id`);
				",
				'fabrik_field_pr_mod' => "
					ALTER TABLE `fabrik_field`
					  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;
				",

				// menu
				'menu' => "
					CREATE TABLE `menu` (
					  `id` int(11) UNSIGNED NOT NULL,
					  `Name` varchar(100) NOT NULL DEFAULT ''
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;
				",
				'menu_i' => "
					INSERT INTO `menu` (`id`, `Name`) VALUES
					(1, 'Main'),
					(2, 'Admin');
				",
				'menu_pr' => "
					ALTER TABLE `menu`
					  ADD PRIMARY KEY (`id`);
				",
				'menu_pr_mod' => "
					ALTER TABLE `menu`
					  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
				",

				// menu_item
				'menu_item' => "
					CREATE TABLE `menu_item` (
					  `id` int(11) UNSIGNED NOT NULL,
					  `Name` varchar(100) NOT NULL DEFAULT '',
					  `Alias` varchar(100) NOT NULL DEFAULT '',
					  `Type` varchar(20) NOT NULL,
					  `Link` varchar(50) NOT NULL DEFAULT '',
					  `ParentId` int(10) NOT NULL,
					  `MenuId` int(10) NOT NULL,
					  `LevelId` int(11) NOT NULL,
					  `IsHome` int(1) NOT NULL,
					  `IsStart` int(1) NOT NULL,
					  `Icon` varchar(100) NOT NULL DEFAULT '',
					  `Display` int(3) NOT NULL
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;
				",
				'menu_item_i' => "
					INSERT INTO `menu_item` (`id`, `Name`, `Alias`, `Type`, `Link`, `ParentId`, `MenuId`, `LevelId`, `IsHome`, `IsStart`, `Icon`, `Display`) VALUES
					(2, 'Dashboard', 'dashboard', 'component', 'option=builder&task=tmpl&id=1', 0, 2, 2, 0, 1, 'far fa-tachometer-alt-fast', 1),
					(4, 'Templates', 'templates', 'url', '', 0, 2, 2, 0, 0, 'fal fa-file-spreadsheet', 4),
					(7, 'Log out', 'logout', 'logout', '', 0, 2, 1, 0, 0, 'far fa-sign-out-alt', 7),
					(8, 'Home', 'home', 'component', 'option=builder&task=tmpl&id=4', 0, 1, 1, 1, 0, '', 1),
					(9, 'Menu', 'menu', 'component', 'option=builder&task=tmpl&id=6', 0, 2, 2, 0, 0, 'far fa-list-alt', 3),
					(10, 'Page', 'page', 'component', 'option=builder&task=tmpl&id=10', 4, 2, 2, 0, 0, 'far fa-circle', 1),
					(11, 'Fabrik Form', 'fabrik-form', 'component', '', 4, 2, 0, 0, 0, 'far fa-circle', 2),
					(12, 'Fabrik Filter', 'fabrik-filter', 'component', '', 4, 2, 0, 0, 0, 'far fa-circle', 3),
					(60, 'Modules', 'modules', 'component', 'option=builder&task=tmpl&id=7', 0, 2, 2, 0, 0, 'fas fa-th', 5),
					(61, 'Fabrik', 'fabrik', 'url', '', 0, 2, 2, 0, 0, 'fad fa-database', 6),
					(62, 'Entity', 'entity', 'component', 'option=builder&task=tmpl&id=8', 61, 2, 2, 0, 0, 'far fa-circle', 1),
					(63, 'Property', 'property', 'component', 'option=builder&task=tmpl&id=9', 61, 2, 2, 0, 0, 'far fa-circle', 2);
				",
				'menu_item_pr' => "
					ALTER TABLE `menu_item`
					  ADD PRIMARY KEY (`id`);
				",
				'menu_item_pr_mod' => "
					ALTER TABLE `menu_item`
					  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;
				",

				// module
				'module' => "
					CREATE TABLE `module` (
					  `id` int(11) UNSIGNED NOT NULL,
					  `Name` varchar(100) NOT NULL DEFAULT '',
					  `Module` varchar(100) NOT NULL,
					  `TmplId` int(11) NOT NULL,
					  `Params` text NOT NULL
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;
				",
				'module_i' => "
					INSERT INTO `module` (`id`, `Name`, `Module`, `TmplId`, `Params`) VALUES
					(4, 'Slider', 'custom', 2, '{\"flag\":\"slider\"}'),
					(5, 'Vertical Menu', 'verticalMenu', 2, '{\"id\":2}'),
					(6, 'Menu Filter', 'fabrikFilter', 6, '{\"elements\":[{\"type\":\"field\",\"label\":\"Menu\",\"elValue\":6,\"elLabel\":7,\"sql\":\"SELECT id AS value, Name AS label FROM menu\",\"moduleId\":7,\"apply\":true},{\"type\":\"button\",\"action\":\"add\",\"moduleId\":7,\"apply\":false}]}'),
					(7, 'Menu Form', 'fabrikForm', 6, '{\"tableId\":2,\"isEditable\":false,\"rowId\":{\"type\":\"fabrikFilter\",\"moduleId\":6,\"fieldId\":0}}'),
					(8, 'Menu Item List', 'fabrikList', 6, '{\"tableId\":3,\"filter\":[{\"join\":\"and\",\"elementId\":14,\"condition\":\"=\",\"value\":{\"type\":\"fabrikFilter\",\"moduleId\":6,\"fieldId\":0}}],\"editElId\":9}'),
					(9, 'Module Filter', 'fabrikFilter', 7, '{\"elements\":[{\"type\":\"field\",\"label\":\"Template Page\",\"elValue\":22,\"elLabel\":23,\"sql\":\"SELECT id AS value, Name AS label FROM builder_tmpl WHERE EntityTypeId=1\",\"apply\":true}]}'),
					(10, 'Module List', 'fabrikList', 7, '{\"tableId\":6,\"filter\":[{\"join\":\"and\",\"elementId\":29,\"condition\":\"=\",\"value\":{\"type\":\"fabrikFilter\",\"moduleId\":9,\"fieldId\":0}}],\"editElId\":28}'),
					(11, 'Fab Entity Filter', 'fabrikFilter', 8, '{\"elements\":[{\"type\":\"field\",\"label\":\"Status\",\"sql\":\"SELECT id AS value, Name AS label FROM status\",\"apply\":true}]}'),
					(12, 'Fab Entity List', 'fabrikList', 8, '{\"tableId\":7,\"filter\":[{\"join\":\"and\",\"elementId\":43,\"condition\":\"=\",\"value\":{\"type\":\"fabrikFilter\",\"moduleId\":11,\"fieldId\":0}},{\"join\":\"and\",\"elementId\":81,\"condition\":\"=\",\"value\":{\"type\":\"text\",\"text\":\"0\"}}],\"editElId\":32}'),
					(13, 'Fab Property Filter', 'fabrikFilter', 9, '{\"elements\":[{\"type\":\"field\",\"label\":\"Entity\",\"sql\":\"SELECT id AS value, Name AS label FROM fabrik_entity WHERE IsSystem=0\",\"apply\":true},{\"type\":\"field\",\"label\":\"Status\",\"sql\":\"SELECT id AS value, Name AS label FROM status\",\"apply\":true}]}'),
					(14, 'Fab Fields List', 'fabrikList', 9, '{\"tableId\":8,\"filter\":[{\"join\":\"and\",\"elementId\":39,\"condition\":\"=\",\"value\":{\"type\":\"fabrikFilter\",\"moduleId\":13,\"fieldId\":0}},{\"join\":\"and\",\"elementId\":44,\"condition\":\"=\",\"value\":{\"type\":\"fabrikFilter\",\"moduleId\":13,\"fieldId\":1}},{\"join\":\"and\",\"elementId\":38,\"condition\":\"!=\",\"value\":{\"type\":\"text\",\"text\":\"calc\"}}],\"editElId\":37}'),
					(15, 'Template Page Filter', 'fabrikFilter', 10, '{\"elements\":[{\"type\":\"field\",\"label\":\"Status\",\"sql\":\"SELECT id AS value, Name AS label FROM status\",\"apply\":true}]}'),
					(16, 'Template Page List', 'fabrikList', 10, '{\"tableId\":5,\"filter\":[{\"join\":\"and\",\"elementId\":46,\"condition\":\"=\",\"value\":{\"type\":\"fabrikFilter\",\"moduleId\":15,\"fieldId\":0}}],\"editElId\":23}'),
					(17, 'Fab Calcs List', 'fabrikList', 9, '{\"tableId\":8,\"filter\":[{\"join\":\"and\",\"elementId\":39,\"condition\":\"=\",\"value\":{\"type\":\"fabrikFilter\",\"moduleId\":13,\"fieldId\":0}},{\"join\":\"and\",\"elementId\":44,\"condition\":\"=\",\"value\":{\"type\":\"fabrikFilter\",\"moduleId\":13,\"fieldId\":1}},{\"join\":\"and\",\"elementId\":38,\"condition\":\"=\",\"value\":{\"type\":\"text\",\"text\":\"calc\"}}],\"editElId\":37}'),
					(18, 'Login', 'custom', 2, '{\"flag\":\"login\"}'),
					(19, 'Dashboard', 'custom', 2, '{\"flag\":\"dashboard\"}');
				",
				'module_pr' => "
					ALTER TABLE `module`
					  ADD PRIMARY KEY (`id`);
				",
				'module_pr_mod' => "
					ALTER TABLE `module`
					  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
				",

				// status
				'status' => "
					CREATE TABLE `status` (
					  `id` int(11) UNSIGNED NOT NULL,
					  `Name` varchar(100) NOT NULL DEFAULT ''
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;
				",
				'status_i' => "
					INSERT INTO `status` (`id`, `Name`) VALUES
					(1, 'New'),
					(2, 'Draft'),
					(3, 'Active'),
					(4, 'Inactive'),
					(5, 'Trashed');
				",
				'status_pr' => "
					ALTER TABLE `status`
					  ADD PRIMARY KEY (`id`);
				",
				'status_pr_mod' => "
					ALTER TABLE `status`
					  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
				",

				// user
				'user' => "
					CREATE TABLE `user` (
					  `id` int(11) UNSIGNED NOT NULL,
					  `Name` varchar(100) NOT NULL DEFAULT '',
					  `Password` varchar(20) NOT NULL,
					  `RoleId` int(10) NOT NULL
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;
				",
				'user_i' => "
					INSERT INTO `user` (`id`, `Name`, `Password`, `RoleId`) VALUES
					(1, 'developer', 'developer', 2);
				",
				'user_pr' => "
					ALTER TABLE `user`
					  ADD PRIMARY KEY (`id`);
				",
				'user_pr_mod' => "
					ALTER TABLE `user`
					  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
				",

				// user_role
				'user_role' => "
					CREATE TABLE `user_role` (
					  `id` int(11) UNSIGNED NOT NULL,
					  `Name` varchar(100) NOT NULL DEFAULT ''
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;
				",
				'user_role_i' => "
					INSERT INTO `user_role` (`id`, `Name`) VALUES
					(1, 'Public'),
					(2, 'Admin');
				",
				'user_role_pr' => "
					ALTER TABLE `user_role`
					  ADD PRIMARY KEY (`id`);
				",
				'user_role_pr_mod' => "
					ALTER TABLE `user_role`
					  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
				",

				// user_role_repeat_groupid
				'user_role_repeat_GroupId' => "
					CREATE TABLE `user_role_repeat_GroupId` (
					  `left` int(11) NOT NULL,
					  `right` int(11) NOT NULL
					) ENGINE=InnoDB DEFAULT CHARSET=latin1;
				",
				'user_role_repeat_GroupId_i' => "
					INSERT INTO `user_role_repeat_GroupId` (`left`, `right`) VALUES
					(2, 1),
					(2, 2);
				",

				// vertical_menu
				'vertical_menu' => "
					CREATE TABLE `vertical_menu` (
					  `id` int(11) UNSIGNED NOT NULL,
					  `Name` varchar(100) NOT NULL DEFAULT '',
					  `MenuId` int(11) NOT NULL,
					  `RoleId` int(11) NOT NULL
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;
				",
				'vertical_menu_i' => "
					INSERT INTO `vertical_menu` (`id`, `Name`, `MenuId`, `RoleId`) VALUES
					(1, 'Admin', 2, 2);
				",
				'vertical_menu_pr' => "
					ALTER TABLE `vertical_menu`
					  ADD PRIMARY KEY (`id`);
				",
				'vertical_menu_pr_mod' => "
					ALTER TABLE `vertical_menu`
					  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
				"
			];

			foreach ($queryArr as $query)
				$dbomy->setQuery($query)->execute();
		}
	}
}