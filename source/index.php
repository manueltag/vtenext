<?php
//ob_start("ob_gzhandler");
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
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/index.php,v 1.93 2005/04/21 16:17:25 ray Exp $
 * Description: Main file and starting point for the application.  Calls the
 * theme header and footer files defined for the user as well as the module as
 * defined by the input parameters.
 ********************************************************************************/

global $entityDel;
global $display;
global $category;
global $site_URL; //crmv@27520

if (version_compare(phpversion(), '5.3') < 0) {
	insert_charset_header();
	require_once('phpversionfail.php');
	die();
}
if (file_exists('modules/Update/free_changes/441.php')) {
	header('location: modules/Update/free_changes/441.php');
}

// crmv@91979
require_once('include/MaintenanceMode.php');
if (MaintenanceMode::check()) {
	MaintenanceMode::display();
	die();
}
// crmv@91979e

require_once('include/utils/utils.php');

global $currentModule;

 /** Function to  return a string with backslashes stripped off
   * @param $value -- value:: Type string
   * @returns $value -- value:: Type string array
 */

function stripslashes_checkstrings($value){
	if(is_string($value)){
		return stripslashes($value);
	}
	return $value;
}
if(get_magic_quotes_gpc() == 1){
	$_REQUEST = array_map("stripslashes_checkstrings", $_REQUEST);
	$_POST = array_map("stripslashes_checkstrings", $_POST);
	$_GET = array_map("stripslashes_checkstrings", $_GET);
}

// Allow for the session information to be passed via the URL for printing.
if(isset($_REQUEST['PHPSESSID']))
{
	session_id($_REQUEST['PHPSESSID']);
	//Setting the same session id to Forums as in CRM
	$sid=$_REQUEST['PHPSESSID'];
}

insert_charset_header();
// Create or reestablish the current session
//crmv@27520 crmv@29377 crmv@80972
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off');
$cookieurl = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
if (empty($cookieurl)) $cookieurl = '/';
session_set_cookie_params(0, $cookieurl, null, $isHttps, true); // crmv@80972
session_start();

$sl = $_COOKIE['savelogin'];
if (!empty($sl)) {
	list($sl, $sltime) = explode(':', $sl, 2);
	if (empty($sltime)) $sltime = time();
}
$savelogin = intval($sl == 1 || in_array($_REQUEST['savelogin'], array(1, 'on')));
$login_expire_time = 3600*24*30; // one month
if ($savelogin) {
	if ($sl == 1) { // use previous cookie
		$sltime = intval($sltime);
	} else { // set new cookie
		$sltime = time();
	}
	setcookie('savelogin', "1:".$sltime, $sltime+$login_expire_time, $cookieurl, "", $isHttps, true);
	unset($_REQUEST['savelogin']);
} else {
	setcookie('savelogin', 0, time()+$login_expire_time, $cookieurl, "", $isHttps, true);
}
//crmv@27520e crmv@29377e crmv@80972e

if (!is_file('config.inc.php')) {
	header("Location: install.php");
	exit();
}

require_once('config.inc.php');
if (!isset($dbconfig['db_hostname']) || $dbconfig['db_status']=='_DB_STAT_') {
		header("Location: install.php");
		exit();
}

// load up the config_override.php file.  This is used to provide default user settings
if (is_file('config_override.php'))
{
	require_once('config_override.php');
}

/**
 * Check for vtiger installed version and codebase
 */
include('vteversion.php');
global $adb, $vtiger_current_version, $table_prefix;
if (empty($table_prefix)) {
	$table_prefix = 'vtiger';
}
if(isset($_SESSION['VTIGER_DB_VERSION']) && isset($_SESSION['authenticated_user_id'])) {
    if(version_compare($_SESSION['VTIGER_DB_VERSION'], $vtiger_current_version, '!=')) {
        unset($_SESSION['VTIGER_DB_VERSION']);
        header("Location: install.php");
        exit();
    }
} else {
    $result = $adb->query("SELECT * FROM ".$table_prefix."_version");
    $dbversion = $adb->query_result($result, 0, 'current_version');
    $cache = Cache::getInstance('vteCacheHV');
    $tmp = $cache->get();
    if ($tmp === false) $cache->set(Users::m_encryption(Users::de_cryption($adb->query_result_no_html($result, 0, 'hash_version'))));
    $_SESSION['enterprise_project'] = $adb->query_result_no_html($result, 0, 'enterprise_project');
    $_SESSION['vtiger_hash_version_check_check'] = true;
    if(version_compare($dbversion, $vtiger_current_version, '=')) {
    	$_SESSION['VTIGER_DB_VERSION']= $dbversion;
    } else {
    	header("Location: install.php");
        exit();
    }
}
include('vteversion.php');
// END

SDK::getUtils();	//crmv@sdk-18503

//crmv@23715
if (isset($_REQUEST['menubar']) && $_REQUEST['menubar'] != '') {
	$_SESSION['menubar'] = $_REQUEST['menubar'];
}
//crmv@23715e

$default_config_values = Array( "allow_exports"=>"all","upload_maxsize"=>"3000000", "listview_max_textlength" => "40", "php_max_execution_time" => "0");

set_default_config($default_config_values);

