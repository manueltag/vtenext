<?php
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter','Targets'));

global $adb, $table_prefix;

// crmv@48861 - add the linked picklist uitype to the list of webservices field
$uitype = 300;
$res = $adb->pquery("select * from {$table_prefix}_ws_fieldtype where uitype = ?", array($uitype));
if ($res && $adb->num_rows($res) == 0) {
	$id = $adb->getUniqueId("{$table_prefix}_ws_fieldtype");
	$res = $adb->pquery("insert into {$table_prefix}_ws_fieldtype (fieldtypeid, uitype, fieldtype) values (?,?,?)", array($id, $uitype, 'picklist'));
}

$sqlarray = $adb->datadict->DropTableSQL("{$table_prefix}_messages_cron");
$adb->datadict->ExecuteSQLArray($sqlarray);

$schema_table =
'<schema version="0.3">
  <table name="'.$table_prefix.'_messages_cron_uid">
	<opt platform="mysql">ENGINE=InnoDB</opt>
    <field name="sequence" type="I" size="19"/>
    <field name="userid" type="I" size="19">
      <KEY/>
    </field>
    <field name="accountid" type="I" size="19">
      <KEY/>
    </field>
    <field name="folder" type="C" size="50">
      <KEY/>
    </field>
    <field name="uid" type="I" size="19">
      <KEY/>
    </field>
    <field name="date" type="T">
      <DEFAULT value="0000-00-00 00:00:00"/>
    </field>
    <field name="action" type="C" size="10"/>
    <field name="status" type="I" size="1">
      <DEFAULT value="0"/>
    </field>
    <index name="'.$table_prefix.'_messages_cron_uid_userid_idx">
      <col>userid</col>
    </index>
    <index name="'.$table_prefix.'_messages_cron_uid_accountid_idx">
      <col>accountid</col>
    </index>
    <index name="'.$table_prefix.'_messages_cron_uid_folder_idx">
      <col>folder</col>
    </index>
    <index name="'.$table_prefix.'_messages_cron_uid_date_idx">
      <col>date</col>
    </index>
    <index name="'.$table_prefix.'_messages_cron_uid_sequence_idx">
      <col>sequence</col>
    </index>
    <index name="'.$table_prefix.'_messages_cron_uid_status_idx">
      <col>status</col>
    </index>
  </table>
</schema>';
if(!Vtiger_Utils::CheckTable($table_prefix.'_messages_cron_uid')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

$schema_table =
'<schema version="0.3">
  <table name="'.$table_prefix.'_messages_cron_uidi">
	<opt platform="mysql">ENGINE=InnoDB</opt>
    <field name="sequence" type="I" size="19"/>
    <field name="userid" type="I" size="19">
      <KEY/>
    </field>
    <field name="accountid" type="I" size="19">
      <KEY/>
    </field>
    <field name="folder" type="C" size="50">
      <KEY/>
    </field>
    <field name="uid" type="I" size="19">
      <KEY/>
    </field>
    <field name="date" type="T">
      <DEFAULT value="0000-00-00 00:00:00"/>
    </field>
    <field name="action" type="C" size="10"/>
    <field name="status" type="I" size="1">
      <DEFAULT value="0"/>
    </field>
    <index name="'.$table_prefix.'_messages_cron_uidi_userid_idx">
      <col>userid</col>
    </index>
    <index name="'.$table_prefix.'_messages_cron_uidi_accountid_idx">
      <col>accountid</col>
    </index>
    <index name="'.$table_prefix.'_messages_cron_uidi_folder_idx">
      <col>folder</col>
    </index>
    <index name="'.$table_prefix.'_messages_cron_uidi_date_idx">
      <col>date</col>
    </index>
    <index name="'.$table_prefix.'_messages_cron_uidi_sequence_idx">
      <col>sequence</col>
    </index>
    <index name="'.$table_prefix.'_messages_cron_uidi_status_idx">
      <col>status</col>
    </index>
  </table>
</schema>';
if(!Vtiger_Utils::CheckTable($table_prefix.'_messages_cron_uidi')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

require_once('include/utils/CronUtils.php');
$CU = CronUtils::getInstance();

$cj = new CronJob();
$cj->name = 'MessagesUids';
$cj->active = 1;
$cj->singleRun = false;
$cj->fileName = 'cron/modules/Messages/MessagesUids.service.php';
$cj->timeout = 1800;
$cj->repeat = 600;
$CU->insertCronJob($cj);
				
$result = $adb->pquery("select cronid from {$table_prefix}_cronjobs where cronname = ?",array('Messages'));
if ($result && $adb->num_rows($result) > 0) {
	$cronid = $adb->query_result($result,0,'cronid');
	$CU->deleteCronJob($cronid);
	$cj = new CronJob();
	$cj->name = 'Messages';
	$cj->active = 1;
	$cj->singleRun = false;
	$cj->fileName = 'cron/modules/Messages/Messages.service.php';
	$cj->timeout = 600;
	$cj->repeat = 60;
	$CU->insertCronJob($cj);
}

$cj = new CronJob();
$cj->name = 'MessagesInboxUids';
$cj->active = 1;
$cj->singleRun = false;
$cj->fileName = 'cron/modules/Messages/InboxUids.service.php';
$cj->timeout = 600;
$cj->repeat = 60;
$CU->insertCronJob($cj);
				
$result = $adb->pquery("select cronid from {$table_prefix}_cronjobs where cronname = ?",array('MessagesInbox'));
if ($result && $adb->num_rows($result) > 0) {
	$cronid = $adb->query_result($result,0,'cronid');
	$CU->deleteCronJob($cronid);
	$cj = new CronJob();
	$cj->name = 'MessagesInbox';
	$cj->active = 1;
	$cj->singleRun = false;
	$cj->fileName = 'cron/modules/Messages/Inbox.service.php';
	$cj->timeout = 600;
	$cj->repeat = 60;
	$CU->insertCronJob($cj);
}

$cj = new CronJob();
$cj->name = 'MessagesSyncFolders';
$cj->active = 1;
$cj->singleRun = false;
$cj->fileName = 'cron/modules/Messages/SyncFolders.service.php';
$cj->timeout = 600;
$cj->repeat = 120;
$CU->insertCronJob($cj);

SDK::setLanguageEntries('Emails', 'LBL_PROBLEMS_MESSAGE_SENDING', array('it_it'=>'Alcuni messaggi potrebbero non essere stati inviati. Contattare l\'assistenza.','en_us'=>'Some messages could not be sent. Please contact help desk.'));
SDK::setLanguageEntries('Messages', 'LBL_PROBLEMS_MESSAGE_FETCHING', array('it_it'=>'Alcuni messaggi non sono stati scaricati. Contattare l\'assistenza.','en_us'=>'Some messages were not downloaded. Please contact help desk.'));
?>