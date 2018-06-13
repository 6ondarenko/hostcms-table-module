<?php
/**
 * Table_Row_Model
 *
 * @package Artatom
 * @subpackage Table
 * @version 1.0
 * @author Konstantin Bondarenko
 * @copyright © 2007-2018 Artatom http://www.artatom.ru
 */
class Table_Row_Model extends Core_Entity
{
	/**
	 * One-to-many or many-to-many relations
	 * @var array
	 */
	protected $_hasMany = array(
		'table_cell' => array(),
	);

	/**
	 * Belongs to relations
	 * @var array
	 */
	protected $_belongsTo = array(
		'table' => array(),
		'table_item' => array(),
	);

	/**
	 * Row object preload values
	 * @var array
	 */
	protected $_preloadValues = array(
		'site_id' => CURRENT_SITE,
		'sorting' => 0,
		'active' => 1,
		'deleted' => 0,
	);

	/**
	 * Constructor.
	 */
	public function __construct($id = NULL)
	{
		parent::__construct($id);

		if (is_null($id))
		{
			$oUserCurrent = Core_Entity::factory('User', 0)->getCurrent();
			$this->_preloadValues['site_id'] = defined('CURRENT_SITE') ? CURRENT_SITE : 0;
		}
	}

	/**
	 * Get row content for view in admin panel
	 * @return void
	 */
	public function content()
	{
		// get table columns
		$table_columns = $this->getTableColumns();
		// create html entity table with one row
		$oHtmlTable = Core::factory('Core_Html_Entity_Table')->class('table');
		$oHtmlThead = Core::factory('Core_Html_Entity_Thead')->style('background-color: rgba(235, 235, 235, .5);');
		$oHtmlTheadTr = Core::factory('Core_Html_Entity_Tr');
		$oHtmlTbodyTr = Core::factory('Core_Html_Entity_Tr');

		// if row is headline
		if ($this->headline) {
			$oHtmlTd = Core::factory('Core_Html_Entity_Td')->style('text-align:center;');
			$oHtmlTd->value($this->name);
			$oHtmlTbodyTr->add($oHtmlTd);
			$oHtmlTable->add($oHtmlTbodyTr);
			$oHtmlTable->execute();
		} else {
			// table columns
			foreach ($table_columns as $column) {
				// table header
				$oHtmlTh = Core::factory('Core_Html_Entity_Th');
				$oHtmlTh->value($column->name);
				$oHtmlTheadTr->add($oHtmlTh);
				// create column
				$oHtmlTd = Core::factory('Core_Html_Entity_Td')->style('text-align:left;');
				// get cell object
				$cell = $this->getTableCellByColumnId($column->id);
				// if column can has many values fill cell as multiple rows with values
				if ($column->multiple == 1) {
					$aValues = $cell->getValues();
					$oValueHtmlTable = Core::factory('Core_Html_Entity_Table')/*->class('table')*/;
					foreach ($aValues as $oValue) {
						if ($oValue) {
							// <tr><td>value</td><tr>
							$oValueHtmlTd = Core::factory('Core_Html_Entity_Td');
							$oValueHtmlTd->value($oValue->getFormattedValue());
							$oValueRow = Core::factory('Core_Html_Entity_Tr');
							$oValueRow->add($oValueHtmlTd);
							$oValueHtmlTable->add($oValueRow);
							// fill column with value cells
						}
					}
					// add values table into column
					$oHtmlTd->add($oValueHtmlTable);
					// add column into table tbody row
					$oHtmlTbodyTr->add($oHtmlTd);
				} else {
					$oValue = $cell->getValue();
					if ($oValue) {
						// add cell value into <td>
						$oHtmlTd->value($oValue->getFormattedValue());
					}
					// add <td> into <tr>
					$oHtmlTbodyTr->add($oHtmlTd);
				}
			} // end foreach
			// adding <tr>
			$oHtmlThead->add($oHtmlTheadTr);
			$oHtmlTable->add($oHtmlThead);
			$oHtmlTable->add($oHtmlTbodyTr);
			$oHtmlTable->execute();
		}
	}

	/**
	 * Get current row columns
	 * @return array collection of Table_Column objects
	 */
	public function getTableColumns()
	{
		$oTableColumns = $this
			->Table
			->Table_Columns;
		$oTableColumns
			->queryBuilder()
			->clearOrderBy()
			->orderBy('sorting', 'asc')
			->where('deleted', '=', 0)
			->where('active', '=', 1);
		return $oTableColumns->findAll();
	}

