<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Properties.
 * Контроллер удаления значения дополнительного свойства
 *
 * @package HostCMS
 * @subpackage Property
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2017 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Table_Cell_Value_Controller_Delete_File extends Admin_Form_Action_Controller
{
	/**
	 * Allowed object properties
	 * @var array
	 */
	protected $_allowedProperties = array(
		'linkedObject',
	);

	/**
	 * Executes the business logic.
	 * @param mixed $operation Operation name
	 * @return boolean
	 */
	public function execute($operation = NULL)
	{
		$windowId = $this->_Admin_Form_Controller->getWindowId();
		if (!is_array($this->linkedObject))
		{
			$this->linkedObject = array($this->linkedObject);
		}

		preg_match('/(\w*)_(\d*)_(\d*)/i', $operation, $matches);

		if (count($matches) == 4)
		{
			$sValueModelName = $matches[1];
			$columnId = $matches[2];
			$valueId = $matches[3];

			// get table column object
			$oTable_Column = Core_Entity::factory('Table_Column')->find($columnId);
			$oTable_Datatype = $oTable_Column->Table_Datatype;

			if ($sValueModelName == strtolower($oTable_Datatype->model_name)) {
				$oTable_Cell_Value = Core_Entity::factory($oTable_Datatype->model_name, $valueId);
				$oTable_Cell_Value->deleteFile();
				// clear table cell value file object properties
				$oTable_Cell_Value
					->file_name('')
					->file('')
					->value('')
					->file_description('')
					->save();
				// delete image icon in edit form
				ob_start();
				Core::factory('Core_Html_Entity_Script')
					->type("text/javascript")
					->value("$(\"#{$windowId} #preview_large_{$valueId}, #{$windowId} #delete_large_{$valueId}\").remove()")
					->execute();
				$this->addMessage(ob_get_clean());
			}
		}
		// Break execution for other
		return TRUE;
	}
}