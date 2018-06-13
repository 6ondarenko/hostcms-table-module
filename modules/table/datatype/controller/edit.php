<?php
/**
 * Table_Datatype_Controller_Edit
 *
 * @package Artatom
 * @subpackage Table
 * @version 1.0
 * @author Konstantin Bondarenko
 * @copyright © 2007-2018 Artatom http://www.artatom.ru
 */
class Table_Datatype_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit
{
	/**
	 * Prepare form for edit table datatype object in admin panel
	 * @return void
	 */
	protected function _prepareForm()
	{
		$sModelCode = "<?php
class Table_Cell_Value_/*New*/_Model extends Table_Cell_Value_Model
{
	public function getFormattedValue()
	{
		//
	};
}";
		$sModelName = $this->_object->model_name;
		if ($sModelName && is_string($sModelName) && $this->checkTypeClassName($sModelName)) {
			$sDirPathName = CMS_FOLDER
			. 'modules'
			. DIRECTORY_SEPARATOR
			. implode(DIRECTORY_SEPARATOR,explode('_', strtolower($sModelName)));
			$sFilePathName = $sDirPathName . DIRECTORY_SEPARATOR . 'model.php';
			if (Core_File::filesize($sFilePathName)) {
				$sModelCode = Core_File::read($sFilePathName);
			}
		}


		parent::_prepareForm();
		$oTab = $this->getTab('main');
		$oDivTextarea = Admin_Form_Entity::factory('div')->class('row');
		$oTextarea = Admin_Form_Entity::factory('textarea');
		$oTextarea
			->name('model_file')
			->caption(Core::_('Table_Datatype.model_file'))
			->rows(50)
			->syntaxHighlighter(true)
			->syntaxHighlighterOptions(
				array(
					'mode' => 'php',
					'lineNumbers' => 'true',
					'styleActiveLine' => 'true',
					'lineWrapping' => 'true',
					'autoCloseTags' => 'true',
					'tabSize' => 2, // из-за indentUnit равного 2-м
					'indentWithTabs' => 'true',
					'smartIndent' => 'true',
				)
			)
			->value($sModelCode);

		$oDivTextarea->add($oTextarea);
		$oTab->add($oDivTextarea);
		// $this->issetTab('additional') && $this->deleteTab('additional');
	}

	/**
	 * Apply table datatype object properties
	 * @return void
	 */
	protected function _applyObjectProperty()
	{
		$aFormValues = $this->_formValues;
		$sModelName = Core_Array::get($aFormValues, 'model_name', false);
		$sModelCode = Core_Array::get($aFormValues, 'model_file', '');

		if (!$this->checkTypeClassName($sModelName)) {
			throw new Exception("Неправильный формат имени класса модели значения типа данных. Имя должно иметь следующий формат: Table_Cell_Value_#Type#", 1);
		}

		// move to constructor?
		// create dir path name
		$sDirPathName = CMS_FOLDER
		. 'modules'
		. DIRECTORY_SEPARATOR
		. implode(DIRECTORY_SEPARATOR,explode('_', strtolower($sModelName)));

		$sFilePathName = $sDirPathName . DIRECTORY_SEPARATOR . 'model.php';

		Core_File::mkdir($sDirPathName);

		if (Core_File::filesize($sFilePathName)) {
			Core_File::write($sFilePathName, $sModelCode, CHMOD_FILE);
		} else {
			Core_File::write($sFilePathName, $sModelCode, CHMOD_FILE);
		}



		// Core_File::mkdir();
		parent::_applyObjectProperty(); // saving row, now we can get Table_Row id
		// Core_File::file
	}

	/**
	 * Check valid of table cell value model class name
	 * @param  string $sName class name
	 * @return boolean
	 */
	public function checkTypeClassName($sName)
	{
		if ($sName && is_string($sName)) {
			$reg = '/^Table_Cell_Value_[A-Z]{1}[a-z0-9]+$/';
			$res = preg_match($reg, $sName);
			if (intval($res) === 0) {
				return false;
				// throw new Exception("Неправильный формат имени класса модели значения типа данных. Имя должно иметь следующий формат: Table_Cell_Value_#Type#", 666);
			} else {
				return true;
			}
		} else {
			throw new Exception("Method's first argument should be a string!", 1);
		}
	}

}