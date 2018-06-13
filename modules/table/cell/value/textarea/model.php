<?php
/**
 * Table_Cell_Value_Textarea_Model
 *
 * @package Artatom
 * @subpackage Table
 * @version 1.0
 * @author Konstantin Bondarenko
 * @copyright © 2007-2018 Artatom http://www.artatom.ru
 */
class Table_Cell_Value_Textarea_Model extends Table_Cell_Value_Model
{
	/**
	 * Column consist item's name
	 * @var string
	 */
	protected $_nameColumn = 'id';

	/**
	 * Belongs to relations
	 * @var array
	 */
	protected $_belongsTo = array(
		'table_cell' => array(),
		'table_column' => array(),
	);

	/**
	 * Name of tag
	 * @var string
	 */
	protected $_tagName = 'table_cell_value';

	/**
	 * Get preformatted cell value
	 * @return string used in Table_Row.content()
	 */
	public function getFormattedValue()
	{
		return htmlspecialchars($this->value);
	}
}