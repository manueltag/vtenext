<?php
global $adb, $table_prefix;

SDK::setLanguageEntry('Newsletter', 'en_us', 'WhichRecipientsToAdd', 'Which recipients do you want to add?');

SDK::setLanguageEntry('Settings', 'en_us', 'LBL_PROFILE_TO_BE_USED_FOR_MOBILE', 'to be used exclusively as a profile for Mobile App');
SDK::setLanguageEntry('Settings', 'it_it', 'LBL_PROFILE_TO_BE_USED_FOR_MOBILE', 'per essere utilizzato esclusivamente come profilo per l\'App Mobile');

$result = $adb->query("SELECT * FROM {$table_prefix}_language WHERE prefix = 'de_de'");
if ($result && $adb->num_rows($result) > 0) {
	$languageInstance = new Vtiger_Language();
	$languageInstance->update($languageInstance, 'packages/vte/optional/Deutsch.zip', true);
}
$result = $adb->query("SELECT * FROM {$table_prefix}_language WHERE prefix = 'nl_nl'");
if ($result && $adb->num_rows($result) > 0) {
	$languageInstance = new Vtiger_Language();
	$languageInstance->update($languageInstance, 'packages/vte/optional/Dutch.zip', true);
}
$result = $adb->query("SELECT * FROM {$table_prefix}_language WHERE prefix = 'pt_br'");
if ($result && $adb->num_rows($result) > 0) {
	$languageInstance = new Vtiger_Language();
	$languageInstance->update($languageInstance, 'packages/vte/optional/PTBrasil.zip', true);
}

require_once('include/utils/CronUtils.php');
$CU = CronUtils::getInstance();

$cj = new CronJob();
$cj->name = 'MessagesAllUids';
$cj->active = 1;
$cj->singleRun = false;
$cj->fileName = 'cron/modules/Messages/AllUids.service.php';
$cj->timeout = 5400;
$cj->repeat = 600;
$CU->insertCronJob($cj);

SDK::setLanguageEntries('Messages', 'LBL_OPTIONAL', array(
	'it_it'=>'Opzionale',
	'en_us'=>'Optional',
	'de_de'=>'Fakultativ',
	'nl_nl'=>'Facultatief',
	'pt_br'=>'Opcional',
));
SDK::setLanguageEntries('Messages', 'LBL_FORCE_SYNC', array(
	'it_it'=>'Forza sincronizzazione',
	'en_us'=>'Force synchronization',
	'de_de'=>'Force-Synchronisation',
	'nl_nl'=>'Force synchronisatie',
	'pt_br'=>'Sincronização da força',
));

$schema_table =
'<schema version="0.3">
  <table name="'.$table_prefix.'_messages_sync_all">
	<opt platform="mysql">ENGINE=InnoDB</opt>
    <field name="userid" type="I" size="19">
      <KEY/>
    </field>
    <field name="accountid" type="I" size="19">
      <KEY/>
    </field>
    <field name="inbox" type="I" size="1">
      <DEFAULT value="0"/>
    </field>
    <field name="other" type="I" size="1">
      <DEFAULT value="0"/>
    </field>
  </table>
</schema>';
if(!Vtiger_Utils::CheckTable($table_prefix.'_messages_sync_all')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

$result = $adb->query("SELECT id, userid FROM {$table_prefix}_messages_account");
if ($result && $adb->num_rows($result) > 0) {
	$focus = CRMEntity::getInstance('Messages');
	while($row=$adb->fetchByASsoc($result)) {
		$focus->syncAll($row['userid'], $row['id']);
	}
}

//crmv@51894 : delete converted leads in email directory
$adb->pquery("DELETE FROM {$table_prefix}_email_directory WHERE module = ? AND crmid IN (SELECT leadid FROM {$table_prefix}_leaddetails WHERE converted = 1)",array('Leads'));
?>