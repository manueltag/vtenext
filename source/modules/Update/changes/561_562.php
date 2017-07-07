<?php
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
global $adb;
$ModCommentsInstance = Vtiger_Module::getInstance('ModComments');
if ($ModCommentsInstance) {
	Vtiger_Menu::detachModule($ModCommentsInstance);
}
?>