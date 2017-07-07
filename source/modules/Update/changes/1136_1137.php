<?php
global $adb, $table_prefix;

$fields = array();
$fields[] = array('module'=>'Messages','block'=>'LBL_FLAGS','name'=>'draft','label'=>'Draft','uitype'=>'56','typeofdata'=>'C~O','quickcreate'=>3,'columntype'=>'INT(1) DEFAULT 0');
include('modules/SDK/examples/fieldCreate.php');

SDK::setLanguageEntries('Messages', 'Draft', array('it_it'=>'Bozza','en_us'=>'Draft','de_de'=>'Entwurf','nl_nl'=>'Ontwerp','pt_br'=>'Rascunho'));

$result = $adb->pquery("SELECT a.id, sf.folder
	FROM {$table_prefix}_messages_account a
	INNER JOIN {$table_prefix}_messages_sfolders sf ON a.id = sf.accountid AND sf.special = ?", array('Drafts'));
if ($result && $adb->num_rows($result) > 0) {
	while($row=$adb->fetchByASsoc($result)) {
		$adb->pquery("update {$table_prefix}_messages set draft = ? where account = ? and folder = ?", array(1,$row['id'],$row['folder']));
	}
}

// set draft flag for duplicate records
if ($adb->isMssql()) {
	$result = $adb->query("
		UPDATE {$table_prefix}_messages
		SET draft = 1
		FROM {$table_prefix}_messages
		INNER JOIN (
			SELECT mo.messagesid
			FROM {$table_prefix}_messages md
			INNER JOIN {$table_prefix}_crmentity ed ON md.messagesid = ed.crmid
			INNER JOIN {$table_prefix}_messages mo ON mo.messagehash = md.messagehash AND mo.messagesid <> md.messagesid
			INNER JOIN {$table_prefix}_crmentity eo ON mo.messagesid = eo.crmid
			WHERE md.draft = 1 AND mo.draft <> 1 AND ed.deleted = 0 AND eo.deleted = 0
		) tmp ON {$table_prefix}_messages.messagesid = tmp.messagesid");
} else {
	$result = $adb->query("
		UPDATE {$table_prefix}_messages
		INNER JOIN (
			SELECT mo.messagesid
			FROM {$table_prefix}_messages md
			INNER JOIN {$table_prefix}_crmentity ed ON md.messagesid = ed.crmid
			INNER JOIN {$table_prefix}_messages mo ON mo.messagehash = md.messagehash AND mo.messagesid <> md.messagesid
			INNER JOIN {$table_prefix}_crmentity eo ON mo.messagesid = eo.crmid
			WHERE md.draft = 1 AND mo.draft <> 1 AND ed.deleted = 0 AND eo.deleted = 0
		) tmp ON {$table_prefix}_messages.messagesid = tmp.messagesid
		SET draft = 1");
}
?>