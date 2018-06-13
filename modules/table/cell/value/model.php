<?php
/**
 * Table_Cell_Value_Model
 *
 * @package Artatom
 * @subpackage Table
 * @version 1.0
 * @author Konstantin Bondarenko
 * @copyright © 2007-2018 Artatom http://www.artatom.ru
 */
abstract class Table_Cell_Value_Model extends Core_Entity
{
	/**
	 * Get preformatted cell value
	 * @return string used in Table_Row.content()
	 */
	abstract public function getFormattedValue();
}