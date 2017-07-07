<?php
require_once('vtlib/Vtiger/Module.php');
global $adb, $table_prefix;

$_SESSION['modules_to_update']['Assets'] = 'packages/vte/mandatory/Assets.zip';
$_SESSION['modules_to_update']['ChangeLog'] = 'packages/vte/mandatory/ChangeLog.zip';
$_SESSION['modules_to_update']['Charts'] = 'packages/vte/mandatory/Charts.zip';
$_SESSION['modules_to_update']['Conditionals'] = 'packages/vte/mandatory/Conditionals.zip';
$_SESSION['modules_to_update']['Ddt'] = 'packages/vte/mandatory/Ddt.zip';
$_SESSION['modules_to_update']['Fax'] = 'packages/vte/mandatory/Fax.zip';
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter','Targets'));
$_SESSION['modules_to_update']['PBXManager'] = 'packages/vte/mandatory/PBXManager.zip';
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';
$_SESSION['modules_to_update']['ProjectsStandard'] = Array('location'=>'packages/vte/mandatory/ProjectsStandard.zip','modules'=>Array('ProjectPlan', 'ProjectMilestone', 'ProjectTask'));
$_SESSION['modules_to_update']['ServiceContracts'] = 'packages/vte/mandatory/ServiceContracts.zip';
$_SESSION['modules_to_update']['Services'] = 'packages/vte/mandatory/Services.zip';
$_SESSION['modules_to_update']['Sms'] = 'packages/vte/mandatory/Sms.zip';
$_SESSION['modules_to_update']['Timecards'] = 'packages/vte/mandatory/Timecards.zip';
$_SESSION['modules_to_update']['Visitreport'] = 'packages/vte/mandatory/Visitreport.zip';

$res = $adb->query("SELECT tabid FROM {$table_prefix}_tab WHERE name = 'Projects'");
if ($res && $adb->num_rows($res)>0) $_SESSION['modules_to_update']['Projects'] = 'packages/vte/optional/Projects.zip';

$res = $adb->query("SELECT tabid FROM {$table_prefix}_tab WHERE name = 'RecycleBin'");
if ($res && $adb->num_rows($res)>0) $_SESSION['modules_to_update']['RecycleBin'] = 'packages/vte/optional/RecycleBin.zip';

@unlink('modules/Emails/Choose.php');
@unlink('modules/Emails/ChooseEmail.php');
@unlink('modules/Emails/InitSquirrelmail.php');
@unlink('modules/Emails/LinkSquirrel.php');
@unlink('modules/Emails/SquirrelmailUtils.php');
@unlink('modules/Emails/PrintEmail.php');
@unlink('modules/Emails/PrintRelatedEmail.php');
@unlink('modules/Emails/PrintRelatedEmailBottom.php');
@unlink('modules/Emails/SaveBookmarklet.php');
@unlink('modules/Users/SaveMailAccount.php');
@unlink('modules/Settings/DeleteMailAccount.php');
@unlink('Smarty/templates/AddMailAccount.tpl');
@unlink('include/js/slimScroll.js');

require_once('vtlib/Vtiger/Package.php');
$package = new Vtiger_Package();
$package->importByManifest('Messages');

// delete Webmails module
$modInst = Vtiger_Module::getInstance('Webmails');
$result = $adb->pquery("select * from tbl_s_menu_modules where tabid = ?",array($modInst->id));
if ($result && $adb->num_rows($result) > 0) {
	$mod_fast = $adb->query_result($result,0,'fast');
	$mod_seq = $adb->query_result($result,0,'sequence');
}
if (is_file('modules/Webmails/Webmails.php')) {
	$module = Vtiger_Module::getInstance('Webmails');
	$module->delete();
} else {
	$adb->pquery("DELETE FROM {$table_prefix}_tab WHERE name = ?",array('Webmails'));
}
if (is_dir('modules/Webmails')) {
	folderDetete('modules/Webmails');
}
$messagesModule = Vtiger_Module::getInstance('Messages');
$result = $adb->pquery('select * from tbl_s_menu_modules where tabid = ?',array($messagesModule->id));
if ($result && $adb->num_rows($result)) {
	$adb->pquery('update tbl_s_menu_modules set fast = ?, sequence = ? where tabid = ?',array($mod_fast,$mod_seq,$messagesModule->id));
} else {
	$adb->pquery('insert into tbl_s_menu_modules (tabid,fast,sequence) values (?,?,?)',array($messagesModule->id,$mod_fast,$mod_seq));
}
SDK::unsetPopupQuery('related', 'Webmails', 'Calendar', 'modules/SDK/src/modules/Webmails/CalendarQuery.php');
if (is_dir('modules/SDK/src/modules/Webmails')) {
	folderDetete('modules/SDK/src/modules/Webmails');
}

