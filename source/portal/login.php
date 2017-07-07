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
require_once("PortalConfig.php");
// crmv@81291
if (empty($_REQUEST['login_language'])){
	$default_language = 'it_it'; // crmv@5946
}else{
	$default_language = $_REQUEST['login_language'];
}
// crmv@81291e
$smarty->assign('LOGINLANGUAGE',$default_language);
require_once("language/$default_language.lang.php");
include("version.php");
include("templates/setting.php");
include_once('include/utils/utils.php');
//require("Smarty/templates/login.tpl");
//require("Smarty/libs/Smarty.class.php");

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off');
$cookieurl = str_replace('login.php', '', $_SERVER['SCRIPT_NAME']) ?: '/';

session_set_cookie_params(0, $cookieurl, null, $isHttps, true);
@session_start();

if(isset($_SESSION['customer_id']) && isset($_SESSION['customer_name']))
{
	/*crmv
	header("Location: index.php?action=index&module=.'$module'");*/
	exit;
}
if($_REQUEST['close_window'] == 'true')
{
   ?>
<script language="javascript">
        	window.close();
	</script>
<?php
}
global $default_charset;
header('Content-Type: text/html; charset='.$default_charset);

$smarty->assign('BROWSERNAME',$browsername );
$smarty->assign('LOGO', get_logo('favicon'));
$smarty->assign('TITLE',$site_title);
$smarty->assign('LANGUAGE', getPortalLanguages());

//Display the login error message 
if($_REQUEST['login_error'] != '')
	$smarty->assign("LOGIN_ERROR", base64_decode($_REQUEST['login_error'])); //echo getTranslatedString(base64_decode($_REQUEST['login_error'])); 
?>
		
		
<script language="javascript">
function validateLoginDetails()
{
	var user = trim(document.getElementById("username").value);
	var pass = trim(document.getElementById("pw").value);
	if(user != '')
	{
		if(pass != '')
			return true;
		else
		{

			alert("Please enter a valid password.");
			return false;
		}
	}
	else
	{
		alert("Please enter valid username.");
		return false;
	}
}
function trim(s)
{
	while (s.substring(0,1) == " " || s.substring(0,1) == "\n")
	{
		s = s.substring(1, s.length);
	}
	while (s.substring(s.length-1, s.length) == " " || s.substring(s.length-1,s.length) == "\n") {
		s = s.substring(0,s.length-1);
	}
	return s;
}

</script>
<?php
// $smarty->display('login_plusreg.tpl'); login + registrazione
$smarty->display('login.tpl');
?>