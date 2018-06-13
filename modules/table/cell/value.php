<?php
/**
 * Table_Cell_Value
 *
 * @package Artatom
 * @subpackage Table
 * @version 1.0
 * @author Konstantin Bondarenko
 * @copyright © 2007-2018 Artatom http://www.artatom.ru
 */
class Table_Cell_Value
{
	/**
	 * Factory of table cell value objects
	 * @param  string $model_name      model name
	 * @param  mixed  $id              object id
	 * @param  mixed  $table_column_id table column id
	 * @param  mixed  $table_cell_id   table cell id
	 * @return Table_Cell_Value_Model  object inherited by Table_Cell_Value_Model
	 */
	public static function factory($model_name, $id = NULL, $table_column_id, $table_cell_id)
	{
		$sTableCellValueModelName = $model_name . '_Model';
		return new $sTableCellValueModelName($id, $table_column_id, $table_cell_id);
	}
}