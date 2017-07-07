<?php
global $adb, $table_prefix;

$adb->query("DELETE FROM {$table_prefix}_emails_send_queue WHERE status = 2 AND s_send = 1 AND s_append = 0");

if(!Vtiger_Utils::CheckTable($table_prefix.'_plugins_tracking')) {
	$schema_table = '<schema version="0.3">
					  <table name="'.$table_prefix.'_plugins_tracking">
					  <opt platform="mysql">ENGINE=InnoDB</opt>
					    <field name="name" type="C" size="50">
					      <KEY/>
					    </field>
					    <field name="last_check" type="T">
					      <DEFAULT value="0000-00-00 00:00:00"/>
					    </field>
					  </table>
					</schema>';
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}
if(!Vtiger_Utils::CheckTable($table_prefix.'_time2check')) {
	$schema_table = '<schema version="0.3">
					  <table name="'.$table_prefix.'_time2check">
					  <opt platform="mysql">ENGINE=InnoDB</opt>
					    <field name="cwhat" type="C" size="50">
					      <KEY/>
					    </field>
					    <field name="cwhen" type="C" size="50"/>
					  </table>
					</schema>';
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

global $adb, $table_prefix;
if (file_exists('hash_version.txt')) {
	$hash_version = file_get_contents('hash_version.txt');
	$adb->updateClob($table_prefix.'_version','hash_version','id=1',$hash_version);
	@unlink('hash_version.txt');
}
$cache = Cache::getInstance('vteCacheHV');
$cache->clear();
?>