<?php
$_SESSION['modules_to_update']['Morphsuit'] = 'packages/vte/mandatory/Morphsuit.zip';
$_SESSION['modules_to_update']['MyNotes'] = 'packages/vte/mandatory/MyNotes.zip';

global $adb, $table_prefix;
if (Vtiger_Utils::CheckTable($table_prefix.'_reload_session')) {
	$sqlarray = $adb->datadict->DropTableSQL($table_prefix.'_reload_session');
	$adb->datadict->ExecuteSQLArray($sqlarray);
	
	$schema_table = '<schema version="0.3">
					  <table name="'.$table_prefix.'_reload_session">
					  <opt platform="mysql">ENGINE=InnoDB</opt>
					    <field name="userid" type="I" size="19">
					      <KEY/>
					    </field>
					    <field name="session_var" type="C" size="255">
					      <KEY/>
					    </field>
					  </table>
					</schema>';
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}
if (in_array('reload_session', $adb->getColumnNames($table_prefix.'_users'))) {
	$sqlarray = $adb->datadict->DropColumnSQL($table_prefix.'_users','reload_session');
	$adb->datadict->ExecuteSQLArray($sqlarray);
}
?>