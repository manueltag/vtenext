<?php

$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter', 'Targets'));

if (isModuleInstalled('RecycleBin')) {
	$_SESSION['modules_to_update']['RecycleBin'] = 'packages/vte/optional/RecycleBin.zip';
}

global $adb, $table_prefix;

// crmv@86123 - fix messages with empty dates
// commented, if you need to fix the history, launch it manually
/*
$focus = CRMEntity::getInstance('Messages');
$focus->update_duplicates = true;

$result = $adb->query(
	"SELECT {$table_prefix}_messages_account.userid, {$table_prefix}_messages.account, {$table_prefix}_messages.folder, {$table_prefix}_messages.xuid
	FROM {$table_prefix}_messages
	INNER JOIN {$table_prefix}_messages_account ON {$table_prefix}_messages.account = {$table_prefix}_messages_account.id
	WHERE mdate = '0000-00-00 00:00:00'
	ORDER BY xuid"
);
if ($result && $adb->num_rows($result) > 0) {
	while($row=$adb->fetchByAssoc($result)) {
		$focus->checkMessage($row['userid'],$row['account'],$row['folder'],$row['xuid'],true);  //userid, accountid, folder, uid, true to overwrite the message
	}
}
*/