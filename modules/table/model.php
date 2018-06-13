<?php
defined('HOSTCMS') || exit('HostCMS: access denied.');
/**
 * Table_Model
 *
 * @package Artatom
 * @subpackage Table
 * @version 1.0
 * @author Konstantin Bondarenko
 * @copyright © 2007-2018 Artatom http://www.artatom.ru
 */
class Table_Model extends Core_Entity
{
	/**
	 * Backend property
	 * @var mixed
	 */
	public $img = 1;

	/**
	 * One-to-many or many-to-many relations
	 * @var array
	 */
	protected $_hasMany = array(
		'table_column' => array(),
		'table_row' => array(),
		'table_item' => array(),
	);

	/**
	 * Belongs to relations
	 * @var array
	 */
	protected $_belongsTo = array(
		'site' => array(),
	);

	/**
	 * Table object preload values
	 * @var array
	 */
	protected $_preloadValues = array(
		'sorting' => 0,
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
	 * Get table href
	 * @return string
	 */
	public function getHref()
	{
		return '/' . $this->Site->uploaddir . 'table_' . intval($this->id);
	}

	/**
	 * Get table path
	 * @return string
	 */
	public function getPath()
	{
		return CMS_FOLDER . $this->getHref();
	}

	/**
	 * Delete object from database
	 * @param  mixed $primaryKey primary key for delting object
	 * @return self
	 */
	public function delete($primaryKey = NULL)
	{
		$aTableItems = $this->Table_Items->findAll();
		foreach ($aTableItems as $oTableItem) {
			$oTableItem->delete();
		}
		$aTableColumns = $this->Table_Columns->findAll();
		foreach ($aTableColumns as $oTableColumn) {
			$oTableColumn->delete();
		}
		parent::delete($primaryKey);
		return $this;
	}

	/**
	 * Mark table as deleted
	 * @return self
	 */
	public function markDeleted()
	{
		$aTableItems = $this->Table_Items->findAll();
		foreach ($aTableItems as $oTableItem) {
			$oTableItem->markDeleted();
		}
		$aTableColumns = $this->Table_Columns->findAll();
		foreach ($aTableColumns as $oTableColumn) {
			$oTableColumn->markDeleted();
		}
		parent::markDeleted();
		return $this;
	}
	/**
	 * Copy table object
	 * @return Table new table object
	 */
	public function copy()
	{
		$newTableScheme = parent::copy();
		$aTableColumns = $this->Table_Columns->findAll();
		foreach ($aTableColumns as $oTableColumn) {
			$oNewTableColumn = $oTableColumn->copy();
			$oNewTableColumn
				->table_id($newTableScheme->id)
				->save();
		}
		return $newTableScheme;
	}
}