// Set the default timezone preferred by user
global $default_timezone;
if(isset($default_timezone) && function_exists('date_default_timezone_set')) {
	@date_default_timezone_set($default_timezone);
}

require_once('include/logging.php');
require_once('modules/Users/Users.php');

if($calculate_response_time) $startTime = microtime();

$log =& LoggerManager::getLogger('index');

global $seclog;
$seclog =& LoggerManager::getLogger('SECURITY');

if (isset($_REQUEST['PHPSESSID'])) $log->debug("****Starting for session ".$_REQUEST['PHPSESSID']);
else $log->debug("****Starting for new session");

// We use the REQUEST_URI later to construct dynamic URLs.  IIS does not pass this field
// to prevent an error, if it is not set, we will assign it to ''
if(!isset($_SERVER['REQUEST_URI']))
{
	$_SERVER['REQUEST_URI'] = '';
}

$action = '';
if(isset($_REQUEST['action']))
{
	$action = $_REQUEST['action'];
}
if($action == 'Export')
{
	include ('include/utils/export.php');
}
if($action == 'ExportAjax')
{
	include ('include/utils/ExportAjax.php');
}
// crmv@37463 - removed code
// END

// crmv@43147
if (!empty($_REQUEST['sharetoken'])) {
	$module = $_REQUEST['module'] = 'Utilities';
	$action = $_REQUEST['action'] = 'ShareRecord';
	require("modules/$module/$action.php");
	return;
}
// crmv@43147e

//Code added for 'Path Traversal/File Disclosure' security fix - Philip
$is_module = false;
$is_action = false;
$in_core = false;	//crmv@40799
if(isset($_REQUEST['module']))
{
	$module = $_REQUEST['module'];
	$dir = @scandir($root_directory.'modules');
	$temp_arr = Array("CVS","Attic");
	$res_arr = @array_intersect($dir,$temp_arr);
	if(count($res_arr) == 0  && !preg_match("/[\/.]/",$module)) {
		if(@in_array($module,$dir))
			$is_module = true;
	}
	$in_dir = @scandir($root_directory.'modules/'.$module);
	$res_arr = @array_intersect($in_dir,$temp_arr);
	if(count($res_arr) == 0 && !preg_match("/[\/.]/",$module)) {
		if(@in_array($action.".php",$in_dir))
			$is_action = true;
	}
	//crmv@40799
	if(!$is_action) {
		$in_dir = @scandir($root_directory.'modules/VteCore');
		$res_arr = @array_intersect($in_dir,$temp_arr);
		if(count($res_arr) == 0 && !preg_match("/[\/.]/",'VteCore')) {
			if(@in_array($action.".php",$in_dir)) {
				$is_action = true;
				$in_core = true;
			}
		}
	}
	//crmv@40799e
	if(!$is_module)
	{
		die("Module name is missing. Please check the module name.");
	}
	if(!$is_action)
	{
		die("Action name is missing. Please check the action name.");
	}
}

//Code added for 'Multiple SQL Injection Vulnerabilities & XSS issue' fixes - Philip
if(isset($_REQUEST['record']) && !is_numeric($_REQUEST['record']) && $_REQUEST['record']!='')
{
	die("An invalid record number specified to view details.");
}

// Check to see if there is an authenticated user in the session.
//crmv@29377
$use_current_login = false;
if(isset($_SESSION["authenticated_user_id"]) && (isset($_SESSION["app_unique_key"]) && $_SESSION["app_unique_key"] == $application_unique_key) && ($_SESSION["vte_root_directory"] == $root_directory)) {
	$use_current_login = true;
} elseif (!empty($_COOKIE['savelogindata']) && !preg_match("/^".$module."Ajax/",$action)) { // skip relogin if ajax
	$_SESSION['lastpage'] = array($_SERVER['QUERY_STRING']);
	require('modules/Users/Authenticate.php');
	die();
}
//crmv@29377e

// Prevent loading Login again if there is an authenticated user in the session.
if (isset($_SESSION["authenticated_user_id"]) && $module == 'Users' && $action == 'Login') {
    header("Location: index.php?action=$default_action&module=$default_module");
}

