<?php
/*
delete
- TABLES:	_mail_accounts, _webmail_structure, _webmail_structure_seq
- BLOCK:	LBL_WEBMAIL_CONFIGURATION
- FIELD:	webmail_username, webmail_password, webmail_structure
*/
$module = Vtiger_Module::getInstance('Users');
$block = Vtiger_Block::getInstance('LBL_WEBMAIL_CONFIGURATION', $module);
$block->delete(true);

global $adb, $table_prefix;
if (Vtiger_Utils::CheckTable("{$table_prefix}_mail_accounts")) $adb->query("drop table {$table_prefix}_mail_accounts");
if (Vtiger_Utils::CheckTable("{$table_prefix}_webmail_structure")) $adb->query("drop table {$table_prefix}_webmail_structure");
if (Vtiger_Utils::CheckTable("{$table_prefix}_webmail_structure_seq")) $adb->query("drop table {$table_prefix}_webmail_structure_seq");
?>