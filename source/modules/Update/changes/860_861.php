<?php
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';

global $adb, $table_prefix;

SDK::setLanguageEntries('Messages', 'LBL_CHECK_CRON_CONFIGURATION', array('it_it'=>'Verifica che sia attivo lo script Messaggi nel crontab','en_us'=>'Check cron configuration'));

require_once('include/utils/CronUtils.php');
$CU = CronUtils::getInstance();
$cj = CronJob::getByName('MessagesInbox'); // to update if existing
if (empty($cj)) {
	$cj = new CronJob();
	$cj->name = 'MessagesInbox';
	$cj->active = 1;
	$cj->singleRun = false;
	$cj->fileName = 'cron/modules/Messages/Inbox.service.php';
	$cj->timeout = 300;			// 5 min timeout
	$cj->repeat = 60;			// run every 1 min
	$CU->insertCronJob($cj);
}
$cj = CronJob::getByName('MessagesPropagateToImap'); // to update if existing
if (empty($cj)) {
	$cj = new CronJob();
	$cj->name = 'MessagesPropagateToImap';
	$cj->active = 1;
	$cj->singleRun = false;
	$cj->fileName = 'cron/modules/Messages/PropagateToImap.service.php';
	$cj->timeout = 300;			// 5 min timeout
	$cj->repeat = 60;			// run every 1 min
	$CU->insertCronJob($cj);
}

if (!Vtiger_Utils::CheckTable($table_prefix.'_messages_sfolders')) {
	$sql = $adb->datadict->RenameTableSQL($table_prefix.'_messages_folders',$table_prefix.'_messages_sfolders');
	$adb->datadict->ExecuteSQLArray($sql);
}

$schema_table =
'<schema version="0.3">
	<table name="'.$table_prefix.'_messages_folders">
	  <opt platform="mysql">ENGINE=InnoDB</opt>
	    <field name="userid" type="I" size="19">
	      <KEY/>
	    </field>
	    <field name="accountid" type="I" size="10">
	      <KEY/>
	    </field>
	    <field name="globalname" type="C" size="255">
	      <KEY/>
	    </field>
	    <field name="localname" type="C" size="255"/>
	    <field name="depth" type="I" size="10">
	      <DEFAULT value="0"/>
	    </field>
	    <field name="selectable" type="I" size="1">
	      <DEFAULT value="0"/>
	    </field>
	    <field name="count" type="I" size="10">
	      <DEFAULT value="0"/>
	    </field>
	    <field name="sequence" type="I" size="10">
	      <DEFAULT value="0"/>
	    </field>
	    <index name="idx_messages_folders_u">
	      <col>userid</col>
	    </index>
	    <index name="idx_messages_folders_a">
	      <col>accountid</col>
	    </index>
	    <index name="idx_messages_folders_l">
	      <col>localname</col>
	    </index>
	  </table>
</schema>';
if(!Vtiger_Utils::CheckTable($table_prefix.'_messages_folders')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

$schema_table =
'<schema version="0.3">
  <table name="'.$table_prefix.'_messages_prop2imap">
	  <opt platform="mysql">ENGINE=InnoDB</opt>
	    <field name="sequence" type="I" size="19">
	      <KEY/>
	    </field>
	    <field name="operation" type="C" size="50"/>
	    <field name="params" type="XL"/>
	    <field name="status" type="I" size="11"/>
	    <field name="attempts" type="I" size="11"/>
	    <field name="max_attempts" type="I" size="11"/>
	    <field name="error" type="C" size="200"/>
	  </table>
</schema>';
if(!Vtiger_Utils::CheckTable($table_prefix.'_messages_prop2imap')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}
?>