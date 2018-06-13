<?php
/**
 * Table_Cell_Value_File_Model
 *
 * @package Artatom
 * @subpackage Table
 * @version 1.0
 * @author Konstantin Bondarenko
 * @copyright © 2007-2018 Artatom http://www.artatom.ru
 */
class Table_Cell_Value_File_Model extends Table_Cell_Value_Model
{
	/**
	 * Database table name
	 * @var string
	 */
	protected $_tableName = 'table_cell_value_files';

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
	 * Preload values of Table_Cell_Value_File_Model object
	 * @var array
	 */
	protected $_preloadValues = array(
		'value' => ' ',
		'file' => ' ',
		'file_name' => ' ',
		'file_description' => ' '
	);

	/**
	 * Name of tag
	 * @var string
	 */
	protected $_tagName = 'table_cell_value';

	/**
	 * Constructor
	 * @param mixed $primaryKey primary key
	 */
	public function __construct($primaryKey = NULL)
	{
		parent::__construct($primaryKey);
	}

	/**
	 * Get preformatted cell value
	 * @return string used in Table_Row.content()
	 */
	public function getFormattedValue()
	{
		return "<a href='{$this->Table_Cell->getCellHref()}{$this->file}'>{$this->file_name}</a>";
	}

	/**
	 * Delete file of current table cell value object
	 * @return void
	 */
	public function deleteFile()
	{
		if(Core_File::filesize($this->Table_Cell->getCellPath() . $this->file)) {
			Core_File::delete($this->Table_Cell->getCellPath() . $this->file);
			$this
				->file('')
				->file_name('')
				->value('')
				->save();
		}
		return $this;
	}

	/**
	 * Save file for current table cell value object
	 * @param  string $fileSourcePath file directory full path
	 * @param  string $fileName       file name
	 * @return string                 description for file value
	 */
	public function saveFile($fileSourcePath, $fileName)
	{
		$fileName = Core_File::filenameCorrection($fileName);
		$this->Table_Cell->createDir();
		$this->file = $fileName;
		$this->value = $fileName;
		$this->save();
		Core_File::upload($fileSourcePath, $this->Table_Cell->getCellPath() . $fileName);
		return $this;
	}

	/**
	 * Delete table cell value file object from database
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
		// delete file
		$this->deleteFile();
		// delete object
		parent::delete($primaryKey);
		return $this;
	}
}