if($use_current_login){
	/*&Added to prevent fatal error before starting migration(5.0.4. patch ).
	//Start
	$arr=$adb->getColumnNames("vtiger_users");
	if(!in_array("internal_mailer", $arr))
	{
		$adb->pquery("alter table vtiger_users add column internal_mailer int(3) NOT NULL default '1'", array());
		$adb->pquery("alter table vtiger_users add column tagcloud_view int(1) default 1", array());
	}
	//End*/

	//getting the internal_mailer flag
	if(!isset($_SESSION['internal_mailer'])){
		$qry_res = $adb->pquery("select internal_mailer from ".$table_prefix."_users where id=?", array($_SESSION["authenticated_user_id"]));
		$_SESSION['internal_mailer'] = $adb->query_result($qry_res,0,"internal_mailer");
	}
	$log->debug("We have an authenticated user id: ".$_SESSION["authenticated_user_id"]);
}else if(isset($action) && isset($module) && $action=="Authenticate" && $module=="Users"){
	$log->debug("We are authenticating user now");
	setcookie('crmvWinMaxStatus','');	//crmv@22622
}else{
	if($_REQUEST['action'] != 'Logout' && $_REQUEST['action'] != 'Login'){
		$_SESSION['lastpage'] = $_SERVER['argv'];
		//crmv@26948
		if (empty($_SERVER['argv'])) {
			$_SESSION['lastpage'] = array($_SERVER['QUERY_STRING']);
		}
		//crmv@26948e
	}
//crmv@offline
	if ($offline_mode){
		$module = 'Offline';
		$skipSecurityCheck = true;
		if ($dbconfig['db_name'] == 'offline'){
			if (!$action || $action == 'Login')
				$action="index_gooffline";
		}
		elseif(($action == 'OfflineAjax') /*&& vtlib_purify($_REQUEST['file']) == 'check_server')*/ || $action == 'index_goonline') {
		}
		else{
			$action = "Login";
		}
	}
	else{
		//crmv@29399 crmv@91082
		// invalid session here
		$SV = SessionValidator::getInstance();
		if(in_array($_REQUEST['file'], $SV->timer_files)){
			echo '<script type="text/javascript">SessionValidator.check({showLogin:true});</script>'; // crmv@106590
			exit();
		} elseif (preg_match("/^".$module."Ajax/",$action)) {
			if ($_REQUEST['file'] == 'CheckSession') {
				$_SESSION['lastpage'] = '';
				require('modules/Utilities/CheckSession.php');
			} else {
				echo '<script type="text/javascript">window.location.reload();</script>';
				exit();
			}
		}
		//crmv@29399e crmv@91082e
		$action = "Login";
		$module = "Users";
		$in_core = false;
	}
}
//crmv@offline end

$log->debug($_REQUEST);
$skipHeaders=false;
(isset($_REQUEST['skip_footer']) && $_REQUEST['skip_footer'] != '') ?  $skipFooters=$_REQUEST['skip_footer'] : $skipFooters=false;	//crmv@62447
$viewAttachment = false;

