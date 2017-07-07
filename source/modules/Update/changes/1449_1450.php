<?php
global $adb, $table_prefix;

if (!function_exists('addColumnToTable')) {
	function addColumnToTable($tablename, $columnname, $type, $extra = '') {
		global $adb;

		// check if already present
		$cols = $adb->getColumnNames($tablename);
		if (in_array($columnname, $cols)) {
			return;
		}

		$col = $columnname.' '.$type.' '.$extra;
		$adb->alterTable($tablename, $col, 'Add_Column');
	}
}

$table = $table_prefix.'_running_processes_logs';
if (Vtiger_Utils::CheckTable($table)) {
	addColumnToTable($table, 'rollbck', 'I(1)', 'DEFAULT 0');
}

$name = "{$table_prefix}_running_processes_logsi";
$schema_table = '<?xml version="1.0"?>
<schema version="0.3">
	<table name="'.$name.'">
		<opt platform="mysql">ENGINE=InnoDB</opt>
	    <field name="id" type="I" size="19">
	      <KEY/>
	    </field>
		<field name="running_process" type="I" size="19"/>
		<field name="elementid" type="C" size="255"/>
	    <field name="info" type="XL"/>
	</table>
</schema>';
if(!Vtiger_Utils::CheckTable($name)) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

SDK::setLanguageEntries('Processes', 'LBL_CHANGE_POSITION', array(
	'it_it'=>'Forza posizione',
	'en_us'=>'Force position',
));
SDK::setLanguageEntries('Processes', 'LBL_MANUAL_CHANGED_POSITION', array(
	'it_it'=>'Cambio posizione manuale',
	'en_us'=>'Manually changed position',
));