<?php
SDK::setLanguageEntry('Messages', 'it_it', 'LBL_FLAGGED_ACTION', 'Rimuovi contrassegno');

global $adb, $table_prefix;
$tableName = $table_prefix.'_messages_tmp_rlist';
$schema_table =
	'<schema version="0.3">
		<table name="'.$tableName.'">
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
if(!Vtiger_Utils::CheckTable($tableName)) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}
?>