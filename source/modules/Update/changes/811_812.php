<?php
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter','Targets'));
$_SESSION['modules_to_update']['MyNotes'] = 'packages/vte/mandatory/MyNotes.zip';

global $adb, $table_prefix;
$adb->pquery("update {$table_prefix}_relatedlists set name = ? where name = ?",array('get_related_list','get_targets'));

if (isModuleInstalled('MyNotes')) {
	$focus = CRMEntity::getInstance('MyNotes');
	$focus->migrateNotebook2MyNotes();
	$adb->pquery("update sdk_menu_fixed set cond = ? where title = ?",array('checkPermissionSDKButton:modules/MyNotes/widgets/Utils.php','MyNotes'));
}

SDK::deleteLanguageEntry('ALERT_ARR',null,'Notebook');
SDK::deleteLanguageEntry('Home',null,'LBL_NOTEBOOK');
SDK::deleteLanguageEntry('Home',null,'LBL_NOTEBOOK_TITLE');
SDK::deleteLanguageEntry('Home',null,'LBL_NOTEBOOK_SAVE_TITLE');

@unlink('include/js/notebook.js');
@unlink('modules/Home/SaveNotebookContents.php');
@unlink('Smarty/templates/Home/notebook.tpl');

@unlink('themes/softed/images/btnL3AllMenu.png');
@unlink('themes/softed/images/btnL3AllMenu_min.png');
?>