<?php

// fix wrong message cache (only 250 messages per user)
$messFocus = CRMEntity::getInstance('Messages');
$users = array();
$res = $adb->query("select id from {$table_prefix}_users");
while ($row = $adb->fetchByAssoc($res, -1, false)) {
	$users[] = $row['id'];
}
foreach ($users as $userid) {
	$res = $adb->limitpQuery("
		select messagesid, xuid, description from {$table_prefix}_messages 
		inner join {$table_prefix}_crmentity on crmid = {$table_prefix}_messages.messagesid
		where deleted = 0 and smownerid = ?
		order by modifiedtime desc", 0, 250, array($userid));
	while ($row = $adb->fetchByAssoc($res, -1, false)) {
		$messFocus->id = $row['messagesid'];
		// save the cleaned body
		$attachments_info = $messFocus->getAttachmentsInfo();
		$message_data = array('other'=>$attachments_info);
		$row['description'] = str_replace('&amp;', '&', $row['description']);
		$magicHTML = $messFocus->magicHTML($row['description'], $row['xuid'], $message_data);
		$messFocus->saveCleanedBody($messFocus->id, $magicHTML['html'], $magicHTML['content_ids']);
	}
}


// translations
$trans = array(
	'APP_STRINGS' => array(
		'it_it' => array(
			'LBL_AUTOMATIC' => 'Automatico',
		),
		'en_us' => array(
			'LBL_AUTOMATIC' => 'Automatic',
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


?>