<?php
global $adb, $table_prefix;

$name = "{$table_prefix}_process_adv_permissions";
$schema_table = '<?xml version="1.0"?>
<schema version="0.3">
  <table name="'.$name.'">
  <opt platform="mysql">ENGINE=InnoDB</opt>
    <field name="running_process" type="I" size="19">
      <KEY/>
    </field>
    <field name="crmid" type="I" size="19">
      <KEY/>
    </field>
    <field name="resource" type="I" size="19">
      <KEY/>
    </field>
	<field name="resource_type" type="C" size="1"/>
    <field name="elementid" type="C" size="255"/>
    <field name="read_perm" type="I" size="1">
      <DEFAULT value="0"/>
    </field>
    <field name="write_perm" type="I" size="1">
      <DEFAULT value="0"/>
    </field>
  </table>
</schema>';
if(!Vtiger_Utils::CheckTable($name)) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

$result = $adb->pquery("select ruleid from {$table_prefix}_def_org_share where tabid = ?", array(14));
if ($result && $adb->num_rows($result) == 0) {
	$orgshareid = $adb->getUniqueId("{$table_prefix}_def_org_share");
	$adb->query("INSERT INTO {$table_prefix}_def_org_share (ruleid,tabid,permission,editstatus) VALUES ($orgshareid,14,2,0)");
}

$result = $adb->pquery("select * from {$table_prefix}_actionmapping where actionname = ?", array('DetailViewBlocks'));
if ($result && $adb->num_rows($result) == 0) $adb->pquery("insert into {$table_prefix}_actionmapping(actionid,actionname,securitycheck) values(?,?,?)", array(4,'DetailViewBlocks',1));

require_once('include/utils/UserInfoUtil.php');
RecalculateSharingRules();
create_tab_data_file();

$focusProcesses = CRMEntity::getInstance('Processes');
$focusProcesses->enableAllAdvancedPermissionsWidget();

// translations
$trans = array(
	'ALERT_ARR' => array(
		'it_it' => array(
			'LBL_PM_SELECT_RESOURCE'=>'Prego selezionare un utente',
			'LBL_PM_SELECT_ENTITY'=>'Prego selezionare un\'entità',
		),
		'en_us' => array(
			'LBL_PM_SELECT_RESOURCE'=>'Please select the user',
			'LBL_PM_SELECT_ENTITY'=>'Please select an entity',
		),
	),
	'Settings' => array(
		'it_it' => array(
			'LBL_PM_ADVANCED_PERMISSIONS'=>'Permessi avanzati',
			'LBL_PM_ADVANCED_PERMISSIONS_WIDGET'=>'Partecipanti',
			'LBL_PM_ADVANCED_PERMISSIONS_VISIBILITY'=>'Visibilità',
			'LBL_PM_PARTECIPANT_OF'=>'Partecipante a',
			'LBL_PM_RESOURCE'=>'Utente',
		),
		'en_us' => array(
			'LBL_PM_ADVANCED_PERMISSIONS'=>'Advanced permissions',
			'LBL_PM_ADVANCED_PERMISSIONS_WIDGET'=>'Participants',
			'LBL_PM_ADVANCED_PERMISSIONS_VISIBILITY'=>'Visibility',
			'LBL_PM_PARTECIPANT_OF'=>'Participant of',
			'LBL_PM_RESOURCE'=>'User',
		),
	),
	'Processes' => array(
		'it_it' => array(
			'LBL_EXECUTION_CONDITION'=>'Il controllo viene eseguito %s',
		),
		'en_us' => array(
			'LBL_EXECUTION_CONDITION'=>'Run the check %s',
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