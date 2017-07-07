<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once('include/utils/UserInfoUtil.php');
require_once('include/utils/utils.php');
global $adb;
global $table_prefix;
$profilename = from_html(decode_html($_REQUEST['profile_name']));
$description= from_html(decode_html($_REQUEST['profile_description']));
$def_module = vtlib_purify($_REQUEST['selected_module']);
$def_tab = vtlib_purify($_REQUEST['selected_tab']);
$profileid = $adb->getUniqueID($table_prefix."_profile");

// crmv@49398
//Inserting values into Profile Table
$enableMobile = (vtlib_purify($_REQUEST['enable_mobile']) == 'on' ? 1 : 0);
$sql1 = "insert into ".$table_prefix."_profile(profileid, profilename, description, mobile) values(?,?,?,?)";
$adb->pquery($sql1, array($profileid,$profilename, $description, $enableMobile));
// crmv@49398e

//Retreiving the first profileid
$prof_query="select profileid from ".$table_prefix."_profile order by profileid ASC";
$prof_result = $adb->pquery($prof_query, array());
$first_prof_id = $adb->query_result($prof_result,0,'profileid');

$tab_perr_result = $adb->pquery("select * from ".$table_prefix."_profile2tab where profileid=?", array($first_prof_id));
$act_perr_result = $adb->pquery("select * from ".$table_prefix."_profile2standardperm where profileid=?", array($first_prof_id));
$act_utility_result = $adb->pquery("select * from ".$table_prefix."_profile2utility where profileid=?", array($first_prof_id));
$num_tab_per = $adb->num_rows($tab_perr_result);
$num_act_per = $adb->num_rows($act_perr_result);
$num_act_util_per = $adb->num_rows($act_utility_result);

$hideTabs = getHideTab('hide_profile');	//crmv@27711

//Updating vtiger_profile2global permissons vtiger_table
$view_all_req=$_REQUEST['view_all'];
$view_all = getPermissionValue($view_all_req);

$edit_all_req=$_REQUEST['edit_all'];
$edit_all = getPermissionValue($edit_all_req);

$sql4="insert into ".$table_prefix."_profile2globalperm values(?,?,?)";
$adb->pquery($sql4, array($profileid,1, $view_all));

$sql4="insert into ".$table_prefix."_profile2globalperm values(?,?,?)";
$adb->pquery($sql4, array($profileid,2, $edit_all));


//profile2tab permissions
for($i=0; $i<$num_tab_per; $i++)
{
	$tab_id = $adb->query_result($tab_perr_result,$i,"tabid");
	$request_var = $tab_id.'_tab';
	if($tab_id != 3 && $tab_id != 16)
	{
		$permission = $_REQUEST[$request_var];
		if($permission == 'on' || in_array($tab_id,$hideTabs))	//crmv@27711
		{
			$permission_value = 0;
		}
		else
		{
			$permission_value = 1;
		}
		$sql4="insert into ".$table_prefix."_profile2tab values(?,?,?)";
		$adb->pquery($sql4, array($profileid, $tab_id, $permission_value));

		if($tab_id ==9)
		{
			$sql4="insert into ".$table_prefix."_profile2tab values(?,?,?)";
			$adb->pquery($sql4, array($profileid,16, $permission_value));
		}
	}
}

//profile2standard permissions
for($i=0; $i<$num_act_per; $i++)
{
	$tab_id = $adb->query_result($act_perr_result,$i,"tabid");
	$action_id = $adb->query_result($act_perr_result,$i,"operation");
	if($tab_id != 16)
	{
		$action_name = getActionname($action_id);
		if($action_name == 'EditView' || $action_name == 'Delete' || $action_name == 'DetailView')
		{
			$request_var = $tab_id.'_'.$action_name;
		}
		elseif($action_name == 'Save')
		{
			$request_var = $tab_id.'_EditView';
		}
		elseif($action_name == 'index')
		{
			$request_var = $tab_id.'_DetailView';
		}

		$permission = $_REQUEST[$request_var];
		if($permission == 'on' || in_array($tab_id,$hideTabs))	//crmv@27711
		{
			$permission_value = 0;
		}
		else
		{
			$permission_value = 1;
		}

		$sql7="insert into ".$table_prefix."_profile2standardperm values(?,?,?,?)";
		$adb->pquery($sql7, array($profileid, $tab_id, $action_id, $permission_value));

		if($tab_id ==9)
		{
			$sql7="insert into ".$table_prefix."_profile2standardperm values(?,?,?,?)";
			$adb->pquery($sql7, array($profileid, 16, $action_id, $permission_value));
		}
	}
}