if(isset($action) && isset($module))
{
	$log->info("About to take action ".$action);
	$log->debug("in $action");
	if(preg_match("/^Save/", $action) ||
		preg_match("/^Delete/", $action) ||
		preg_match("/^Choose/", $action) ||
		preg_match("/^Popup/", $action) ||
		preg_match("/^ChangePassword/", $action) ||
		preg_match("/^Authenticate/", $action) ||
		preg_match("/^Logout/", $action) ||
		preg_match("/^add2db/", $action) ||
		preg_match("/^result/", $action) ||
		preg_match("/^LeadConvertToEntities/", $action) ||
		preg_match("/^downloadfile/", $action) ||
		preg_match("/^massdelete/", $action) ||
		preg_match("/^updateLeadDBStatus/",$action) ||
		preg_match("/^AddCustomFieldToDB/", $action) ||
		preg_match("/^updateRole/",$action) ||
		preg_match("/^UserInfoUtil/",$action) ||
		preg_match("/^deleteRole/",$action) ||
		preg_match("/^UpdateComboValues/",$action) ||
		preg_match("/^fieldtypes/",$action) ||
		preg_match("/^app_ins/",$action) ||
		preg_match("/^minical/",$action) ||
		preg_match("/^minitimer/",$action) ||
		preg_match("/^app_del/",$action) ||
		preg_match("/^send_mail/",$action) ||
		preg_match("/^populatetemplate/",$action) ||
		preg_match("/^TemplateMerge/",$action) ||
		preg_match("/^testemailtemplateusage/",$action) ||
		preg_match("/^saveemailtemplate/",$action) ||
		preg_match("/^ProcessDuplicates/", $action ) ||
		preg_match("/^lastImport/", $action ) ||
		preg_match("/^lookupemailtemplate/",$action) ||
		preg_match("/^deletewordtemplate/",$action) ||
		preg_match("/^deleteemailtemplate/",$action) ||
		preg_match("/^CurrencyDelete/",$action) ||
		preg_match("/^deleteattachments/",$action) ||
		preg_match("/^MassDeleteUsers/",$action) ||
		preg_match("/^UpdateFieldLevelAccess/",$action) ||
		preg_match("/^UpdateDefaultFieldLevelAccess/",$action) ||
		preg_match("/^UpdateProfile/",$action)  ||
		preg_match("/^updateRelations/",$action) ||
		preg_match("/^updateNotificationSchedulers/",$action) ||
		preg_match("/^Star/",$action) ||
		preg_match("/^addPbProductRelToDB/",$action) ||
		preg_match("/^UpdateListPrice/",$action) ||
		preg_match("/^PriceListPopup/",$action) ||
		preg_match("/^SalesOrderPopup/",$action) ||
		preg_match("/^CreatePDF/",$action) ||
		preg_match("/^CreateSOPDF/",$action) ||
		preg_match("/^redirect/",$action) ||
		preg_match("/^webmail/",$action) ||
		preg_match("/^left_main/",$action) ||
		preg_match("/^delete_message/",$action) ||
		preg_match("/^mime/",$action) ||
		preg_match("/^move_messages/",$action) ||
		preg_match("/^folders_create/",$action) ||
		preg_match("/^imap_general/",$action) ||
		preg_match("/^mime/",$action) ||
		preg_match("/^download/",$action) ||
		preg_match("/^about_us/",$action) ||
		preg_match("/^SendMailAction/",$action) ||
		preg_match("/^CreateXL/",$action) ||
		preg_match("/^savetermsandconditions/",$action) ||
		preg_match("/^home_rss/",$action) ||
		preg_match("/^ConvertAsFAQ/",$action) ||
		preg_match("/^Tickerdetail/",$action) ||
		preg_match("/^".$module."Ajax/",$action) ||
		preg_match("/^ActivityAjax/",$action) ||
		preg_match("/^chat/",$action) ||
		preg_match("/^vtchat/",$action) ||
		preg_match("/^updateCalendarSharing/",$action) ||
		preg_match("/^disable_sharing/",$action) ||
		preg_match("/^HeadLines/",$action) ||
		preg_match("/^TodoSave/",$action) ||
		preg_match("/^RecalculateSharingRules/",$action) ||
		preg_match("/^savewordtemplate/",$action) ||
		preg_match("/^mailmergedownloadfile/",$action) ||
		preg_match("/^download/",$action) ||
		preg_match("/^getListOfRecords/", $action) ||
		preg_match("/^AddBlockFieldToDB/", $action) ||
		preg_match("/^AddBlockToDB/", $action)  ||
		preg_match("/^MassEditSave/", $action) ||
		preg_match("/^iCalExport/",$action) ||
		//crmv@project
		preg_match("/^PrintProject/",$action) ||
		preg_match("/^CreatePWXL/",$action) ||
		//crmv@project end
		($module == 'MyNotes' && in_array($action,array('SimpleView','DetailView'))) ||	//crmv@3083m
		preg_match("/^ModuleManagerExport/",$action) // crmv@37463
		)
	{
		$skipHeaders=true;
		//skip headers for all these invocations as they are mostly popups
		if(preg_match("/^Popup/", $action) ||
			preg_match("/^ChangePassword/", $action) ||
			//preg_match("/^Export/", $action) ||
			preg_match("/^downloadfile/", $action) ||
			preg_match("/^fieldtypes/",$action) ||
			preg_match("/^lookupemailtemplate/",$action) ||
			preg_match("/^about_us/",$action) ||
			preg_match("/^home_rss/",$action) ||
			preg_match("/^".$module."Ajax/",$action) ||
			preg_match("/^chat/",$action) ||
			preg_match("/^vtchat/",$action) ||
			preg_match("/^massdelete/", $action) ||
			preg_match("/^mailmergedownloadfile/",$action) || 	preg_match("/^get_img/",$action) ||
			preg_match("/^download/",$action) ||
			preg_match("/^ProcessDuplicates/", $action ) ||
			preg_match("/^lastImport/", $action ) ||
			preg_match("/^massdelete/", $action ) ||
			preg_match("/^getListOfRecords/", $action) ||
			preg_match("/^MassEditSave/", $action) ||
			preg_match("/^iCalExport/",$action) ||
			preg_match("/^CreatePWXL/",$action) ||
			($module == 'MyNotes' && in_array($action,array('SimpleView','DetailView'))) ||	//crmv@3083m
			//crmv@project
			preg_match("/^PrintProject/",$action)
			//crmv@project end
			)
			$skipFooters=true;
		//skip footers for all these invocations as they are mostly popups
		if(preg_match("/^downloadfile/", $action)
		|| preg_match("/^fieldtypes/",$action)
		|| preg_match("/^mailmergedownloadfile/",$action)
		|| preg_match("/^get_img/",$action)
		|| preg_match("/^MergeFieldLeads/", $action)
		|| preg_match("/^MergeFieldContacts/", $action )
		|| preg_match("/^MergeFieldAccounts/", $action )
		|| preg_match("/^MergeFieldProducts/", $action )
		|| preg_match("/^MergeFieldHelpDesk/", $action )
		|| preg_match("/^MergeFieldPotentials/", $action )
		|| preg_match("/^MergeFieldVendors/", $action )
		|| preg_match("/^dlAttachments/", $action )
		|| preg_match("/^iCalExport/", $action)
		|| preg_match("/^CreatePWXL/",$action)
		)
		{
			$viewAttachment = true;
		}
		if(($action == ' Delete ') && (!$entityDel))
		{
			$skipHeaders=false;
		}
	}

	// crmv@65455
	if ($_REQUEST['skip_vte_header'] == 'true') {
		$skipHeaders = true;
	}
	// crmv@65455e
	
	// crmv@120738
	$fastMode = intval($_REQUEST['fastmode']);
	if ($fastMode) {
		$skipFooters = true;
	}
	// crmv@120738e

	if($action == 'Save')
	{
		header( "Expires: Mon, 20 Dec 1998 01:00:00 GMT" );
		header( "Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
		header( "Cache-Control: no-cache, must-revalidate" );
		header( "Pragma: no-cache" );
 	}
	//crmv@16312+16267
    if(($module == 'Users' || $module == 'Home' || $module == 'uploads' || ($module == 'Settings' && $action == 'UpdatePDFSettings')) && $_REQUEST['parenttab'] != 'Settings'){
    	$skipSecurityCheck=true;
    }
	//crmv@16312+16267 end
	//crmv@22613
	if($module == 'Users' && $action == 'Login') {
		$skipSecurityCheck=true;
	}
	//crmv@22613e
    //crmv@sdk-25183	//crmv@25671
    $sdk_action = '';
    if (isModuleInstalled('SDK')) {
    	$sdk_action = SDK::getFile($module,$action);
    }
	$call_sdk = true;
	if ($sdk_action == '') {
		$sdk_action = $action;
		$call_sdk = false;
	}
    if ($in_core && !$call_sdk) {
		$currentModuleFile = 'modules/VteCore/'.$sdk_action.'.php';
    } else {
    	$currentModuleFile = 'modules/'.$module.'/'.$sdk_action.'.php';
    }
	//crmv@sdk-25183e	//crmv@25671e
	$currentModule = $module;
}
elseif(isset($module))
{
	$currentModule = $module;
	$currentModuleFile = $moduleDefaultFile[$currentModule];
}
else {
	// use $default_module and $default_action as set in config.php
	// Redirect to the correct module with the correct action.  We need the URI to include these fields.
	//crmv@26523
	$current_user = CRMEntity::getInstance('Users');
	if($use_current_login) {
		$current_user->retrieveCurrentUserInfoFromFile($_SESSION['authenticated_user_id']);
		if ($current_user->column_fields['default_module'] != '') {
			$default_module = $current_user->column_fields['default_module'];
		}
	}
	//crmv@26523e
	if ($in_core) {
		header("Location: index.php?module=VteCore&action=$default_action");
	} else {
		header("Location: index.php?module=$default_module&action=$default_action");
	}
    exit();
}

$log->info("current page is $currentModuleFile");
$log->info("current module is $currentModule ");

// for printing
$module = (isset($_REQUEST['module'])) ? vtlib_purify($_REQUEST['module']) : "";
$action = (isset($_REQUEST['action'])) ? vtlib_purify($_REQUEST['action']) : "";
$record = (isset($_REQUEST['record'])) ? vtlib_purify($_REQUEST['record']) : "";
$lang_crm = (isset($_SESSION['authenticated_user_language'])) ? $_SESSION['authenticated_user_language'] : "";
$GLOBALS['request_string'] = "&module=$module&action=$action&record=$record&lang_crm=$lang_crm";

$current_user = CRMEntity::getInstance('Users');

if($use_current_login) 
{
	//getting the current user info from flat file
	$result = $current_user->retrieveCurrentUserInfoFromFile($_SESSION['authenticated_user_id']);

	// crmv@91082
	$SV = SessionValidator::getInstance();

	if ($currentModule != 'Update' && $SV->isStarted()) {
		if (!$SV->isValid()) {
			$result = null;
			$user_expelled = true;
			$current_user->deleteCookieSaveLogin();
			$SV->saveSessionVars($current_user->id); // crmv@101201
		}
	}
	if(isset($_REQUEST['file']) && in_array($_REQUEST['file'], $SV->skip_files)){
		// do nothing
	}elseif ($currentModule == 'Update' && $action == 'DoUpdate') {
		// no check during update, the table might still be old
	} else {
		$SV->refresh();
		$SV->saveUser();
	}
	
	if ($result == null) {
	
		// Recording Logout Info
		$loghistory = LoginHistory::getInstance();
		$loghistory->user_logout($current_user->user_name, null, null, 'auto', $user_expelled);
	
		session_regenerate_id(true);
		session_unset();
		session_destroy();
		
		// if i am here, i'm not logged in and i come from CheckSession so i return NO_OK for the ajax request
		if($_REQUEST['file'] == 'CheckSession'){
			$output = array('success' => true, 'valid' => false, 'updated' => false);
			$SV->ajaxOutput($output);
		}

		//crmv@104205
		if ($action == $module.'Ajax') {
			echo '<script type="text/javascript">SessionValidator.showLogin()</script>';
		} else {		
	    	echo '<script type="text/javascript">window.location.reload();</script>';
		}
		exit();
		//crmv@104205e
	}
	// crmv@91082e

	//crmv@29377
	if ($savelogin && $current_user) {
		$cookieval = $current_user->getCookieForSavelogin();
		setcookie('savelogindata', $cookieval, time()+$login_expire_time, $cookieurl, '', $isHttps, true); // crmv@80972
	}
	//crmv@29377e

	$moduleList = getPermittedModuleNames();

	foreach ($moduleList as $mod) {
		if ($in_core) {
			$moduleDefaultFile[$mod] = 'modules/VteCore/index.php';
		} else {
			$moduleDefaultFile[$mod] = "modules/".$currentModule."/index.php";
		}
	}

	//auditing
	require_once('user_privileges/audit_trail.php');
	if($audit_trail == 'true') {
		if($record == '') {
			$auditrecord = '';
		} else {
			$auditrecord = $record;
		}
		/* Skip audit trial log for special request types */
		$skip_auditing = false;
		if($action == 'chat') {
			$skip_auditing = true;
		} else if(($action == 'ActivityReminderCallbackAjax' || $_REQUEST['file'] == 'ActivityReminderCallbackAjax') && $module == 'Calendar') {
			$skip_auditing = true;
		} else if(($action == 'TraceIncomingCall' || $_REQUEST['file'] == 'TraceIncomingCall') && $module == 'PBXManager') {
			$skip_auditing = true;
		} else if($action == 'SDKAjax' && $module == 'SDK') {
			$skip_auditing = true;
		}
		/* END */
		if (!$skip_auditing) {
			$date_var = $adb->formatDate(date('Y-m-d H:i:s'), true);
			$query = "insert into ".$table_prefix."_audit_trial values(?,?,?,?,?,?)";
			if($action == $module.'Ajax') {
				$audit_action = $_REQUEST['file'];
			} else {
				$audit_action = $action;
			}
			$qparams = array($adb->getUniqueID($table_prefix.'_audit_trial'), $current_user->id, $module, $audit_action, $auditrecord, $date_var);
			$adb->pquery($query, $qparams);
		}
	}
	eval(Users::m_de_cryption());
	eval($hash_version[0]);
	//crmv@sdk	crmv@47905bis
	if (PerformancePrefs::get('CACHE_TYPE') == 'session' && Vtiger_Utils::CheckTable($table_prefix.'_reload_session')) { // crmv@115378
		$result = $adb->pquery("select session_var from {$table_prefix}_reload_session where userid = ?",array($current_user->id));
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByAssoc($result)) {
				$cache = Cache::getInstance($row['session_var']);
				$cache->clear(false);
			}
		}
	}
	//crmv@sdke	crmv@47905bise
	$log->debug('Current user is: '.$current_user->user_name);
} else {
	eval(Users::m_de_cryption());
	eval($hash_version[1]);
}

