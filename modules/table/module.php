<?php
defined('HOSTCMS') || exit('HostCMS: access denied.');
/**
 * Table
 *
 * @package Artatom
 * @subpackage Table
 * @version 1.0
 * @author Konstantin Bondarenko
 * @copyright © 2007-2018 Artatom http://www.artatom.ru
 */
class Table_Module extends Core_Module
{
	/**
	 * Module version
	 * @var string
	 */
	public $version = '1.0';

	/**
	 * Module date
	 * @var date
	 */
	public $date = '2018-05-18';

	/**
	 * Module name
	 * @var string
	 */
	protected $_moduleName = 'tables';

	/**
	 * Get Module's Menu
	 * @return array
	 */
	public function getMenu()
	{
		$this->setMenu(
			array(
				array(
					'sorting' => 10,
					'block' => 0,
					'ico' => 'fa fa-table',
					'name' => Core::_('Table.menu'),
					'href' => "/admin/table/index.php",
					'onclick' => "$.adminLoad({path: '/admin/table/index.php'}); return false"
				),
				array(
					'sorting' => 20,
					'block' => 0,
					'ico' => 'fa fa-database',
					'name' => Core::_('Table_Datatype.menu'),
					'href' => "/admin/table/datatype/index.php",
					'onclick' => "$.adminLoad({path: '/admin/table/datatype/index.php'}); return false"
				),
			)
		);
		return parent::getMenu();
	}

