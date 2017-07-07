<?php
include_once('modules/SDK/InstallTables.php');

global $adb, $table_prefix;
$excluded_modules = array('Calendar','Events','Sms','Fax','Emails','Faq','Users','Myfiles','ChangeLog','MyNotes','PBXManager','Messages','Processes','ProductLines');
($adb->isMssql()) ? $query = "SELECT {$table_prefix}_tab.tabid, MIN({$table_prefix}_tab.name) AS \"name\"" : $query = "SELECT {$table_prefix}_tab.tabid, {$table_prefix}_tab.name";
$query .= " FROM {$table_prefix}_field
			INNER JOIN {$table_prefix}_tab ON {$table_prefix}_tab.tabid = {$table_prefix}_field.tabid
			WHERE {$table_prefix}_tab.name NOT IN (".generateQuestionMarks($excluded_modules).")
			GROUP BY {$table_prefix}_tab.tabid";
$result = $adb->pquery($query, array($excluded_modules));
if ($result) {
	while($row=$adb->fetchByAssoc($result)) {
		// check if already exists smcreatorid field
		$check = $adb->pquery("SELECT fieldid FROM {$table_prefix}_field WHERE tabid = ? AND columnname = ?", array($row['tabid'],'smcreatorid'));
		if ($check && $adb->num_rows($check) > 0) continue;
		$check = $adb->pquery("SELECT fieldid FROM {$table_prefix}_field WHERE tabid = ? AND fieldname = ?", array($row['tabid'],'creator'));
		if ($check && $adb->num_rows($check) > 0) continue;
		
		$moduleInstance = Vtiger_Module::getInstance($row['name']);
		$blocks = Vtiger_Block::getAllForModule($moduleInstance);
		if (empty($blocks)) continue;
		$blockInstance = $blocks[0];

		$field = new Vtiger_Field();
		$field->column = 'smcreatorid';
		$field->name = 'creator';
		$field->label= 'Creator';
		$field->table = $table_prefix.'_crmentity';
		$field->readonly = 1;
		$field->presence = 2;
		$field->typeofdata = 'V~O';
		$field->uitype = 52;
		$field->displaytype = 2;
		$field->masseditable = 0;
		$field->quickcreate = 3;
		$blockInstance->addField($field);
		
		SDK::setLanguageEntries($row['name'], 'Creator', array('it_it'=>'Creato da','en_us'=>'Creator'));
	}
}

SDK::setLanguageEntry('ModComments', 'it_it', 'Creator', 'Creato da');
SDK::setLanguageEntry('ModNotifications', 'it_it', 'Creator', 'Creato da');
?>