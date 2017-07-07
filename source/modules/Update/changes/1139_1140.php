<?php
/* crmv@85493 */
SDK::clearSessionValues();

global $adb, $table_prefix;

$schema = 
	'<?xml version="1.0"?>
	<schema version="0.3">
		<table name="'.$table_prefix.'_messages_mref">
			<opt platform="mysql">ENGINE=InnoDB</opt>
			<field name="messagesid" type="I" size="19">
				<NOTNULL/>
				<key/>
			</field>
			<field name="mreference" type="C" size="127">
				<NOTNULL/>
				<key/>
			</field>
			<index name="messages_mref_idx">
				<col>mreference</col>
			</index>
		</table>
	</schema>';
if (!Vtiger_Utils::CheckTable($table_prefix.'_messages_mref')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema));
}

// populate mref table
$focus = CRMEntity::getInstance('Messages');
$focus->rebuildMrefTable();

// remove some indexes and add a new one!
$table = "{$table_prefix}_messages";

$dropIndexes = array("{$table_prefix}_messages_adoptchildren", "{$table_prefix}_messages_referencechildren_idx", "{$table_prefix}_messages_account_idx");

// disable die on error for indexes
$oldDie = $adb->dieOnError;
$adb->setDieOnError(false);

if ($adb->isMysql()) {
	// fast code, only for mysql
	
	// check if they exists and drop them
	$drops = array();
	foreach ($dropIndexes as $idx) {
		$res = $adb->pquery("SHOW INDEX FROM `$table` WHERE KEY_NAME = ?", array($idx));
		if ($res && $adb->num_rows($res) > 0) {
			$drops[] = "DROP INDEX $idx";
		}
	}
	$alter = implode(', ', $drops).(count($drops) > 0 ? ", " : "")."ADD INDEX {$table_prefix}_messages_accfolddate (account, folder, mdate)";
	$res = $adb->query("ALTER TABLE `$table` {$alter}");

} else {
	// generic queries
	
	$indexes = $adb->database->MetaIndexes($table);

	// drop indexes
	foreach($indexes as $name => $index) {
		if (in_array($name, $dropIndexes)) {
			$sql = $adb->datadict->DropIndexSQL($name, $table);
			if ($sql) $adb->datadict->ExecuteSQLArray($sql);
		}
	}

	// create the index
	$sql = $adb->datadict->CreateIndexSQL("{$table_prefix}_messages_accfolddate", $table, array('account', 'folder', 'mdate'));
	if ($sql) $adb->datadict->ExecuteSQLArray($sql);

}

$adb->setDieOnError($oldDie);