	/**
	 * Install module.
	 */
	public function install()
	{
		// SQL dump
		$query = "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";
			SET AUTOCOMMIT = 0;
			START TRANSACTION;
			SET time_zone = \"+00:00\";

			/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
			/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
			/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
			/*!40101 SET NAMES utf8 */;

			CREATE TABLE `tables` (
			  `id` int(10) UNSIGNED NOT NULL,
			  `site_id` int(11) NOT NULL,
			  `name` varchar(255) NOT NULL,
			  `sorting` smallint(10) UNSIGNED NOT NULL,
			  `deleted` tinyint(1) UNSIGNED NOT NULL DEFAULT '0'
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;

			CREATE TABLE `table_cells` (
			  `id` int(11) NOT NULL,
			  `site_id` int(11) NOT NULL,
			  `table_column_id` int(11) NOT NULL,
			  `table_row_id` int(11) NOT NULL,
			  `sorting` smallint(6) NOT NULL,
			  `active` tinyint(1) NOT NULL,
			  `deleted` tinyint(1) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;

			CREATE TABLE `table_cell_value_files` (
			  `id` int(11) NOT NULL,
			  `table_column_id` int(11) NOT NULL,
			  `table_cell_id` int(11) NOT NULL,
			  `value` varchar(255) NOT NULL,
			  `file` varchar(255) NOT NULL,
			  `file_name` varchar(255) NOT NULL,
			  `file_description` mediumtext NOT NULL,
			  `deleted` tinyint(1) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;

			CREATE TABLE `table_cell_value_ints` (
			  `id` int(11) NOT NULL,
			  `table_column_id` int(11) NOT NULL,
			  `table_cell_id` int(11) NOT NULL,
			  `value` int(11) NOT NULL,
			  `deleted` tinyint(1) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;

			CREATE TABLE `table_cell_value_strings` (
			  `id` int(11) NOT NULL,
			  `table_column_id` int(11) NOT NULL,
			  `table_cell_id` int(11) NOT NULL,
			  `value` varchar(255) NOT NULL,
			  `deleted` tinyint(1) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;

			CREATE TABLE `table_cell_value_textareas` (
			  `id` int(11) NOT NULL,
			  `table_column_id` int(11) NOT NULL,
			  `table_cell_id` int(11) NOT NULL,
			  `value` longtext NOT NULL,
			  `deleted` tinyint(1) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;

			CREATE TABLE `table_cell_value_wysiwygs` (
			  `id` int(11) NOT NULL,
			  `table_column_id` int(11) NOT NULL,
			  `table_cell_id` int(11) NOT NULL,
			  `value` longtext NOT NULL,
			  `deleted` tinyint(1) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;

			CREATE TABLE `table_columns` (
			  `id` int(11) NOT NULL,
			  `site_id` int(11) NOT NULL,
			  `table_id` int(10) UNSIGNED NOT NULL,
			  `table_datatype_id` int(10) NOT NULL,
			  `name` varchar(255) NOT NULL,
			  `multiple` tinyint(1) NOT NULL,
			  `sorting` smallint(6) NOT NULL,
			  `active` tinyint(1) NOT NULL,
			  `deleted` tinyint(1) NOT NULL DEFAULT '0'
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;

			CREATE TABLE `table_datatypes` (
			  `id` int(11) NOT NULL,
			  `name` varchar(255) NOT NULL,
			  `code` varchar(255) NOT NULL,
			  `model_name` varchar(255) NOT NULL,
			  `sorting` smallint(6) NOT NULL,
			  `active` tinyint(1) NOT NULL,
			  `deleted` tinyint(1) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;

			INSERT INTO `table_datatypes` (`id`, `name`, `code`, `model_name`, `sorting`, `active`, `deleted`) VALUES
			(1, 'Число', 'integer', 'Table_Cell_Value_Int', 0, 1, 0),
			(2, 'Строка', 'string', 'Table_Cell_Value_String', 10, 1, 0),
			(3, 'Файл', 'file', 'Table_Cell_Value_File', 20, 1, 0),
			(4, 'Wysiwyg-редактор', 'wysiwyg', 'Table_Cell_Value_Wysiwyg', 30, 1, 0),
			(5, 'Большое текстовое поле', 'textarea', 'Table_Cell_Value_Textarea', 40, 1, 0);

			CREATE TABLE `table_items` (
			  `id` int(11) NOT NULL,
			  `site_id` int(11) NOT NULL,
			  `table_id` int(10) UNSIGNED NOT NULL,
			  `name` varchar(255) NOT NULL,
			  `description` text,
			  `sorting` smallint(6) NOT NULL,
			  `active` tinyint(1) NOT NULL,
			  `deleted` tinyint(1) UNSIGNED NOT NULL DEFAULT '0'
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;

			CREATE TABLE `table_rows` (
			  `id` int(11) NOT NULL,
			  `site_id` int(11) NOT NULL,
			  `table_id` int(10) UNSIGNED NOT NULL,
			  `table_item_id` int(10) NOT NULL,
			  `name` varchar(255) NOT NULL DEFAULT '',
			  `headline` tinyint(1) NOT NULL,
			  `sorting` smallint(6) NOT NULL,
			  `active` tinyint(1) NOT NULL,
			  `deleted` tinyint(1) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;

			ALTER TABLE `tables`
			  ADD PRIMARY KEY (`id`),
			  ADD KEY `site_id` (`site_id`);

			ALTER TABLE `table_cells`
			  ADD PRIMARY KEY (`id`),
			  ADD KEY `table_column_id` (`table_column_id`),
			  ADD KEY `table_row_id` (`table_row_id`),
			  ADD KEY `site_id` (`site_id`);

			ALTER TABLE `table_cell_value_files`
			  ADD PRIMARY KEY (`id`),
			  ADD KEY `table_cell_id` (`table_cell_id`),
			  ADD KEY `table_column_id` (`table_column_id`);

			ALTER TABLE `table_cell_value_ints`
			  ADD PRIMARY KEY (`id`),
			  ADD KEY `table_cell_id` (`table_cell_id`),
			  ADD KEY `table_column_id` (`table_column_id`);

			ALTER TABLE `table_cell_value_strings`
			  ADD PRIMARY KEY (`id`),
			  ADD KEY `table_cell_id` (`table_cell_id`),
			  ADD KEY `table_column_id` (`table_column_id`);

			ALTER TABLE `table_cell_value_textareas`
			  ADD PRIMARY KEY (`id`),
			  ADD KEY `table_column_id` (`table_column_id`) USING BTREE,
			  ADD KEY `table_cell_id` (`table_cell_id`);

			ALTER TABLE `table_cell_value_wysiwygs`
			  ADD PRIMARY KEY (`id`),
			  ADD KEY `table_column_id` (`table_column_id`),
			  ADD KEY `table_cell_id` (`table_cell_id`);

			ALTER TABLE `table_columns`
			  ADD PRIMARY KEY (`id`),
			  ADD KEY `table_id` (`table_id`),
			  ADD KEY `table_datatype_id` (`table_datatype_id`),
			  ADD KEY `site_id` (`site_id`);

			ALTER TABLE `table_datatypes`
			  ADD PRIMARY KEY (`id`);

			ALTER TABLE `table_items`
			  ADD PRIMARY KEY (`id`),
			  ADD KEY `site_id` (`site_id`),
			  ADD KEY `table_id` (`table_id`);

			ALTER TABLE `table_rows`
			  ADD PRIMARY KEY (`id`),
			  ADD KEY `table_item_id` (`table_item_id`) USING BTREE,
			  ADD KEY `table_rows_ibfk_1` (`table_id`),
			  ADD KEY `site_id` (`site_id`);

			ALTER TABLE `tables`
			  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

			ALTER TABLE `table_cells`
			  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

			ALTER TABLE `table_cell_value_files`
			  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

			ALTER TABLE `table_cell_value_ints`
			  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

			ALTER TABLE `table_cell_value_strings`
			  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

			ALTER TABLE `table_cell_value_textareas`
			  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

			ALTER TABLE `table_cell_value_wysiwygs`
			  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

			ALTER TABLE `table_columns`
			  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

			ALTER TABLE `table_datatypes`
			  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

			ALTER TABLE `table_items`
			  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

			ALTER TABLE `table_rows`
			  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
			COMMIT;

			/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
			/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
			/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
			";
		Sql_Controller::instance()->execute($query);

		$oAdmin_Form = Core_Entity::factory('Admin_Form')->getByGuid('80023F3D-22AB-618C-B104-DB8B034AD549');

		if (is_null($oAdmin_Form))
		{
			/**
			 * Создаем значения Admin_Word_Value
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Типы данных';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Data types';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			/**
			 * Создаем форму
			 */
			$oAdmin_Form = Core_Entity::factory('Admin_Form');
			$oAdmin_Form->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form->on_page = 20;
			$oAdmin_Form->key_field = 'id';
			$oAdmin_Form->show_operations = 1;
			$oAdmin_Form->show_group_operations = 1;
			$oAdmin_Form->default_order_field = 'sorting';
			$oAdmin_Form->default_order_direction = 1;
			$oAdmin_Form->guid = '80023F3D-22AB-618C-B104-DB8B034AD549';
			$oAdmin_Form->save();

			/**
			 * Создаем поле формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'ID';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'ID';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Field = Core_Entity::factory('Admin_Form_Field');
			$oAdmin_Form_Field->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Field->name = 'id';
			$oAdmin_Form_Field->sorting = 0;
			$oAdmin_Form_Field->ico = '';
			$oAdmin_Form_Field->type = 1;
			$oAdmin_Form_Field->format = '';
			$oAdmin_Form_Field->allow_sorting = 1;
			$oAdmin_Form_Field->allow_filter = 0;
			$oAdmin_Form_Field->editable = 0;
			$oAdmin_Form_Field->filter_type = 0;
			$oAdmin_Form_Field->class = '';
			$oAdmin_Form_Field->width = '55px';
			$oAdmin_Form_Field->image = '';
			$oAdmin_Form_Field->link = '';
			$oAdmin_Form_Field->onclick = '';
			$oAdmin_Form_Field->list = '';
			$oAdmin_Form->add($oAdmin_Form_Field);

			/**
			 * Создаем поле формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Название типа данных';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Data type name';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Field = Core_Entity::factory('Admin_Form_Field');
			$oAdmin_Form_Field->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Field->name = 'name';
			$oAdmin_Form_Field->sorting = 10;
			$oAdmin_Form_Field->ico = '';
			$oAdmin_Form_Field->type = 1;
			$oAdmin_Form_Field->format = '';
			$oAdmin_Form_Field->allow_sorting = 1;
			$oAdmin_Form_Field->allow_filter = 1;
			$oAdmin_Form_Field->editable = 1;
			$oAdmin_Form_Field->filter_type = 0;
			$oAdmin_Form_Field->class = '';
			$oAdmin_Form_Field->width = '';
			$oAdmin_Form_Field->image = '';
			$oAdmin_Form_Field->link = '';
			$oAdmin_Form_Field->onclick = '';
			$oAdmin_Form_Field->list = '';
			$oAdmin_Form->add($oAdmin_Form_Field);

			/**
			 * Создаем поле формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Символьный код';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Symbolic code';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Field = Core_Entity::factory('Admin_Form_Field');
			$oAdmin_Form_Field->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Field->name = 'code';
			$oAdmin_Form_Field->sorting = 20;
			$oAdmin_Form_Field->ico = '';
			$oAdmin_Form_Field->type = 1;
			$oAdmin_Form_Field->format = '';
			$oAdmin_Form_Field->allow_sorting = 1;
			$oAdmin_Form_Field->allow_filter = 1;
			$oAdmin_Form_Field->editable = 0;
			$oAdmin_Form_Field->filter_type = 0;
			$oAdmin_Form_Field->class = '';
			$oAdmin_Form_Field->width = '';
			$oAdmin_Form_Field->image = '';
			$oAdmin_Form_Field->link = '';
			$oAdmin_Form_Field->onclick = '';
			$oAdmin_Form_Field->list = '';
			$oAdmin_Form->add($oAdmin_Form_Field);

			/**
			 * Создаем поле формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Сортировка';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Sorting';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Field = Core_Entity::factory('Admin_Form_Field');
			$oAdmin_Form_Field->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Field->name = 'sorting';
			$oAdmin_Form_Field->sorting = 30;
			$oAdmin_Form_Field->ico = 'fa fa-sort-numeric-asc';
			$oAdmin_Form_Field->type = 2;
			$oAdmin_Form_Field->format = '';
			$oAdmin_Form_Field->allow_sorting = 1;
			$oAdmin_Form_Field->allow_filter = 0;
			$oAdmin_Form_Field->editable = 0;
			$oAdmin_Form_Field->filter_type = 0;
			$oAdmin_Form_Field->class = 'hidden-xxs hidden-xs hidden-sm';
			$oAdmin_Form_Field->width = '60px';
			$oAdmin_Form_Field->image = '';
			$oAdmin_Form_Field->link = '';
			$oAdmin_Form_Field->onclick = '';
			$oAdmin_Form_Field->list = '';
			$oAdmin_Form->add($oAdmin_Form_Field);

			/**
			 * Создаем поле формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Активность';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Activity';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Field = Core_Entity::factory('Admin_Form_Field');
			$oAdmin_Form_Field->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Field->name = 'active';
			$oAdmin_Form_Field->sorting = 40;
			$oAdmin_Form_Field->ico = 'fa fa-lightbulb-o';
			$oAdmin_Form_Field->type = 7;
			$oAdmin_Form_Field->format = '';
			$oAdmin_Form_Field->allow_sorting = 0;
			$oAdmin_Form_Field->allow_filter = 1;
			$oAdmin_Form_Field->editable = 0;
			$oAdmin_Form_Field->filter_type = 0;
			$oAdmin_Form_Field->class = 'hidden-xxs text-center';
			$oAdmin_Form_Field->width = '25px';
			$oAdmin_Form_Field->image = '1=/admin/images/check.gif==fa fa-lightbulb-o fa-active
				0=/admin/images/not_check.gif==fa fa-lightbulb-o fa-inactive';
			$oAdmin_Form_Field->link = '/admin/table/datatype/index.php?hostcms[action]=changeActive&hostcms[checked][{dataset_key}][{id}]=1';
			$oAdmin_Form_Field->onclick = '$.adminLoad({path: \'/admin/table/datatype/index.php\',additionalParams: \'hostcms[checked][{dataset_key}][{id}]=1\', action: \'changeActive\', windowId: \'{windowId}\'}); return false';
			$oAdmin_Form_Field->list = '';
			$oAdmin_Form->add($oAdmin_Form_Field);

			/**
			 * Создаем действие формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Применить';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Apply';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Action = Core_Entity::factory('Admin_Form_Action');
			$oAdmin_Form_Action->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Action->name = 'apply';
			$oAdmin_Form_Action->picture = '/admin/images/apply.gif';
			$oAdmin_Form_Action->icon = 'fa fa-check';
			$oAdmin_Form_Action->color = 'palegreen';
			$oAdmin_Form_Action->single = '0';
			$oAdmin_Form_Action->group = '1';
			$oAdmin_Form_Action->sorting = '0';
			$oAdmin_Form_Action->dataset = '-1';
			$oAdmin_Form_Action->confirm = '0';
			$oAdmin_Form->add($oAdmin_Form_Action);

			/**
			 * Создаем действие формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Редактировать';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Edit';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Action = Core_Entity::factory('Admin_Form_Action');
			$oAdmin_Form_Action->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Action->name = 'edit';
			$oAdmin_Form_Action->picture = '/admin/images/edit.gif';
			$oAdmin_Form_Action->icon = 'fa fa-pencil';
			$oAdmin_Form_Action->color = 'palegreen';
			$oAdmin_Form_Action->single = '1';
			$oAdmin_Form_Action->group = '0';
			$oAdmin_Form_Action->sorting = '10';
			$oAdmin_Form_Action->dataset = '-1';
			$oAdmin_Form_Action->confirm = '0';
			$oAdmin_Form->add($oAdmin_Form_Action);

			/**
			 * Создаем действие формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Удалить';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Delete';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Action = Core_Entity::factory('Admin_Form_Action');
			$oAdmin_Form_Action->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Action->name = 'markDeleted';
			$oAdmin_Form_Action->picture = '/admin/images/delete.gif';
			$oAdmin_Form_Action->icon = 'fa fa-trash-o';
			$oAdmin_Form_Action->color = 'darkorange';
			$oAdmin_Form_Action->single = '1';
			$oAdmin_Form_Action->group = '1';
			$oAdmin_Form_Action->sorting = '20';
			$oAdmin_Form_Action->dataset = '-1';
			$oAdmin_Form_Action->confirm = '1';
			$oAdmin_Form->add($oAdmin_Form_Action);

			/**
			 * Создаем действие формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Изменить статус элемента';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Activity of element';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Action = Core_Entity::factory('Admin_Form_Action');
			$oAdmin_Form_Action->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Action->name = 'changeActive';
			$oAdmin_Form_Action->picture = '';
			$oAdmin_Form_Action->icon = '';
			$oAdmin_Form_Action->color = '';
			$oAdmin_Form_Action->single = '0';
			$oAdmin_Form_Action->group = '0';
			$oAdmin_Form_Action->sorting = '40';
			$oAdmin_Form_Action->dataset = '-1';
			$oAdmin_Form_Action->confirm = '0';
			$oAdmin_Form->add($oAdmin_Form_Action);
		}

		$oAdmin_Form = Core_Entity::factory('Admin_Form')->getByGuid('479BEBAC-A43C-FF69-6137-9AC6286CC144');

		if (is_null($oAdmin_Form))
		{
			/**
			 * Создаем значения Admin_Word_Value
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Строки';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Rows';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			/**
			 * Создаем форму
			 */
			$oAdmin_Form = Core_Entity::factory('Admin_Form');
			$oAdmin_Form->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form->on_page = 20;
			$oAdmin_Form->key_field = 'id';
			$oAdmin_Form->show_operations = 1;
			$oAdmin_Form->show_group_operations = 1;
			$oAdmin_Form->default_order_field = 'sorting';
			$oAdmin_Form->default_order_direction = 1;
			$oAdmin_Form->guid = '479BEBAC-A43C-FF69-6137-9AC6286CC144';
			$oAdmin_Form->save();

			/**
			 * Создаем поле формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'ID';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'ID';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Field = Core_Entity::factory('Admin_Form_Field');
			$oAdmin_Form_Field->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Field->name = 'id';
			$oAdmin_Form_Field->sorting = 0;
			$oAdmin_Form_Field->ico = '';
			$oAdmin_Form_Field->type = 1;
			$oAdmin_Form_Field->format = '';
			$oAdmin_Form_Field->allow_sorting = 1;
			$oAdmin_Form_Field->allow_filter = 1;
			$oAdmin_Form_Field->editable = 0;
			$oAdmin_Form_Field->filter_type = 0;
			$oAdmin_Form_Field->class = '';
			$oAdmin_Form_Field->width = '55px';
			$oAdmin_Form_Field->image = '';
			$oAdmin_Form_Field->link = '';
			$oAdmin_Form_Field->onclick = '';
			$oAdmin_Form_Field->list = '';
			$oAdmin_Form->add($oAdmin_Form_Field);

			/**
			 * Создаем поле формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Содержимое строки';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Row content';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Field = Core_Entity::factory('Admin_Form_Field');
			$oAdmin_Form_Field->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Field->name = 'content';
			$oAdmin_Form_Field->sorting = 5;
			$oAdmin_Form_Field->ico = '';
			$oAdmin_Form_Field->type = 10;
			$oAdmin_Form_Field->format = '';
			$oAdmin_Form_Field->allow_sorting = 0;
			$oAdmin_Form_Field->allow_filter = 0;
			$oAdmin_Form_Field->editable = 0;
			$oAdmin_Form_Field->filter_type = 1;
			$oAdmin_Form_Field->class = '';
			$oAdmin_Form_Field->width = '';
			$oAdmin_Form_Field->image = '';
			$oAdmin_Form_Field->link = '';
			$oAdmin_Form_Field->onclick = '';
			$oAdmin_Form_Field->list = '';
			$oAdmin_Form->add($oAdmin_Form_Field);

			/**
			 * Создаем поле формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Сортировка';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Sorting';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Field = Core_Entity::factory('Admin_Form_Field');
			$oAdmin_Form_Field->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Field->name = 'sorting';
			$oAdmin_Form_Field->sorting = 10;
			$oAdmin_Form_Field->ico = 'fa fa-sort-numeric-asc';
			$oAdmin_Form_Field->type = 2;
			$oAdmin_Form_Field->format = '';
			$oAdmin_Form_Field->allow_sorting = 1;
			$oAdmin_Form_Field->allow_filter = 0;
			$oAdmin_Form_Field->editable = 0;
			$oAdmin_Form_Field->filter_type = 0;
			$oAdmin_Form_Field->class = 'hidden-xxs hidden-xs hidden-sm';
			$oAdmin_Form_Field->width = '60px';
			$oAdmin_Form_Field->image = '';
			$oAdmin_Form_Field->link = '';
			$oAdmin_Form_Field->onclick = '';
			$oAdmin_Form_Field->list = '';
			$oAdmin_Form->add($oAdmin_Form_Field);

			/**
			 * Создаем поле формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Активность';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Active';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Field = Core_Entity::factory('Admin_Form_Field');
			$oAdmin_Form_Field->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Field->name = 'active';
			$oAdmin_Form_Field->sorting = 20;
			$oAdmin_Form_Field->ico = 'fa fa-lightbulb-o';
			$oAdmin_Form_Field->type = 7;
			$oAdmin_Form_Field->format = '';
			$oAdmin_Form_Field->allow_sorting = 0;
			$oAdmin_Form_Field->allow_filter = 1;
			$oAdmin_Form_Field->editable = 0;
			$oAdmin_Form_Field->filter_type = 0;
			$oAdmin_Form_Field->class = 'hidden-xxs text-center';
			$oAdmin_Form_Field->width = '25px';
			$oAdmin_Form_Field->image = '1=/admin/images/check.gif==fa fa-lightbulb-o fa-active
				0=/admin/images/not_check.gif==fa fa-lightbulb-o fa-inactive';
			$oAdmin_Form_Field->link = '/admin/table/row/index.php?hostcms[action]=changeActive&hostcms[checked][{dataset_key}][{id}]=1&table_item_id={table_item_id}';
			$oAdmin_Form_Field->onclick = '$.adminLoad({path: \'/admin/table/row/index.php\',additionalParams: \'hostcms[checked][{dataset_key}][{id}]=1&table_item_id={table_item_id}\', action: \'changeActive\', windowId: \'{windowId}\'}); return false';
			$oAdmin_Form_Field->list = '';
			$oAdmin_Form->add($oAdmin_Form_Field);

			/**
			 * Создаем действие формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Применить';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Apply';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Action = Core_Entity::factory('Admin_Form_Action');
			$oAdmin_Form_Action->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Action->name = 'apply';
			$oAdmin_Form_Action->picture = '/admin/images/apply.gif';
			$oAdmin_Form_Action->icon = 'fa fa-check';
			$oAdmin_Form_Action->color = 'palegreen';
			$oAdmin_Form_Action->single = '0';
			$oAdmin_Form_Action->group = '1';
			$oAdmin_Form_Action->sorting = '0';
			$oAdmin_Form_Action->dataset = '-1';
			$oAdmin_Form_Action->confirm = '0';
			$oAdmin_Form->add($oAdmin_Form_Action);

			/**
			 * Создаем действие формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Редактировать';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Edit';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Action = Core_Entity::factory('Admin_Form_Action');
			$oAdmin_Form_Action->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Action->name = 'edit';
			$oAdmin_Form_Action->picture = '/admin/images/edit.gif';
			$oAdmin_Form_Action->icon = 'fa fa-pencil';
			$oAdmin_Form_Action->color = 'palegreen';
			$oAdmin_Form_Action->single = '1';
			$oAdmin_Form_Action->group = '0';
			$oAdmin_Form_Action->sorting = '10';
			$oAdmin_Form_Action->dataset = '-1';
			$oAdmin_Form_Action->confirm = '0';
			$oAdmin_Form->add($oAdmin_Form_Action);

			/**
			 * Создаем действие формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Копировать';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Copy';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Action = Core_Entity::factory('Admin_Form_Action');
			$oAdmin_Form_Action->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Action->name = 'copy';
			$oAdmin_Form_Action->picture = '/admin/images/copy.gif';
			$oAdmin_Form_Action->icon = 'fa fa-copy';
			$oAdmin_Form_Action->color = 'info';
			$oAdmin_Form_Action->single = '1';
			$oAdmin_Form_Action->group = '1';
			$oAdmin_Form_Action->sorting = '15';
			$oAdmin_Form_Action->dataset = '-1';
			$oAdmin_Form_Action->confirm = '1';
			$oAdmin_Form->add($oAdmin_Form_Action);

			/**
			 * Создаем действие формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Удалить';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Delete';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Action = Core_Entity::factory('Admin_Form_Action');
			$oAdmin_Form_Action->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Action->name = 'markDeleted';
			$oAdmin_Form_Action->picture = '/admin/images/delete.gif';
			$oAdmin_Form_Action->icon = 'fa fa-trash-o';
			$oAdmin_Form_Action->color = 'darkorange';
			$oAdmin_Form_Action->single = '1';
			$oAdmin_Form_Action->group = '1';
			$oAdmin_Form_Action->sorting = '20';
			$oAdmin_Form_Action->dataset = '-1';
			$oAdmin_Form_Action->confirm = '1';
			$oAdmin_Form->add($oAdmin_Form_Action);

			/**
			 * Создаем действие формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Изменить статус активности строки';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Activity of information element';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Action = Core_Entity::factory('Admin_Form_Action');
			$oAdmin_Form_Action->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Action->name = 'changeActive';
			$oAdmin_Form_Action->picture = '';
			$oAdmin_Form_Action->icon = '';
			$oAdmin_Form_Action->color = '';
			$oAdmin_Form_Action->single = '0';
			$oAdmin_Form_Action->group = '0';
			$oAdmin_Form_Action->sorting = '40';
			$oAdmin_Form_Action->dataset = '-1';
			$oAdmin_Form_Action->confirm = '0';
			$oAdmin_Form->add($oAdmin_Form_Action);

			/**
			 * Создаем действие формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Удаление файла';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Delete file';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Action = Core_Entity::factory('Admin_Form_Action');
			$oAdmin_Form_Action->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Action->name = 'deleteFile';
			$oAdmin_Form_Action->picture = '';
			$oAdmin_Form_Action->icon = 'fa fa-trash-o';
			$oAdmin_Form_Action->color = 'darkorange';
			$oAdmin_Form_Action->single = '0';
			$oAdmin_Form_Action->group = '0';
			$oAdmin_Form_Action->sorting = '50';
			$oAdmin_Form_Action->dataset = '-1';
			$oAdmin_Form_Action->confirm = '0';
			$oAdmin_Form->add($oAdmin_Form_Action);
		}

		$oAdmin_Form = Core_Entity::factory('Admin_Form')->getByGuid('8AFDC286-A8FD-6148-B4A3-FADCB59D6050');

		if (is_null($oAdmin_Form))
		{
			/**
			 * Создаем значения Admin_Word_Value
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Колонки';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Columns';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			/**
			 * Создаем форму
			 */
			$oAdmin_Form = Core_Entity::factory('Admin_Form');
			$oAdmin_Form->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form->on_page = 20;
			$oAdmin_Form->key_field = 'id';
			$oAdmin_Form->show_operations = 1;
			$oAdmin_Form->show_group_operations = 1;
			$oAdmin_Form->default_order_field = 'sorting';
			$oAdmin_Form->default_order_direction = 1;
			$oAdmin_Form->guid = '8AFDC286-A8FD-6148-B4A3-FADCB59D6050';
			$oAdmin_Form->save();

			/**
			 * Создаем поле формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'ID';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'ID';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Field = Core_Entity::factory('Admin_Form_Field');
			$oAdmin_Form_Field->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Field->name = 'id';
			$oAdmin_Form_Field->sorting = 0;
			$oAdmin_Form_Field->ico = '';
			$oAdmin_Form_Field->type = 1;
			$oAdmin_Form_Field->format = '';
			$oAdmin_Form_Field->allow_sorting = 1;
			$oAdmin_Form_Field->allow_filter = 1;
			$oAdmin_Form_Field->editable = 0;
			$oAdmin_Form_Field->filter_type = 0;
			$oAdmin_Form_Field->class = '';
			$oAdmin_Form_Field->width = '55px';
			$oAdmin_Form_Field->image = '';
			$oAdmin_Form_Field->link = '';
			$oAdmin_Form_Field->onclick = '';
			$oAdmin_Form_Field->list = '';
			$oAdmin_Form->add($oAdmin_Form_Field);

			/**
			 * Создаем поле формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Название колонки';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Name';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Field = Core_Entity::factory('Admin_Form_Field');
			$oAdmin_Form_Field->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Field->name = 'name';
			$oAdmin_Form_Field->sorting = 10;
			$oAdmin_Form_Field->ico = '';
			$oAdmin_Form_Field->type = 1;
			$oAdmin_Form_Field->format = '';
			$oAdmin_Form_Field->allow_sorting = 1;
			$oAdmin_Form_Field->allow_filter = 1;
			$oAdmin_Form_Field->editable = 1;
			$oAdmin_Form_Field->filter_type = 0;
			$oAdmin_Form_Field->class = '';
			$oAdmin_Form_Field->width = '';
			$oAdmin_Form_Field->image = '';
			$oAdmin_Form_Field->link = '';
			$oAdmin_Form_Field->onclick = '';
			$oAdmin_Form_Field->list = '';
			$oAdmin_Form->add($oAdmin_Form_Field);

			/**
			 * Создаем поле формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Сортировка';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Sorting';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Field = Core_Entity::factory('Admin_Form_Field');
			$oAdmin_Form_Field->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Field->name = 'sorting';
			$oAdmin_Form_Field->sorting = 20;
			$oAdmin_Form_Field->ico = 'fa fa-sort-numeric-asc';
			$oAdmin_Form_Field->type = 2;
			$oAdmin_Form_Field->format = '';
			$oAdmin_Form_Field->allow_sorting = 1;
			$oAdmin_Form_Field->allow_filter = 0;
			$oAdmin_Form_Field->editable = 0;
			$oAdmin_Form_Field->filter_type = 0;
			$oAdmin_Form_Field->class = 'hidden-xxs hidden-xs hidden-sm';
			$oAdmin_Form_Field->width = '60px';
			$oAdmin_Form_Field->image = '';
			$oAdmin_Form_Field->link = '';
			$oAdmin_Form_Field->onclick = '';
			$oAdmin_Form_Field->list = '';
			$oAdmin_Form->add($oAdmin_Form_Field);

			/**
			 * Создаем поле формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Активность';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Active';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Field = Core_Entity::factory('Admin_Form_Field');
			$oAdmin_Form_Field->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Field->name = 'active';
			$oAdmin_Form_Field->sorting = 30;
			$oAdmin_Form_Field->ico = 'fa fa-lightbulb-o';
			$oAdmin_Form_Field->type = 7;
			$oAdmin_Form_Field->format = '';
			$oAdmin_Form_Field->allow_sorting = 0;
			$oAdmin_Form_Field->allow_filter = 1;
			$oAdmin_Form_Field->editable = 0;
			$oAdmin_Form_Field->filter_type = 0;
			$oAdmin_Form_Field->class = 'hidden-xxs text-center';
			$oAdmin_Form_Field->width = '25px';
			$oAdmin_Form_Field->image = '1=/admin/images/check.gif==fa fa-lightbulb-o fa-active
				0=/admin/images/not_check.gif==fa fa-lightbulb-o fa-inactive';
			$oAdmin_Form_Field->link = '/admin/table/column/index.php?hostcms[action]=changeActive&hostcms[checked][{dataset_key}][{id}]=1&table_id={table_id}';
			$oAdmin_Form_Field->onclick = '$.adminLoad({path: \'/admin/table/column/index.php\',additionalParams: \'hostcms[checked][{dataset_key}][{id}]=1&table_id={table_id}\', action: \'changeActive\', windowId: \'{windowId}\'}); return false';
			$oAdmin_Form_Field->list = '';
			$oAdmin_Form->add($oAdmin_Form_Field);

			/**
			 * Создаем действие формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Применить';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Apply';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Action = Core_Entity::factory('Admin_Form_Action');
			$oAdmin_Form_Action->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Action->name = 'apply';
			$oAdmin_Form_Action->picture = '/admin/images/edit.gif';
			$oAdmin_Form_Action->icon = 'fa fa-check';
			$oAdmin_Form_Action->color = 'palegreen';
			$oAdmin_Form_Action->single = '0';
			$oAdmin_Form_Action->group = '1';
			$oAdmin_Form_Action->sorting = '0';
			$oAdmin_Form_Action->dataset = '-1';
			$oAdmin_Form_Action->confirm = '0';
			$oAdmin_Form->add($oAdmin_Form_Action);

			/**
			 * Создаем действие формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Редактировать';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Edit';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Action = Core_Entity::factory('Admin_Form_Action');
			$oAdmin_Form_Action->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Action->name = 'edit';
			$oAdmin_Form_Action->picture = '/admin/images/edit.gif';
			$oAdmin_Form_Action->icon = 'fa fa-pencil';
			$oAdmin_Form_Action->color = 'palegreen';
			$oAdmin_Form_Action->single = '1';
			$oAdmin_Form_Action->group = '0';
			$oAdmin_Form_Action->sorting = '10';
			$oAdmin_Form_Action->dataset = '-1';
			$oAdmin_Form_Action->confirm = '0';
			$oAdmin_Form->add($oAdmin_Form_Action);

			/**
			 * Создаем действие формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Копировать';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Copy';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Action = Core_Entity::factory('Admin_Form_Action');
			$oAdmin_Form_Action->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Action->name = 'copy';
			$oAdmin_Form_Action->picture = '/admin/images/copy.gif';
			$oAdmin_Form_Action->icon = 'fa fa-copy';
			$oAdmin_Form_Action->color = 'info';
			$oAdmin_Form_Action->single = '1';
			$oAdmin_Form_Action->group = '1';
			$oAdmin_Form_Action->sorting = '15';
			$oAdmin_Form_Action->dataset = '-1';
			$oAdmin_Form_Action->confirm = '1';
			$oAdmin_Form->add($oAdmin_Form_Action);

			/**
			 * Создаем действие формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Удалить';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Delete';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Action = Core_Entity::factory('Admin_Form_Action');
			$oAdmin_Form_Action->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Action->name = 'markDeleted';
			$oAdmin_Form_Action->picture = '/admin/images/delete.gif';
			$oAdmin_Form_Action->icon = 'fa fa-trash-o';
			$oAdmin_Form_Action->color = 'darkorange';
			$oAdmin_Form_Action->single = '1';
			$oAdmin_Form_Action->group = '1';
			$oAdmin_Form_Action->sorting = '20';
			$oAdmin_Form_Action->dataset = '-1';
			$oAdmin_Form_Action->confirm = '1';
			$oAdmin_Form->add($oAdmin_Form_Action);

			/**
			 * Создаем действие формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Изменить активность колонки';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Change activity of column';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Action = Core_Entity::factory('Admin_Form_Action');
			$oAdmin_Form_Action->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Action->name = 'changeActive';
			$oAdmin_Form_Action->picture = '';
			$oAdmin_Form_Action->icon = '';
			$oAdmin_Form_Action->color = '';
			$oAdmin_Form_Action->single = '0';
			$oAdmin_Form_Action->group = '0';
			$oAdmin_Form_Action->sorting = '30';
			$oAdmin_Form_Action->dataset = '-1';
			$oAdmin_Form_Action->confirm = '0';
			$oAdmin_Form->add($oAdmin_Form_Action);
		}

		$oAdmin_Form = Core_Entity::factory('Admin_Form')->getByGuid('C77303E5-279E-0E68-F8F1-206EE698B19D');

		if (is_null($oAdmin_Form))
		{
			/**
			 * Создаем значения Admin_Word_Value
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Таблицы';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Tables';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			/**
			 * Создаем форму
			 */
			$oAdmin_Form = Core_Entity::factory('Admin_Form');
			$oAdmin_Form->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form->on_page = 20;
			$oAdmin_Form->key_field = 'id';
			$oAdmin_Form->show_operations = 1;
			$oAdmin_Form->show_group_operations = 1;
			$oAdmin_Form->default_order_field = 'sorting';
			$oAdmin_Form->default_order_direction = 1;
			$oAdmin_Form->guid = 'C77303E5-279E-0E68-F8F1-206EE698B19D';
			$oAdmin_Form->save();

			/**
			 * Создаем поле формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'ID';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'ID';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Field = Core_Entity::factory('Admin_Form_Field');
			$oAdmin_Form_Field->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Field->name = 'id';
			$oAdmin_Form_Field->sorting = 0;
			$oAdmin_Form_Field->ico = '';
			$oAdmin_Form_Field->type = 1;
			$oAdmin_Form_Field->format = '';
			$oAdmin_Form_Field->allow_sorting = 1;
			$oAdmin_Form_Field->allow_filter = 0;
			$oAdmin_Form_Field->editable = 0;
			$oAdmin_Form_Field->filter_type = 0;
			$oAdmin_Form_Field->class = '';
			$oAdmin_Form_Field->width = '55px';
			$oAdmin_Form_Field->image = '';
			$oAdmin_Form_Field->link = '';
			$oAdmin_Form_Field->onclick = '';
			$oAdmin_Form_Field->list = '';
			$oAdmin_Form->add($oAdmin_Form_Field);

			/**
			 * Создаем поле формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Название таблицы';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Table name';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Field = Core_Entity::factory('Admin_Form_Field');
			$oAdmin_Form_Field->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Field->name = 'name';
			$oAdmin_Form_Field->sorting = 10;
			$oAdmin_Form_Field->ico = '';
			$oAdmin_Form_Field->type = 4;
			$oAdmin_Form_Field->format = '';
			$oAdmin_Form_Field->allow_sorting = 1;
			$oAdmin_Form_Field->allow_filter = 0;
			$oAdmin_Form_Field->editable = 0;
			$oAdmin_Form_Field->filter_type = 0;
			$oAdmin_Form_Field->class = '';
			$oAdmin_Form_Field->width = '';
			$oAdmin_Form_Field->image = '';
			$oAdmin_Form_Field->link = '/admin/table/row/index.php?table_id={table_id}&table_item_id={id}';
			$oAdmin_Form_Field->onclick = '$.adminLoad({path: \'/admin/table/row/index.php\',additionalParams: \'table_id={table_id}&table_item_id={id}\', windowId: \'{windowId}\'}); return false';
			$oAdmin_Form_Field->list = '';
			$oAdmin_Form->add($oAdmin_Form_Field);

			/**
			 * Создаем поле формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Сортировка';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Sorting';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Field = Core_Entity::factory('Admin_Form_Field');
			$oAdmin_Form_Field->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Field->name = 'sorting';
			$oAdmin_Form_Field->sorting = 20;
			$oAdmin_Form_Field->ico = 'fa fa-sort-numeric-asc';
			$oAdmin_Form_Field->type = 2;
			$oAdmin_Form_Field->format = '';
			$oAdmin_Form_Field->allow_sorting = 1;
			$oAdmin_Form_Field->allow_filter = 0;
			$oAdmin_Form_Field->editable = 1;
			$oAdmin_Form_Field->filter_type = 0;
			$oAdmin_Form_Field->class = 'hidden-xxs hidden-xs hidden-sm';
			$oAdmin_Form_Field->width = '60px';
			$oAdmin_Form_Field->image = '';
			$oAdmin_Form_Field->link = '';
			$oAdmin_Form_Field->onclick = '';
			$oAdmin_Form_Field->list = '';
			$oAdmin_Form->add($oAdmin_Form_Field);

			/**
			 * Создаем действие формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Применить';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Apply';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Action = Core_Entity::factory('Admin_Form_Action');
			$oAdmin_Form_Action->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Action->name = 'apply';
			$oAdmin_Form_Action->picture = '/admin/images/apply.gif';
			$oAdmin_Form_Action->icon = 'fa fa-check';
			$oAdmin_Form_Action->color = 'palegreen';
			$oAdmin_Form_Action->single = '0';
			$oAdmin_Form_Action->group = '1';
			$oAdmin_Form_Action->sorting = '0';
			$oAdmin_Form_Action->dataset = '-1';
			$oAdmin_Form_Action->confirm = '0';
			$oAdmin_Form->add($oAdmin_Form_Action);

			/**
			 * Создаем действие формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Редактировать';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Edit';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Action = Core_Entity::factory('Admin_Form_Action');
			$oAdmin_Form_Action->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Action->name = 'edit';
			$oAdmin_Form_Action->picture = '/admin/images/edit.gif';
			$oAdmin_Form_Action->icon = 'fa fa-pencil';
			$oAdmin_Form_Action->color = 'palegreen';
			$oAdmin_Form_Action->single = '1';
			$oAdmin_Form_Action->group = '0';
			$oAdmin_Form_Action->sorting = '10';
			$oAdmin_Form_Action->dataset = '-1';
			$oAdmin_Form_Action->confirm = '0';
			$oAdmin_Form->add($oAdmin_Form_Action);

			/**
			 * Создаем действие формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Копировать';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Copy';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Action = Core_Entity::factory('Admin_Form_Action');
			$oAdmin_Form_Action->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Action->name = 'copy';
			$oAdmin_Form_Action->picture = '/admin/images/copy.gif';
			$oAdmin_Form_Action->icon = 'fa fa-copy';
			$oAdmin_Form_Action->color = 'info';
			$oAdmin_Form_Action->single = '1';
			$oAdmin_Form_Action->group = '1';
			$oAdmin_Form_Action->sorting = '15';
			$oAdmin_Form_Action->dataset = '-1';
			$oAdmin_Form_Action->confirm = '1';
			$oAdmin_Form->add($oAdmin_Form_Action);

			/**
			 * Создаем действие формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Удалить';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Delete';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Action = Core_Entity::factory('Admin_Form_Action');
			$oAdmin_Form_Action->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Action->name = 'markDeleted';
			$oAdmin_Form_Action->picture = '/admin/images/delete.gif';
			$oAdmin_Form_Action->icon = 'fa fa-trash-o';
			$oAdmin_Form_Action->color = 'darkorange';
			$oAdmin_Form_Action->single = '1';
			$oAdmin_Form_Action->group = '1';
			$oAdmin_Form_Action->sorting = '20';
			$oAdmin_Form_Action->dataset = '-1';
			$oAdmin_Form_Action->confirm = '1';
			$oAdmin_Form->add($oAdmin_Form_Action);
		}

		$oAdmin_Form = Core_Entity::factory('Admin_Form')->getByGuid('72B7CBE2-B5C7-46F9-47A5-F77DF5BB1570');

		if (is_null($oAdmin_Form))
		{
			/**
			 * Создаем значения Admin_Word_Value
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Схемы таблиц';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			/**
			 * Создаем форму
			 */
			$oAdmin_Form = Core_Entity::factory('Admin_Form');
			$oAdmin_Form->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form->on_page = 20;
			$oAdmin_Form->key_field = 'id';
			$oAdmin_Form->show_operations = 1;
			$oAdmin_Form->show_group_operations = 1;
			$oAdmin_Form->default_order_field = 'sorting';
			$oAdmin_Form->default_order_direction = 2;
			$oAdmin_Form->guid = '72B7CBE2-B5C7-46F9-47A5-F77DF5BB1570';
			$oAdmin_Form->save();

			/**
			 * Создаем поле формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'ID';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'ID';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Field = Core_Entity::factory('Admin_Form_Field');
			$oAdmin_Form_Field->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Field->name = 'id';
			$oAdmin_Form_Field->sorting = 0;
			$oAdmin_Form_Field->ico = '';
			$oAdmin_Form_Field->type = 1;
			$oAdmin_Form_Field->format = '';
			$oAdmin_Form_Field->allow_sorting = 1;
			$oAdmin_Form_Field->allow_filter = 0;
			$oAdmin_Form_Field->editable = 0;
			$oAdmin_Form_Field->filter_type = 0;
			$oAdmin_Form_Field->class = '';
			$oAdmin_Form_Field->width = '55px';
			$oAdmin_Form_Field->image = '';
			$oAdmin_Form_Field->link = '';
			$oAdmin_Form_Field->onclick = '';
			$oAdmin_Form_Field->list = '';
			$oAdmin_Form->add($oAdmin_Form_Field);

			/**
			 * Создаем поле формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Название схемы';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Scheme name';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Field = Core_Entity::factory('Admin_Form_Field');
			$oAdmin_Form_Field->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Field->name = 'name';
			$oAdmin_Form_Field->sorting = 5;
			$oAdmin_Form_Field->ico = '';
			$oAdmin_Form_Field->type = 4;
			$oAdmin_Form_Field->format = '';
			$oAdmin_Form_Field->allow_sorting = 0;
			$oAdmin_Form_Field->allow_filter = 0;
			$oAdmin_Form_Field->editable = 1;
			$oAdmin_Form_Field->filter_type = 0;
			$oAdmin_Form_Field->class = '';
			$oAdmin_Form_Field->width = '';
			$oAdmin_Form_Field->image = '';
			$oAdmin_Form_Field->link = '/admin/table/item/index.php?table_id={id}';
			$oAdmin_Form_Field->onclick = '$.adminLoad({path: \'/admin/table/item/index.php\',additionalParams: \'table_id={id}\', windowId: \'{windowId}\'}); return false';
			$oAdmin_Form_Field->list = '';
			$oAdmin_Form->add($oAdmin_Form_Field);

			/**
			 * Создаем поле формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Символьный код';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Code';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Field = Core_Entity::factory('Admin_Form_Field');
			$oAdmin_Form_Field->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Field->name = 'code';
			$oAdmin_Form_Field->sorting = 10;
			$oAdmin_Form_Field->ico = '';
			$oAdmin_Form_Field->type = 1;
			$oAdmin_Form_Field->format = '';
			$oAdmin_Form_Field->allow_sorting = 0;
			$oAdmin_Form_Field->allow_filter = 0;
			$oAdmin_Form_Field->editable = 1;
			$oAdmin_Form_Field->filter_type = 0;
			$oAdmin_Form_Field->class = '';
			$oAdmin_Form_Field->width = '';
			$oAdmin_Form_Field->image = '';
			$oAdmin_Form_Field->link = '/admin/table/item/index.php?table_id={id}';
			$oAdmin_Form_Field->onclick = '$.adminLoad({path: \'/admin/table/item/index.php\',additionalParams: \'table_id={id}\', windowId: \'{windowId}\'}); return false';
			$oAdmin_Form_Field->list = '';
			$oAdmin_Form->add($oAdmin_Form_Field);

			/**
			 * Создаем поле формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Сортировка';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Sorting';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Field = Core_Entity::factory('Admin_Form_Field');
			$oAdmin_Form_Field->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Field->name = 'sorting';
			$oAdmin_Form_Field->sorting = 15;
			$oAdmin_Form_Field->ico = 'fa fa-sort-numeric-asc';
			$oAdmin_Form_Field->type = 2;
			$oAdmin_Form_Field->format = '';
			$oAdmin_Form_Field->allow_sorting = 1;
			$oAdmin_Form_Field->allow_filter = 0;
			$oAdmin_Form_Field->editable = 0;
			$oAdmin_Form_Field->filter_type = 0;
			$oAdmin_Form_Field->class = 'hidden-xxs hidden-xs hidden-sm';
			$oAdmin_Form_Field->width = '60px';
			$oAdmin_Form_Field->image = '';
			$oAdmin_Form_Field->link = '';
			$oAdmin_Form_Field->onclick = '';
			$oAdmin_Form_Field->list = '';
			$oAdmin_Form->add($oAdmin_Form_Field);

			/**
			 * Создаем действие формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Применить';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Apply';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Action = Core_Entity::factory('Admin_Form_Action');
			$oAdmin_Form_Action->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Action->name = 'apply';
			$oAdmin_Form_Action->picture = '/admin/images/apply.gif';
			$oAdmin_Form_Action->icon = 'fa fa-check';
			$oAdmin_Form_Action->color = 'palegreen';
			$oAdmin_Form_Action->single = '0';
			$oAdmin_Form_Action->group = '1';
			$oAdmin_Form_Action->sorting = '0';
			$oAdmin_Form_Action->dataset = '-1';
			$oAdmin_Form_Action->confirm = '0';
			$oAdmin_Form->add($oAdmin_Form_Action);

			/**
			 * Создаем действие формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Редактировать';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Edit';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Action = Core_Entity::factory('Admin_Form_Action');
			$oAdmin_Form_Action->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Action->name = 'edit';
			$oAdmin_Form_Action->picture = '/admin/images/edit.gif';
			$oAdmin_Form_Action->icon = 'fa fa-pencil';
			$oAdmin_Form_Action->color = 'palegreen';
			$oAdmin_Form_Action->single = '1';
			$oAdmin_Form_Action->group = '0';
			$oAdmin_Form_Action->sorting = '10';
			$oAdmin_Form_Action->dataset = '-1';
			$oAdmin_Form_Action->confirm = '0';
			$oAdmin_Form->add($oAdmin_Form_Action);

			/**
			 * Создаем действие формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Копировать';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Copy';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Action = Core_Entity::factory('Admin_Form_Action');
			$oAdmin_Form_Action->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Action->name = 'copy';
			$oAdmin_Form_Action->picture = '/admin/images/copy.gif';
			$oAdmin_Form_Action->icon = 'fa fa-copy';
			$oAdmin_Form_Action->color = 'info';
			$oAdmin_Form_Action->single = '1';
			$oAdmin_Form_Action->group = '1';
			$oAdmin_Form_Action->sorting = '15';
			$oAdmin_Form_Action->dataset = '-1';
			$oAdmin_Form_Action->confirm = '1';
			$oAdmin_Form->add($oAdmin_Form_Action);

			/**
			 * Создаем действие формы
			 */
			$oAdmin_Word = Core_Entity::factory('Admin_Word')->save();

			$oAdmin_Word_Value_RU = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_RU->admin_language_id = 1;
			$oAdmin_Word_Value_RU->name = 'Удалить';
			$oAdmin_Word->add($oAdmin_Word_Value_RU);

			$oAdmin_Word_Value_EN = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_EN->admin_language_id = 2;
			$oAdmin_Word_Value_EN->name = 'Delete';
			$oAdmin_Word->add($oAdmin_Word_Value_EN);

			$oAdmin_Word_Value_UA = Core_Entity::factory('Admin_Word_Value');
			$oAdmin_Word_Value_UA->admin_language_id = 3;
			$oAdmin_Word_Value_UA->name = '';
			$oAdmin_Word->add($oAdmin_Word_Value_UA);

			$oAdmin_Form_Action = Core_Entity::factory('Admin_Form_Action');
			$oAdmin_Form_Action->admin_word_id = $oAdmin_Word->id;
			$oAdmin_Form_Action->name = 'markDeleted';
			$oAdmin_Form_Action->picture = '/admin/images/delete.gif';
			$oAdmin_Form_Action->icon = 'fa fa-trash-o';
			$oAdmin_Form_Action->color = 'darkorange';
			$oAdmin_Form_Action->single = '1';
			$oAdmin_Form_Action->group = '1';
			$oAdmin_Form_Action->sorting = '20';
			$oAdmin_Form_Action->dataset = '-1';
			$oAdmin_Form_Action->confirm = '1';
			$oAdmin_Form->add($oAdmin_Form_Action);
		}

		$oShortcode = Core_Entity::factory('Shortcode');

		if (!is_null($oShortcode)) {
			$oShortcode->name = 'Показ таблиц';
			$oShortcode->shortcode('table');
			$oShortcode->sorting(777);
			$oShortcode->example('[table id="1" limit="10" item="0" xsl="ArtatomTables"]');
			$oShortcode->php('$args += array(
\'item\' => 0,
\'limit\' => 10,
\'wrap_table\' => 1,
\'show_table_header\' => 1,
\'show_table_description\' => 1,
\'file_extension_label_class\' => \'label label-default\',
\'panel_class\' => \'panel panel-default rounded-0\',
\'panel_header_class\' => \'panel-heading\',
\'panel_body_class\' => \'panel-body\',
\'table_class\' => \'table\'
);

ob_start();

if (Core::moduleIsActive(\'table\'))
{
if (isset($args[\'id\']) && $args[\'id\'])
{
	$Table_Controller_Show = new Table_Controller_Show(
		Core_Entity::factory(\'Table\', $args[\'id\'])
	);

	$oXsl = Core_Entity::factory(\'Xsl\')->getByName($args[\'xsl\']);

	if ($oXsl)
	{
		$Table_Controller_Show
			->wrap_table($args[\'wrap_table\'])
			->show_table_header($args[\'show_table_header\'])
			->show_table_description($args[\'show_table_description\'])
			->file_extension_label_class($args[\'file_extension_label_class\'])
			->panel_class($args[\'panel_class\'])
			->panel_header_class($args[\'panel_header_class\'])
			->panel_body_class($args[\'panel_body_class\'])
			->table_class($args[\'table_class\'])
			->item($args[\'item\'])
			->limit($args[\'limit\'])
			->xsl($oXsl)
			->show();
	}
	else
	{
		?>Ошибка, XSL не найден!<?php
	}
}
else
{
	?>Ошибка, ID схемы таблиц не указан!<?php
}
}

return ob_get_clean();
')->save();
		}
	}

	/**
	 * Uninstall module.
	 * @return void
	 */
	public function uninstall()
	{
		// SQL drop module tables
		$query = "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";
			SET AUTOCOMMIT = 0;
			START TRANSACTION;
			SET time_zone = \"+00:00\";
			DROP TABLE IF EXISTS `tables`, `table_cells`, `table_cell_value_files`, `table_cell_value_ints`, `table_cell_value_strings`, `table_cell_value_textareas`, `table_cell_value_wysiwygs`, `table_columns`, `table_datatypes`, `table_items`, `table_rows`;
			COMMIT;";
		Sql_Controller::instance()->execute($query);

		// delete admin forms
		Core_Entity::factory('Admin_Form')->getByGuid('80023F3D-22AB-618C-B104-DB8B034AD549')->delete();
		Core_Entity::factory('Admin_Form')->getByGuid('479BEBAC-A43C-FF69-6137-9AC6286CC144')->delete();
		Core_Entity::factory('Admin_Form')->getByGuid('8AFDC286-A8FD-6148-B4A3-FADCB59D6050')->delete();
		Core_Entity::factory('Admin_Form')->getByGuid('C77303E5-279E-0E68-F8F1-206EE698B19D')->delete();
		Core_Entity::factory('Admin_Form')->getByGuid('72B7CBE2-B5C7-46F9-47A5-F77DF5BB1570')->delete();
		$oShortcode = Core_Entity::factory('Shortcode')->getByName('Показ таблиц');
		if ($oShortcode->id) {
			$oShortcode->delete();
		}
	}
}
