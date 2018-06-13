<?php
require_once('../../../bootstrap.php');
Core_Auth::authorization($sModule = 'table');

///////////////
// constants //
///////////////

$iTableId = intval(Core_Array::getGet('table_id'));
$iTableItemId = intval(Core_Array::getGet('table_item_id'));
$oAdmin_Form = Core_Entity::factory('Admin_Form')->getByGuid('479BEBAC-A43C-FF69-6137-9AC6286CC144');
$iAdmin_Form_Id = $oAdmin_Form->id;

// Код формы
$sAdminFormAction = '/admin/table/row/index.php';

// Контроллер формы
$oAdmin_Form_Controller = Admin_Form_Controller::create($oAdmin_Form);
$oAdmin_Form_Controller
	->module(Core_Module::factory($sModule))
	->setUp()
	->path($sAdminFormAction)
	->title(Core::_('Table_Row.model_name'))
	->pageTitle(Core::_('Table_Row.model_name'));

	// Меню формы
$oAdmin_Form_Entity_Menus = Admin_Form_Entity::factory('Menus');

// Элементы меню
$oAdmin_Form_Entity_Menus->add(
	Admin_Form_Entity::factory('Menu')
		->name(Core::_('Table_Row.menu'))
		->icon('fa fa-tasks')
		->add(
			Admin_Form_Entity::factory('Menu')
				->name(Core::_('Table_Row.add'))
				->icon('fa fa-plus')
				->img('/admin/images/page_add.gif')
				->href(
					$oAdmin_Form_Controller->getAdminActionLoadHref($oAdmin_Form_Controller->getPath(), 'edit', NULL, 0, 0)
				)
				->onclick(
					$oAdmin_Form_Controller->getAdminActionLoadAjax($oAdmin_Form_Controller->getPath(), 'edit', NULL, 0, 0)
				)
		)
);

// Добавляем все меню контроллеру
$oAdmin_Form_Controller->addEntity($oAdmin_Form_Entity_Menus);

// Элементы строки навигации
$oAdmin_Form_Entity_Breadcrumbs = Admin_Form_Entity::factory('Breadcrumbs');

// Элементы строки навигации
$oAdmin_Form_Entity_Breadcrumbs->add(
	Admin_Form_Entity::factory('Breadcrumb')
		->name(Core::_('Table.model_name'))
		->href($oAdmin_Form_Controller->getAdminLoadHref('/admin/table/index.php', NULL, NULL, ''))
		->onclick($oAdmin_Form_Controller->getAdminLoadAjax('/admin/table/index.php', NULL, NULL, ''))
)->add(
	Admin_Form_Entity::factory('Breadcrumb')
		->name(Core::_('Table_Item.model_name'))
		->href($oAdmin_Form_Controller->getAdminLoadHref('/admin/table/item/index.php', NULL, NULL, 'table_id=' . $iTableId))
		->onclick($oAdmin_Form_Controller->getAdminLoadAjax('/admin/table/item/index.php', NULL, NULL, 'table_id=' . $iTableId)
	)
)->add(
	Admin_Form_Entity::factory('Breadcrumb')
		->name(Core::_('Table_Row.model_name'))
		->href($oAdmin_Form_Controller->getAdminLoadHref($oAdmin_Form_Controller->getPath(), NULL, NULL, 'table_id=' . $iTableId . '&table_item_id=' . $iTableItemId))
		->onclick($oAdmin_Form_Controller->getAdminLoadAjax($oAdmin_Form_Controller->getPath(), NULL, NULL, 'table_id=' . $iTableId . '&table_item_id=' . $iTableItemId)
	)
);

$oAdmin_Form_Controller->addEntity($oAdmin_Form_Entity_Breadcrumbs);

// Действие редактирования
$oAdmin_Form_Action = Core_Entity::factory('Admin_Form', $iAdmin_Form_Id)
	->Admin_Form_Actions
	->getByName('edit');

if ($oAdmin_Form_Action && $oAdmin_Form_Controller->getAction() == 'edit')
{
	$oTable_Row_Controller_Edit = new Table_Row_Controller_Edit(
		$oAdmin_Form_Action
	);
	$oTable_Row_Controller_Edit
		->addSkipColumn('id')
		->addSkipColumn('table_id')
		->addSkipColumn('table_item_id')
		->addSkipColumn('site_id')
		->addEntity($oAdmin_Form_Entity_Breadcrumbs);
	// Добавляем типовой контроллер редактирования контроллеру формы
	$oAdmin_Form_Controller->addAction($oTable_Row_Controller_Edit);
}

// Действие "Применить"
$oAdminFormActionApply = Core_Entity::factory('Admin_Form', $iAdmin_Form_Id)
	->Admin_Form_Actions
	->getByName('apply');

if ($oAdminFormActionApply && $oAdmin_Form_Controller->getAction() == 'apply')
{
	$oSeoSiteControllerApply = Admin_Form_Action_Controller::factory(
		'Admin_Form_Action_Controller_Type_Apply', $oAdminFormActionApply
	);

	// Добавляем типовой контроллер редактирования контроллеру формы
	$oAdmin_Form_Controller->addAction($oSeoSiteControllerApply);
}


// Действие "Удаление файлового значения"
$oAdminFormActionDeleteFile = Core_Entity::factory('Admin_Form', $iAdmin_Form_Id)
	->Admin_Form_Actions
	->getByName('deleteFile');

if ($oAdminFormActionDeleteFile && $oAdmin_Form_Controller->getAction() == 'deleteFile')
{
	$oRowControllerdeleteFile = Admin_Form_Action_Controller::factory(
		'Table_Cell_Value_Controller_Delete_File', $oAdminFormActionDeleteFile
	);
	$oAdmin_Form_Controller->addAction($oRowControllerdeleteFile);
}

// Источник данных 0
$oAdmin_Form_Dataset = new Admin_Form_Dataset_Entity(
	Core_Entity::factory('Table_Row')
);

// Ограничение источника 1 по родительской группе
$oAdmin_Form_Dataset->addCondition(
	array('where' =>
		array('site_id', '=', CURRENT_SITE)
	)
);

// ограничение по Id схемы
$oAdmin_Form_Dataset->addCondition(
	array('where' =>
		array('table_item_id', '=', $iTableItemId)
	)
);

// Добавляем источник данных контроллеру формы
$oAdmin_Form_Controller->addDataset(
	$oAdmin_Form_Dataset
);

// Показ формы
$oAdmin_Form_Controller->execute();