	/**
	 * Get table cell by column id
	 * @param  integer $column_id id ofcolumn
	 * @return Table_Cell object
	 */
	public function getTableCellByColumnId($column_id)
	{
		$oTableCell = $this->Table_Cells;
		$oTableCell->queryBuilder()
			->where('table_column_id', '=', intval($column_id))
			->where('table_row_id', '=', intval($this->id))
			->where('active', '=', 1)
			->where('deleted', '=', 0);
		$aTableCell = $oTableCell->findAll();
		// return first if it exists or return new created object
		if (count($aTableCell)) {
			return reset($aTableCell);
		} else {
			$oTableCell = Core_Entity::factory('Table_Cell');
			$oTableCell->table_column_id = $column_id;
			$oTableCell->table_row_id = $this->id;
			return $oTableCell;
		}
	}

	/**
	 * Get row href
	 * @return string
	 */
	public function getRowHref()
	{
		return $this->Table_Item->getItemHref() . 'row_' . $this->id . '/';
	}

	/**
	 * Get row path
	 * @return string
	 */
	public function getRowPath()
	{
		return $this->Table_Item->getItemPath() . 'row_' . $this->id . '/';
	}

	/**
	 * Delete row from database
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

		$oTable_Item = $this->Table_Item;
		if (!$oTable_Item->id) {
			$oTable_Item = Core_Orm::factory('Table_Item')->find($this->table_item_id);
			$oTable_Item->undelete();
		}
		if (!$oTable_Item->id) {
			throw new Exception("Error Processing Request", 1);
		}
		// delete references objects
		$aTableCells = $this->Table_Cells->findAll();
		foreach ($aTableCells as $oTableCell) {
			$oTableCell->delete($oTableCell->id);
		}
		// delete object
		parent::delete($primaryKey);
		return $this;
	}

	/**
	 * Change row activity status
	 * @return void
	 */
	public function changeActive()
	{
		$this->active = 1 - $this->active;
		$this->save();
	}

	/**
	 * Copy table row object with referenced child objects
	 * @return Table_Row_Model table row object
	 */
	public function copy()
	{
		$oNewTableRow = parent::copy();
		$aTableCells = $this->Table_Cells->findAll();
		foreach ($aTableCells as $oTableCell) {
			$oNewTableCell = $oTableCell->copy();
			$oNewTableCell
				->table_row_id($oNewTableRow->id)
				->save();
			// get referenced table cell value collection
			$aTableCellValues = $oTableCell->getValues();
			foreach ($aTableCellValues as $oTableCellValue) {
				switch ($oTableCellValue->Table_Column->Table_Datatype->code) {
					case 'file':
						// copy object
						$oNewFileValue = $oTableCellValue->copy();
						// set new file value references
						$oNewFileValue
							->table_column_id($oNewTableCell->Table_Column->id)
							->table_cell_id($oNewTableCell->id);
						// get referenced table column
						$oTableColumn = $oTableCellValue->Table_Column;
						// get lowercased model name
						$sTableDatatypeLc = strtolower($oTableColumn->Table_Datatype->model_name);
						// set old file path and name
						$sOldFileCellPath = $oTableCellValue->Table_Cell->getCellPath();
						$sOldFileName = $oTableCellValue->file;
						// copy file only if exists old file
						if (trim($sOldFileName) && Core_File::filesize($sOldFileCellPath . $sOldFileName)) {
							// set new file path and name
							$sNewFileCellPath = $oNewFileValue
								->Table_Cell
								->getCellPath();
							$aFileName = explode('.', $oNewFileValue->file);
							$sFileExtension = end($aFileName);
							$sNewFileName = $sTableDatatypeLc
								. "_" . $oTableColumn->id
								. "_" . $oNewFileValue->id
								. "." . $sFileExtension;
							// create dir for new file values
							$oNewTableCell->createDir();
							// copy file
							Core_File::upload(
								$sOldFileCellPath . $sOldFileName, // source
								$sNewFileCellPath . $sNewFileName  // destination
							);
							$oNewFileValue
								->file($sNewFileName)
								->value($sNewFileName)
								->save();
						} else {
							$oNewFileValue
								->file('')
								->file_name('')
								->file_description('')
								->value('')
								->save();
						}
					break;
					default:
						$oNewTableCellValue = $oTableCellValue->copy();
						$oNewTableCellValue
							->table_column_id($oNewTableCell->Table_Column->id)
							->table_cell_id($oNewTableCell->id)
							->save();
					break;
				}
			}
		}
		return $oNewTableRow;
	}

	/**
	 * Copy table row object whithout child referenced items
	 * @return Table_Row_Model table row object
	 */
	public function copyWhithoutChildren()
	{
		$oNewTableRow = parent::copy();
		return $oNewTableRow;
	}
}