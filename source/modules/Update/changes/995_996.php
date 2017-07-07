<?php
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter', 'Targets'));
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';

global $adb, $table_prefix;

$idxs_messages = array_keys($adb->database->MetaIndexes($table_prefix.'_messages'));
$idxs_modcomments_users = array_keys($adb->database->MetaIndexes($table_prefix.'_modcomments_users'));
$idxs_modcomments = array_keys($adb->database->MetaIndexes($table_prefix.'_modcomments'));
$idxs_crmentity = array_keys($adb->database->MetaIndexes($table_prefix.'_crmentity'));

if (in_array("{$table_prefix}_messages_acc_fold_mtype_idx", $idxs_messages)) $adb->datadict->ExecuteSQLArray((Array)$adb->datadict->DropIndexSQL("{$table_prefix}_messages_acc_fold_mtype_idx", "{$table_prefix}_messages"));
if (in_array('NewIndex1', $idxs_modcomments)) $adb->datadict->ExecuteSQLArray((Array)$adb->datadict->DropIndexSQL('NewIndex1', "{$table_prefix}_modcomments"));
if (in_array('NewIndex2', $idxs_modcomments)) $adb->datadict->ExecuteSQLArray((Array)$adb->datadict->DropIndexSQL('NewIndex2', "{$table_prefix}_modcomments"));
if (in_array('NewIndex3', $idxs_modcomments)) $adb->datadict->ExecuteSQLArray((Array)$adb->datadict->DropIndexSQL('NewIndex3', "{$table_prefix}_modcomments"));
if (in_array('modifiedtime_idx', $idxs_crmentity)) $adb->datadict->ExecuteSQLArray((Array)$adb->datadict->DropIndexSQL('modifiedtime_idx', "{$table_prefix}_crmentity"));

$indexes = array(
	array("{$table_prefix}_messages", "{$table_prefix}_messages_checkflagchanges_idx", 'mdate, mtype, account, folder, xuid, seen, answered, flagged, forwarded'),
	array("{$table_prefix}_messages", "{$table_prefix}_messages_adoptchildren", 'folder, mreferences(200)'),
	array("{$table_prefix}_messages", "{$table_prefix}_messages_referencechildren_idx", 'mdate, folder, mreferences(200)'),
	array("{$table_prefix}_messages", "{$table_prefix}_messages_flaggedcount_idx", 'mtype, account, flagged'),
);
foreach($indexes as $index) {
	if (!in_array($index[1], $idxs_messages)) {
		$adb->datadict->ExecuteSQLArray((Array)$adb->datadict->CreateIndexSQL($index[1], $index[0], $index[2]));
	}
}

$indexes = array(
	array("{$table_prefix}_modcomments_users", 'user_id_idx', 'user, id'),
);
foreach($indexes as $index) {
	if (!in_array($index[1], $idxs_modcomments_users)) {
		$adb->datadict->ExecuteSQLArray((Array)$adb->datadict->CreateIndexSQL($index[1], $index[0], $index[2]));
	}
}

$indexes = array(
	array("{$table_prefix}_modcomments", 'commentcontent_idx', 'commentcontent(255)'),
	array("{$table_prefix}_modcomments", 'parent_comments_idx', 'parent_comments'),
	array("{$table_prefix}_modcomments", 'related_to_visibility_idx', 'related_to, visibility_comm'),
);
foreach($indexes as $index) {
	if (!in_array($index[1], $idxs_modcomments)) {
		$adb->datadict->ExecuteSQLArray((Array)$adb->datadict->CreateIndexSQL($index[1], $index[0], $index[2]));
	}
}

SDK::setLanguageEntries('Emails', 'LBL_MESSAGE_TOO_BIG', array(
	'de_de'=>'die Nachricht Anlagen enthält, die den durch den sendenden Server festgelegte Grenze überschreiten, verringern die Größe der Anlagen',
	'nl_nl'=>'het bericht bevat bijlagen die de door de verzendende server limiet overschrijdt, probeer het verminderen van de grootte van de bijlagen',
	'pt_br'=>'a mensagem contém anexos que excedem o limite imposto pelo servidor de envio, tente reduzir o tamanho dos anexos',
	'en_us'=>'the message contains attachments that exceed the limit imposed by the sending server, try reducing the size of attachments',
	'it_it'=>'il messaggio contiene allegati che eccedono il limite di invio imposto dal server, provare a ridurre la dimensione degli allegati',
));

$docModuleInstance = Vtiger_Module::getInstance('Documents');
$docModuleInstance->setRelatedList(Vtiger_Module::getInstance('Assets'),'Assets',array('select','add'),'get_documents_dependents_list');
?>