//Update Profile 2 utility
for($i=0; $i<$num_act_util_per; $i++)
{
	$tab_id = $adb->query_result($act_utility_result,$i,"tabid");

	$action_id = $adb->query_result($act_utility_result,$i,"activityid");
	$action_name = getActionname($action_id);
	$request_var = $tab_id.'_'.$action_name;


	$permission = $_REQUEST[$request_var];
	if($permission == 'on' || in_array($tab_id,$hideTabs))	//crmv@27711
	{
		$permission_value = 0;
	}
	else
	{
		$permission_value = 1;
	}

	$sql9="insert into ".$table_prefix."_profile2utility values(?,?,?,?)";
	$adb->pquery($sql9, array($profileid, $tab_id, $action_id, $permission_value));
}

$modArr=getFieldModuleAccessArray();

foreach($modArr as $fld_module => $fld_label)
{
	$fieldListResult = getProfile2FieldList($fld_module, $first_prof_id);
	$noofrows = $adb->num_rows($fieldListResult);
	//crmv@24665
	$moduleinstance = Vtiger_Module::getInstance($fld_module);
	if ($moduleinstance){
		$tab_id = $moduleinstance->id;
	}
	//crmv@24665e
	//crmv@49510
	for($i=0; $i<$noofrows; $i++)
	{
		$fieldid =  $adb->query_result($fieldListResult,$i,"fieldid");
		$visible = $_REQUEST[$fieldid];
		$mandatory = $_REQUEST['m_'.$fieldid];
		
		//Updating the Mandatory vtiger_fields
		$uitype = $adb->query_result($fieldListResult,$i,"uitype");
		$displaytype =  $adb->query_result($fieldListResult,$i,"displaytype");
		$fieldname =  $adb->query_result($fieldListResult,$i,"fieldname");
		$typeofdata = $adb->query_result($fieldListResult,$i,"typeofdata");
		$fieldtype = explode("~",$typeofdata);
		
		($visible == 'on' || in_array($tab_id,$hideTabs)) ? $visible_value = 0 : $visible_value = 1;	//crmv@27711
		($mandatory == 'on' || in_array($tab_id,$hideTabs)) ? $mandatory_value = 0 : $mandatory_value = 1;
		
       	if($fieldtype[1] == 'M')
   		{
			$visible_value = 0;
			$mandatory_value = 0;
		}
		
		//Updating the database
		// crmv@39110
		$sequence = $adb->query_result($fieldListResult,$i,'sequence');
		$sql11="insert into ".$table_prefix."_profile2field (profileid, tabid, fieldid, visible, readonly, sequence, mandatory) values(?,?,?,?,?,?,?)";
        $adb->pquery($sql11, array($profileid, $tab_id, $fieldid, $visible_value, 1, (empty($sequence) ? $i : $sequence), $mandatory_value));
        // crmv@39110e
	}
	//crmv@49510e
}

// crmv@49398
global $metaLogs;
if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_ADDPROFILE, $profileid);
// crmv@49398e

$loc = "Location: index.php?action=ListProfiles&module=Settings&mode=view&parenttab=Settings&profileid=".vtlib_purify($profileid)."&selected_tab=".vtlib_purify($def_tab)."&selected_module=".vtlib_purify($def_module);
header($loc);


/** returns value 0 if request permission is on else returns value 1
  * @param $req_per -- Request Permission:: Type varchar
  * @returns $permission - can have value 0 or 1:: Type integer
  *
 */
function getPermissionValue($req_per)
{
	if($req_per == 'on')
	{
		$permission_value = 0;
	}
	else
	{
		$permission_value = 1;
	}
	return $permission_value;
}

?>