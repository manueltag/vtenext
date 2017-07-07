<?php

$_SESSION['modules_to_update']['Services'] = 'packages/vte/mandatory/Services.zip';
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';

global $adb, $table_prefix;

/* crmv@91980 */

// add an index
$check = false;
$indexes = $adb->database->MetaIndexes("{$table_prefix}_messages_account");
foreach($indexes as $name => $index) {
	if (count($index['columns']) == 1 && $index['columns'][0] == 'userid') {
		$check = true;
		break;
	}
}
if (!$check) {
	$sql = $adb->datadict->CreateIndexSQL('vte_mess_acc_user_idx', "{$table_prefix}_messages_account", 'userid');
	if ($sql) $adb->datadict->ExecuteSQLArray($sql);
}


// create the phone numbers table
$schema = 
	'<?xml version="1.0"?>
	<schema version="0.3">
		<table name="'.$table_prefix.'_messages_ntel">
			<opt platform="mysql">ENGINE=InnoDB</opt>
			<field name="messagesid" type="I" size="19" />
			<field name="phone" type="C" size="63" />
			<field name="type" type="C" size="15" />
			<index name="messages_ntel_idx">
				<col>messagesid</col>
			</index>
		</table>
	</schema>';
if (!Vtiger_Utils::CheckTable($table_prefix.'_messages_ntel')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema));
}

// parse old messages (messages of last month, capped to max 1000)
$messFocus = CRMEntity::getInstance('Messages');
$users = array();
$res = $adb->query("select distinct userid from {$table_prefix}_messages_account");
while ($row = $adb->fetchByAssoc($res, -1, false)) {
	$users[] = $row['userid'];
}
foreach ($users as $userid) {
	$res = $adb->limitpQuery("
		select messagesid, description from {$table_prefix}_messages 
		inner join {$table_prefix}_crmentity on crmid = {$table_prefix}_messages.messagesid
		where deleted = 0 and smownerid = ? and mdate > ?
		order by mdate desc", 0, 1000, array($userid, date('Y-m-d H:i:s', time()-3600*24*30)));
	while ($row = $adb->fetchByAssoc($res, -1, false)) {
		$mid = $row['messagesid'];
		$numbers = $messFocus->extractPhoneNumbers($row['description']);
		if (count($numbers) > 0) {
			$messFocus->deletePhoneNumbers($mid);
			$messFocus->savePhoneNumbers($mid, $numbers);
		}
	}
}


// empty the description for the messages
$adb->query("UPDATE {$table_prefix}_crmentity SET description = NULL WHERE setype = 'Messages'");


// fix pdfmaker table
if (Vtiger_Utils::CheckTable($table_prefix.'_pdfmaker')) {
	Vtiger_Utils::AlterTable($table_prefix.'_pdfmaker','body XL');
}