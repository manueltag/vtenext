<?php

/* crmv@97237 crmv@100399 */

// change the name to that stupid report table
if (Vtiger_Utils::CheckTable($table_prefix.'_reportfilters')) {
	$cols = $adb->database->MetaColumns($table_prefix.'_reportfilters');
	if (array_key_exists('NAME', $cols)) {
		$sql = $adb->datadict->RenameTableSQL($table_prefix.'_reportfilters',$table_prefix.'_reportvisibility');
		if ($sql) $adb->datadict->ExecuteSQLArray($sql);
	}
}

// drop this useless table
if (Vtiger_Utils::CheckTable("{$table_prefix}_selectquery")) {
	$sqlarray = $adb->datadict->DropTableSQL("{$table_prefix}_selectquery");
	$adb->datadict->ExecuteSQLArray($sqlarray);
}

if (Vtiger_Utils::CheckTable("{$table_prefix}_selectquery_seq")) {
	$sqlarray = $adb->datadict->DropTableSQL("{$table_prefix}_selectquery_seq");
	$adb->datadict->ExecuteSQLArray($sqlarray);
}

// fix the main seq table
if (Vtiger_Utils::CheckTable("{$table_prefix}_report_seq")) {
	$res = $adb->query("SELECT MAX(reportid) as id FROM {$table_prefix}_report");
	$maxid = $adb->query_result_no_html($res, 0, 'id');
	if ($maxid > 0) {
		$res = $adb->pquery("UPDATE {$table_prefix}_report_seq SET id = ?", array($maxid));
	}
} else {
	// create the table
	$adb->getUniqueID("{$table_prefix}_report");
	// and now fix the sequence
	if (Vtiger_Utils::CheckTable("{$table_prefix}_report_seq")) {
		$res = $adb->query("SELECT MAX(reportid) as id FROM {$table_prefix}_report");
		$maxid = $adb->query_result_no_html($res, 0, 'id');
		if ($maxid > 0) {
			$res = $adb->pquery("UPDATE {$table_prefix}_report_seq SET id = ?", array($maxid));
		}
	}
}

// remove some useless columns
if (in_array('category', $adb->getColumnNames($table_prefix.'_report'))) {
	$sqlarray = $adb->datadict->DropColumnSQL($table_prefix.'_report','category');
	$adb->datadict->ExecuteSQLArray($sqlarray);
}

// create a nice table for the report config
$schema = 
	'<?xml version="1.0"?>
	<schema version="0.3">
		<table name="'.$table_prefix.'_reportconfig">
			<opt platform="mysql">ENGINE=InnoDB</opt>
			<field name="reportid" type="I" size="19">
				<key/>
			</field>
			<field name="module" type="C" size="63">
				<NOTNULL/>
			</field>
			<field name="relations" type="XL" />
			<field name="fields" type="XL" />
			<field name="stdfilters" type="XL" />
			<field name="advfilters" type="XL" />
			<field name="totals" type="XL" />
			<field name="summary" type="XL" />
		</table>
	</schema>';
if (!Vtiger_Utils::CheckTable($table_prefix.'_reportconfig')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema));
}


// translations
$trans = array(
	'Reports' => array(
		'it_it' => array(
			'LBL_FIELD_FOR_SUMMARY' => 'Raggruppa',
			'LBL_GROUPING_SORT' => 'Ordinamento',	// existing label!
			'LBL_FORMULA' => 'Formula',
			'LBL_GROUP_BY_FIELD' => 'Raggruppa per questo campo',
			'MODULE_RELATED_TO' => 'Modulo relazionato a',
		),
		'en_us' => array(
			'LBL_FIELD_FOR_SUMMARY' => 'Raggruppa',
			'LBL_GROUPING_SORT' => 'Sorting',
			'LBL_FORMULA' => 'Formula',
			'LBL_GROUP_BY_FIELD' => 'Group by this field',
			'MODULE_RELATED_TO' => 'Module related to',
		),
	),
	'ALERT_ARR' => array(
		'it_it' => array(
			'LBL_GROUPBY' => 'Raggruppa',
			'LBL_SUMMARY' => 'Riassuntivo',
			'MODULE_RELATED_TO' => 'Modulo relazionato a',
		),
		'en_us' => array(
			'LBL_GROUPBY' => 'Group by',
			'LBL_SUMMARY' => 'Summary',
			'MODULE_RELATED_TO' => 'Module related to',
		),
	),
);
foreach ($trans as $module=>$modlang) {
	foreach ($modlang as $lang=>$translist) {
		foreach ($translist as $label=>$translabel) {
			SDK::setLanguageEntry($module, $lang, $label, $translabel);
		}
	}
}

// report migration
include('modules/Update/changes/1235_1236reports.php');
