<?php
SDK::setLanguageEntries('Settings', 'LBL_PM_CONDITIONALS', array(
	'it_it'=>'Campi condizionali',
	'en_us'=>'Conditionals',
));
SDK::setLanguageEntries('Settings', 'LBL_PM_FIELD_GO_BACK', array(
	'it_it'=>'Torna a opzioni',
	'en_us'=>'Back to options',
));
SDK::setLanguageEntries('Settings', 'LBL_PM_SELECT_OPTION_FIELD', array(
	'it_it'=>'Seleziona campo...',
	'en_us'=>'Select field...',
));
SDK::setLanguageEntries('Processes', 'LBL_CONTINUE_EXECUTION', array(
	'it_it'=>'Continua esecuzione',
	'en_us'=>'Continue execution',
));
SDK::setLanguageEntries('Settings', 'LBL_PM_ACTION_DeleteConditionals', array(
	'it_it'=>'Resetta campi condizionali',
	'en_us'=>'Reset conditional fields',
));

global $adb, $table_prefix;
$table = $table_prefix.'_running_processes';
if (Vtiger_Utils::CheckTable($table)) {
	addColumnToTable($table, 'active', 'I(1)', 'DEFAULT 1');
}
$adb->pquery("update $table set active = ?", array(1));

$name = "{$table_prefix}_processmaker_conditionals";
$schema_table = '<?xml version="1.0"?>
<schema version="0.3">
  <table name="'.$name.'">
  <opt platform="mysql">ENGINE=InnoDB</opt>
    <field name="id" type="I" size="19">
      <KEY/>
    </field>
    <field name="running_process" type="I" size="19"/>
    <field name="crmid" type="I" size="19"/>
    <field name="elementid" type="C" size="255"/>
  </table>
</schema>';
if(!Vtiger_Utils::CheckTable($name)) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}