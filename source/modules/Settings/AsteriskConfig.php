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
$smarty = new vtigerCRM_Smarty;
if($_REQUEST['error'] != '')
{
//        require_once("modules/Fax/fax_.php");
        $error_msg =$_REQUEST['error'];
//	$error_msg = $mod_strings['LBL_FAXSENDERROR'].$error_msg;
	$smarty->assign("ERROR_MSG",' <b><font class="warning">'.$error_msg.'</font></b>');
}

global $adb,$table_prefix;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$sql="select * from ".$table_prefix."_systems where server_type = ?";
$result = $adb->pquery($sql,Array('asterisk'));
$server = $adb->query_result($result,0,'server');
$server_port = $adb->query_result($result,0,'server_port');
$server_username = $adb->query_result($result,0,'server_username');
$server_password = $adb->query_result($result,0,'server_password');
$inc_call = $adb->query_result($result,0,'inc_call');
if(isset($_REQUEST['asterisk_server_mode']) && $_REQUEST['asterisk_server_mode'] != '')
	$smarty->assign("ASTERISK_SERVER_MODE",$_REQUEST['asterisk_server_mode']);
else
	$smarty->assign("ASTERISK_SERVER_MODE",'view');
if(isset($_REQUEST['server']))
	$smarty->assign("ASTERISKSERVER",$_REQUEST['server']);
elseif (isset($server))
	$smarty->assign("ASTERISKSERVER",$server);
if (isset($_REQUEST['port']))
        $smarty->assign("ASTERISKPORT",$_REQUEST['port']);      
elseif (isset($server_port))
        $smarty->assign("ASTERISKPORT",$server_port);
else  $smarty->assign("ASTERISKPORT",'5038'); 
if (isset($_REQUEST['server_user']))
	$smarty->assign("ASTERISKUSER",$_REQUEST['server_user']);
elseif (isset($server_username))
        $smarty->assign("ASTERISKUSER",$server_username);
else  $smarty->assign("ASTERISKUSER",'phpagi');      
if (isset($server_password))
	$smarty->assign("ASTERISKPASSWORD",$server_password);
else $smarty->assign("ASTERISKPASSWORD",'phpagi');
if (isset($inc_call))
	$smarty->assign("ASTERISKINC_CALL",$inc_call);
else $smarty->assign("ASTERISKINC_CALL",0);

if ($server_port) $smarty->assign("ACTIVE", 'yes');
$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("APP", $app_strings);
$smarty->assign("CMOD", $mod_strings);
$smarty->display("Settings/AsteriskServer.tpl");
?>