if(isset($_SESSION['vtiger_authenticated_user_theme']) && $_SESSION['vtiger_authenticated_user_theme'] != '')
{
	$theme = $_SESSION['vtiger_authenticated_user_theme'];
}
else
{
	$theme = $default_theme;
}
$log->debug('Current theme is: '.$theme);

//Used for current record focus
$focus = "";

// if the language is not set yet, then set it to the default language.
if(isset($_SESSION['authenticated_user_language']) && $_SESSION['authenticated_user_language'] != '')
{
	$current_language = $_SESSION['authenticated_user_language'];
}
else
{
	$current_language = $default_language;
}
$log->debug('current_language is: '.$current_language);

//set module and application string arrays based upon selected language
if (isModuleInstalled('SDK')) {	//crmv@sdk
	$app_currency_strings = return_app_currency_strings_language($current_language);
	$app_strings = return_application_language($current_language);
	$app_list_strings = return_app_list_strings_language($current_language);
	$mod_strings = return_module_language($current_language, $currentModule);
//crmv@25671
} else {
	// try to retrieve languages from language files
	@include("include/language/$current_language.lang.php");
	@include("modules/$currentModule/language/$current_language.lang.php");
}
//crmv@25671e

if ($use_current_login && vtlib_isModuleActive('Morphsuit') && $_SESSION['vtiger_hash_version_check_check'] && !$_SESSION['vtiger_hash_version_check']) {
	die('Hash version not valid. Contact info@crmvillage.biz');
}

