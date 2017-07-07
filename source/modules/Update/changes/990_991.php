<?php
$_SESSION['modules_to_update']['ChangeLog'] = 'packages/vte/mandatory/ChangeLog.zip';
$_SESSION['modules_to_update']['Ddt'] = 'packages/vte/mandatory/Ddt.zip';
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';
$_SESSION['modules_to_update']['Timecards'] = 'packages/vte/mandatory/Timecards.zip';

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

// add column license_id to vte_version
addColumnToTable($table_prefix.'_version', 'license_id', 'I(19)', 'DEFAULT 0');

SDK::setLanguageEntry('APP_STRINGS', 'nl_nl', 'OTHER_EMAIL_FILED_IS', 'in \'overig\' e-mail veld is');

/*
 * TABLE _config_layout
 * 
 * default_detail_view | string | summary / (empty string)
 * enable_switch_detail_view | int | 0 / 1
 * old_style | int | 0 / 1
 * tb_relations_order | string | num_of_records / layout_editor
 */
$tablename = $table_prefix.'_config_layout';
$schema_table =
'<schema version="0.3">
	<table name="'.$tablename.'">
		<opt platform="mysql">ENGINE=InnoDB</opt>
		<field name="default_detail_view" type="C" size="10" />
		<field name="enable_switch_detail_view" type="I" size="1">
			<DEFAULT value="0"/>
		</field>
		<field name="old_style" type="I" size="1">
			<DEFAULT value="0"/>
		</field>
		<field name="tb_relations_order" type="C" size="20" />
	</table>
</schema>';
if(!Vtiger_Utils::CheckTable($tablename)) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
	
	$params = array('summary',1,0,'num_of_records');
	$adb->pquery("insert into {$tablename} (default_detail_view,enable_switch_detail_view,old_style,tb_relations_order) values (?,?,?,?)",$params);
}
?>