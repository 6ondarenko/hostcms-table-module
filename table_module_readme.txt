Для правильного отображения модуля в меню панели администрирования добавьте в /bootstrap.php следующий код для обработки события (в последнем блоке IF):
Core_Event::attach('Skin_Bootstrap.onLoadSkinConfig', array('Module_Skin_Bootstrap_Table_Module', 'onLoadSkinConfig'));
