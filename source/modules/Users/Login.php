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
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Users/Login.php,v 1.6 2005/01/08 13:15:03 jack Exp $
 * Description: TODO:  To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

/* crmv@24269 crmv@91082 (ajax login) */

global $default_language, $current_language;
global $app_strings;

$login_ajax = ($_REQUEST['login_view'] == 'ajax');

//crmv@56023
$focus = CRMEntity::getInstance('Users');
if ($focus->checkBannedLogin() && !$login_ajax) {	// crmv@91082
	header('HTTP/1.0 403 Forbidden');
	include('modules/Users/403error.html');
	exit;
}
//crmv@56023

$theme_path="themes/$theme/";
$theme_path_login="themes/$theme/images/login/";

//we don't want the parent module's string file, but rather the string file specifc to this subpanel
$current_module_strings = return_module_language($current_language, 'Users');

define("IN_LOGIN", true);

include_once('vtlib/Vtiger/Language.php');

//crmv@16312
// Retrieve username and password from the session if possible.
if(isset($_SESSION["login_user_name"]))
{
	if (isset($_REQUEST['default_user_name'])) {
		$login_user_name = trim(vtlib_purify($_REQUEST['default_user_name']), '"\'');
	} else {
		$login_user_name =  trim(vtlib_purify($_REQUEST['login_user_name']), '"\'');
	}
} else {
	if (isset($_REQUEST['default_user_name'])) {
		$login_user_name = trim(vtlib_purify($_REQUEST['default_user_name']), '"\'');
	} elseif (isset($_REQUEST['ck_login_id_vtiger'])) {
		$login_user_name = getUserName($_REQUEST['ck_login_id_vtiger']);
	} else {
		$login_user_name = $default_user_name;
	}
	$_session['login_user_name'] = $login_user_name;
}
$current_module_strings['VLD_ERROR'] = base64_decode('UGxlYXNlIHJlcGxhY2UgdGhlIFN1Z2FyQ1JNIGxvZ29zLg==');

// Retrieve username and password from the session if possible.
if(isset($_SESSION["login_password"])) {
	$login_password = trim(vtlib_purify($_REQUEST['login_password']), '"\'');
} else {
	$login_password = $default_password;
	$_session['login_password'] = $login_password;
}
//crmv@16312 end
if(isset($_SESSION["login_error"])) {
	$login_error = $_SESSION['login_error'];
}

eval(Users::m_de_cryption());

require_once('Smarty_setup.php');
$smarty = new vtigerCRM_Smarty;

$smarty->assign('THEME', $theme);
$smarty->assign('MOD', $current_module_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign('LOGIN_AJAX', $login_ajax);
$smarty->assign('USERNAME', $login_user_name);
$smarty->assign('PASSWORD', $login_password);
$smarty->assign('SAVELOGIN', $savelogin);

if ($_REQUEST['logout_reason_code']) {
	$str = '';
	if ($_REQUEST['logout_reason_code'] == 'concurrent') {
		$str = getTranslatedString('LBL_LOGOUT_REASON_CONCURRENT', 'Users');
	} elseif ($_REQUEST['logout_reason_code'] == 'expired') {
		$str = getTranslatedString('LBL_LOGOUT_REASON_EXPIRED', 'Users');
	}
	$smarty->assign('LOGOUT_REASON', $str);
}

// define this function (SDK::setUtil) to override the logo with anything
if (function_exists('get_logo_override')) {
	$logoImg = get_logo_override('project');
} else {
	global $enterprise_project; 
	if (!empty($enterprise_project)) $logoImg = '<img src="'.get_logo('project').'" border="0">';
}
$smarty->assign('LOGOIMG', $logoImg);

$error_str = '&nbsp;';
if (isset($_SESSION['validation'])) {
	$error_str = $current_module_strings['VLD_ERROR'];
} elseif (isset($login_error) && $login_error != "") {
	$error_str = $login_error;
}
$smarty->assign('ERROR_STR', $error_str);

$smarty->display('Login.tpl');
