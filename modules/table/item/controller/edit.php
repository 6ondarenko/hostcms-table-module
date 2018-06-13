<?php
/**
 * Table_Item_Controller_Edit
 *
 * @package Artatom
 * @subpackage Table
 * @version 1.0
 * @author Konstantin Bondarenko
 * @copyright © 2007-2018 Artatom http://www.artatom.ru
 */
class Table_Item_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit
{
	/**
	 * Apply table item object properties
	 * @return void
	 */
	protected function _applyObjectProperty()
	{
		$table_id = Core_Array::getGet('table_id', NULL);
		$this->_object->table_id = $table_id;
		parent::_applyObjectProperty();
	}
}