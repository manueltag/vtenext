<?php


// useful functions
if (!function_exists('getPrimaryKeyName')) {
	function getPrimaryKeyName($tablename) {
		global $adb, $dbconfig;
		$ret = '';
		if ($adb->isMysql()) {
			// for mysql just check if it exists
			$res = $adb->query("SHOW KEYS FROM {$tablename} WHERE Key_name = 'PRIMARY'");
			if ($res && $adb->num_rows($res) > 0) $ret = 'PRIMARY';
		} elseif ($adb->isMssql()) {
			$res = $adb->pquery("SELECT CONSTRAINT_NAME as cn from INFORMATION_SCHEMA.TABLE_CONSTRAINTS where CONSTRAINT_CATALOG = ? and TABLE_NAME = ? and CONSTRAINT_TYPE = 'PRIMARY KEY'", array($dbconfig['db_name'], $tablename));
			if ($res) $ret = $adb->query_result_no_html($res, 0, 'cn');
		} elseif ($adb->isOracle()) {
			$res = $adb->pquery("SELECT CONSTRAINT_NAME as cn FROM all_constraints cons     WHERE cons.table_name = ? AND cons.constraint_type = 'P'", array(strtoupper($tablename)));
			if ($res) $ret = $adb->query_result_no_html($res, 0, 'cn');
		}
		return $ret;
	}
}

if (!function_exists('dropPrimaryKey')) {
	function dropPrimaryKey($tablename) {
		global $adb;
		if ($adb->isMysql()) {
			$keyname = getPrimaryKeyName($tablename);
			if ($keyname == 'PRIMARY') $adb->query("ALTER TABLE {$tablename} DROP PRIMARY KEY");
		} elseif ($adb->isMssql() || $adb->isOracle()) {
			$keyname = getPrimaryKeyName($tablename);
			$adb->query("ALTER TABLE {$tablename} DROP CONSTRAINT {$keyname}");
		} else {
			echo "Drop Primary key not supported for this database";
		}
	}
}

global $table_prefix;

// fix a problem with the key of this table
if (Vtiger_Utils::CheckTable($table_prefix.'_messages_ntel')) {
	// check the key
	$pkey = getPrimaryKeyName($table_prefix.'_messages_ntel');
	if (!empty($pkey)) {
		dropPrimaryKey($table_prefix.'_messages_ntel');
		// create the index
		$sql = $adb->datadict->CreateIndexSQL('messages_ntel_idx', $table_prefix.'_messages_ntel', 'messagesid');
		if ($sql) $adb->datadict->ExecuteSQLArray($sql);
	}
}

// fix a missing table
$schema = 
	'<?xml version="1.0"?>
	<schema version="0.3">
	<table name="'.$table_prefix.'_messages_deflist">
		<opt platform="mysql">ENGINE=InnoDB</opt>
		<field name="userid" type="I" size="19">
			<KEY/>
		</field>
		<field name="parentid" type="I" size="19">
			<KEY/>
		</field>
		<field name="id" type="I" size="19">
			<KEY/>
		</field>
	</table>
	</schema>';
if (!Vtiger_Utils::CheckTable($table_prefix.'_messages_deflist')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema));
}
