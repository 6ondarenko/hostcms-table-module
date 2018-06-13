<?php
defined('HOSTCMS') || exit('HostCMS: access denied.');
/**
 * Table.
 *
 * @package Table
 * @version 1.0
 * @author Konstantin Bondarenko
 * @copyright © 2007-2018 Артатом, https://www.artatom.ru
 */
class Module_Skin_Bootstrap_Table_Module extends Table_Module
{
	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		Core_Event::attach('Skin_Bootstrap.onLoadSkinConfig', array('Skin_Bootstrap_Module_Supermodule_Module', 'onLoadSkinConfig'));
	}

	static public function onLoadSkinConfig($object, $args)
	{
		// Load config
		$aConfig = $object->getConfig();
		// Add module into 'content' section, see config.php
		$aConfig['adminMenu']['table'] = array(
			'ico' => 'fa fa-table',
			'caption' => Core::_('Table.topmenu'),
			'modules' => array('table'),
		);
		// Set new config
		$object->setConfig($aConfig);
	}
}