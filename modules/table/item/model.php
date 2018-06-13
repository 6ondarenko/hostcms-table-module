<?php
/**
 * Table_Item_Model
 *
 * @package Artatom
 * @subpackage Table
 * @version 1.0
 * @author Konstantin Bondarenko
 * @copyright © 2007-2018 Artatom http://www.artatom.ru
 */
class Table_Item_Model extends Core_Entity
{
	/**
	 * One-to-many or many-to-many relations
	 * @var array
	 */
	protected $_hasMany = array(
		'table_row' => array(),
	);

	/**
	 * Belongs to relations
	 * @var array
	 */
	protected $_belongsTo = array(
		'table' => array('table_id'),
	);

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
	 * Get item path
	 * @return string
	 */
	public function getItemPath()
	{
		return $this->Table->getPath() . '/item_' . $this->id . '/';
	}

	/**
	 * Get item href
	 * @return string
	 */
	public function getItemHref()
	{
		return $this->Table->getHref() . '/item_' . $this->id . '/';
	}

	/**
	 * Delete table item from database
	 * @param  mixed $primaryKey  primary key
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
		$aTableRows = $this->Table_Rows->findAll();
		foreach ($aTableRows as $oTableRow) {
			$oTableRow->delete($oTableRow->id);
		}
		// delete object
		parent::delete($primaryKey);
		return $this;
	}

	/**
	 * Mark table item as deleted
	 * @return self
	 */
	public function markDeleted()
	{
		parent::markDeleted();
		return $this;
	}

	/**
	 * Copy table item object with referenced child objects
	 * @return Table_Item_Model table item object
	 */
	public function copy()
	{
		$oNewTableItem = parent::copy();
		$aTableRows = $this->Table_Rows->findAll();
		foreach ($aTableRows as $oTableRow) {
			$oNewTableRow = $oTableRow->copyWhithoutChildren();
			$oNewTableRow
				->table_item_id($oNewTableItem->id)
				->save();
			$aTableCells = $oTableRow->Table_Cells->findAll();
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
								->table_cell_id($oNewTableCell->id)
								->save();
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
		}
		return $oNewTableItem;
	}
}
