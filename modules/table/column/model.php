<?php
/**
 * Table_Column_Model
 *
 * @package Artatom
 * @subpackage Table
 * @version 1.0
 * @author Konstantin Bondarenko
 * @copyright © 2007-2018 Artatom http://www.artatom.ru
 */
class Table_Column_Model extends Core_Entity
{
	/**
	 * Has to relations
	 * @var array
	 */

	protected $_belongsTo = array(
		'table' => array(),
		'table_datatype' => array(),
	);

	protected $_hasMany = array(
		'table_cell' => array()
	);

	protected $_preloadValues = array(
		'multiple' => 0,
		'sorting' => 0,
		'active' => 1,
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
	 * Change activity status of column object
	 * @return void
	 */
	public function changeActive()
	{
		$this->active = 1 - $this->active;
		$this->save();
	}

	/**
	 * Delete column object from database
	 * @param  mixed $primaryKey primary key
	 * @return self
	 */
	public function delete($primaryKey = NULL)
	{
		$aTableCells = $this->Table_Cells->findAll();
		foreach ($aTableCells as $oTableCell) {
			$oTableCell->delete();
		}
		parent::delete($primaryKey);
		return $this;
	}

	/**
	 * Mark column object as deleted
	 * @return self
	 */
	public function markDeleted()
	{
		$aTableCells = $this->Table_Cells->findAll();
		foreach ($aTableCells as $oTableCell) {
			$oTableCell->markDeleted();
		}
		parent::markDeleted();
		return $this;
	}

}