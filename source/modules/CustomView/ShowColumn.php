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
//crmv@29615
require_once('Smarty_setup.php');
require_once('data/Tracker.php');
require_once('include/CustomFieldUtil.php');
require_once('include/database/PearDatabase.php');
require_once('include/utils/utils.php');
require_once('user_privileges/default_module_view.php');
global $app_strings, $app_list_strings, $log, $currentModule, $singlepane_view, $current_user;
global $theme,$adb,$table_prefix;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once('modules/VteCore/layout_utils.php');	//crmv@30447

$smarty = new vtigerCRM_Smarty;
$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);
$smarty->assign("COLUMNAME", $columnname);

$module_name = $_REQUEST["parent_module"];
if ($module_name == 'Calendar')
	$tabid = '9,16';
else
	$tabid =  getTabid($module_name);
$language = $_SESSION['authenticated_user_language'];
$mod_strings = return_module_language($language, $module_name);

$option = $_REQUEST['option'];
//list($tablename,$columnname,$fieldname,$fieldlabel,$typeofdata,$reallabel) = explode(":",$_REQUEST["value"]);
if ($_REQUEST['type'] == 'AdvancedSearch') {
	$value = explode(":",$_REQUEST["value"]);
	$value = explode('.',$value[0]);
	if ($value[1] != '') {
		$field = $value[1];
		$sql = "SELECT * FROM ".$table_prefix."_field WHERE tabid IN ($tabid) AND columnname='$field'";
	}
	else {
		$field = $value[0];
		$sql = "SELECT * FROM ".$table_prefix."_field WHERE tabid IN ($tabid) AND fieldname='$field'";
	}
} elseif ($_REQUEST['type'] == 'CustomView') {
	$value = explode(":",$_REQUEST["value"]);
	$value1 = explode('_',$value[3]);
	$tabid = getTabid($value1[0]);
	if ($tabid == 9) $tabid = '9,16';
	$sql = "SELECT * FROM ".$table_prefix."_field WHERE tabid IN ($tabid) AND columnname='".$value[1]."'";
	
	// get saved value for the view
	$filtervalue = null;
	//crmv@42329
	$viewid = $_REQUEST['viewid'];
	$seq = intval($_REQUEST['selectsequence'])-1;
	if (!empty($viewid)) {
		if (strpos($viewid,"ADVSHARE_") !== false){
			$viewid = intval(str_replace("ADVSHARE_",'',$viewid));
			if (!empty($viewid)) {
				$res = $adb->pquery("select value from tbl_s_advancedrulefilters where advrule_id = ? and columnindex = ? and columnname = ?", array($viewid, $seq, vtlib_purify($_REQUEST['value'])));
				if ($res && $adb->num_rows($res) > 0){
					$filtervalue = $adb->query_result($res, 0, 'value');
				}
			}
		}
		else{
			$viewid = intval($viewid);
			$res = $adb->pquery("select value from {$table_prefix}_cvadvfilter where cvid = ? and columnindex = ? and columnname = ?", array($viewid, $seq, vtlib_purify($_REQUEST['value'])));
			if ($res && $adb->num_rows($res) > 0){
				$filtervalue = $adb->query_result($res, 0, 'value');
			}
		}
	}
	//crmv@42329e
}
$result = $adb->query($sql);
$uitype = $adb->query_result($result,0,"uitype");
$typeofdata = $adb->query_result($result,0,"typeofdata");  
$generatedtype = $adb->query_result($result,0,"generatedtype");
$fieldname = $adb->query_result($result,0,"fieldname");
$fieldid = $adb->query_result($result,0,"fieldid");
$maxlength = "100";
$readonly = 1;
$col_fields = array();

if ($uitype == 56) {
	($filtervalue == 'Yes') ? $filtervalue = '1' : $filtervalue = '0';
}

if (!is_null($filtervalue)) $col_fields[$fieldname] = $filtervalue;

if (!in_array($option,array('e','n'))) {
	$uitype = 1;
} else {
	if ($uitype == 70) {
		$uitype = 5;
	}
	$field_obj = WebserviceField::fromQueryResult($adb,$result,0);
	$fieldDataType = $field_obj->getFieldDataType();
	if($fieldDataType == 'picklist'){
		$uitype = 33;
	}
}

if ($_REQUEST['mode'] == 'DisplayFieldName')
	echo getDisplayFieldName($uitype,$fieldname);
else {
	if ($option != '') {
		global $showfullusername;
		$showfullusername_change = false;
		if($showfullusername) {
			$showfullusername_change = true;
			$showfullusername = false;
		}
		
		$custfld = getOutputHtml($uitype, $fieldname, $fieldlabel, $maxlength, $col_fields, $generatedtype, $module_name, '', $readonly, $typeofdata);
		$custfld[] = $fieldid;
		$Column_Data[][] = $custfld;
		
		if($showfullusername) $showfullusername = true;
		
		echo "<table>";
		$smarty->assign('data', $Column_Data);
		$smarty->assign('NOLABEL', true);
		echo $smarty->fetch("DisplayFields.tpl");
		echo "</table>";
	}	
	echo "$$$";
	echo getDisplayFieldName($uitype,$fieldname);
	echo "@@@";
	echo $reallabel;
	echo "@@@";
	echo $typeofdata;
}
//crmv@29615e
?>