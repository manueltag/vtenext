<?php
$sdkInstance = Vtiger_Module::getInstance('SDK');
$sdkInstance->addLink('HEADERSCRIPT', 'ProcessesScript', 'modules/Processes/Processes.js');

// translations
$trans = array(
	'Processes' => array(
		'it_it' => array(
			'LBL_RUN_PROCESS'=>'Prosegui processo',
			'LBL_RUN_PROCESSES'=>'Prosegui processo',
			'Process Graph'=>'Grafico processo',
		),
		'en_us' => array(
			'LBL_RUN_PROCESS'=>'Continue process',
			'LBL_RUN_PROCESSES'=>'Continue process',
			'Process Graph'=>'Process Graph',
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

global $adb, $table_prefix;
$name = "{$table_prefix}_processmaker_versions";

if (Vtiger_Utils::CheckTable($name)) {
	$sqlarray = $adb->datadict->DropTableSQL($name);
	$adb->datadict->ExecuteSQLArray($sqlarray);
}

$schema_table = '<?xml version="1.0"?>
<schema version="0.3">
  <table name="'.$name.'">
  <opt platform="mysql">ENGINE=InnoDB</opt>
    <field name="processmakerid" type="I" size="19">
      <KEY/>
    </field>
    <field name="xml_version" type="I" size="19">
      <KEY/>
    </field>
    <field name="userid" type="I" size="19"/>
    <field name="date_version" type="T">
      <DEFAULT value="0000-00-00 00:00:00"/>
    </field>
    <field name="xml" type="XL"/>
    <field name="vte_metadata" type="XL"/>
    <field name="structure" type="XL"/>
    <field name="helper" type="XL"/>
  </table>
</schema>';
if(!Vtiger_Utils::CheckTable($name)) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}