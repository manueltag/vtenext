<?php
include('config.inc.php');
global $adb, $table_prefix;

// insert into fieldmodule rel SalesrOrder->accountsid
$res = $adb->pquery("select fieldid from {$table_prefix}_field where fieldname = ? and uitype = ? and tabid = ? ", array('account_id', 73, getTabid('SalesOrder')));
if ($res && $adb->num_rows($res) > 0) {
	$fieldid = $adb->query_result($res, 0, 'fieldid');
	// delete it
	$adb->pquery("delete from {$table_prefix}_fieldmodulerel where fieldid = ? and module = ? and relmodule = ?", array($fieldid, 'SalesOrder', 'Accounts'));
	// insert
	$adb->pquery("insert into {$table_prefix}_fieldmodulerel (fieldid, module, relmodule) values (?,?,?)", array($fieldid, 'SalesOrder', 'Accounts'));
}

SDK::setLanguageEntry('Import', 'it_it', 'LBL_UNDO_LAST_IMPORT', 'Annulla l\'importazione');

$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
?>