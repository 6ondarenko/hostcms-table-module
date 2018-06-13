<?php
/**
 * Table_Controller_Show
 *
 * Set XSL:
 * <code>
 * ->xsl(
 * 	Core_Entity::factory('Xsl')->getByName('myXslName')
 * )
 * </code>
 *
 * Add external entity:
 * <code>
 * ->addEntity(
 * 	Core::factory('Core_Xml_Entity')->name('my_tag')->value(123)
 * )
 * </code>
 *
 * Add additional cache signature:
 * <code>
 * ->addCacheSignature('option=' . $value)
 * </code>
 *
 * Controller properties:
 * wrap_table                 - true/false
 * show_table_header          - true/false
 * show_table_description     - true/false
 * file_extension_label_class - string
 * panel_class                - string
 * panel_header_class         - string
 * panel_body_class           - string
 * table_class                - string
 *
 * @package Artatom
 * @subpackage Table
 * @version 1.0
 * @author Konstantin Bondarenko
 * @copyright © 2007-2018 Artatom http://www.artatom.ru
 */
class Table_Controller_Show extends Core_Controller
{
	/**
	 * Allowed properties.
	 * @var array
	 */
	protected $_allowedProperties = array(
		'item',
		'limit',
		'wrap_table',
		'show_table_header',
		'show_table_description',
		'file_extension_label_class',
		'panel_class',
		'panel_header_class',
		'panel_body_class',
		'table_class'
	);

	/**
	 * Preload values.
	 * @var array
	 */
	protected $_preloadValues = array(
		'item' => 0,
		'limit' => 10,
		'wrap_table' => 1,
		'show_table_header' => 1,
		'show_table_description' => 1,
		'file_extension_label_class' => 'label label-default',
		'panel_class' => 'panel panel-default rounded-0',
		'panel_header_class' => 'panel-heading',
		'panel_body_class' => 'panel-body',
		'table_class' => 'table'
	);

	/**
	 * Constructor.
	 */
	public function __construct(Table_Model $oTable)
	{
		$this->setEntity($oTable);
		parent::__construct($oTable);
		$this->addCacheSignature('entityId=' . $oTable->getPrimaryKey());
		// preload values
		foreach ($this->_preloadValues as $key => $value) {
			$this->$key($value);
		}
	}

	/**
	 * Get XML for entity and children entities
	 * @return string
	 */
	public function getXml()
	{
		$this->_loadValues();
		$aTable_Columns = $this->_entity->Table_Columns->findAll();
		$this->addEntities($aTable_Columns);

		// if item id == 0 show all items
		if ($this->item == 0) {
			$oTable_Items = $this->_entity->Table_Items;
			$oTable_Items
				->queryBuilder()
				->limit($this->limit);
			$aTable_Items = $oTable_Items->findAll();
			foreach ($aTable_Items as $oTable_Item) {
				$aTable_Rows = $oTable_Item->Table_Rows->findAll();
				foreach ($aTable_Rows as $oTable_Row) {
					$aTable_Cells = $oTable_Row->Table_Cells->findAll();
					foreach ($aTable_Cells as $oTable_Cell) {
						// add values into cells
						$aValues = $oTable_Cell->getValues();
						foreach ($aValues as $oValue) {
							switch (strtolower(trim($oTable_Cell->Table_Column->Table_Datatype->code))) {
								case 'file':
									$oValue
										->addEntity(Core::factory('Core_Xml_Entity')
											->name('extension')
											->value(strtolower(trim(array_reverse(explode('.', $oValue->file))[0])))
										);
									break;
								default:
									# code...
									break;
							}
						}
						$oTable_Cell
							->addEntity(Core::factory('Core_Xml_Entity')
								->name('dir')
								->value($oTable_Cell->getCellHref())
							)
							->addEntity(Core::factory('Core_Xml_Entity')
								->name('type')
								->value($oTable_Cell->Table_Column->Table_Datatype->code)
							)
							->addEntities($aValues);
					}
					// add cells into rows
					$oTable_Row->addEntities($aTable_Cells);
				}
				// add rows into items
				$oTable_Item->addEntities($aTable_Rows);
			}
			// add items into table scheme childs array
			$this->addEntities($aTable_Items);
			// add child entities
			$this->_entity
				->clearEntities()
				->addEntities($this->_entities);
		} else {
			$oTable_Item = Core_Entity::factory('Table_Item', intval($this->item));
			if ($oTable_Item) {
				$aTable_Rows = $oTable_Item->Table_Rows->findAll();
				foreach ($aTable_Rows as $oTable_Row) {
					$aTable_Cells = $oTable_Row->Table_Cells->findAll();
					foreach ($aTable_Cells as $oTable_Cell) {
						// add values into cells
						$aValues = $oTable_Cell->getValues();
						foreach ($aValues as $oValue) {
							switch (strtolower(trim($oTable_Cell->Table_Column->Table_Datatype->code))) {
								case 'file':
									$oValue
										->addEntity(Core::factory('Core_Xml_Entity')
											->name('extension')
											->value(strtolower(trim(array_reverse(explode('.', $oValue->file))[0])))
										);
									break;
								default:
									# code...
									break;
							}
						}
						$oTable_Cell
							->addEntity(Core::factory('Core_Xml_Entity')
								->name('dir')
								->value($oTable_Cell->getCellHref())
							)
							->addEntity(Core::factory('Core_Xml_Entity')
								->name('type')
								->value($oTable_Cell->Table_Column->Table_Datatype->code)
							)
							->addEntities($aValues);
					}
					// add cells into rows
					$oTable_Row->addEntities($aTable_Cells);
				}
				// add rows into items
				$oTable_Item->addEntities($aTable_Rows);
			}

			// add items into table scheme childs array
			$this->addEntity($oTable_Item);
			// add child entities
			$this->_entity
				->clearEntities()
				->addEntities($this->_entities);
		}

		return $this->_entity->getXml();
	}

	/**
	 * Add values in xml entity
	 * @return self
	 */
	protected function _loadValues()
	{
		foreach ($this->_allowedProperties as $value) {
			$this
				->addEntity(
					Core::factory('Core_Xml_Entity')
						->name($value)
						->value($this->$value)
				);
		}
		return $this;
	}
}