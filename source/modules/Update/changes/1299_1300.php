<?php

// crmv@94125
global $adb, $table_prefix;

$name = "{$table_prefix}_resource_version";
$schema_table = '<?xml version="1.0"?>
<schema version="0.3">
<table name="'.$name.'">
	<opt platform="mysql">ENGINE=InnoDB</opt>
	<field name="resource" type="C" size="200">
		<KEY/>
	</field>
	<field name="revision" type="I" size="8">
		<NOTNULL/>
		<DEFAULT value="-1"/>
	</field>
	<field name="versioned_resource" type="C" size="200"/>
	<field name="type" type="C" size="31"/>
	<field name="update_revision" type="I" size="1">
		<DEFAULT value="0"/>
	</field>
	<field name="filemtime" type="I" size="21"/>
	<field name="last_update" type="T"/>
	<index name="res_version_urev_idx">
		<col>update_revision</col>
	</index>
</table>
</schema>';
if(!Vtiger_Utils::CheckTable($name)) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}
