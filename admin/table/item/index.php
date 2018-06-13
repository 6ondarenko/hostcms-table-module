<?php
require_once('../../../bootstrap.php');
Core_Auth::authorization($sModule = 'table');

///////////////
// constants //
///////////////

$iTableId = intval(Core_Array::getGet('table_id'));

// Код формы
$sAdminFormAction = '/admin/table/item/index.php';
$oAdmin_Form = Core_Entity::factory('Admin_Form')->getByGuid('C77303E5-279E-0E68-F8F1-206EE698B19D');
$iAdmin_Form_Id = $oAdmin_Form->id;

// Контроллер формы
$oAdmin_Form_Controller = Admin_Form_Controller::create($oAdmin_Form);
$oAdmin_Form_Controller
	->module(Core_Module::factory($sModule))
	->setUp()
	->path($sAdminFormAction)
	->title(Core::_('Table_Item.model_name'))
	->pageTitle(Core::_('Table_Item.model_name'));

	// Меню формы
$oAdmin_Form_Entity_Menus = Admin_Form_Entity::factory('Menus');

$sTableColumns = '/admin/table/column/index.php';
$sTableColumnsParams = 'table_id=' . $iTableId;

// Элементы меню
$oAdmin_Form_Entity_Menus->add(
	Admin_Form_Entity::factory('Menu')
		->name(Core::_('Table_Item.menu'))
		->icon('fa fa-tasks')
		->add(
			Admin_Form_Entity::factory('Menu')
				->name(Core::_('Table_Item.add'))
				->icon('fa fa-plus')
				->img('/admin/images/page_add.gif')
				->href(
					$oAdmin_Form_Controller->getAdminActionLoadHref($oAdmin_Form_Controller->getPath(), 'edit', NULL, 0, 0)
				)
				->onclick(
					$oAdmin_Form_Controller->getAdminActionLoadAjax($oAdmin_Form_Controller->getPath(), 'edit', NULL, 0, 0)
				)
		)
		->add(
			Admin_Form_Entity::factory('Menu')
				->name(Core::_('Table_Item.columns'))
				->icon('fa fa-columns')
				->img('/admin/images/page_add.gif')
				->href(
					$oAdmin_Form_Controller->getAdminLoadHref($sTableColumns, NULL, NULL, $sTableColumnsParams)
				)
				->onclick(
					$oAdmin_Form_Controller->getAdminLoadAjax($sTableColumns, NULL, NULL, $sTableColumnsParams)
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
		->href(
			$oAdmin_Form_Controller->getAdminLoadHref('/admin/table/index.php', NULL, NULL, '')
		)
		->onclick(
			$oAdmin_Form_Controller->getAdminLoadAjax('/admin/table/index.php', NULL, NULL, '')
	)
)->add(
	Admin_Form_Entity::factory('Breadcrumb')
		->name(Core::_('Table_Item.model_name'))
		->href(
			$oAdmin_Form_Controller->getAdminLoadHref($oAdmin_Form_Controller->getPath(), NULL, NULL, 'table_id=' . $iTableId)
		)
		->onclick(
			$oAdmin_Form_Controller->getAdminLoadAjax($oAdmin_Form_Controller->getPath(), NULL, NULL, 'table_id=' . $iTableId)
	)
);

$oAdmin_Form_Controller->addEntity($oAdmin_Form_Entity_Breadcrumbs);

// Действие редактирования
$oAdmin_Form_Action = Core_Entity::factory('Admin_Form', $iAdmin_Form_Id)
	->Admin_Form_Actions
	->getByName('edit');

if ($oAdmin_Form_Action && $oAdmin_Form_Controller->getAction() == 'edit')
{
	$oTable_Item_Controller_Edit = new Table_Item_Controller_Edit(
		$oAdmin_Form_Action
	);

	$oTable_Item_Controller_Edit
		->addSkipColumn('id')
		->addSkipColumn('table_id')
		->addSkipColumn('site_id')
		->addEntity($oAdmin_Form_Entity_Breadcrumbs);

	// Добавляем типовой контроллер редактирования контроллеру формы
	$oAdmin_Form_Controller->addAction($oTable_Item_Controller_Edit);
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

// Источник данных 0
$oAdmin_Form_Dataset = new Admin_Form_Dataset_Entity(
	Core_Entity::factory('Table_Item')
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
		array('table_id', '=', intval($iTableId))
	)
);

// Добавляем источник данных контроллеру формы
$oAdmin_Form_Controller->addDataset(
	$oAdmin_Form_Dataset
);

// Показ формы
$oAdmin_Form_Controller->execute();