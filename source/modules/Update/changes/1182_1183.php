<?php
global $adb;

require_once('include/utils/VTEProperties.php');
$VTEProperties = VTEProperties::getInstance();
$schema_table =
'<schema version="0.3">
	<table name="'.$VTEProperties->table_name_prop.'">
		<opt platform="mysql">ENGINE=InnoDB</opt>
		<field name="property" type="C" size="63">
			<KEY/>
		</field>
		<field name="value" type="C" size="1023" />
	</table>
</schema>';
if(!Vtiger_Utils::CheckTable($VTEProperties->table_name_prop)) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}
$VTEProperties->initDefaultProperties();
?>