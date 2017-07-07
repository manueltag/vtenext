<?php
$_SESSION['modules_to_update']['Charts'] = 'packages/vte/optional/Charts.zip';
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/optional/Touch.zip';
$_SESSION['modules_to_update']['Webforms'] = 'packages/vte/mandatory/Webforms.zip';

global $adb, $table_prefix;
$adb->pquery("UPDATE {$table_prefix}_users SET default_module = ? WHERE default_module = ?",array('Messages','Webmails'));

// traduzioni per revision
global $enterprise_current_version,$enterprise_mode;
SDK::setLanguageEntries('APP_STRINGS', 'LBL_BROWSER_TITLE', array(
	'it_it'=>"$enterprise_mode $enterprise_current_version",
	'en_us'=>"$enterprise_mode $enterprise_current_version",
	'pt_br'=>"$enterprise_mode $enterprise_current_version",
	'de_de'=>"$enterprise_mode $enterprise_current_version"
));

// add eventually missing index on cntactivityrel
$indexes = array_keys($adb->database->MetaIndexes($table_prefix.'_cntactivityrel'));
if (!in_array('cntactivityrel_contactid_idx',$indexes)) {
	$sql = $adb->datadict->CreateIndexSQL('cntactivityrel_contactid_idx', $table_prefix.'_cntactivityrel', 'contactid');
	if ($sql) @$adb->datadict->ExecuteSQLArray($sql);
}
if (!in_array('cntactivityrel_activityid_idx',$indexes)) {
	$sql = $adb->datadict->CreateIndexSQL('cntactivityrel_activityid_idx', $table_prefix.'_cntactivityrel', 'activityid');
	if ($sql) @$adb->datadict->ExecuteSQLArray($sql);
}

folderDetete('include/ckeditor/_samples');
folderDetete('include/ckeditor/_source');
@unlink('include/ckeditor/ckeditor_basic_source.js');
@unlink('include/ckeditor/ckeditor_source.js');

SDK::setLanguageEntries('APP_STRINGS','LBL_SHOW_DETAILS',array('it_it'=>'Dettagli','en_us'=>'Details'));
?>