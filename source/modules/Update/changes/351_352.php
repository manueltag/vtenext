<?php
global $adb;
$_SESSION['modules_to_update']['Projects'] = 'packages/vte/mandatory/Projects.zip';
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';
$res = $adb->query("SELECT tabid FROM vtiger_tab WHERE name = 'RecycleBin'");
if ($res && $adb->num_rows($res)>0)
	$_SESSION['modules_to_update']['RecycleBin'] = 'packages/vte/optional/RecycleBin.zip';
$_SESSION['modules_to_update']['Sms'] = 'packages/vte/mandatory/Sms.zip';

$adb->query("UPDATE vtiger_taskstatus SET history='1' WHERE taskstatus IN ('Completed','Deferred')");
$adb->query("UPDATE vtiger_eventstatus SET history='1' WHERE eventstatus IN ('Held','Tenuto')");
$adb->query("UPDATE vtiger_field SET readonly='100' WHERE fieldname='webmail_structure'");

@unlink('themes/images/Leads.gif');

$modulo = Vtiger_Module::getInstance('Users');
$block = new Vtiger_Block();
$block->label = 'LBL_CALENDAR_CONFIGURATION';
$modulo->addBlock($block);
$adb->query("UPDATE vtiger_blocks SET blocklabel='LBL_WEBMAIL_CONFIGURATION' WHERE blocklabel='Configurazione Webmail' AND tabid=29");
$adb->query("UPDATE vtiger_blocks SET sequence=5 WHERE blocklabel='LBL_CALENDAR_CONFIGURATION' AND tabid=29");
$adb->query("UPDATE vtiger_blocks SET sequence=6 WHERE blocklabel='LBL_WEBMAIL_CONFIGURATION' AND tabid=29");
$adb->query("UPDATE vtiger_blocks SET sequence=7 WHERE blocklabel='Asterisk Configuration' AND tabid=29");
$adb->query("UPDATE vtiger_blocks SET sequence=8 WHERE blocklabel='LBL_USER_ADV_OPTIONS' AND tabid=29");
$adb->query("UPDATE vtiger_field SET block = $block->id WHERE fieldname = 'activity_view' AND tabid = 29");
$adb->query("UPDATE vtiger_field SET sequence = 1 WHERE fieldname = 'activity_view' AND tabid = 29");
$adb->query("UPDATE vtiger_field SET block = $block->id WHERE fieldname = 'date_format' AND tabid = 29");
$adb->query("UPDATE vtiger_field SET sequence = 2 WHERE fieldname = 'date_format' AND tabid = 29");
$adb->query("UPDATE vtiger_field SET block = $block->id WHERE fieldname = 'reminder_interval' AND tabid = 29");
$adb->query("UPDATE vtiger_field SET sequence = 3 WHERE fieldname = 'reminder_interval' AND tabid = 29");
$adb->query("UPDATE vtiger_field SET block = $block->id WHERE fieldname = 'hour_format' AND tabid = 29");
$adb->query("UPDATE vtiger_field SET sequence = 4 WHERE fieldname = 'hour_format' AND tabid = 29");
$adb->query("UPDATE vtiger_field SET block = $block->id WHERE fieldname = 'start_hour' AND tabid = 29");
$adb->query("UPDATE vtiger_field SET sequence = 5 WHERE fieldname = 'start_hour' AND tabid = 29");
$adb->query("UPDATE vtiger_field SET block = $block->id WHERE fieldname = 'end_hour' AND tabid = 29");
$adb->query("UPDATE vtiger_field SET sequence = 6 WHERE fieldname = 'end_hour' AND tabid = 29");
$adb->query("UPDATE vtiger_field SET typeofdata = 'T~O' WHERE fieldname = 'start_hour' AND tabid = 29");
$adb->query("UPDATE vtiger_field SET displaytype = 1 WHERE fieldname = 'start_hour' AND tabid = 29");
$field = new Vtiger_Field();
$field->name = 'no_week_sunday';
$field->label= 'Disable Sunday on Week';
$field->table = $modulo->basetable;
$field->uitype = 56;
$field->typeofdata = 'C~O';
$field->columntype = 'I(1)';
$block->addField($field);
$adb->query('UPDATE vtiger_users SET no_week_sunday = 0');