//If DetailView, set focus to record passed in
if($action == "DetailView" || $action == "EditView")
{
	if($action == "DetailView" && !isset($_REQUEST['record'])) {
		die("A record number must be specified to view details.");
	}

	if(isset($_REQUEST['record']) && $_REQUEST['record']!='' && $current_user->id != '')
	{
		// Only track a viewing if the record was retrieved.
		$focus = CRMEntity::getInstance($currentModule);
		$focus->track_view($current_user->id, $currentModule, $_REQUEST['record']);
	}
}

if($_REQUEST['module'] == 'Documents' && $action == 'DownloadFile')
{
	include('modules/Documents/DownloadFile.php');
	exit;
}

//skip headers for popups, deleting, saving, importing and other actions
if(!$skipHeaders) {
	$log->debug("including headers");
	if($use_current_login)
	{
		if(isset($_REQUEST['category']) && $_REQUEST['category'] !='')
		{
			$category = vtlib_purify($_REQUEST['category']);
		}
		else
		{
			$category = getParentTabFromModule($currentModule);
		}
		$sdk_header_action = '';
		if (isModuleInstalled('SDK')) $sdk_header_action = SDK::getFile('VteCore','header');
		if (empty($sdk_header_action)) $sdk_header_action = 'header';
		include("modules/VteCore/$sdk_header_action.php");	//crmv@30447
	}
	else
		include('themes/LoginHeader.php');

	if(isset($_SESSION['administrator_error']))
	{
		// only print DB errors once otherwise they will still look broken after they are fixed.
		// Only print the errors for admin users.
		if(is_admin($current_user))
			echo $_SESSION['administrator_error'];
		unset($_SESSION['administrator_error']);
	}

	echo "<!-- startscrmprint -->";
}
else {
		$log->debug("skipping headers");
}



//fetch the permission set from session and search it for the requisite data

if(isset($_SESSION['vtiger_authenticated_user_theme']) && $_SESSION['vtiger_authenticated_user_theme'] != '')
{
	$theme = $_SESSION['vtiger_authenticated_user_theme'];
}
else
{
	$theme = $default_theme;
}


//logging the security Information
$seclog->debug('########  Module -->  '.$module.'  :: Action --> '.$action.' ::  UserID --> '.$current_user->id.' :: RecordID --> '.$record.' #######');

