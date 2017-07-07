<?php
@unlink('Smarty/templates/ActivityListView.tpl');

$moduleInstance = Vtiger_Module::getInstance('Sms');
Vtiger_Menu::detachModule($moduleInstance);
?>