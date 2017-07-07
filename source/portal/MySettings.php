<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
*
 ********************************************************************************/
require_once("Smarty_setup.php");

$smarty = new VTECRM_Smarty();

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off');
$cookieurl = str_replace('MySettings.php', '', $_SERVER['SCRIPT_NAME']) ?: '/';

session_set_cookie_params(0, $cookieurl, null, $isHttps, true);
session_start();

$errormsg = '';
require_once("PortalConfig.php");
if(!isset($_SESSION['customer_id']) || $_SESSION['customer_id'] == '') {
	@header("Location: $Authenticate_Path/login.php");
	exit;
}
require_once("include/utils/utils.php");
$default_language = getPortalCurrentLanguage();
require_once("language/".$default_language.".lang.php");
global $default_charset;
header('Content-Type: text/html; charset='.$default_charset);

if($_REQUEST['fun'] != '' && $_REQUEST['fun'] == 'savepassword')
{
	include("include.php");
	require_once("HelpDesk/Utils.php");
	include("version.php");
	global $version;
	$errormsg = SavePassword($version);
}
$smarty->assign("ERRORMSG",$errormsg);

if($_REQUEST['last_login'] != '')
{
	$last_login = portal_purify(stripslashes($_REQUEST['last_login']));
	$_SESSION['last_login'] = $last_login;
	$smarty->assign('LASTLOGIN',$last_login);
}
elseif($_SESSION['last_login'] != '')
{
	$last_login = $_SESSION['last_login'];
}

if($_REQUEST['support_start_date'] != '')
	$_SESSION['support_start_date'] = $support_start_date = portal_purify(stripslashes(
		$_REQUEST['support_start_date']));
elseif($_SESSION['support_start_date'] != '')
	$support_start_date = $_SESSION['support_start_date'];

$smarty->assign('SUPPORTSTART',$support_start_date);

if($_REQUEST['support_end_date'] != '')
	$_SESSION['support_end_date'] = $support_end_date = portal_purify(stripslashes(
		$_REQUEST['support_end_date']));
elseif($_SESSION['support_end_date'] != '')
	$support_end_date = $_SESSION['support_end_date'];

$smarty->assign("SUPPORTEND",$support_end_date);

$smarty->display('MySettings.tpl');
?>