// backup Squirrelmail configurations and delete folder
if (is_dir('include/squirrelmail')) {
	$remove_squirrelmail_folder = true;
	
	$src = 'include/squirrelmail/config';
	$dst = 'modules/Messages/src/squirrelmail_old_conf/config';
	@mkdir($dst,0777,true);
	$dir = opendir($src);
	if ($dir !== false && is_dir($dst)) {
		while(false !== ($file = readdir($dir))) {
	        if (($file != '.' ) && ($file != '..') && is_file($src.'/'.$file)) {
	            copy($src.'/'.$file, $dst.'/'.$file); 
	        }
	    }
	    closedir($dir);
	}
	$files = scandir($dst);
	unset($files[array_search(".",$files)]);
	unset($files[array_search("..",$files)]);
	if (count($files) == 0) {
		$remove_squirrelmail_folder = false;
	}
	
	$src = 'include/squirrelmail/data';
    $dst = 'modules/Messages/src/squirrelmail_old_conf/data';
	@mkdir($dst,0777,true);
	$dir = opendir($src);
	if ($dir !== false && is_dir($dst)) {
		while(false !== ($file = readdir($dir))) {
			if (($file != '.' ) && ($file != '..') && is_file($src.'/'.$file)) {
	            copy($src.'/'.$file, $dst.'/'.$file); 
	        }
	    }
	    closedir($dir);
	}
	$files = scandir($dst);
	unset($files[array_search(".",$files)]);
	unset($files[array_search("..",$files)]);
	if (count($files) == 0) {
		$remove_squirrelmail_folder = false;
	}
	
	if ($remove_squirrelmail_folder) {
		folderDetete('include/squirrelmail');
	}
}

SDK::deleteLanguageEntry('ALERT_ARR',NULL,'LBL_PRINT_EMAIL');
SDK::deleteLanguageEntry('ALERT_ARR',NULL,'LBL_DELETE_EMAIL');
SDK::deleteLanguageEntry('ALERT_ARR',NULL,'LBL_DOWNLOAD_ATTACHMENTS');
SDK::deleteLanguageEntry('ALERT_ARR',NULL,'LBL_QUALIFY_EMAIL');
SDK::deleteLanguageEntry('ALERT_ARR',NULL,'LBL_FORWARD_EMAIL');
SDK::deleteLanguageEntry('ALERT_ARR',NULL,'LBL_REPLY_TO_SENDER');
SDK::deleteLanguageEntry('ALERT_ARR',NULL,'LBL_REPLY_TO_ALL');
SDK::deleteLanguageEntry('APP_STRINGS',NULL,'Webmails');
SDK::deleteLanguageEntry('APP_STRINGS',NULL,'SINGLE_Webmails');
SDK::setLanguageEntries('APP_STRINGS', 'SINGLE_Messages', array('it_it'=>'Messaggio','en_us'=>'Message'));
SDK::setLanguageEntries('Emails', 'LBL_FROM', array('it_it'=>'Da','en_us'=>'From'));
SDK::setLanguageEntries('Emails', 'LBL_TO', array('it_it'=>'A','en_us'=>'To'));
SDK::setLanguageEntries('Emails', 'LBL_CC', array('it_it'=>'Cc','en_us'=>'Cc'));
SDK::setLanguageEntries('Emails', 'LBL_BCC', array('it_it'=>'Ccn','en_us'=>'Bcc'));
SDK::setLanguageEntries('Emails', 'LBL_ADD_BCC', array('it_it'=>'Aggiungi Ccn','en_us'=>'Add Bcc'));
SDK::setLanguageEntries('Emails', 'LBL_SINGLE_HELPINFO', array('it_it'=>'Invia una mail visualizzando tutti i destinatari per A, CC e CCN.','en_us'=>'Send an email showing all recipients To, CC and BCC.'));
SDK::setLanguageEntries('Emails', 'LBL_MULTIPLE_HELPINFO', array('it_it'=>'Invia una mail distinta per ogni destinatario in A, includendo sempre CC e CCN.','en_us'=>'Send a separate email for each recipient in To, always including CC and BCC.'));

@unlink('soap/vtigerolservice.php');
@unlink('soap/thunderbirdplugin.php');
@unlink('soap/webforms.php.deprecated');

$result = $adb->pquery("SELECT account, server, ssl_tls FROM {$table_prefix}_systems WHERE server_type = ?",array('email_imap'));
if ($result && $adb->num_rows($result) > 0) {
	$account = $adb->query_result($result,0,'account');
	$ssl_tls = $adb->query_result($result,0,'ssl_tls');
	$server = $adb->query_result($result,0,'server');
	if ($ssl_tls == 'true') {
		switch ($account) {
			case 'Gmail':
			case 'Yahoo!':
				$adb->pquery("UPDATE {$table_prefix}_systems SET ssl_tls = ? WHERE server_type = ?",array('ssl','email_imap'));
				break;
			case 'Exchange':
				$adb->pquery("UPDATE {$table_prefix}_systems SET ssl_tls = ? WHERE server_type = ?",array('tls','email_imap'));
				break;
			default:
				$adb->pquery("UPDATE {$table_prefix}_systems SET server = ?, ssl_tls = ? WHERE server_type = ?",array('tls://'.$server,'','email_imap'));
				break;
		}
	}
}
?>