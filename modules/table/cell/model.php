<?php
/**
 * Table_Cell_Model
 *
 * @package Artatom
 * @subpackage Table
 * @version 1.0
 * @author Konstantin Bondarenko
 * @copyright © 2007-2018 Artatom http://www.artatom.ru
 */
class Table_Cell_Model extends Core_Entity
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
		'table_row' => array(),
		'table_column' => array(),
	);

	/**
	 * Table cell object preload values
	 * @var array
	 */
	protected $_preloadValues = array(
		'site_id' => CURRENT_SITE,
		'sorting' => 0,
		'active' => 1,
		'deleted' => 0
	);

	/**
	 * Get table datatype object for current cell
	 * @return Table_Datatype_Model
	 */
	public function getTableDatatype()
	{
		return $this->Table_Column->Table_Datatype;
	}

	/**
	 * Get single value for current cell object
	 * @return Table_Cell_Value_Model object inherited by Table_Cell_Value_Model
	 */
	public function getValue()
	{
		return $this->getValues(true);
	}

	/**
	 * Get collection of table cell value object for current table cell object
	 * @param  boolean                  $only_first  only first found object
	 * @return Table_Cell_Value_Object               collection or one first object inherited by Table_Cell_Value_Model
	 */
	public function getValues($only_first = false)
	{
		$oTableColumn = $this->Table_Column;
		$oTableDatatype = $oTableColumn->Table_Datatype;
		$sTableCellValueModelName = $oTableDatatype->model_name;
		// это не получение по колонке и ячейке
		$oValues = Core_Entity::factory(strval($sTableCellValueModelName));
		$oValues
			->queryBuilder()
			->where('table_column_id', '=', intval($oTableColumn->id))
			->setAnd()
			->where('table_cell_id', '=', intval($this->id));
		$aValues = $oValues->findAll();
		// если берём только первый элемент
		if ($only_first == true) {
			return reset($aValues); // first item
		} else {
			return $aValues; // all items
		}
	}

	/**
	 * Get next primary key (last + 1)
	 * @return integer primary key
	 */
	static public function getNextPrimaryKey()
	{
		$oTableCells = Core_Entity::factory("Table_Cell");
		$oTableCells
			->queryBuilder()
			->clearOrderBy()
			->orderBy('id', 'DESC')
			->limit(1);
		$aTableCells = $oTableCells->findAll(false);
		return intval(reset($aTableCells)->id);
	}

	/**
	 * Get cell path
	 * @return [type] [description]
	 */
	public function getCellPath()
	{
		return $this->Table_Row->getRowPath() . 'cell_' . $this->id . '/';
	}

	/**
	 * Get cell href
	 * @return string
	 */
	public function getCellHref()
	{
		return $this->Table_Row->getRowHref() . 'cell_' . $this->id . '/';
	}

	/**
	 * Create dir for file cell values
	 * @return self
	 */
	public function createDir()
	{
		if (!is_dir($this->getCellPath()))
		{
			try
			{
				Core_File::mkdir($this->getCellPath(), CHMOD, TRUE);
			} catch (Exception $e) {}
		}
		return $this;
	}

	/**
	 * Delete table cell object from database
	 * @param  mixed $primaryKey primary key
	 * @return self
	 */
	public function delete($primaryKey = NULL)
	{
		if (is_null($primaryKey))
		{
			$primaryKey = $this->getPrimaryKey();
		}
		$this->id = $primaryKey;
		// first of all, undelete for restoring dependencies
		$this->undelete();
		// delete references objects
		$aValues = $this->getValues();
		foreach ($aValues as $oValue) {
			$oValue->delete($oValue->id);
		}
		parent::delete($primaryKey);
		return $this;
	}

	/**
	 * Mark table cell object as deleted
	 * @return self
	 */
	public function markDeleted()
	{
		parent::markDeleted();
		return $this;
	}
}
