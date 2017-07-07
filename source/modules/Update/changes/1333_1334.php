<?php
$fields = array();
$fields[] = array('module'=>'Processes','block'=>'LBL_PROCESSES_INFORMATION','name'=>'process_actor','label'=>'Actor','uitype'=>'51','readonly'=>1,'presence'=>0,'displaytype'=>2,'quickcreate'=>3);
$fields[] = array('module'=>'Processes','block'=>'LBL_PROCESSES_INFORMATION','name'=>'process_status','label'=>'Status','uitype'=>'15','readonly'=>1,'presence'=>2,'displaytype'=>1,'quickcreate'=>3,'picklist'=>array('Running','Waiting','Ended'));
include('modules/SDK/examples/fieldCreate.php');

// TODO replace expiration with process_status in the filter All

SDK::addView('Processes', 'modules/SDK/src/modules/Processes/View.php', 'constrain', 'continue');
SDK::setAdvancedQuery('Processes', "advQueryProcesses", 'modules/SDK/src/modules/Processes/Utils.php');
SDK::setAdvancedPermissionFunction('Processes', "advPermProcesses",  'modules/SDK/src/modules/Processes/Utils.php');

// translations
$trans = array(
	'Processes' => array(
		'it_it' => array(
			'LBL_SELECT_ACTOR'=>'Seleziona partecipante',
			'Actor'=>'Partecipante',
			'Status'=>'Stato',
			'Running'=>'In corso',
			'Waiting'=>'In attesa',
			'Ended'=>'Terminato',
		),
		'en_us' => array(
			'LBL_SELECT_ACTOR'=>'Select actor',
			'Actor'=>'Partecipant',
		),
	),
	'Settings' => array(
		'it_it' => array(
			'LBL_PM_GATEWAY_END_PARALLEL'=>'Scegli gateway di chiusura del parallelo',
		),
		'en_us' => array(
			'LBL_PM_GATEWAY_END_PARALLEL'=>'Choose the parallel closing gateway',
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

$name = "{$table_prefix}_process_dynaform_cl";
$schema_table = '<?xml version="1.0"?>
<schema version="0.3">
	<table name="'.$name.'">
		<opt platform="mysql">ENGINE=InnoDB</opt>
	    <field name="running_process" type="I" size="19">
	      <KEY/>
	    </field>
	    <field name="metaid" type="I" size="19">
	      <KEY/>
	    </field>
	    <field name="seq" type="I" size="19">
	      <DEFAULT value="0"/>
	      <KEY/>
	    </field>
		<field name="userid" type="I" size="19"/>
	    <field name="change_date" type="T">
	      <DEFAULT value="0000-00-00 00:00:00"/>
	    </field>
	    <field name="form" type="XL"/>
	</table>
</schema>';
if(!Vtiger_Utils::CheckTable($name)) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

$name = "{$table_prefix}_process_gateway_conn";
$schema_table = '<?xml version="1.0"?>
<schema version="0.3">
	<table name="'.$name.'">
		<opt platform="mysql">ENGINE=InnoDB</opt>
	    <field name="running_process" type="I" size="19">
	      <KEY/>
	    </field>
	    <field name="elementid" type="C" size="255">
	      <KEY/>
	    </field>
	    <field name="flow" type="C" size="255">
	      <KEY/>
	    </field>
	    <field name="seq" type="I" size="19">
	      <DEFAULT value="0"/>
	    </field>
		<field name="bpmn_type" type="C" size="50"/>
		<field name="elementsons" type="XL"/>
		<field name="processesid" type="I" size="19"/>
		<field name="casperid" type="I" size="19"/>
		<field name="current_dynaform" type="C" size="50"/>
	</table>
</schema>';
if(!Vtiger_Utils::CheckTable($name)) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}