if (!$skipSecurityCheck) {
	require_once('include/utils/UserInfoUtil.php');
	if(preg_match('/Ajax/',$action)) {
		if($_REQUEST['ajxaction'] == 'LOADRELATEDLIST'){
			$now_action = 'DetailView';
		} else {
			$now_action=str_replace('..', '', vtlib_purify($_REQUEST['file'])); // crmv@37463
		}
	} else {
		$now_action=$action;
	}

	//cmrv@17889 crmv@106441
	$permModule = $module;
	if($now_action == 'EditPDFTemplate') {
    	$now_action = 'EditView';
	} elseif($now_action == 'DetailViewPDFTemplate') {
	    $now_action = 'DetailView';
	} elseif($now_action == 'HistoryTab' && $_REQUEST['pmodule']) {
		$now_action = 'DetailView';
		$permModule = $_REQUEST['pmodule'];
	} elseif($now_action == 'SavePDFTemplate') {
	    $now_action = 'Save';
	} elseif($now_action == 'DeletePDFTemplate') {
	    $now_action = 'Delete';
	}
	//cmrv@17889e
	
    if (isset($_REQUEST['record']) && $_REQUEST['record'] != '') {
    	$display = isPermitted($permModule,$now_action,$_REQUEST['record']);
    } else {
    	$display = isPermitted($permModule,$now_action);
    }
    // crmv@106441e

	$seclog->debug('########### Pemitted ---> '.$display.'  ##############');
} else {
	$seclog->debug('########### Pemitted ---> yes  ##############');
}

//crmv@18857
if( (($action == 'ActivityReminderCallbackAjax' || $_REQUEST['file'] == 'ActivityReminderCallbackAjax') && $module == 'Calendar')
	|| (($action == 'TraceIncomingCall' || $_REQUEST['file'] == 'TraceIncomingCall') && $module == 'PBXManager')
	)
{
	if (($display == "no") || !vtlib_isModuleActive($currentModule))
		die('');
}
//crmv@18857e

if($display == "no")
{
	//crmv@28661
	if ($action == $module.'Ajax') {
		die($app_strings['LBL_PERMISSION']);
	}
	//crmv@28661e
	echo "<link rel='stylesheet' type='text/css' href='themes/$theme/style.css'>";
	echo "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
	echo "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 55%; position: relative; z-index: 10000000;'>

		<table border='0' cellpadding='5' cellspacing='0' width='98%'>
		<tbody><tr>
		<td rowspan='2' width='11%'><img src='". vtiger_imageurl('denied.gif', $theme) . "' ></td>
		<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'><span class='genHeaderSmall'>$app_strings[LBL_PERMISSION]</span></td>
		</tr>
		<tr>
		<td class='small' align='right' nowrap='nowrap'>
		<a href='javascript:window.history.back();'>$app_strings[LBL_GO_BACK]</a><br>								   						     </td>
		</tr>
		</tbody></table>
		</div>";
	echo "</td></tr></table>";
}
// vtlib customization: Check if module has been de-activated
else if(!vtlib_isModuleActive($currentModule)) {
	die(getTranslatedString($currentModule,$currentModule).' '.$app_strings['VTLIB_MOD_NOT_ACTIVE']); //crmv@28661
}
// END
else
{
	include($currentModuleFile);
}

if((!$viewAttachment) && (!$viewAttachment && $action != 'home_rss' && $action != $module."Ajax" && $action != "chat" && $action != 'massdelete' && $action != "body") )
{
	echo "<!-- stopscrmprint -->";
}

//added to get the theme . This is a bad fix as we need to know where the problem lies yet
if(isset($_SESSION['vtiger_authenticated_user_theme']) && $_SESSION['vtiger_authenticated_user_theme'] != '')
{
        $theme = $_SESSION['vtiger_authenticated_user_theme'];
}
else
{
        $theme = $default_theme;
}
$Ajx_module= $module;
if($module == 'Events')
	$Ajx_module = 'Calendar';
