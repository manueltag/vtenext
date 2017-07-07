<?php
global $adb, $table_prefix;

$schema_table =
'<schema version="0.3">
  <table name="'.$table_prefix.'_emails_send_queue">
  <opt platform="mysql">ENGINE=InnoDB</opt>
    <field name="id" type="I" size="19">
      <KEY/>
    </field>
    <field name="userid" type="I" size="19"/>
    <field name="method" type="C" size="50"/>
    <field name="request" type="XL"/>
    <field name="status" type="I" size="1">
      <DEFAULT value="0"/>
    </field>
    <field name="s_send" type="I" size="1">
      <DEFAULT value="0"/>
    </field>
    <field name="s_append" type="I" size="1">
      <DEFAULT value="0"/>
    </field>
    <field name="s_clean_pupload_attach" type="I" size="1">
      <DEFAULT value="0"/>
    </field>
    <field name="s_clean_drafts" type="I" size="1">
      <DEFAULT value="0"/>
    </field>
    <field name="error" type="C" size="200"/>
    <index name="'.$table_prefix.'_emails_send_queue_u_idx">
      <col>userid</col>
    </index>
    <index name="'.$table_prefix.'_emails_send_queue_m_idx">
      <col>method</col>
    </index>
    <index name="'.$table_prefix.'_emails_send_queue_s_idx">
      <col>status</col>
    </index>
  </table>
</schema>';
if(!Vtiger_Utils::CheckTable($table_prefix.'_emails_send_queue')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

require_once('include/utils/CronUtils.php');
$CU = CronUtils::getInstance();
$cj = CronJob::getByName('MessagesSend'); // to update if existing
if (empty($cj)) {
	$cj = new CronJob();
	$cj->name = 'MessagesSend';
	$cj->active = 1;
	$cj->singleRun = false;
	$cj->fileName = 'cron/modules/Messages/SendMessages.service.php';
	$cj->timeout = 300;			// 5 min timeout
	$cj->repeat = 60;			// run every 1 min
	$CU->insertCronJob($cj);
}

SDK::setLanguageEntries('Emails', 'MESSAGE_MAIL_SENT_SUCCESSFULLY', array('it_it'=>'Messaggio inviato','en_us'=>'Message sent'));
SDK::setLanguageEntries('Emails', 'LBL_PROBLEMS_MESSAGE_SENDING', array('it_it'=>'Alcuni messaggi potrebbero non essere stati inviati.','en_us'=>'Some messages could not be sent.'));

$result = $adb->query("SELECT * FROM {$table_prefix}_language WHERE prefix = 'nl_nl'");
if ($result && $adb->num_rows($result) > 0) {
	$languageInstance = new Vtiger_Language();
	$languageInstance->update($languageInstance, 'packages/vte/optional/Dutch.zip', true);
}
?>