<?php
SDK::setUitype(221, 'modules/SDK/src/221/221.php', 'modules/SDK/src/221/221.tpl', 'modules/SDK/src/221/221.js', 'picklist');

SDK::setLanguageEntries('Settings', 'LBL_PM_ACTION_Relate', array(
	'it_it'=>'Collega entità',
	'en_us'=>'Link entities',
));

if (isModuleInstalled('MyNotes')) {
	global $adb, $table_prefix;
	$myNotesInstance = Vtecrm_Module::getInstance('MyNotes');

	$adb->pquery("update {$table_prefix}_field set readonly = ?, displaytype = ? where tabid = ? and fieldname = ?", array(1, 3, $myNotesInstance->id, 'assigned_user_id'));
	
	$new_sequence = array('subject','assigned_user_id','user_and_time','createdtime','modifiedtime','description');
	$sequence = 1;
	foreach($new_sequence as $fieldname) {
		$adb->pquery("update {$table_prefix}_field set sequence = ? where tabid = ? and fieldname = ?", array($sequence, $myNotesInstance->id, $fieldname));
		$sequence++;
	}
}