if((!$viewAttachment) && (!$viewAttachment && $action != 'home_rss') && $action != 'Tickerdetail' && $action != $Ajx_module."Ajax" && $action != "chat" && $action != "HeadLines" && $action != 'massdelete'  &&  $action != "DashboardAjax" && $action != "ActivityAjax")
{
	// Under the SPL you do not have the right to remove this copyright statement.
	$copyrightstatement="<style>
		.bggray
		{
			background-color: #dfdfdf;
		}
	.bgwhite
	{
		background-color: #FFFFFF;
	}
	.copy
	{
		font-size:9px;
		font-family: Verdana, Arial, Helvetica, Sans-serif;
	}
	</style>
		<script language=javascript>
		function LogOut(e)
		{
			var nav4 = window.Event ? true : false;
			var iX,iY;
			if (nav4)
			{
				iX = e.pageX;
				iY = e.pageY;
			}
			else
			{
				iX = event.clientX + document.body.scrollLeft;
				iY = event.clientY + document.body.scrollTop;

			}
			if (iX <= 30 && iY < 0 )
			{
				w=window.open(\"index.php?action=Logout&module=Users\");
				w.close();
			}
		}
	//window.onunload=LogOut
	</script>
		";

	if((!$skipFooters) && $action != "about_us" && $action != "vtchat" && $action != "ChangePassword" && $action != "body" && $action != $module."Ajax" && $action!='Popup' && $action != 'ImportStep3' && $action != 'ActivityAjax' && $action != 'getListOfRecords')
	{
		echo $copyrightstatement;
		//crmv@18592
		echo "<script language = 'JavaScript' type='text/javascript' src = 'include/js/popup.js'></script>";
		//crmv@vte10usersFix
		echo "<table border=0 cellspacing=0 width=100% style='padding:0px 5px 0px 5px; clear:both; font-size:75%;' id='vte_footer'>";//ds@24s login script	//crmv@16265
		echo "<tr><td align=left width=\"33%\"><span style='color: #255a9b;'>$enterprise_mode $enterprise_current_version</span></td>";
		if($calculate_response_time)
		{
			echo "<td align=center width=\"33%\" style='color: #255a9b;'>";
			$endTime = microtime();
			list($usec, $sec) = explode(" ", $endTime);
			$endTime = ((float)$usec + (float)$sec);
			list($usec, $sec) = explode(" ", $startTime);
			$startTime = ((float)$usec + (float)$sec);
			$deltaTime = round($endTime - $startTime,2);
			if (vtelog::getpageid() != ''){
				echo('&nbsp;['.vtelog::getpageid().'] Server response time: '.$deltaTime.' seconds.');
			}
			else{
				echo('&nbsp;Server response time: '.$deltaTime.' seconds.');
			}
			echo "</td>";
		}
		$license_par = "";
		if ($use_current_login) {
			$license_par = "'?use_current_login=yes'";
		}
		echo "<td align=right width=\"33%\"><span style='color: #255a9b;'>&copy; 2008-".date('Y')."  <a href='http://www.vtenext.org' target='_blank'>vtenext.org</a></span><span style='color: #255a9b;'> | <a href=\"javascript:mypopup($license_par)\">".$app_strings['LNK_READ_LICENSE']."</a></span></td></tr></table>";
		//crmv@vte10usersFix e
		//crmv@18592e
		//crmv@35153
		if (!empty($_SESSION['login_alert'])) {
			echo "<script type='text/javascript'>
					alert('".addslashes($_SESSION['login_alert'])."');
					getFile('index.php?module=Users&action=UsersAjax&file=UnsetSessionVar&var=login_alert');
			</script>";
		}
		if (!empty($_SESSION['login_confirm'])) {
			echo "<script type='text/javascript'>
					if (confirm('".addslashes($_SESSION['login_confirm']['text'])."')) {
						getFile('index.php?module=Users&action=UsersAjax&file=UnsetSessionVar&var=login_confirm');
						{$_SESSION['login_confirm']['action']}
					} else {
						getFile('index.php?module=Users&action=UsersAjax&file=UnsetSessionVar&var=login_confirm');
					}
			</script>";
		}
		//crmv@35153e
	}

	// ActivityReminder Customization for callback - crmv@OPER5904
	if(!$skipFooters) {
		if($current_user->id!=NULL && isPermitted('Calendar','index') == 'yes' && vtlib_isModuleActive('Calendar')) {
			$cur_time = time();
			$interval = $_SESSION['next_reminder_interval'] + (rand(0,9)-5);
			$reminder_interval_reset = ($_SESSION['last_reminder_check_time'] - $cur_time + $interval) * 1000;
			echo "<script type='text/javascript'>
				ActivityReminderCallbackInit();
				if(typeof(ActivityReminderCallback) != 'undefined') ";
			if(isset($_SESSION['last_reminder_check_time']) && $reminder_interval_reset > 0){
				echo "
					window.setTimeout(function(){
						ActivityReminderCallback($interval);
					},$reminder_interval_reset);";
			} else {
				echo "ActivityReminderCallback();";
			}
			echo "</script>";
		}
		eval($hash_version[4]);
	}
	// End

	if((!$skipFooters) && ($action != "body") && ($action != $module."Ajax") && ($action != "ActivityAjax"))
		include('modules/VteCore/footer.php');	//crmv@30447
}

//crmv@show_query
if ($_REQUEST['show_query'] == 'true') {
	$stats = '';
	if ($_REQUEST['show_stats'] == 'true') {
		$stats = "\n\nQUERY COUNT: {$adb->statistics['query_count']}\n";
		$stats .= "  SELECTS: {$adb->statistics['select']}\n";
		$stats .= "  INSERTS: {$adb->statistics['insert']}\n";
		$stats .= "  UPDATES: {$adb->statistics['update']}\n";
		$stats .= "  DELETES: {$adb->statistics['delete']}\n";
		$stats .= "  OTHERS: {$adb->statistics['other']}\n\n";
		$stats .= "  DUPLICATES: {$adb->statistics['duplicates_count']}\n";
		if ($adb->statistics['duplicates_count'] > 0) {
			usort($adb->statistics['duplicates'], function($a, $b) {
				return $a['count'] == $b['count'] ? 0 : ($a['count'] < $b['count'] ? 1 : -1);
			});
			$stats .= "  LIST: ".print_r(array_values($adb->statistics['duplicates']), true)."\n";
		}
	}
	echo "<textarea>".$_SESSION['query_show'].$stats."</textarea>";
	unset($_SESSION['query_show']);
}
//crmv@show_query e
