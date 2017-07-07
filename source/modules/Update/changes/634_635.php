<?php
$_SESSION['modules_to_update']['Morphsuit'] = 'packages/vte/mandatory/Morphsuit.zip';

global $adb, $table_prefix;

$result = $adb->query("SELECT {$table_prefix}_tab.name FROM vte_hide_tab
						INNER JOIN {$table_prefix}_tab ON vte_hide_tab.tabid = {$table_prefix}_tab.tabid
						WHERE hide_module_manager = 1");
if ($result && $adb->num_rows($result) > 0) {
	while($row=$adb->fetchByAssoc($result)) {
		vtlib_toggleModuleAccess($row['name'],true);
	}
}

if(!Vtiger_Utils::CheckTable($table_prefix.'_reload_session')) {
	$schema_table = '<schema version="0.3">
					  <table name="'.$table_prefix.'_reload_session">
					  <opt platform="mysql">ENGINE=InnoDB</opt>
					    <field name="userid" type="I" size="19">
					      <KEY/>
					    </field>
					    <field name="session_var" type="C" size="255"/>
					  </table>
					</schema>';
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}
?>