$flds = "	
	userid I(19) NOTNULL PRIMARY,
	shownid C(255) NOTNULL PRIMARY,
	selected I(1) DEFAULT 0
";
$sqlarray = $adb->datadict->CreateTableSQL('tbl_s_showncalendar', $flds);
$adb->datadict->ExecuteSQLArray($sqlarray);
require_once('modules/Calendar/CalendarCommon.php');
$res = $adb->query('SELECT id FROM vtiger_users');
while($row=$adb->fetchByAssoc($res)) {
	$shownusers = array_keys((array)getShownUserList($row['id']));
	if (!empty($shownusers)) {
		foreach($shownusers as $id) {
			$adb->pquery('insert into tbl_s_showncalendar (userid,shownid,selected) values (?,?,1)',array($row['id'],$id));
		}
	}
	$adb->pquery("insert into tbl_s_showncalendar (userid,shownid,selected) values (?,?,1)", array($row['id'],'mine'));
	$adb->pquery("insert into tbl_s_showncalendar (userid,shownid,selected) values (?,?,0)", array($row['id'],'all'));
	$adb->pquery("insert into tbl_s_showncalendar (userid,shownid,selected) values (?,?,0)", array($row['id'],'others'));
}
@unlink('modules/Calendar/updateCalendarSharing.php');

$sqlarray = $adb->datadict->CreateTableSQL('tbl_s_cal_color', "color C(255) NOTNULL PRIMARY");
$adb->datadict->ExecuteSQLArray($sqlarray);
$colors = array('d1c2f0d1c2f0','6f96e46f96e4','c2d1e1c2d1e1','ace96face96f','84c64284c642','fab066fab066','f8d4aff8d4af','e4984de4984d','ecec5cecec5c','e1e123e1e123','cccccccccccc','e0e0e0e0e0e0','fc7777fc7777','f8a3a3f8a3a3','ffdedeffdede','f0c2c2f0c2c2','f8739ff8739f','e6bc13e6bc13','cca2cccca2cc','fa9efafa9efa','b2f0f7b2f0f7','b399e6b399e6','99cccc99cccc','c0e3e3c0e3e3','6e65e76e65e7','fe4b4bfe4b4b','e8e0ebe8e0eb','f0e8c4f0e8c4','d0a400d0a400','fcdc64fcdc64','4cd5da4cd5da','8bf7a78bf7a7','79acdf79acdf','99ccff99ccff','d56bfed56bfe','c0c01dc0c01d','e17272e17272','31c99e31c99e','d3d36dd3d36d','6ce1826ce182','a3c91ea3c91e','8a9fba8a9fba','93930e93930e','fceaa3fceaa3','fff000fff000','ffe4feffe4fe','e29394e29394','dbc48ddbc48d','ff9933ff9933','d9832fd9832f','d6e1f0d6e1f0','fcfc82fcfc82');
foreach ($colors as $color) {
	$adb->pquery('insert into tbl_s_cal_color values (?)',array($color));
}
$res = $adb->query('SELECT id FROM vtiger_users');
while($row=$adb->fetchByAssoc($res)) {
	$adb->pquery('update vtiger_users set cal_color=? where id=?',array(calculateCalColor(),$row['id']));
}
require_once("modules/Update/Update.php");
Update::change_field('vtiger_users','cal_color','C','12');

//ldap fix - i
$sqlarray = $adb->datadict->DropTableSQL('tbl_s_ldap_config');
$adb->datadict->ExecuteSQLArray($sqlarray);
$flds = "	
	ldap_active I(1),
	ldap_host C(50),
	ldap_port I(5),
	ldap_basedn C(100),
	ldap_username C(50),
	ldap_pass C(50),
	ldap_objclass C(50),
	ldap_account C(60),
	ldap_fullname C(50),
	ldap_userfilter C(100),
	user_role C(10)
";
$sqlarray = $adb->datadict->CreateTableSQL('tbl_s_ldap_config', $flds);
$adb->datadict->ExecuteSQLArray($sqlarray);
//ldap fix - e
?>