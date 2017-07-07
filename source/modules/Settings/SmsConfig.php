<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
require_once('Smarty_setup.php');
global $mod_strings;
global $app_strings;
global $app_list_strings;
global $table_prefix;

//Display the mail send status
$smarty = new vtigerCRM_Smarty;

if($_REQUEST['sms_error'] != '')
{
    require_once("modules/Sms/sms_.php");
    $error_msg = strip_tags(parseSmsErrorString($_REQUEST['sms_error']));
	$error_msg = $mod_strings['LBL_SMSSENDERROR'].$error_msg;
	$smarty->assign("ERROR_MSG",$mod_strings['LBL_TESTSMSSTATUS'].' <b><font class="warning">'.$error_msg.'</font></b>');
}
global $adb;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$sql="select server_type from tbl_s_smsservertype where presence = ?";
$result = $adb->pquery($sql, array(1));
$n=$adb->num_rows($result);
for ($i=0;$i<$n;$i++){
	$sms_server_type_arr[] = $adb->query_result($result,$i,'server_type');
	$questionmarks[]="?";
}
$sql="select * from ".$table_prefix."_systems where server_type = 'sms' and service_type in (".implode(",",$questionmarks).")";
$result = $adb->pquery($sql,$sms_server_type_arr);
$sms_server_type = $adb->query_result($result,0,'service_type');
$sms_server = $adb->query_result($result,0,'server');
$sms_server_username = $adb->query_result($result,0,'server_username');
$sms_server_domain = $adb->query_result($result,0,'domain');
$sms_server_account = $adb->query_result($result,0,'account');
$sms_server_prefix = $adb->query_result($result,0,'prefix');
$sms_server_name = $adb->query_result($result,0,'name');
$sms_server_password = $adb->query_result($result,0,'server_password');
$smtp_auth = $adb->query_result($result,0,'smtp_auth');
if(isset($_REQUEST['adv_domain']))
	$smarty->assign("ADVDOMAIN",$_REQUEST['adv_domain']);
elseif(isset($sms_server_domain))
	$smarty->assign("ADVDOMAIN",$sms_server_domain);
if(isset($_REQUEST['adv_account']))
	$smarty->assign("ADVACCOUNT",$_REQUEST['adv_account']);
elseif(isset($sms_server_account))
	$smarty->assign("ADVACCOUNT",$sms_server_account);
if(isset($_REQUEST['adv_prefix']))
	$smarty->assign("ADVPREFIX",$_REQUEST['adv_prefix']);
elseif(isset($sms_server_prefix))
	$smarty->assign("ADVPREFIX",$sms_server_prefix);
if(isset($_REQUEST['adv_name']))
	$smarty->assign("ADVNAME",$_REQUEST['adv_name']);
elseif(isset($sms_server_name))
	$smarty->assign("ADVNAME",$sms_server_name);	
if(isset($_REQUEST['server_name']))
	$smarty->assign("SMSSERVER",$_REQUEST['server_name']);
elseif(isset($sms_server))
	$smarty->assign("SMSSERVER",$sms_server);
if(isset($_REQUEST['server_user']))
	$smarty->assign("USERNAME",$_REQUEST['server_user']);
elseif(isset($sms_server_username))
	$smarty->assign("USERNAME",$sms_server_username);
if (isset($sms_server_password))
	$smarty->assign("PASSWORD",$sms_server_password);
if(isset($_REQUEST['auth_check']))
{
	if($_REQUEST['auth_check'] == 'on')
                $smarty->assign("SMTP_AUTH",'checked');
        else
                $smarty->assign("SMTP_AUTH",'');
}
elseif (isset($smtp_auth))
{
	if($smtp_auth == 'true')
		$smarty->assign("SMTP_AUTH",'checked');
	else
		$smarty->assign("SMTP_AUTH",'');
}

if(isset($_REQUEST['smsconfig_mode']) && $_REQUEST['smsconfig_mode'] != '')
	$smarty->assign("SMSCONFIG_MODE",$_REQUEST['smsconfig_mode']);
else
	$smarty->assign("SMSCONFIG_MODE",'view');
$smarty->assign("SERVER_TYPE",$sms_server_type_arr);
$smarty->assign("SMSSERVERTYPE",$sms_server_type);
$smarty->assign("THEME", $theme);
$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("APP", $app_strings);
$smarty->assign("CMOD", $mod_strings);
$smarty->display("Settings/SmsConfig.tpl");

?>
