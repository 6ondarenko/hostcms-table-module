<?php
/**
 * Table_Column_Controller_Edit
 *
 * @package Artatom
 * @subpackage Table
 * @version 1.0
 * @author Konstantin Bondarenko
 * @copyright © 2007-2018 Artatom http://www.artatom.ru
 */
class Table_Column_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit
{
	/**
	 * Prepare form for edit table column object in admin panel
	 * @return void
	 */
	protected function _prepareForm()
	{
		// parent::_prepareForm();
		// $oTab = $this->getTab('main');
		// $this->issetTab('additional') && $this->deleteTab('additional');

		// create main tab
		$oTab = Admin_Form_Entity::factory('Tab')->name('main');

		// create name field
		$oDivNameRow = Admin_Form_Entity::factory('Div')->class('row');
		// create input:name field
		$oInputName = Admin_Form_Entity::factory('Input');
		$oInputName
			->caption(Core::_('Table_Column.name'))
			->format(array(
				'maxlen' => array('value' => 255),
				'minlen' => array('value' => 1),
				// 'reg' => array('value' => '/^[\-|+]{0,1}[0-9]+$/'), // digits
			))
			->name('name')
			->value($this->_object->name)
			->divAttr(array('class' => 'form-group col-xs-12'));
		// add input to div.row
		$oDivNameRow->add($oInputName);
		// add div.row to main tab
		$oTab->add($oDivNameRow);

		// create select field with data types
		$oDivSelectRow = Admin_Form_Entity::factory('Div')->class('row');
		$oSelect = Admin_Form_Entity::factory('Select');
		$oSelect
			->name('table_datatype_id')
			->value($this->_object->table_datatype_id)
			->caption(Core::_('Table_Column.datatype_caption'));
		$aOptions = array();
		// fill select options
		$oTableDatatypes = Core_Entity::factory('Table_Datatype');
		$aTableDatatypes = $oTableDatatypes->findAll();
		foreach ($aTableDatatypes as $oTableDatatype) {
			$aOptions[$oTableDatatype->id] = $oTableDatatype->name;
		}
		// add select options
		$oSelect->options($aOptions);
		// add select into div.row
		$oDivSelectRow->add($oSelect);
		// add div.row with select into main tab
		$oTab->add($oDivSelectRow);
		// add main tab into controller
		$this->addTab($oTab);

		// create multiple checkbox
		$oDivMultipleRow = Admin_Form_Entity::factory('Div')->class('row');
		$oCheckboxMultiple = Admin_Form_Entity::factory('Checkbox');
		$oCheckboxMultiple
			->name('multiple')
			->value($this->_object->multiple)
			->postingUnchecked(true)
			->caption(Core::_('Table_Column.multiple'));
		// add input to div.row
		$oDivMultipleRow->add($oCheckboxMultiple);
		// add div.row to main tab
		$oTab->add($oDivMultipleRow);

		// create sorting field
		$oDivSortingRow = Admin_Form_Entity::factory('Div')->class('row');
		// create input:name field
		$oInputSorting = Admin_Form_Entity::factory('Input');
		$oInputSorting
			->caption(Core::_('Table_Column.sorting'))
			->format(array(
				'maxlen' => array('value' => 6),
				'reg' => array('value' => '/^[\-|+]{0,1}[0-9]+$/'), // digits
			))
			->name('sorting')
			->value($this->_object->sorting)
			->divAttr(array('class' => 'form-group col-xs-12'));
		// add input to div.row
		$oDivSortingRow->add($oInputSorting);
		// add div.row to main tab
		$oTab->add($oDivSortingRow);

		// create active checkbox
		$oDivActiveRow = Admin_Form_Entity::factory('Div')->class('row');
		$oCheckboxActive = Admin_Form_Entity::factory('Checkbox');
		$oCheckboxActive
			->name('active')
			->value($this->_object->active)
			->postingUnchecked(true)
			->caption(Core::_('Table_Column.active'));
		// add input to div.row
		$oDivActiveRow->add($oCheckboxActive);
		// add div.row to main tab
		$oTab->add($oDivActiveRow);
	}

	/**
	 * Apply table column object properties
	 * @return void
	 */
	protected function _applyObjectProperty()
	{
		$table_id = Core_Array::getGet('table_id', NULL);
		$this->_object->table_id = $table_id;
		parent::_applyObjectProperty(); // saving row, now we can get Table_Row id
	}
}