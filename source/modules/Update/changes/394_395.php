<?php
require_once("modules/Update/Update.php");
Update::change_field('vtiger_users','user_password','C','128');
Update::change_field('vtiger_users','confirm_password','C','128');
$_SESSION['modules_to_update']['Morphsuit'] = 'packages/vte/mandatory/Morphsuit.zip';
if (Vtiger_Module::getInstance('Morphsuit')){
	$module = Vtiger_Module::getInstance('Morphsuit');
	$module->addLink('HEADERSCRIPT', 'MorphsuitCommonScript', 'modules/Morphsuit/MorphsuitCommon.js');
}
?>