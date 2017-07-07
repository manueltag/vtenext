<?php

/* crmv@107655 */

// create a nice table for the report config
$schema = 
	'<?xml version="1.0"?>
	<schema version="0.3">
		<table name="'.$table_prefix.'_email_directory_sync">
			<opt platform="mysql">ENGINE=InnoDB</opt>
			<field name="userid" type="I" size="19">
				<key/>
			</field>
			<field name="last_update" type="T">
				<DEFAULT value="0000-00-00 00:00:00"/>
			</field>
		</table>
	</schema>';
if (!Vtiger_Utils::CheckTable($table_prefix.'_email_directory_sync')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema));
}

// and populate the table
$adb->query("TRUNCATE table {$table_prefix}_email_directory_sync");

$now = date("Y-m-d H:i:s");
$adb->query("INSERT INTO {$table_prefix}_email_directory_sync (userid, last_update) SELECT DISTINCT userid, '$now' FROM {$table_prefix}_email_directory");
