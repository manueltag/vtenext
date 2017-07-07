<?php 
//crmv@26807
echo header('Pragma: public');
echo header('Expires: 0');
echo header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
echo header('Cache-Control: private', false);
include_once('../../config.inc.php');
chdir($root_directory);
require_once('Smarty_setup.php');
require_once('include/utils/utils.php');

global $application_unique_key, $root_directory;
if ($_REQUEST['app_key'] != $application_unique_key) {
	exit;
}
global $adb,$site_URL,$current_language,$table_prefix;

$smarty = new vtigerCRM_Smarty;
$smarty->assign('PATH','../../');

$from = $_REQUEST['from'];
if ($from == 'invite_con') {
	$user_id = 1;
	//crmv@28584
	$result = $adb->pquery('SELECT id FROM '.$table_prefix.'_users WHERE user_name = ? AND status = ?',array('admin','Active'));
	if ($result && $adb->num_rows($result) > 0) {
		$user_id = $adb->query_result($result,0,'id');
	}
	//crmv@28584e
}
else {
	$user_id = $_REQUEST['userid'];
}
$current_user = CRMEntity::getInstance('Users');
$current_user->retrieveCurrentUserInfoFromFile($user_id);
$current_language = $current_user->column_fields['default_language'];
if ($current_language == '') {
	$current_language = 'en_us';
}

if (isModuleInstalled('SDK')) {
	$app_strings = return_application_language($current_language);
	$mod_strings = return_module_language($current_language, 'Calendar');
}

$link = $site_URL.'/index.php?module=Calendar&action=DetailView&record='.$_REQUEST['record'].'&openTab=cellTabInvite';
$_REQUEST['activityid'] = $_REQUEST['record'];

include_once('modules/Calendar/SavePartecipation.php');

require_once("modules/Emails/mail.php");

$focus_event = CRMEntity::getInstance('Calendar');
$focus_event->id = $_REQUEST['record'];
$focus_event->retrieve_entity_info($focus_event->id,'Events');
$invites = getTranslatedString('INVITATION','Calendar');
if ($_REQUEST['partecipation'] == 2) {
	$answer = getTranslatedString('LBL_YES','Calendar');
}
elseif ($_REQUEST['partecipation'] == 1) {
	$answer = getTranslatedString('LBL_NO','Calendar');
}
$subject = $invites.': '.$focus_event->column_fields['subject'];
$description .= getEmailInvitationDescription($focus_event->column_fields,$_REQUEST['userid'],$_REQUEST['record'],$answer,$from);

$smarty->assign('CURRENT_LANGUAGE',$current_language);
$smarty->assign('BODY',$description);
$smarty->display('NoLoginMsg.tpl');
//crmv@26807e
?>