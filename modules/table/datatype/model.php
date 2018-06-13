<?php
/**
 * Table_Datatype_Model
 *
 * @package Artatom
 * @subpackage Table
 * @version 1.0
 * @author Konstantin Bondarenko
 * @copyright © 2007-2018 Artatom http://www.artatom.ru
 */
class Table_Datatype_Model extends Core_Entity
{
	/**
	 * Has many relations
	 * @var array
	 */
	protected $_hasMany = array(
		'table_column' => array(),
	);

	protected $_preloadValues = array(
		'sorting' => 0,
		'active' => 1,
	);

	/**
	 * Constructor.
	 */
	public function __construct($id = NULL)
	{
		parent::__construct($id);
	}

	/**
	 * Change table datatype object activity status
	 * @return void
	 */
	public function changeActive()
	{
		$this->active = 1 - $this->active;
		$this->save();
	}

	/**
	 * Delete table datatype object from database
	 * @param  mixed $primaryKey primary key
	 * @return self
	 */
	public function delete($primaryKey = NULL)
	{
		$aTableColumns = $this->Table_Columns->findAll();
		foreach ($aTableColumns as $oTableColumn) {
			$oTableColumn->delete();
		}
		parent::delete($primaryKey);
		return $this;
	}

	/**
	 * Mark table datatype object as deleted
	 * @return self
	 */
	public function markDeleted()
	{
		$aTableColumns = $this->Table_Columns->findAll();
		foreach ($aTableColumns as $oTableColumn) {
			$oTableColumn->markDeleted();
		}
		parent::markDeleted();
		return $this;
	}
}