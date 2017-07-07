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

global $currentModule,$theme,$app_strings;
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
		<a href='javascript:window.history.back();'>$app_strings[LBL_GO_BACK]</a><br></td>
		</tr>
		</tbody></table> 
		</div>";
	echo "</td></tr></table>";
die;

require_once('include/database/PearDatabase.php');
require_once('Smarty_setup.php');
global $theme;
global $table_prefix;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$smarty = new vtigerCRM_Smarty;
$query = "SELECT * FROM ".$table_prefix."_notifyscheduler order by schedulednotificationid";
$result = $adb->pquery($query, array());
if($adb->num_rows($result) >=1)
{
	$notifiy_array = Array();
	while($result_row = $adb->fetch_array($result))
	{
		$result_data = Array();
		$result_data['active'] = $result_row['active'];
		$result_data['schedulename'] = $mod_strings[$result_row['schedulednotificationname']];
		$result_data['id'] = $result_row['schedulednotificationid'];
	
		if($result_data['active'] != 1)	
			$result_data['active'] = $mod_strings['LBL_INACTIVE'];
		else
			$result_data['active'] = $mod_strings['LBL_ACTIVE'];
			
		$result_data['label'] = $mod_strings[$result_row['label']];
		$notifiy_array []= $result_data;
	}
	$smarty->assign("NOTIFICATION",$notifiy_array);
}	
		
$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("APP", $app_strings);
$smarty->assign("CMOD", $mod_strings);
if($_REQUEST['directmode'] != '')
	$smarty->display("Settings/EmailNotificationContents.tpl");
else
	$smarty->display("Settings/EmailNotification.tpl");
?>