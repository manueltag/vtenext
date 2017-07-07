<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Users/Authenticate.php,v 1.10 2005/02/28 05:25:22 jack Exp $
 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('modules/Users/Users.php');
require_once('modules/Users/CreateUserPrivilegeFile.php');
require_once('include/logging.php');
require_once('user_privileges/audit_trail.php');

global $mod_strings, $default_charset;
global $login_error;	//crmv@28327
global $table_prefix;
$focus = CRMEntity::getInstance('Users');

$login_ajax = ($_REQUEST['login_view'] == 'ajax'); //crmv@91082

// Add in defensive code here.
$focus->column_fields["user_name"] = to_html($_REQUEST['user_name']);
$user_password = $_REQUEST['user_password'];	//crmv@38918

//crmv@29377
$cookielogin = false;
if (!empty($_COOKIE['savelogindata'])) $cookielogin = true;
$loadResult = $focus->load_user($user_password, $cookielogin);
//crmv@29377e

if($focus->is_authenticated())
{
	//Inserting entries for audit trail during login
	if($audit_trail == 'true')
	{
		if($record == '')
			$auditrecord = '';
		else
			$auditrecord = $record;

		$date_var = $adb->formatDate(date('Y-m-d H:i:s'), true);
 	    $query = "insert into ".$table_prefix."_audit_trial values(?,?,?,?,?,?)";
		$params = array($adb->getUniqueID($table_prefix.'_audit_trial'), $focus->id, 'Users','Authenticate','',$date_var);
		$adb->pquery($query, $params);
	}

	// crmv@91082
	// Recording the login info
	require_once('modules/Users/LoginHistory.php');
	$loghistory = LoginHistory::getInstance();
	$Signin = $loghistory->user_login($focus->column_fields["user_name"]);
	// crmv@91082e

	//Security related entries start
	require_once('include/utils/UserInfoUtil.php');
	createUserPrivilegesfile($focus->id);
	createUserPrivilegesfile($focus->id, 1); // crmv@39110

	//Security related entries end
	unset($_SESSION['login_password']);
	unset($_SESSION['login_error']);
	unset($_SESSION['login_user_name']);

	$_SESSION['authenticated_user_id'] = $focus->id;
	$_SESSION['app_unique_key'] = $application_unique_key;
	$_SESSION['vte_root_directory'] = $root_directory;

	// store the user's theme in the session
	// crmv@26809
	$focus->column_fields['default_theme'] = getSingleFieldValue($table_prefix.'_users', 'default_theme', 'id', $focus->id);
	if (!empty($focus->column_fields['default_theme'])) {
		$authenticated_user_theme = $focus->column_fields['default_theme'];
	} else {
		$authenticated_user_theme = $default_theme;
	}

	// store the user's language in the session
	$focus->column_fields['default_language'] = getSingleFieldValue($table_prefix.'_users', 'default_language', 'id', $focus->id);
	if (!empty($focus->column_fields['default_language'])) {
		$authenticated_user_language = $focus->column_fields['default_language'];
	} else {
		$authenticated_user_language = $default_language;
	}
	// crmv@26809-end

	// If this is the default user and the default user theme is set to reset, reset it to the default theme value on each login
	if($reset_theme_on_default_user && $focus->user_name == $default_user_name)
	{
		$authenticated_user_theme = $default_theme;
	}
	if(isset($reset_language_on_default_user) && $reset_language_on_default_user && $focus->user_name == $default_user_name)
	{
		$authenticated_user_language = $default_language;
	}

	$_SESSION['vtiger_authenticated_user_theme'] = $authenticated_user_theme;
	$_SESSION['authenticated_user_language'] = $authenticated_user_language;

	$log->debug("authenticated_user_theme is $authenticated_user_theme");
	$log->debug("authenticated_user_language is $authenticated_user_language");
	$log->debug("authenticated_user_id is ". $focus->id);
	$log->debug("app_unique_key is $application_unique_key");

	//Clear all uploaded import files for this user if it exists

	global $import_dir;

	$tmp_file_name = $import_dir. "IMPORT_".$focus->id;

	if (file_exists($tmp_file_name)) {
		unlink($tmp_file_name);
	}

	//crmv@91082 crmv@101201
	if ($login_ajax) {
		$SV = SessionValidator::getInstance();
		$userChanged = $SV->userChanged($focus);
		if (!$userChanged) {
			$SV->restoreSessionVars($focus->id);
		} else {
			$SV->clearSessionVars($focus->id);
		}
		$SV->refresh();
		$output = array('success' => true, 'user_changed' => $userChanged);
		$SV->ajaxOutput($output);
	}
	//crmv@91082e crmv@101201e

	$arr = $_SESSION['lastpage'];
	if(isset($_SESSION['lastpage']))
		header("Location: index.php?".$arr[0]);
	else
		header("Location: index.php");
}
else
{
	$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off'); // crmv@80972
	setcookie('savelogindata', false, 0, $cookieurl, "", $isHttps, true); //crmv@29377 (cookie url is set in index.php)

	// crmv@43592
	if ($loadResult == 'EXPIRED_RECENTLY') {
		// generate a token for the change password (1 hour only)
		$key = getUserAuthtokenKey('password_recovery',$focus->id,3600);
		header('Location: modules/Users/Recover.php?action=change_old_pwd&key='.$key);
		exit;
	}
	// crmv@43592e

	$_SESSION['login_user_name'] = $focus->column_fields["user_name"];
	$_SESSION['login_password'] = $user_password;
	//crmv@28327
	if ($login_error != '') {
		$_SESSION['login_error'] = $login_error;
	} else {
		$_SESSION['login_error'] = $mod_strings['ERR_INVALID_PASSWORD'];
	}
	//crmv@28327e

	//crmv@91082
	if ($login_ajax) {
		$SV = SessionValidator::getInstance();
		$output = array('success' => false, 'error' => $_SESSION['login_error']);
		$SV->ajaxOutput($output);
	}
	//crmv@91082e

	// go back to the login screen.
	// create an error message for the user.
	header("Location: index.php");
}
