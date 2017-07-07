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
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Accounts/DetailView.php,v 1.37 2005/04/18 10:37:49 samk Exp $
 * Description:  TODO To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('Smarty_setup.php');
require_once('data/Tracker.php');
require_once('include/CustomFieldUtil.php');
require_once('include/database/PearDatabase.php');
require_once('include/utils/utils.php');
require_once('user_privileges/default_module_view.php');
global $mod_strings;
global $app_strings;
global $app_list_strings;
global $log, $currentModule, $singlepane_view;

global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once('modules/VteCore/layout_utils.php');	//crmv@30447

$LVU = ListViewUtils::getInstance();

$smarty = new vtigerCRM_Smarty;
$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);

$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);
 
$header = array($mod_strings["LBL_FIRST_NAME"],$mod_strings["LBL_LAST_NAME"],$mod_strings["LBL_COMPANY"]);

$smarty->assign("LISTHEADER", $header);

$sql = $LVU->getListQuery($currentModule);
$params=array();
//2 array
$fieldnames=$_REQUEST['fieldnames'];
$fieldvalues=$_REQUEST['fieldvalues'];

$fldnames=explode(',',$fieldnames);
$fldval=explode(',',$fieldvalues);

foreach($fldnames as $fieldname){
	$sql.=" AND $fieldname=?";
}
foreach($fldval as $value){
	array_push($params, $value);
}

$result = $adb->pquery($sql,$params);
$i = 0;
while($row = $adb->fetchByAssoc($result))
{
	$i++;
  $Entities[$i][1] = "<a href='index.php?module=Leads&action=DetailView&record=".$row['leadid']."' target='_new'>".$row['firstname']."</a>";
	$Entities[$i][2] = "<a href='index.php?module=Leads&action=DetailView&record=".$row['leadid']."' target='_new'>".$row['lastname']."</a>";
	$Entities[$i][3] = $row['company'];
}

$smarty->assign("MODULE", $module);

$smarty->assign("LISTENTITY", $Entities);

$smarty->display("PopupDuplicate.tpl");
?>
