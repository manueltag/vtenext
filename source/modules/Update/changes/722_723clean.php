<?php
include_once('../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user;

die('Remove this line prior to execution. Restore it afterwards.');

set_time_limit(0);
echo "<br><b>Deleting obsolete tables and records...</b><br>\n"; flush();


// ----- NEWSLETTER -----

// rimozione tabella _click
if (Vtiger_Utils::CheckTable('tbl_s_newsletter_tl_click')) {
	$adb->query('drop table tbl_s_newsletter_tl_click');
}


// ----- OLD WEBMAIL -----

// delete entries from tables
$adb->query("delete from {$table_prefix}_crmentity where setype = 'Emails'");
$adb->query("delete from {$table_prefix}_activity where activitytype = 'Emails'");

// delete tables
if (Vtiger_Utils::CheckTable("{$table_prefix}_emaildetails")) $adb->query("drop table {$table_prefix}_emaildetails");
if (Vtiger_Utils::CheckTable("{$table_prefix}_email_access")) $adb->query("drop table {$table_prefix}_email_access");
if (Vtiger_Utils::CheckTable("{$table_prefix}_email_track")) $adb->query("drop table {$table_prefix}_email_track");

if (Vtiger_Utils::CheckTable("vte_mailcache_folders")) $adb->query("drop table vte_mailcache_folders");
if (Vtiger_Utils::CheckTable("vte_mailcache_list")) $adb->query("drop table vte_mailcache_list");
if (Vtiger_Utils::CheckTable("vte_mailcache_messages")) $adb->query("drop table vte_mailcache_messages");

if (Vtiger_Utils::CheckTable("crmv_squirrelmailrel")) $adb->query("drop table crmv_squirrelmailrel");


echo "<br><b>Done.</b><br>\n";
?>