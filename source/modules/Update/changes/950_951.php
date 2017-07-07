<?php
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';

// add related for Events-Messages (for the app)
if (isModuleInstalled('Messages')) {
	$messagesModule = Vtiger_Module::getInstance('Messages');
	$modInst = Vtiger_Module::getInstance('Events');
	$modInst->setRelatedList($messagesModule, 'Messages', Array('ADD'), 'get_messages_list');
}


?>