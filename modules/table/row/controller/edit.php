<?php
/**
 * Table_Row_Controller_Edit
 *
 * @package Artatom
 * @subpackage Table
 * @version 1.0
 * @author Konstantin Bondarenko
 * @copyright © 2007-2018 Artatom http://www.artatom.ru
 */
class Table_Row_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit
{

	/**
	 * Prepare form for edit row in admin panel
	 * @return void
	 */
	protected function _prepareForm()
	{
		parent::_prepareForm();
		$object = $this->_object;
		// if (!$this->_object->getPrimaryKey()) {
		// 	$this->_object->save();
		// }
		$table_item_id = Core_Array::getGet('table_item_id', NULL);
		$this->_object->table_item_id = $table_item_id;
		$this->_object->table_id = Core_Entity::factory('Table_Item', $table_item_id)->Table->id;
		$aTableColumns = $this->_object->getTableColumns();
		// add columns in sections
		foreach ($aTableColumns as $oTableColumn) {
			// add new section
			$oSection = Admin_Form_Entity::factory('Section')
				->caption($oTableColumn->name)
				->id($oTableColumn->id);
			// get cell object
			$oTableCell = $this->_object->getTableCellByColumnId($oTableColumn->id);
			// adding cell in section
			$oTableDatatype = $oTableCell->getTableDatatype();
			$sTableCellValueModelName = $oTableDatatype->model_name;
			$sTableCellValueModelNameLc = strtolower($sTableCellValueModelName);

			// if cell can has multiple values
			// если колонка multiple == 1, то получаем массив значений и используем цикл
			// иначе получаем одно значение
			if ($oTableColumn->multiple) {
				$aValues = $oTableCell->getValues();
				// if $aValues is empty, add one empty object of value
				if (!count($aValues)) {
					$oValue = Core_Entity::factory($sTableCellValueModelNameLc);
					$oValue->table_column_id($oTableColumn->id);
					$oValue->table_cell_id($oTableCell->id);
					array_push($aValues, $oValue);
				}
				foreach ($aValues as $oValue) {
					$sFormFieldName = (!$oValue->id)
						? $sTableCellValueModelNameLc . "_" . $oTableColumn->id . "[]"
						: $sTableCellValueModelNameLc . "_" . $oTableColumn->id ."_" . $oValue->id;
					// check datatype of value
					switch ($oTableDatatype->code) {
						case 'file':
							// add div.row in section
							$oSection->add($oRow = Admin_Form_Entity::factory('Div')->class('row multiple_value')->id('table_cell_value_file_' . $oTableColumn->id));
							$oAdminFormEntityFIle = Admin_Form_Entity::factory('File');
							$oAdminFormEntityFIle
								->name($sFormFieldName)
								->caption('Значение ячейки типа "' . $oTableDatatype->name . '"')
								->smallImage(array('show' => false));
							$oAdminFormEntityFIle->largeImage(array(
								'id' => $oValue->id,
								'show_params' => false,
								'path' => (trim($oValue->file) != '') ? $oValue->Table_Cell->getCellHref() . $oValue->file : false,
								'description' => $oValue->file_description,
								'show_description' => true,
								'delete_href' => null,
								'delete_onclick' => $this->_Admin_Form_Controller->getAdminActionLoadAjax(
									$this->_Admin_Form_Controller->getPath(),                           // path
									'deleteFile',                                                       // action
									"{$sTableCellValueModelNameLc}_{$oTableColumn->id}_{$oValue->id}",  // operation
									$this->_datasetId, $this->_object->id                               // datasetKey
									                                                                    // datasetValue
									                                                                    // additionalParams
									                                                                    // limit
									                                                                    // current
									                                                                    // sortingFieldId
									                                                                    // sortingDirection
									                                                                    // view
								)
							));

							$oTableColumn->multiple && $this->imgBox(
								$oAdminFormEntityFIle,
								$sTableCellValueModelNameLc,
								$oTableColumn,
								'$.cloneMultipleTableValue',
								'$.deleteNewMultipleValue(this)'
							);

							$oRow->add($oAdminFormEntityFIle);
							break;
						case 'textarea':
							// add div.row in section
							$oSection->add($oRow = Admin_Form_Entity::factory('Div')->class('row multiple_value'));
							// create and add input to row
							$oValueTextarea = Admin_Form_Entity::factory('Textarea')
								->name($sFormFieldName)
								->value($oValue->value) // value
								->caption('Значение ячейки типа "' . $oTableDatatype->name . '"')
								->rows(10)
								// ->syntaxHighlighter(true)
								->wysiwyg(true)
								->class('form-control input-lg');
							$oTableColumn->multiple && $this->imgBox(
								$oValueTextarea,
								$sTableCellValueModelNameLc,
								$oTableColumn,
								'$.cloneMultipleTableValue',
								'$.deleteNewMultipleValue(this)'
							);
							$oRow->add($oValueTextarea);
							break;
						case 'wysiwyg':
							// add div.row in section
							$oSection->add($oRow = Admin_Form_Entity::factory('Div')->class('row multiple_value'));
							// create and add input to row
							$oValueTextarea = Admin_Form_Entity::factory('Textarea')
								->name($sFormFieldName)
								->value($oValue->value) // value
								->caption('Значение ячейки типа "' . $oTableDatatype->name . '"')
								->rows(10)
								// ->syntaxHighlighter(true)
								->wysiwyg(true)
								->class('form-control input-lg');
							$oTableColumn->multiple && $this->imgBox(
								$oValueTextarea,
								$sTableCellValueModelNameLc,
								$oTableColumn,
								'$.cloneMultipleTableValue',
								'$.deleteNewMultipleValue(this)'
							);
							$oRow->add($oValueTextarea);
							break;
						case 'string':
							// add div.row in section
							$oSection->add($oRow = Admin_Form_Entity::factory('Div')->class('row multiple_value'));
							// create and add input to row
							$oValueInput = Admin_Form_Entity::factory('Input')
								->name($sFormFieldName)
								->value($oValue->value) // value
								->caption('Значение ячейки типа "' . $oTableDatatype->name . '"')
								// ->syntaxHighlighter(true)
								// ->wysiwyg(true)
								->class('form-control input-lg');
							$oTableColumn->multiple && $this->imgBox(
								$oValueInput,
								$sTableCellValueModelNameLc,
								$oTableColumn,
								'$.cloneMultipleTableValue',
								'$.deleteNewMultipleValue(this)'
							);
							$oRow->add($oValueInput);
							break;
						case 'integer':
							// add div.row in section
							$oSection->add($oRow = Admin_Form_Entity::factory('Div')->class('row multiple_value'));
							// create and add input to row
							$oValueInput = Admin_Form_Entity::factory('Input')
								->name($sFormFieldName)
								->value($oValue->value) // value
								->caption('Значение ячейки типа "' . $oTableDatatype->name . '"')
								->class('form-control input-lg');
							$oTableColumn->multiple && $this->imgBox(
								$oValueInput,
								$sTableCellValueModelNameLc,
								$oTableColumn,
								'$.cloneMultipleTableValue',
								'$.deleteNewMultipleValue(this)'
							);
							$oRow->add($oValueInput);
							break;
						default:
							break;
					}
				}
			} else {
				$oValue = $oTableCell->getValue();
				// if value not exists create new value object
				if (!$oValue) {
					$oValue = Core_Entity::factory($sTableCellValueModelNameLc);
					$oValue->table_column_id($oTableColumn->id);
					$oValue->table_cell_id($oTableCell->id);
				} else {
					$oValue->table_column_id($oTableColumn->id);
					$oValue->table_cell_id($oTableCell->id);
				}
				$sFormFieldName = (!$oValue->id)
					? $sTableCellValueModelNameLc . "_" . $oTableColumn->id . "[]"
					: $sTableCellValueModelNameLc . "_" . $oTableColumn->id ."_" . $oValue->id;
				// check datatype of value
				switch ($oTableDatatype->code) {
					case 'file':
						// add div.row in section
						$oSection->add($oRow = Admin_Form_Entity::factory('Div')->class('row'));
						$oAdminFormEntityFIle = Admin_Form_Entity::factory('File');
						// $oAdminFormEntityFIle = Core::factory('Core_Html_Entity_Img');
						$oAdminFormEntityFIle
							->name($sFormFieldName)
							->caption('Значение ячейки типа "' . $oTableDatatype->name . '"')
							->smallImage(array('show' => false));
						$oAdminFormEntityFIle->largeImage(array(
							'id' => $oValue->id,
							'show_params' => false,
							'path' => (trim($oValue->file) != '') ? $oValue->Table_Cell->getCellHref() . $oValue->file : false,
							'description' => $oValue->file_description,
							'show_description' => true,
							'delete_href' => NULL,
							'delete_onclick' => $this->_Admin_Form_Controller->getAdminActionLoadAjax(
								$this->_Admin_Form_Controller->getPath(),                           // path
								'deleteFile',                                                       // action
								"{$sTableCellValueModelNameLc}_{$oTableColumn->id}_{$oValue->id}",  // operation
								$this->_datasetId, $this->_object->id                               // datasetKey
								                                                                    // datasetValue
								                                                                    // additionalParams
								                                                                    // limit
								                                                                    // current
								                                                                    // sortingFieldId
								                                                                    // sortingDirection
								                                                                    // view
							),
						));
						$oRow->add($oAdminFormEntityFIle);
						break;
					case 'string':
						// add div.row in section
						$oSection->add($oRow = Admin_Form_Entity::factory('Div')->class('row'));
						// create and add input to row
						$oValueInput = Admin_Form_Entity::factory('Input')
							->name($sFormFieldName)
							->value($oValue->value) // value
							->caption('Значение ячейки типа "' . $oTableDatatype->name . '"')
							// ->syntaxHighlighter(true)
							// ->wysiwyg(true)
							->class('form-control input-lg');
						$oRow->add($oValueInput);
						break;
					case 'textarea':
						// add div.row in section
						$oSection->add($oRow = Admin_Form_Entity::factory('Div')->class('row'));
						// create and add input to row
						$oValueTextarea = Admin_Form_Entity::factory('Textarea')
							->name($sFormFieldName)
							->value($oValue->value) // value
							->caption('Значение ячейки типа "' . $oTableDatatype->name . '"')
							->rows(10)
							// ->syntaxHighlighter(true)
							// ->wysiwyg(true)
							->class('form-control input-lg');
						$oRow->add($oValueTextarea);
						break;
					case 'wysiwyg':
						// add div.row in section
						$oSection->add($oRow = Admin_Form_Entity::factory('Div')->class('row'));
						// create and add input to row
						$oValueTextarea = Admin_Form_Entity::factory('Textarea')
							->name($sFormFieldName)
							->value($oValue->value) // value
							->caption('Значение ячейки типа "' . $oTableDatatype->name . '"')
							->rows(10)
							// ->syntaxHighlighter(true)
							->wysiwyg(true)
							->class('form-control input-lg');
						$oRow->add($oValueTextarea);
						break;
					case 'integer':
						// add div.row in section
						$oSection->add($oRow = Admin_Form_Entity::factory('Div')->class('row'));
						// create and add input to row
						$oValueInput = Admin_Form_Entity::factory('Input')
							->name($sFormFieldName)
							->value($oValue->value) // value
							->caption('Значение ячейки типа "' . $oTableDatatype->name . '"')
							->class('form-control input-lg');
						$oRow->add($oValueInput);
						break;
					default:
						break;
				}
			}

			// adding tab (column)
			$this->getTab('main')->add($oSection);
		} // end foreach
		// Функции для редактирования множественных значений
		$oCore_Html_Entity_Script = Core::factory('Core_Html_Entity_Script')
			->type("text/javascript")
			->value("
			$.extend({
				cloneMultipleTableValue: function(windowId, cloneDelete, model_name, column_id)
				{
					//
					var jMultipleValue = jQuery(cloneDelete).closest('.multiple_value'),
						jNewObject = jMultipleValue.clone();

					// Change input name
					jNewObject.find('input,select').not('#description_large').prop('name', model_name + '_' + column_id + '[]');
					jNewObject.find('input,select').not('#description_large').val('');
					jNewObject.find('a').remove();
					jNewObject.find('input#description_large,select').prop('name', 'description_' + model_name + '_' + column_id + '[]');
					jNewObject.find('input#description_large,select').val('');

					jMultipleValue.closest('.panel-body').append(jNewObject);
				},
				deleteNewMultipleTableValue: function(object)
				{
					var jObject = jQuery(object).closest('.multiple_value').remove();
				}
			});
		");
		$this->getTab('main')->add($oCore_Html_Entity_Script);
	}

	/**
	 * Execute
	 * @param  string $operation
	 * @return boolean result
	 */
	public function execute($operation = NULL)
	{
		Core_Event::notify(
			'Admin_Form_Action_Controller_Type_Edit.onBeforeExecute',
			$this,
			array($operation, $this->_Admin_Form_Controller)
		);

		$eventResult = Core_Event::getLastReturn();

		if (!is_null($eventResult)) { return $eventResult; }

		// check operation type
		switch ($operation)
		{
			case NULL: // Показ формы

				if (!$this->_prepeared)
				{
					$this->_prepareForm();

					// Событие onAfterRedeclaredPrepareForm вызывается в двух местах
					Core_Event::notify('Admin_Form_Action_Controller_Type_Edit.onAfterRedeclaredPrepareForm', $this, array($this->_object, $this->_Admin_Form_Controller));
				}

				$this->_Admin_Form_Controller
					->title($this->title)
					->pageTitle($this->title);

				$this->_return = $this->_showEditForm();

			break;
			case 'save':
				//
			case 'saveModal':
				// "сохранить"
				$primaryKeyName = $this->_object->getPrimaryKeyName();

				// Значение первичного ключа до сохранения
				$prevPrimaryKeyValue = $this->_object->$primaryKeyName;

				// устанавливаем значения
				$this->_applyObjectProperty();

				ob_start();
				$modelName = $this->_object->getModelName();
				$actionName = $this->_Admin_Form_Controller->getAction();

				Core_Message::show(Core::_("{$modelName}.{$actionName}_success"));

				if (is_null($prevPrimaryKeyValue))
				{
					$windowId = $this->_Admin_Form_Controller->getWindowId();
					?><script type="text/javascript"><?php
					?>$.appendInput('<?php echo $windowId?>', '<?php echo $this->_formId?>', '<?php echo $primaryKeyName?>', '<?php echo $this->_object->$primaryKeyName?>');<?php
					/*?>$.appendInput('<?php echo $windowId?>', '<?php echo $this->_formId?>', 'hostcms[checked][<?php echo $this->_datasetId?>][<?php echo $this->_object->$primaryKeyName?>]', '1');<?php*/
					?></script><?php
				}

				$this->addMessage(ob_get_clean());
				$this->_return = TRUE;
			break;
			case 'modal':
				$windowId = $this->_Admin_Form_Controller->getWindowId();

				//$newWindowId = 'Modal_' . time();

				ob_start();

				if (!$this->_prepeared)
				{
					$this->_prepareForm();

					// Событие onAfterRedeclaredPrepareForm вызывается в двух местах
					Core_Event::notify('Admin_Form_Action_Controller_Type_Edit.onAfterRedeclaredPrepareForm', $this, array($this->_object, $this->_Admin_Form_Controller));
				}

				$oAdmin_Form_Action_Controller_Type_Edit_Show = Admin_Form_Action_Controller_Type_Edit_Show::create();

				$oAdmin_Form_Action_Controller_Type_Edit_Show
					->Admin_Form_Controller($this->_Admin_Form_Controller)
					->formId($this->_formId)
					->tabs($this->_tabs)
					->buttons($this->_addButtons());

				echo $oAdmin_Form_Action_Controller_Type_Edit_Show->showEditForm();

				$this->addContent(ob_get_clean());

				$this->_return = TRUE;
			break;
			case 'applyModal':
				$this->_applyObjectProperty();

				$windowId = $this->_Admin_Form_Controller->getWindowId();
				$this->addContent('<script type="text/javascript">$(\'#' . $windowId . '\').parents(\'.bootbox\').remove();</script>');

				$this->_return = TRUE;
			break;
			case 'markDeleted':
				$windowId = $this->_Admin_Form_Controller->getWindowId();
				$this->addContent('<script type="text/javascript">$(\'#' . $windowId . '\').parents(\'.bootbox\').remove();</script>');

				$this->_return = TRUE;
			break;
			default:
				// "применить"
				$this->_applyObjectProperty();
				$this->_return = FALSE; // Показываем форму
			break;
		}

		Core_Event::notify('Admin_Form_Action_Controller_Type_Edit.onAfterExecute', $this, array($operation, $this->_Admin_Form_Controller));

		return $this->_return;
	}

	/**
	 * Apply object properties after form sending
	 * @return void
	 */
	protected function _applyObjectProperty()
	{
		$table_item_id = Core_Array::getGet('table_item_id', NULL);
		$this->_object->table_item_id = $table_item_id;
		$this->_object->table_id = Core_Entity::factory('Table_Item', $table_item_id)->Table->id;
		parent::_applyObjectProperty(); // saving row, now we can get Table_Row id

		foreach ($this->_object->getTableColumns() as $oTableColumn) {
			$oTableDatatype = $oTableColumn->Table_Datatype;
			$sTableDatatypeLc = strtolower($oTableDatatype->model_name);
			switch ($oTableDatatype->code) {
				// column datatype is file
				case 'file':
					$oTableCell = $this->_object->getTableCellByColumnId($oTableColumn->id);
					$oTableCell->table_row_id($this->_object->id);
					$oTableCell->table_column_id($oTableColumn->id);
					$oTableCell->save();
					$sTableCellValueModelName = $oTableCell->getTableDatatype()->model_name;
					// delete values
					$aTableCellValues = $oTableCell->getValues();
					foreach ($aTableCellValues as $oValue) {
						$bDeleteValue = true;
						$sValueFieldName = $sTableDatatypeLc . "_" . $oTableColumn->id . "_" . $oValue->id;
						// check input field
						foreach ($this->_formValues as $key => $value) {
							if ($sValueFieldName == $key) $bDeleteValue = false;
						}
						// check global files array
						foreach ($_FILES as $key => $value) {
							if ($sValueFieldName == $key) $bDeleteValue = false;
						}
						if ($bDeleteValue) {
							$oValue->delete();
						}
					}

					// get form values for creating new values
					$aValues = Core_Array::getFiles($sTableDatatypeLc . "_" . $oTableColumn->id, array());
					$aKeys = array_keys($aValues);
					$iCount = null;
					isset($aValues['name']) && $iCount = count($aValues['name']);
					if ($iCount) {
						$aFileValuesClean = array();
						for ($i = 0; $i < $iCount; $i++) {
							foreach ($aKeys as $key) {
								$aFileValuesClean[$i][$key] = $aValues[$key][$i];
							}
						}
						// set form values for cell
						foreach ($aFileValuesClean as $key => $value) {
							// save value object for getting id
							$oValue = Core_Entity::factory($sTableCellValueModelName);
							$oValue
								->table_column_id($oTableColumn->id)
								->table_cell_id($oTableCell->id)
								->deleted(0)
								->save();

							// set file name
							$aFileName = explode('.', $value['name']);
							$sFileExtension = end($aFileName);
							$sFileName = $sTableDatatypeLc . "_" . $oTableColumn->id . "_" . $oValue->id . "." . $sFileExtension;

							// 'description_' only in old values
							$aFileDescription = Core_Array::get(
								$this->_formValues,
								'description_' . $sTableDatatypeLc . "_" . $oTableColumn->id,
								array()
							);
							$sFileDescription = isset($aFileDescription[$key]) ? $aFileDescription[$key] : ' ';

							// save file and fill value fields
							$oValue
								->saveFile($value['tmp_name'], $sFileName)
								->file($sFileName)
								->file_name($value['name'])
								->file_description($sFileDescription)
								->save();
						}
					}

					// get form values for creating new values
					$aValues = $_FILES;
					$aKeys = array_keys($aValues);
					$iCount = null;
					isset($aValues['name']) && $iCount = count($aValues[0]['name']);
					if ($iCount) {
						$aFileValuesClean = array();
						for ($i = 0; $i < $iCount; $i++) {
							foreach ($aKeys as $key) {
								$aFileValuesClean[$i][$key] = $aValues[$key][$i];
							}
						}
					}

					// get form values for updating old FILE values
					foreach ($_FILES as $key => $value) {
						// explode field name
						$aFieldName = explode("_", $key);
						// if array of field name is key of old value
						if (count($aFieldName) === 6) {
							// get value id
							$iValueId = array_pop($aFieldName);
							// get column id
							$iColumnId = array_pop($aFieldName);
							// get value datatype
							$sDatatype = implode('_', $aFieldName);
							if ($sDatatype == $sTableDatatypeLc && $oTableColumn->id == $iColumnId) {
								// old file value object
								$oValue = Core_Entity::factory($sTableCellValueModelName, $iValueId);
								// delete old file
								try {
									$oValue->deleteFile();
								} catch (Exception $e) {
									//
								}
								// set file name
								$aFileName = explode('.', $value['name']);
								$sFileExtension = end($aFileName);
								$sFileName = $sTableDatatypeLc . "_" . $oTableColumn->id . "_" . $oValue->id . "." . $sFileExtension;

								// 'description_' only in old values
								$sFileDescription = Core_Array::get(
									$this->_formValues,
									'description_' . $sTableDatatypeLc . "_" . $oTableColumn->id . "_" . $oValue->id,
									' '
								);

								// save file and fill value fields
								$oValue
									->saveFile($value['tmp_name'], $sFileName)
									->value($sFileName)
									->file($sFileName)
									->file_name($value['name'])
									->file_description($sFileDescription)
									->save();
							}
						}
					}

					// updating file description field
					foreach ($this->_formValues as $key => $value) {
						// explode field name
						$aFieldName = explode("_", $key);
						// if array of field name is key of old value
						if (count($aFieldName) === 7) {
							// get value id
							$iValueId = array_pop($aFieldName);
							// get column id
							$iColumnId = array_pop($aFieldName);
							array_shift($aFieldName);
							// get value datatype
							$sDatatype = implode('_', $aFieldName);
							if ($sDatatype == $sTableDatatypeLc && $oTableColumn->id == $iColumnId) {
								$oValue = Core_Entity::factory($sTableCellValueModelName, $iValueId);
								$oValue
									->file_description($value)
									->save();
							}
						}
					}

					break;
				// column datatype is other...
				default:
					// getting cell object and save
					$oTableCell = $this->_object->getTableCellByColumnId($oTableColumn->id);
					$oTableCell->table_row_id($this->_object->id);
					$oTableCell->table_column_id($oTableColumn->id);
					$oTableCell->save();
					$sTableCellValueModelName = $oTableCell->getTableDatatype()->model_name;

					// delete values
					$aTableCellValues = $oTableCell->getValues();
					foreach ($aTableCellValues as $oValue) {
						$bDeleteValue = true;
						$sValueFieldName = $sTableDatatypeLc . "_" . $oTableColumn->id . "_" . $oValue->id;
						foreach ($this->_formValues as $key => $value) {
							if ($sValueFieldName == $key) $bDeleteValue = false;
						}
						if ($bDeleteValue) {
							$oValue->delete();
						}
					}
					// get form values for creating new values
					$aValues = Core_Array::get($this->_formValues, $sTableDatatypeLc . "_" . $oTableColumn->id, array());
					// set form values for cell
					foreach ($aValues as $value) {
						$oValue = Core_Entity::factory($sTableCellValueModelName);
						$oValue
							->table_column_id($oTableColumn->id)
							->table_cell_id($oTableCell->id)
							->value($value)
							->deleted(0)
							->save();
					}
					// get form values for updating old values
					foreach ($this->_formValues as $key => $value) {
						// explode field name
						$aFieldName = explode("_", $key);
						// if array of field name is key of old value
						if (count($aFieldName) === 6) {
							// get value id
							$iValueId = array_pop($aFieldName);
							// get column id
							$iColumnId = array_pop($aFieldName);
							// get value datatype
							$sDatatype = implode('_', $aFieldName);
							if ($sDatatype == $sTableDatatypeLc && $oTableColumn->id == $iColumnId) {
								$oValue = Core_Entity::factory($sTableCellValueModelName, $iValueId);
								$oValue
									->table_column_id($oTableColumn->id)
									->table_cell_id($oTableCell->id)
									->value($value)
									->deleted(0)
									->save();
							}
						}
					}
			} // switch end
		}
	}

	/**
	 * Add image box into admin form
	 * @param  Admin_Form_Entity $oAdmin_Form_Entity admin form entity
	 * @param  string $sTableCellValueModelNameLc    table cell value model name
	 * @param  Table_Column $oTableColumn            table column object
	 * @param  string $addFunction                   js function for adding image box
	 * @param  string $deleteOnclick                 js function for deleting
	 * @return self
	 */
	public function imgBox(
		$oAdmin_Form_Entity,
		$sTableCellValueModelNameLc,
		$oTableColumn,
		$addFunction = '$.cloneProperty',
		$deleteOnclick = '$.deleteNewProperty(this)'
	)	{
		$windowId = $this->_Admin_Form_Controller->getWindowId();
		$oAdmin_Form_Entity
			->add(
				Admin_Form_Entity::factory('Div')
					->class('input-group-addon no-padding add-remove-property')
					->add(
						Admin_Form_Entity::factory('Div')
						->class('no-padding-' . ($oTableColumn->Table_Datatype->code == 'file' ? 'left' : 'right') . ' col-xs-12')
						->add(
							Admin_Form_Entity::factory('Div')
								->class('btn btn-palegreen')
								->add(Admin_Form_Entity::factory('Code')->html('<i class="fa fa-plus-circle close"></i>'))
								->onclick("{$addFunction}(
									'{$windowId}',
									this,
									'{$sTableCellValueModelNameLc}',
									'{$oTableColumn->id}'
								); event.stopPropagation();")
						)
						->add(
							Admin_Form_Entity::factory('Div')
								->class('btn btn-darkorange')
								->add(Admin_Form_Entity::factory('Code')->html('<i class="fa fa-minus-circle close"></i>'))
								->onclick($deleteOnclick . '; event.stopPropagation();')
						)
					)
			);
		return $this;
	}
}
