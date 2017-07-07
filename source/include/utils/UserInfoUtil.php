<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ********************************************************************************/

require_once('include/BaseClasses.php');
require_once('include/database/PearDatabase.php');
require_once('include/database/Postgres8.php');
require_once('include/utils/utils.php');
require_once('include/utils/GetUserGroups.php');
require_once('include/utils/TempTables.php'); // crmv@63349
require_once('include/utils/SessionValidator.php'); // crmv@91082


class UserInfoUtils extends SDKExtendableUniqueClass {

	// handy function for the compatibility functions
	static public function callMethodByName($name, $arguments = array()) {
		$UIUtils = UserInfoUtils::getInstance();
		return call_user_func_array(array($UIUtils, $name), $arguments);
	}
	
	/**
	 * To get the Role of the specified user
	 * @param $userid -- The user Id:: Type integer
	 * @returns  vtiger_roleid :: Type String
	 */
	function fetchUserRole($userid) {
		global $log, $adb, $table_prefix;
		$log->debug("Entering fetchUserRole(".$userid.") method ...");
		$sql = "select roleid from ".$table_prefix."_user2role where userid = ?";
		$result = $adb->pquery($sql, array($userid));
		$roleid = $adb->query_result_no_html($result,0,"roleid");
		$log->debug("Exiting fetchUserRole method ...");
		return $roleid;
	}
	
	/**
	 * @deprecated.
	 * Function to be replaced by getUserProfile()
	 * Should be done accross the product
	 */
	function fetchUserProfileId($userid) {
		global $log, $adb, $table_prefix;
		$log->debug("Entering fetchUserProfileId(".$userid.") method ...");

		// Look up information in cache first
		$profileid = VTCacheUtils::lookupUserProfileId($userid);

		if($profileid === false) {
			$query  = "SELECT rp.profileid FROM {$table_prefix}_role2profile rp INNER JOIN {$table_prefix}_user2role ur on ur.roleid = rp.roleid WHERE ur.userid = ?"; // crmv@39110
			$result = $adb->pquery($query, array($userid));

			if($result && $adb->num_rows($result)) {
				$profileid = $adb->query_result($result, 0, 'profileid');
				// TODO: What if there are multiple profile to one role?
			}

			// Update information to cache for re-use
			VTCacheUtils::updateUserProfileId($userid, $profileid);
		}

		$log->debug("Exiting fetchUserProfileId method ...");
		return $profileid;
	}
	
	/**
	 * Function to get the lists of groupids releated with an user
	 * This function accepts the user id as arguments and
	 * returns the groupids related with the user id
	 * as a comma seperated string
	 */
	function fetchUserGroupids($userid) {
		global $log;
		$log->debug("Entering fetchUserGroupids(".$userid.") method ...");
		$focus = new GetUserGroups();
		$focus->getAllUserGroups($userid);
		//Asha: Remove implode if not required and if so, also remove explode functions used at the recieving end of this function
		$groupidlists = implode(",",$focus->user_groups);
		$log->debug("Exiting fetchUserGroupids method ...");
		return $groupidlists;
	}
	
	/**
	 * Function to load all the permissions
	 */
	function loadAllPerms() {
		global $log,$adb, $table_prefix;
		$log->debug("Entering loadAllPerms() method ...");
		
		global $MAX_TAB_PER;
		global $persistPermArray;

		$persistPermArray = Array();
		$profiles = Array();
		$sql = "select distinct profileid from ".$table_prefix."_profile2tab";
		$result = $adb->pquery($sql, array());
		$num_rows = $adb->num_rows($result);
		for ( $i=0; $i < $num_rows; $i++ )
				$profiles[] = $adb->query_result($result,$i,'profileid');

		$persistPermArray = Array();
		foreach ( $profiles as $profileid ) {
			$sql = "select * from ".$table_prefix."_profile2tab where profileid=?";
			$result = $adb->pquery($sql, array($profileid));
			if($MAX_TAB_PER !='') {
				$persistPermArray[$profileid] = array_fill(0,$MAX_TAB_PER,0);
			}
			$num_rows = $adb->num_rows($result);
			for($i=0; $i<$num_rows; $i++) {
				$tabid= $adb->query_result($result,$i,'tabid');
				$tab_per= $adb->query_result($result,$i,'permissions');
				$persistPermArray[$profileid][$tabid] = $tab_per;
			}
		}
		$log->debug("Exiting loadAllPerms method ...");
	}

	/**
	 * Function to get all the vtiger_tab permission for the specified vtiger_profile
 	 * @param $profileid -- Profile Id:: Type integer
	 * @returns  TabPermission Array in the following format:
	 * $tabPermission = Array($tabid1=>permission,
	 *                        $tabid2=>permission,
	 *                                |
	 *                        $tabidn=>permission)
	 *
	 */
	function getAllTabsPermission($profileid) {
		global $log,$adb, $table_prefix;
		$log->debug("Entering getAllTabsPermission(".$profileid.") method ...");
		global $persistPermArray;
		global $MAX_TAB_PER;
		// Mike Crowe Mod --------------------------------------------------------
		if ($cache_tab_perms) {
			if ( count($persistPermArray) == 0 ) {
				loadAllPerms();
			}
			$log->debug("Exiting getAllTabsPermission method ...");
			return $persistPermArray[$profileid];
		}
		else
		{
			$sql = "select * from ".$table_prefix."_profile2tab where profileid=?";
			$result = $adb->pquery($sql, array($profileid));
			$tab_perr_array = Array();
			if($MAX_TAB_PER !='')
			{
					$tab_perr_array = array_fill(0,$MAX_TAB_PER,0);
			}
			$num_rows = $adb->num_rows($result);
			for($i=0; $i<$num_rows; $i++)
			{
				$tabid= $adb->query_result($result,$i,'tabid');
				$tab_per= $adb->query_result($result,$i,'permissions');
				$tab_perr_array[$tabid] = $tab_per;
			}
			$log->debug("Exiting getAllTabsPermission method ...");
			return $tab_perr_array;
		}
		// Mike Crowe Mod ----------------------------------------------------------------
	}

	/**
	 * Function to get all the vtiger_tab permission for the specified vtiger_profile other than tabid 15
	 * @param $profileid -- Profile Id:: Type integer
	 * @returns  TabPermission Array in the following format:
	 * $tabPermission = Array($tabid1=>permission,
	 *                        $tabid2=>permission,
	 *                                |
	 *                        $tabidn=>permission)
	 *
	 */
	function getTabsPermission($profileid)
	{
		global $log;
		$log->debug("Entering getTabsPermission(".$profileid.") method ...");
		global $persistPermArray;
		global $adb,$table_prefix;
		// Mike Crowe Mod -------------------------------------------------------
		if ($cache_tab_perms) {
			if (count($persistPermArray) == 0) {
				loadAllPerms();
			}
			$tab_perr_array = $persistPermArray;
			foreach(array(1,3,16,15) as $tabid) {
				$tab_perr_array[$tabid] = 0;
			}
			$log->debug("Exiting getTabsPermission method ...");
			return $tab_perr_array;
		} else {
			$sql = "select ".$table_prefix."_profile2tab.*, vte_hide_tab.hide_profile from ".$table_prefix."_profile2tab left join vte_hide_tab on ".$table_prefix."_profile2tab.tabid = vte_hide_tab.tabid where profileid=?";	//crmv@27711
			$result = $adb->pquery($sql, array($profileid));
			$tab_perr_array = Array();
			$num_rows = $adb->num_rows($result);
			for($i=0; $i<$num_rows; $i++)
			{
				//crmv@27711
				$hide_profile = $adb->query_result($result,$i,'hide_profile');
				if ($hide_profile == '1') {
					continue;
				}
				//crmv@27711e
				$tabid= $adb->query_result($result,$i,'tabid');
				$tab_per= $adb->query_result($result,$i,'permissions');
				if($tabid != 3 && $tabid != 16) {
					$tab_perr_array[$tabid] = $tab_per;
				}
			}
			$log->debug("Exiting getTabsPermission method ...");
			return $tab_perr_array;
		}
	}
	
	/**
	 * Function to get all the vtiger_tab standard action permission for the specified vtiger_profile
	 * @param $profileid -- Profile Id:: Type integer
	 * @returns  Tab Action Permission Array in the following format:
	 * $tabPermission = Array($tabid1=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission),
	 *                        $tabid2=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission),
	 *                                |
	 *                        $tabidn=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission))
	 *
	 */
	function getTabsActionPermission($profileid)
	{
		global $log;
		$log->debug("Entering getTabsActionPermission(".$profileid.") method ...");
		global $adb,$table_prefix;
		$check = Array();
		$temp_tabid = Array();
		$sql1 = "select * from ".$table_prefix."_profile2standardperm where profileid=? and tabid not in(16) order by(tabid)";
		$result1 = $adb->pquery($sql1, array($profileid));
		$num_rows1 = $adb->num_rows($result1);
		for($i=0; $i<$num_rows1; $i++) {
			$tab_id = $adb->query_result($result1,$i,'tabid');
			if(! in_array($tab_id,$temp_tabid))
			{
				$temp_tabid[] = $tab_id;
				$access = Array();
			}

			$action_id = $adb->query_result($result1,$i,'operation');
			$per_id = $adb->query_result($result1,$i,'permissions');
			$access[$action_id] = $per_id;
			$check[$tab_id] = $access;
		}
		$log->debug("Exiting getTabsActionPermission method ...");
		return $check;
	}

	/** 
	 * Function to get all the vtiger_tab utility action permission for the specified vtiger_profile
	 * @param $profileid -- Profile Id:: Type integer
	 * @returns  Tab Utility Action Permission Array in the following format:
	 * $tabPermission = Array($tabid1=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission),
	 *                        $tabid2=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission),
	 *                                |
	 *                        $tabidn=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission))
	 *
	 */
	function getTabsUtilityActionPermission($profileid)
	{
		global $log;
		$log->debug("Entering getTabsUtilityActionPermission(".$profileid.") method ...");

		global $adb,$table_prefix;
		$check = Array();
		$temp_tabid = Array();
		$sql1 = "select * from ".$table_prefix."_profile2utility where profileid=? order by(tabid)";
		$result1 = $adb->pquery($sql1, array($profileid));
		$num_rows1 = $adb->num_rows($result1);
		for($i=0; $i<$num_rows1; $i++) {
			$tab_id = $adb->query_result($result1,$i,'tabid');
			if(! in_array($tab_id,$temp_tabid))
			{
				$temp_tabid[] = $tab_id;
				$access = Array();
			}

			$action_id = $adb->query_result($result1,$i,'activityid');
			$per_id = $adb->query_result($result1,$i,'permission');
			$access[$action_id] = $per_id;
			$check[$tab_id] = $access;
		}

		$log->debug("Exiting getTabsUtilityActionPermission method ...");
		return $check;
	}
	
	/**
	 * This Function returns the Default Organisation Sharing Action Array for all modules whose sharing actions are editable
	 * The result array will be in the following format:
	 * Arr=(tabid1=>Sharing Action Id,
	 *      tabid2=>SharingAction Id,
	 *            |
	 *            |
	 *            |
	 *      tabid3=>SharingAcion Id)
	 */
	function getDefaultSharingEditAction() {
		global $log, $adb,$table_prefix;
		$log->debug("Entering getDefaultSharingEditAction() method ...");
		
		//retreiving the standard permissions
		$sql= "select * from ".$table_prefix."_def_org_share where editstatus=0";
		$result = $adb->pquery($sql, array());
		$permissionRow=$adb->fetch_array($result);
		do
		{
			for($j=0;$j<count($permissionRow);$j++)
			{
				$copy[$permissionRow[1]]=$permissionRow[2];
			}

		} while($permissionRow=$adb->fetch_array($result));

		$log->debug("Exiting getDefaultSharingEditAction method ...");
		return $copy;

	}

	/**
	 * This Function returns the Default Organisation Sharing Action Array for modules with edit status in (0,1)
	 * The result array will be in the following format:
	 * Arr=(tabid1=>Sharing Action Id,
	 *      tabid2=>SharingAction Id,
	 *            |
	 *            |
	 *            |
	 *      tabid3=>SharingAcion Id)
	 */
	function getDefaultSharingAction() {
		global $log, $adb,$table_prefix;
		$log->debug("Entering getDefaultSharingAction() method ...");
		
		//retreivin the standard permissions
		$sql= "select * from ".$table_prefix."_def_org_share where editstatus in(0,1)";
		$result = $adb->pquery($sql, array());
		$permissionRow=$adb->fetch_array($result);
		do
		{
			for($j=0;$j<count($permissionRow);$j++)
			{
				$copy[$permissionRow[1]]=$permissionRow[2];
			}

		}while($permissionRow=$adb->fetch_array($result));
		$log->debug("Exiting getDefaultSharingAction method ...");
		return $copy;

	}

	/** 
	 * Returns the list of sharing rules for the specified module
	 * @param $module -- Module Name:: Type varchar
	 * @returns $access_permission -- sharing rules list info array:: Type array
	 *
	 */
	function getSharingRuleList($module)
	{
		global $adb,$mod_strings,$table_prefix;

		$tabid = getTabid($module);
		$dataShareTableArray=getDataShareTableandColumnArray();

		$i=1;
		$access_permission = array();
		foreach($dataShareTableArray as $table_name => $colName)
		{

			$colNameArr=explode("::",$colName);
			$query = "select ".$table_name.".* from ".$table_name." inner join ".$table_prefix."_datashare_mod_rel on ".$table_name.".shareid=".$table_prefix."_datashare_mod_rel.shareid where ".$table_prefix."_datashare_mod_rel.tabid=?";
			$result=$adb->pquery($query, array($tabid));
			$num_rows=$adb->num_rows($result);

			$share_colName=$colNameArr[0];
			$share_modType=getEntityTypeFromCol($share_colName);

			$to_colName=$colNameArr[1];
			$to_modType=getEntityTypeFromCol($to_colName);

			for($j=0;$j<$num_rows;$j++)
			{
				$shareid=$adb->query_result($result,$j,"shareid");
				$share_id=$adb->query_result($result,$j,$share_colName);
				$to_id=$adb->query_result($result,$j,$to_colName);
				$permission = $adb->query_result($result,$j,'permission');

				$share_ent_disp = getEntityDisplayLink($share_modType,$share_id);
				$to_ent_disp = getEntityDisplayLink($to_modType,$to_id);

				if($permission == 0)
				{
					$perr_out = $mod_strings['Read Only '];
				}
				elseif($permission == 1)
				{
					$perr_out = $mod_strings['Read/Write'];
				}

				$access_permission [] = $shareid;
				$access_permission [] = $share_ent_disp;
				$access_permission [] = $to_ent_disp;
				$access_permission [] = $perr_out;

				$i++;
			}

		}
		
		if(is_array($access_permission)) {
			$access_permission = array_chunk($access_permission,4);
		}
		return $access_permission;
	}
	
	/**
	 * This Function returns the Default Organisation Sharing Action Array for all modules
	 * The result array will be in the following format:
	 * Arr=(tabid1=>Sharing Action Id,
	 *      tabid2=>SharingAction Id,
	 *            |
	 *            |
	 *            |
	 *      tabid3=>SharingAcion Id)
	 */
	function getAllDefaultSharingAction() {
		global $log, $adb,$table_prefix;
		$log->debug("Entering getAllDefaultSharingAction() method ...");
		
		$copy=Array();
		//retreiving the standard permissions
		$sql= "select * from ".$table_prefix."_def_org_share";
		$result = $adb->pquery($sql, array());
		$num_rows=$adb->num_rows($result);

		for($i=0;$i<$num_rows;$i++)
		{
			$tabid=$adb->query_result($result,$i,'tabid');
			$permission=$adb->query_result($result,$i,'permission');
			$copy[$tabid]=$permission;

		}

		$log->debug("Exiting getAllDefaultSharingAction method ...");
		return $copy;
	}

}

/* compatibility functions */

function fetchUserRole($userid) { return UserInfoUtils::callMethodByName(__FUNCTION__, func_get_args()); }
function fetchUserProfileId($userid) { return UserInfoUtils::callMethodByName(__FUNCTION__, func_get_args()); }
function fetchUserGroupids($userid) { return UserInfoUtils::callMethodByName(__FUNCTION__, func_get_args()); }

function loadAllPerms() { return UserInfoUtils::callMethodByName(__FUNCTION__); }
function getAllTabsPermission($profileid) { return UserInfoUtils::callMethodByName(__FUNCTION__, func_get_args()); }
function getTabsPermission($profileid) { return UserInfoUtils::callMethodByName(__FUNCTION__, func_get_args()); }
function getTabsActionPermission($profileid) { return UserInfoUtils::callMethodByName(__FUNCTION__, func_get_args()); }
function getTabsUtilityActionPermission($profileid) { return UserInfoUtils::callMethodByName(__FUNCTION__, func_get_args()); }

function getDefaultSharingEditAction() { return UserInfoUtils::callMethodByName(__FUNCTION__); }
function getDefaultSharingAction() { return UserInfoUtils::callMethodByName(__FUNCTION__); }
function getSharingRuleList($module) { return UserInfoUtils::callMethodByName(__FUNCTION__, func_get_args()); }
function getAllDefaultSharingAction() { return UserInfoUtils::callMethodByName(__FUNCTION__); }


// TODO: move the other functions in the class
// TODO: separate the functions in different classes: UsersUtils, GroupUtils, ProfileUtils, RoleUtils, ...



/** Function to create the vtiger_role
  * @param $roleName -- Role Name:: Type varchar
  * @param $parentRoleId -- Parent Role Id:: Type varchar
  * @param $roleProfileArray -- Profile to be associated with this vtiger_role:: Type Array
  * @returns  the Rold Id :: Type varchar
  *
 */

function createRole($roleName,$parentRoleId,$roleProfileArray)
{
	global $log, $metaLogs; // crmv@49398
	$log->debug("Entering createRole(".$roleName.",".$parentRoleId.",".$roleProfileArray.") method ...");
	global $adb,$table_prefix;
	$parentRoleDetails=getRoleInformation($parentRoleId);
	$parentRoleInfo=$parentRoleDetails[$parentRoleId];
	$roleid_no=$adb->getUniqueId($table_prefix."_role");
        $roleId='H'.$roleid_no;
        $parentRoleHr=$parentRoleInfo[1];
        $parentRoleDepth=$parentRoleInfo[2];
        $nowParentRoleHr=$parentRoleHr.'::'.$roleId;
        $nowRoleDepth=$parentRoleDepth + 1;

    // Invalidate any cached information
    VTCacheUtils::clearRoleSubordinates($roleId);

	//Inserting vtiger_role into db
	$query="insert into ".$table_prefix."_role values(?,?,?,?)";
	$qparams = array($roleId,$roleName,$nowParentRoleHr,$nowRoleDepth);
	$adb->pquery($query,$qparams);

	//Inserting into vtiger_role2profile vtiger_table
	foreach($roleProfileArray as $profileId)
        {
                if($profileId != '')
                {
                        insertRole2ProfileRelation($roleId,$profileId);
                }
        }

	if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_ADDROLE, 0, array('roleid'=>$roleId)); // crmv@49398
	$log->debug("Exiting createRole method ...");
	return $roleId;

}

/** Function to update the vtiger_role
  * @param $roleName -- Role Name:: Type varchar
  * @param $roleId -- Role Id:: Type varchar
  * @param $roleProfileArray -- Profile to be associated with this vtiger_role:: Type Array
  *
 */
function updateRole($roleId,$roleName,$roleProfileArray,$mobile=0) { // crmv@39110
	global $log, $metaLogs; // crmv@49398
	$log->debug("Entering updateRole(".$roleId.",".$roleName.",".$roleProfileArray.") method ...");

	// Invalidate any cached information
    VTCacheUtils::clearRoleSubordinates($roleId);

	global $adb,$table_prefix;
	$sql1 = "update ".$table_prefix."_role set rolename=? where roleid=?";
    $adb->pquery($sql1, array($roleName, $roleId));
	//Updating the Role2Profile relation
    // crmv@39110
	$sql2 = "delete from ".$table_prefix."_role2profile where roleId=? and mobile = ?";
	$adb->pquery($sql2, array($roleId, $mobile));

	foreach ($roleProfileArray as $profileId) {
		if ($profileId != '') {
			insertRole2ProfileRelation($roleId,$profileId,$mobile);
		}
	}
	// crmv@39110e
	if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_EDITROLE, 0, array('roleid'=>$roleId)); // crmv@49398
	$log->debug("Exiting updateRole method ...");
}

/** Function to add the vtiger_role to vtiger_profile relation
  * @param $profileId -- Profile Id:: Type integer
  * @param $roleId -- Role Id:: Type varchar
  *
 */
function insertRole2ProfileRelation($roleId,$profileId,$mobile=0) { // crmv@39110
	global $log, $adb,$table_prefix;
	$log->debug("Entering insertRole2ProfileRelation(".$roleId.",".$profileId.") method ...");
	// crmv@39110
	$query="insert into ".$table_prefix."_role2profile (roleid,profileid,mobile) values(?,?,?)";
	$qparams = array($roleId,$profileId,$mobile);
	// crmv@39110e
	$adb->pquery($query, $qparams);
	$log->debug("Exiting insertRole2ProfileRelation method ...");
}


/** Function to get the vtiger_roleid from vtiger_rolename
  * @param $rolename -- Role Name:: Type varchar
  * @returns Role Id:: Type varchar
  *
 */
function fetchRoleId($rolename) {
  global $log;
  $log->debug("Entering fetchRoleId(".$rolename.") method ...");

  global $adb,$table_prefix;
  $sqlfetchroleid = "select roleid from ".$table_prefix."_role where rolename=?";
  $resultroleid = $adb->pquery($sqlfetchroleid, array($rolename));
  $role_id = $adb->query_result($resultroleid,0,"roleid");
  $log->debug("Exiting fetchRoleId method ...");
  return $role_id;
}

/** Function to update user to vtiger_role mapping based on the userid
  * @param $roleid -- Role Id:: Type varchar
  * @param $userid User Id:: Type integer
  *
 */
function updateUser2RoleMapping($roleid,$userid)
{
global $log;
$log->debug("Entering updateUser2RoleMapping(".$roleid.",".$userid.") method ...");
  global $adb,$table_prefix;
  //Check if row already exists
  $sqlcheck = "select * from ".$table_prefix."_user2role where userid=?";
  $resultcheck = $adb->pquery($sqlcheck, array($userid));
  if($adb->num_rows($resultcheck) == 1)
  {
  	$sqldelete = "delete from ".$table_prefix."_user2role where userid=?";
	$delparams = array($userid);
  	$result_delete = $adb->pquery($sqldelete, $delparams);
  }
  $sql = "insert into ".$table_prefix."_user2role(userid,roleid) values(?,?)";
  $params = array($userid, $roleid);
  $result = $adb->pquery($sql, $params);
	$log->debug("Exiting updateUser2RoleMapping method ...");

}


/** Function to update user to group mapping based on the userid
  * @param $groupname -- Group Name:: Type varchar
  * @param $userid User Id:: Type integer
  *
 */
function updateUsers2GroupMapping($groupname,$userid)
{
global $log;
$log->debug("Entering updateUsers2GroupMapping(".$groupname.",".$userid.") method ...");
  global $adb,$table_prefix;
  $sqldelete = "delete from ".$table_prefix."_users2group where userid = ?";
  $delparams = array($userid);
  $result_delete = $adb->pquery($sqldelete, $delparams);

  $sql = "insert into ".$table_prefix."_users2group(groupname,userid) values(?,?)";
  $params = array($groupname,$userid);
  $result = $adb->pquery($sql, $params);
  $log->debug("Exiting updateUsers2GroupMapping method ...");
}

/** Function to add user to vtiger_role mapping
  * @param $roleid -- Role Id:: Type varchar
  * @param $userid User Id:: Type integer
  *
 */
function insertUser2RoleMapping($roleid,$userid)
{
global $log;
$log->debug("Entering insertUser2RoleMapping(".$roleid.",".$userid.") method ...");

  global $adb,$table_prefix;
  $sql = "insert into ".$table_prefix."_user2role(userid,roleid) values(?,?)";
  $params = array($userid, $roleid);
  $adb->pquery($sql, $params);
$log->debug("Exiting insertUser2RoleMapping method ...");

}

/** Function to add user to group mapping
  * @param $groupname -- Group Name:: Type varchar
  * @param $userid User Id:: Type integer
  *
 */
function insertUsers2GroupMapping($groupname,$userid)
{
global $log;
$log->debug("Entering insertUsers2GroupMapping(".$groupname.",".$userid.") method ...");
  global $adb,$table_prefix;
  $sql = "insert into ".$table_prefix."_users2group(groupname,userid) values(?,?)";
  $params = array($groupname, $userid);
  $adb->pquery($sql, $params);
$log->debug("Exiting insertUsers2GroupMapping method ...");
}

/** Function to get the word template resultset
  * @param $module -- Module Name:: Type varchar
  * @returns Type:: resultset
  *
 */
function fetchWordTemplateList($module)
{
  	return array();
}



/** Function to get the email template iformation
  * @param $templateName -- Template Name:: Type varchar
  * @returns Type:: resultset
  *
 */
function fetchEmailTemplateInfo($templateName)
{
	global $log;
	$log->debug("Entering fetchEmailTemplateInfo(".$templateName.") method ...");
	global $adb,$table_prefix;
   	$sql= "select * from ".$table_prefix."_emailtemplates where templatename=?";
    $result = $adb->pquery($sql, array($templateName));
	$log->debug("Exiting fetchEmailTemplateInfo method ...");
    return $result;
}

/** Function to get the vtiger_role name from the vtiger_roleid
  * @param $roleid -- Role Id:: Type varchar
  * @returns $rolename -- Role Name:: Type varchar
  *
 */
function getRoleName($roleid)
{
	global $log;
	$log->debug("Entering getRoleName(".$roleid.") method ...");
	global $adb,$table_prefix;
	$sql1 = "select * from ".$table_prefix."_role where roleid=?";
	$result = $adb->pquery($sql1, array($roleid));
	$rolename = $adb->query_result($result,0,"rolename");
	$log->debug("Exiting getRoleName method ...");
	return $rolename;
}

/** Function to get the vtiger_profile name from the vtiger_profileid
  * @param $profileid -- Profile Id:: Type integer
  * @returns $rolename -- Role Name:: Type varchar
  *
 */
function getProfileName($profileid)
{
	global $log;
	$log->debug("Entering getProfileName(".$profileid.") method ...");
	global $adb,$table_prefix;
	$sql1 = "select profilename from ".$table_prefix."_profile where profileid=?"; // crmv@39110
	$result = $adb->pquery($sql1, array($profileid));
	$profilename = $adb->query_result($result,0,"profilename");
	$log->debug("Exiting getProfileName method ...");
	return $profilename;
}
/** Function to get the vtiger_profile Description from the vtiger_profileid
  * @param $profileid -- Profile Id:: Type integer
  * @returns $rolename -- Role Name:: Type varchar
  *
 */
function getProfileDescription($profileid)
{
        global $log;
        $log->debug("Entering getProfileDescription(".$profileid.") method ...");
        global $adb,$table_prefix;
        $sql1 = "select  description from ".$table_prefix."_profile where profileid=?";
        $result = $adb->pquery($sql1, array($profileid));
        $profileDescription = $adb->query_result($result,0,"description");
        $log->debug("Exiting getProfileDescription method ...");
        return $profileDescription;
}

/* cmrv@39110 */
function getProfileInfo($profileid) {
	global $log,$adb,$table_prefix;
	$log->debug("Entering getProfileInfo({$profileid}) method ...");

	$ret = array();
	$result = $adb->pquery("select * from {$table_prefix}_profile where profileid=?", array($profileid));
	if ($result && $adb->num_rows($result) > 0) {
		$ret = $adb->FetchByAssoc($result, -1, false);
	}
	$log->debug("Exiting getProfileInfo method ...");
	return $ret;
}
/* cmrv@39110e */


/** Function to check if the currently logged in user is permitted to perform the specified action
  * @param $module -- Module Name:: Type varchar
  * @param $actionname -- Action Name:: Type varchar
  * @param $recordid -- Record Id:: Type integer
  * @returns yes or no. If Yes means this action is allowed for the currently logged in user. If no means this action is not allowed for the currently logged in user
  *
 */
function isPermitted($module,$actionname,$record_id='', $useSDK = true) // crmv@81553
{
	global $log, $seclog;
	$log->debug("Entering isPermitted(".$module.",".$actionname.",".$record_id.") method ...");
	global $adb, $table_prefix;
	global $current_user;

	require('user_privileges/requireUserPrivileges.php'); // crmv@39110
	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
	$zm_check = false;
	eval(Users::m_de_cryption());
	eval($hash_version[11]);
	if ($zm_check) return 'no';

	$permission = "no";
	if (($module == 'Home' || $module == 'uploads') && $_REQUEST['parenttab'] != 'Settings') { //crmv@16312 crmv@37463
		//These modules dont have security right now
		$permission = "yes";
		$log->debug("Exiting isPermitted method ...");
		return $permission;

	}

	//Checking the Access for the Settings Module
	if($module == 'Settings' || $module == 'Administration' || $module == 'System' || $_REQUEST['parenttab'] == 'Settings')
	{
		if(! $is_admin)
		{
			$permission = "no";
		}
		else
		{
			$permission = "yes";
		}
		$log->debug("Exiting isPermitted method ...");
		return $permission;
	}

	//crmv@39092
	if (file_exists("modules/$module/$module.php") && !in_array($module,array('PickList'))) {
		require_once("modules/$module/$module.php");
		if (class_exists($module) && method_exists($module,'getAdvancedPermissionFunction')) {
			$focus = CRMEntity::getInstance($module);
			$permission = $focus->getAdvancedPermissionFunction($is_admin,$module,$actionname,$record_id);
			if ($permission != '') {
				return $permission;
			}
		}
	}
	//crmv@39092e

	//Checking whether the user is admin
	if($is_admin)
	{
		$permission ="yes";
		$log->debug("Exiting isPermitted method ...");
		return $permission;
	}

	//crmv@sdk-18506 crmv@81553
	if ($useSDK) {
		$advPermFunct = SDK::getAdvancedPermissionFunction($module);
		if ($advPermFunct != '') {
			$permission = $advPermFunct($module,$actionname,$record_id);
			if ($permission != '') {
				return $permission;
			}
		}
	}
	//crmv@sdk-18506e crmv@81553e
	
	//crmv@100731
	if (!empty($record_id)) {
		require_once('modules/Settings/ProcessMaker/ProcessMakerUtils.php');
		$PMUtils = ProcessMakerUtils::getInstance();
		$permission = $PMUtils->checkAdvancedPermissions($module,$actionname,$record_id);
		if ($permission != '') return $permission;
	}
	//crmv@100731e

	//Retreiving the Tabid and Action Id
	$tabid = getTabid($module);
	$actionid=getActionid($actionname);
	//If no actionid, then allow action is vtiger_tab permission is available
	if($actionid == '')
	{
		if($profileTabsPermission[$tabid] ==0)
        	{
                	$permission = "yes";
			$log->debug("Exiting isPermitted method ...");
        	}
		else
		{
			$permission ="no";
		}
               	return $permission;

	}

	$action = getActionname($actionid);

	// crmv@37463 - user permissions
	if ($module == 'Users') {
		if (is_admin($current_user) == true) {
			$permission = 'yes';
		} else {
			switch ($actionid) {
				case 0: // save
				case 1: // editview
					if ($current_user->id == $record_id) $permission = 'yes';
					break;
				case 2: // delete
					break;
				case 3: // index
					break;
				case 4: // detailview
					if ($current_user->id == $record_id) $permission = 'yes';
					break;
				default:
					$permission = "no";
					break;
			}
		}
		return $permission;
	}
	// crmv@37463e

	//Checking for view all permission
	if($profileGlobalPermission[1] ==0 || $profileGlobalPermission[2] ==0)
	{
		if($actionid == 3 || $actionid == 4)
		{
			$permission = "yes";
			$log->debug("Exiting isPermitted method ...");
			return $permission;

		}
	}
	//Checking for edit all permission
	if($profileGlobalPermission[2] ==0)
	{
		if($actionid == 3 || $actionid == 4 || $actionid ==0 || $actionid ==1)
		{
			$permission = "yes";
			$log->debug("Exiting isPermitted method ...");
			return $permission;

		}
	}
	//Checking for vtiger_tab permission
	if($profileTabsPermission[$tabid] !=0)
	{
		$permission = "no";
		$log->debug("Exiting isPermitted method ...");
		return $permission;
	}
	//Checking for Action Permission
	if(strlen($profileActionPermission[$tabid][$actionid]) <  1 && $profileActionPermission[$tabid][$actionid] == '')
	{
		$permission = "yes";
		$log->debug("Exiting isPermitted method ...");
		return $permission;
	}

	if($profileActionPermission[$tabid][$actionid] != 0 && $profileActionPermission[$tabid][$actionid] != '')
	{
		$permission = "no";
		$log->debug("Exiting isPermitted method ...");
		return $permission;
	}
	//Checking and returning true if recorid is null
	if($record_id == '')
	{
		$permission = "yes";
		$log->debug("Exiting isPermitted method ...");
		return $permission;
	}

	//If modules is owned by admin (eg: Vendors,Faq,PriceBook) then no sharing
	if($record_id != '')
	{
		if(getTabOwnedBy($module) == 1)
		{
			$permission = "yes";
			$log->debug("Exiting isPermitted method ...");
			return $permission;
		}
	}

	//Retreiving the RecordOwnerId
	$recOwnType='';
	$recOwnId='';
	$recordOwnerArr=getRecordOwnerId($record_id);
	foreach($recordOwnerArr as $type=>$id)
	{
		$recOwnType=$type;
		$recOwnId=$id;
	}
	//Retreiving the default Organisation sharing Access
	$others_permission_id = $defaultOrgSharingPermission[$tabid];

	if($recOwnType == 'Users')
	{
		//Checking if the Record Owner is the current User
		if($current_user->id == $recOwnId)
		{
			$permission = "yes";
			$log->debug("Exiting isPermitted method ...");
			return $permission;
		}
		//crmv@17001 : Private Permissions
		if ($module == 'Calendar') {
			// crmv@70053
			$visibility = $adb->query_result($adb->query('select visibility from '.$table_prefix.'_activity where activityid = '.$record_id),0,'visibility');
			if ($visibility == 'Private') {
				$permission = "no";
				$log->debug("Exiting isPermitted method ...");
				return $permission;
			} elseif ($visibility == 'Public') {
				$permission = "yes";
				$log->debug("Exiting isPermitted method ...");
				return $permission;
			}
			// crmv@70053e
		}
		//crmv@17001e
		//Checking if the Record Owner is the Subordinate User
		foreach($subordinate_roles_users as $roleid=>$userids)
		{
			if(in_array($recOwnId,$userids))
			{
				$permission='yes';
				$log->debug("Exiting isPermitted method ...");
				return $permission;
			}

		}
	}
	elseif($recOwnType == 'Groups')
	{
		//Checking if the record owner is the current user's group
		if(in_array($recOwnId,$current_user_groups))
		{
			$permission='yes';
			$log->debug("Exiting isPermitted method ...");
			return $permission;
		}
	}

	//Checking for Default Org Sharing permission
	if($others_permission_id == 0)
	{
		if($actionid == 1 || $actionid == 0)
		{

			if($module == 'Calendar')
			{
				if($recOwnType == 'Users')
				{
					$permission = isCalendarPermittedBySharing($record_id);
				}
				else
				{
					$permission='no';
				}
			}
			else
			{
				$permission = isReadWritePermittedBySharing($module,$tabid,$actionid,$record_id);
			}
			$log->debug("Exiting isPermitted method ...");
			return $permission;
		}
		elseif($actionid == 2)
		{
			$permission = "no";
			$log->debug("Exiting isPermitted method ...");
			return $permission;
		}
		else
		{
			$permission = "yes";
			$log->debug("Exiting isPermitted method ...");
			return $permission;
		}
	}
	elseif($others_permission_id == 1)
	{
		if($actionid == 2)
		{
			$permission = "no";
			$log->debug("Exiting isPermitted method ...");
			return $permission;
		}
		else
		{
			$permission = "yes";
			$log->debug("Exiting isPermitted method ...");
			return $permission;
		}
	}
	elseif($others_permission_id == 2)
	{

		$permission = "yes";
		$log->debug("Exiting isPermitted method ...");
		return $permission;
	}
	elseif($others_permission_id == 3)
	{

		if($actionid == 3 || $actionid == 4)
		{
			if($module == 'Calendar')
			{
				if($recOwnType == 'Users')
				{
					$permission = isCalendarPermittedBySharing($record_id);
				}
				else
				{
					$permission='no';
				}
				//crmv@17001 : Inviti	//crmv@20324	//crmv@25593
				if ($permission != 'yes') {
					$permission = isCalendarInvited($current_user->id,$record_id);
					$permission = $permission[0];
				}
				//crmv@17001e	//crmv@20324e	//crmv@25593e
			}
			else
			{
				$permission = isReadPermittedBySharing($module,$tabid,$actionid,$record_id);
			}
			$log->debug("Exiting isPermitted method ...");
			return $permission;
		}
		elseif($actionid ==0 || $actionid ==1)
		{
			if($module == 'Calendar')
			{
				$permission='no';
			}
			else
			{
				$permission = isReadWritePermittedBySharing($module,$tabid,$actionid,$record_id);
			}
			$log->debug("Exiting isPermitted method ...");
			return $permission;
		}
		elseif($actionid ==2)
		{
				$permission ="no";
				return $permission;
		}
		else
		{
			$permission = "yes";
			$log->debug("Exiting isPermitted method ...");
			return $permission;
		}
	}
	else
	{
		$permission = "yes";
	}

	$log->debug("Exiting isPermitted method ...");
	return $permission;

}

/** Function to check if the currently logged in user has Read Access due to Sharing for the specified record
  * @param $module -- Module Name:: Type varchar
  * @param $actionid -- Action Id:: Type integer
  * @param $recordid -- Record Id:: Type integer
  * @param $tabid -- Tab Id:: Type integer
  * @returns yes or no. If Yes means this action is allowed for the currently logged in user. If no means this action is not allowed for the currently logged in user
 */
function isReadPermittedBySharing($module,$tabid,$actionid,$record_id)
{
	global $log;
	$log->debug("Entering isReadPermittedBySharing(".$module.",".$tabid.",".$actionid.",".$record_id.") method ...");
	global $adb;
	global $current_user;
	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
	$ownertype='';
	$ownerid='';
	$sharePer='no';

	if ($module == 'Projects')	return 'yes';

	$sharingModuleList=getSharingModuleList();
	if(!in_array($module,$sharingModuleList))
	{
		$sharePer='no';
		return $sharePer;
	}

	$recordOwnerArr=getRecordOwnerId($record_id);
	foreach($recordOwnerArr as $type=>$id)
	{
		$ownertype=$type;
		$ownerid=$id;
	}

	$varname=$module."_share_read_permission";
	$read_per_arr=$$varname;
	if($ownertype == 'Users')
	{
		//Checking the Read Sharing Permission Array in Role Users
		$read_role_per=$read_per_arr['ROLE'];
		foreach($read_role_per as $roleid=>$userids)
		{
			if(in_array($ownerid,$userids))
			{
				$sharePer='yes';
				$log->debug("Exiting isReadPermittedBySharing method ...");
				return $sharePer;
			}

		}

		//Checking the Read Sharing Permission Array in Groups Users
		$read_grp_per=$read_per_arr['GROUP'];
		foreach($read_grp_per as $grpid=>$userids)
		{
			if(in_array($ownerid,$userids))
			{
				$sharePer='yes';
				$log->debug("Exiting isReadPermittedBySharing method ...");
				return $sharePer;
			}

		}
//crmv@7222
		$read_usr_per=$read_per_arr['USR'];
		if (is_array($read_usr_per)){
			if(in_array($ownerid,$read_usr_per))
			{
				$sharePer='yes';
				$log->debug("Exiting isReadPermittedBySharing method ...");
				return $sharePer;
			}
		}
//crmv@7222e
	}
	elseif($ownertype == 'Groups')
	{
		$read_grp_per=$read_per_arr['GROUP'];
		if(array_key_exists($ownerid,$read_grp_per))
		{
			$sharePer='yes';
			$log->debug("Exiting isReadPermittedBySharing method ...");
			return $sharePer;
		}
	}

	//crmv@7221
	if (isAdvancedShared($record_id,$module,'read') == 'yes') {
		$log->debug("Exiting isReadPermittedBySharing method ...");
		return 'yes';
	}
	//crmv@7221e

	//Checking for the Related Sharing Permission
	// crmv@73480 - removed, it was not configurable through interface

	//crmv@7221
	$relatedModuleArray=$related_module_adv_share[$tabid];
	if(is_array($relatedModuleArray))
	{
		foreach($relatedModuleArray as $parModId)
		{
			if (isParentAdvancedShared($record_id,$module,$parModId,'read') == 'yes') {
			$log->debug("Exiting isReadPermittedBySharing method ...");
			return 'yes';
			}
		}
	}
//crmv@7221e
	$log->debug("Exiting isReadPermittedBySharing method ...");
	return $sharePer;
}



/** Function to check if the currently logged in user has Write Access due to Sharing for the specified record
  * @param $module -- Module Name:: Type varchar
  * @param $actionid -- Action Id:: Type integer
  * @param $recordid -- Record Id:: Type integer
  * @param $tabid -- Tab Id:: Type integer
  * @returns yes or no. If Yes means this action is allowed for the currently logged in user. If no means this action is not allowed for the currently logged in user
 */
function isReadWritePermittedBySharing($module,$tabid,$actionid,$record_id)
{
	global $log;
	$log->debug("Entering isReadWritePermittedBySharing(".$module.",".$tabid.",".$actionid.",".$record_id.") method ...");
	global $adb;
	global $current_user;
	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
	$ownertype='';
	$ownerid='';
	$sharePer='no';
	if ($module == 'Projects')	return 'yes';

	$sharingModuleList=getSharingModuleList();
	if(! in_array($module,$sharingModuleList))
	{
    	$sharePer='no';
        return $sharePer;
    }

	$recordOwnerArr=getRecordOwnerId($record_id);
	foreach($recordOwnerArr as $type=>$id)
	{
		$ownertype=$type;
		$ownerid=$id;
	}

	$varname=$module."_share_write_permission";
	$write_per_arr=$$varname;

	if($ownertype == 'Users')
	{
		//Checking the Write Sharing Permission Array in Role Users
		$write_role_per=$write_per_arr['ROLE'];
		foreach($write_role_per as $roleid=>$userids)
		{
			if(in_array($ownerid,$userids))
			{
				$sharePer='yes';
				$log->debug("Exiting isReadWritePermittedBySharing method ...");
				return $sharePer;
			}

		}
//crmv@7222
		$write_usr_per=$write_per_arr['USR'];
		if (is_array($write_usr_per)){
			if(in_array($ownerid,$write_usr_per))
			{
				$sharePer='yes';
				$log->debug("Exiting isReadWritePermittedBySharing method ...");
				return $sharePer;
			}
		}
//crmv@7222e
		//Checking the Write Sharing Permission Array in Groups Users
		$write_grp_per=$write_per_arr['GROUP'];
		foreach($write_grp_per as $grpid=>$userids)
		{
			if(in_array($ownerid,$userids))
			{
				$sharePer='yes';
				$log->debug("Exiting isReadWritePermittedBySharing method ...");
				return $sharePer;
			}
		}
	}
	elseif($ownertype == 'Groups')
	{
		$write_grp_per=$write_per_arr['GROUP'];
		if(array_key_exists($ownerid,$write_grp_per))
		{
			$sharePer='yes';
			$log->debug("Exiting isReadWritePermittedBySharing method ...");
			return $sharePer;
		}
	}

	//crmv@7221
	if (isAdvancedShared($record_id,$module,'write') == 'yes') {
		$log->debug("Exiting isReadWritePermittedBySharing method ...");
		return 'yes';
	}
	//crmv@7221e

	//Checking for the Related Sharing Permission
	// crmv@73480 - removed, it was not configurable through interface

	//crmv@7221
	$relatedModuleArray=$related_module_adv_share[$tabid];
	if(is_array($relatedModuleArray))
	{
		foreach($relatedModuleArray as $parModId)
		{
			if (isParentAdvancedShared($record_id,$module,$parModId,'write') == 'yes') {
			$log->debug("Exiting isReadWritePermittedBySharing method ...");
			return 'yes';
			}
		}
	}
	//crmv@7221e
	$log->debug("Exiting isReadWritePermittedBySharing method ...");
	return $sharePer;
}

/** Function to check if the outlook user is permitted to perform the specified action
  * @param $module -- Module Name:: Type varchar
  * @param $actionname -- Action Name:: Type varchar
  * @param $recordid -- Record Id:: Type integer
  * @returns yes or no. If Yes means this action is allowed for the currently logged in user. If no means this action is not allowed for the currently logged in user
  *
 */
function isAllowed_Outlook($module,$action,$user_id,$record_id)
{
	global $log;
	$log->debug("Entering isAllowed_Outlook(".$module.",".$action.",".$user_id.",".$record_id.") method ...");

	$permission = "no";
	if($module == 'Users' || $module == 'Home' || $module == 'Administration' || $module == 'uploads' ||  $module == 'Settings' || $module == 'Calendar')
	{
		//These modules done have security
		$permission = "yes";

	}
	else
	{
		global $adb;
		global $current_user;
		$tabid = getTabid($module);
		$actionid = getActionid($action);
		$profile_id = fetchUserProfileId($user_id);
		$tab_per_Data = getAllTabsPermission($profile_id);

		$permissionData = getTabsActionPermission($profile_id);
		$defSharingPermissionData = getDefaultSharingAction();
		$others_permission_id = $defSharingPermissionData[$tabid];

		//Checking whether this vtiger_tab is allowed
		if($tab_per_Data[$tabid] == 0)
		{
			$permission = 'yes';
			//Checking whether this action is allowed
			if($permissionData[$tabid][$actionid] == 0)
			{
				$permission = 'yes';
				$rec_owner_id = '';
				if($record_id != '' && $module != 'Products' && $module != 'Faq')
				{
					$rec_owner_id = getUserId($record_id);
				}

				if($record_id != '' && $others_permission_id != '' && $module != 'Products' && $module != 'Faq' && $rec_owner_id != 0)
				{
					if($rec_owner_id != $current_user->id)
					{
						if($others_permission_id == 0)
						{
							if($action == 'EditView' || $action == 'Delete')
							{
								$permission = "no";
							}
							else
							{
								$permission = "yes";
							}
						}
						elseif($others_permission_id == 1)
						{
							if($action == 'Delete')
							{
								$permission = "no";
							}
							else
							{
								$permission = "yes";
							}
						}
						elseif($others_permission_id == 2)
						{

							$permission = "yes";
						}
						elseif($others_permission_id == 3)
						{
							if($action == 'DetailView' || $action == 'EditView' || $action == 'Delete')
							{
								$permission = "no";
							}
							else
							{
								$permission = "yes";
							}
						}


					}
					else
					{
						$permission = "yes";
					}
				}
			}
			else
			{
				$permission = "no";
			}
		}
		else
		{
			$permission = "no";
		}
	}
	$log->debug("Exiting isAllowed_Outlook method ...");
	return $permission;

}


/** Function to get the Profile Global Information for the specified vtiger_profileid
  * @param $profileid -- Profile Id:: Type integer
  * @returns Profile Gloabal Permission Array in the following format:
  * $profileGloblaPermisson=Array($viewall_actionid=>permission, $editall_actionid=>permission)
 */
function getProfileGlobalPermission($profileid)
{
global $log;
$log->debug("Entering getProfileGlobalPermission(".$profileid.") method ...");
  global $adb,$table_prefix;
  $sql = "select * from ".$table_prefix."_profile2globalperm where profileid=?" ;
  $result = $adb->pquery($sql, array($profileid));
  $num_rows = $adb->num_rows($result);

  for($i=0; $i<$num_rows; $i++)
  {
	$act_id = $adb->query_result($result,$i,"globalactionid");
	$per_id = $adb->query_result($result,$i,"globalactionpermission");
	$copy[$act_id] = $per_id;
  }

	$log->debug("Exiting getProfileGlobalPermission method ...");
   return $copy;

}

/** Function to get the Profile Tab Permissions for the specified vtiger_profileid
  * @param $profileid -- Profile Id:: Type integer
  * @returns Profile Tabs Permission Array in the following format:
  * $profileTabPermisson=Array($tabid1=>permission, $tabid2=>permission,........., $tabidn=>permission)
 */
function getProfileTabsPermission($profileid)
{
	global $log;
	$log->debug("Entering getProfileTabsPermission(".$profileid.") method ...");
	global $adb,$table_prefix;
	$sql = "select * from ".$table_prefix."_profile2tab where profileid=?" ;
	$result = $adb->pquery($sql, array($profileid));
	$num_rows = $adb->num_rows($result);

	for($i=0; $i<$num_rows; $i++)
	{
		$tab_id = $adb->query_result($result,$i,"tabid");
		$per_id = $adb->query_result($result,$i,"permissions");
		$copy[$tab_id] = $per_id;
	}

	$log->debug("Exiting getProfileTabsPermission method ...");
	return $copy;

}


/** Function to get the Profile Action Permissions for the specified vtiger_profileid
  * @param $profileid -- Profile Id:: Type integer
  * @returns Profile Tabs Action Permission Array in the following format:
  *    $tabActionPermission = Array($tabid1=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission),
  *                        $tabid2=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission),
  *                                |
  *                        $tabidn=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission))
 */
function getProfileActionPermission($profileid)
{
	global $log;
	$log->debug("Entering getProfileActionPermission(".$profileid.") method ...");
	global $adb,$table_prefix;
	$check = Array();
	$temp_tabid = Array();
	$sql1 = "select * from ".$table_prefix."_profile2standardperm where profileid=? order by tabid";	//crmv@fix
	$result1 = $adb->pquery($sql1, array($profileid));
	$num_rows1 = $adb->num_rows($result1);
	for($i=0; $i<$num_rows1; $i++)
	{
		$tab_id = $adb->query_result($result1,$i,'tabid');
		if(! in_array($tab_id,$temp_tabid))
		{
			$temp_tabid[] = $tab_id;
			$access = Array();
		}
		$action_id = $adb->query_result($result1,$i,'operation');
		$per_id = $adb->query_result($result1,$i,'permissions');
		$access[$action_id] = $per_id;
		$check[$tab_id] = $access;
	}
	$log->debug("Exiting getProfileActionPermission method ...");
	return $check;
}


/** Function to get the Standard and Utility Profile Action Permissions for the specified vtiger_profileid
  * @param $profileid -- Profile Id:: Type integer
  * @returns Profile Tabs Action Permission Array in the following format:
  *    $tabActionPermission = Array($tabid1=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission),
  *                        $tabid2=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission),
  *                                |
  *                        $tabidn=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission))
 */
function getProfileAllActionPermission($profileid)
{
	global $log;
	$log->debug("Entering getProfileAllActionPermission(".$profileid.") method ...");
	global $adb;
	$actionArr=getProfileActionPermission($profileid);
	$utilArr=getTabsUtilityActionPermission($profileid);
	foreach($utilArr as $tabid=>$act_arr)
	{
		$act_tab_arr=$actionArr[$tabid];
		foreach($act_arr as $utilid=>$util_perr)
		{
			$act_tab_arr[$utilid]=$util_perr;
		}
		$actionArr[$tabid]=$act_tab_arr;
	}
	$log->debug("Exiting getProfileAllActionPermission method ...");
	return $actionArr;
}


/** Function to create vtiger_profile
  * @param $profilename -- Profile Name:: Type varchar
  * @param $parentProfileId -- Profile Id:: Type integer
 */
function createProfile($profilename,$parentProfileId,$description, $mobile = 0) { //crmv@39110
global $log, $metaLogs; // crmv@49398
$log->debug("Entering createProfile(".$profilename.",".$parentProfileId.",".$description.") method ...");
	global $adb,$table_prefix;
	$current_profile_id = $adb->getUniqueID($table_prefix."_profile");
	//Inserting values into Profile Table
	//crmv@39110
	$sql1 = "insert into ".$table_prefix."_profile (profileid, profilename, description, mobile) values(?,?,?,?)";
	$params1 = array($current_profile_id, $profilename, $description, $mobile);
	//crmv@39110e
	$adb->pquery($sql1, $params1);

	//Inserting values into vtiger_profile2globalperm
	$sql3 = "select * from ".$table_prefix."_profile2globalperm where profileid=?";
	$params3 = array($parentProfileId);
	$result3= $adb->pquery($sql3, $params3);
	$p2tab_rows = $adb->num_rows($result3);
	for($i=0; $i<$p2tab_rows; $i++)
	{
		$act_id=$adb->query_result($result3,$i,'globalactionid');
		$permissions=$adb->query_result($result3,$i,'globalactionpermission');
		$sql4="insert into ".$table_prefix."_profile2globalperm values(?,?,?)";
		$params4 = array($current_profile_id, $act_id, $permissions);
		$adb->pquery($sql4, $params4);
	}

	//Inserting values into Profile2tab vtiger_table
	$sql3 = "select * from ".$table_prefix."_profile2tab where profileid=?";
	$params3 = array($parentProfileId);
	$result3= $adb->pquery($sql3, $params3);
	$p2tab_rows = $adb->num_rows($result3);
	for($i=0; $i<$p2tab_rows; $i++)
	{
		$tab_id=$adb->query_result($result3,$i,'tabid');
		$permissions=$adb->query_result($result3,$i,'permissions');
		$sql4="insert into ".$table_prefix."_profile2tab values(?,?,?)";
		$params4 = array($current_profile_id, $tab_id, $permissions);
		$adb->pquery($sql4, $params4);
	}

	//Inserting values into Profile2standard vtiger_table
	$sql6 = "select * from ".$table_prefix."_profile2standardperm where profileid=?";
	$params6 = array($parentProfileId);
	$result6= $adb->pquery($sql6, $params6);
	$p2per_rows = $adb->num_rows($result6);
	for($i=0; $i<$p2per_rows; $i++)
	{
		$tab_id=$adb->query_result($result6,$i,'tabid');
		$action_id=$adb->query_result($result6,$i,'operation');
		$permissions=$adb->query_result($result6,$i,'permissions');
		$sql7="insert into ".$table_prefix."_profile2standardperm values(?,?,?,?)";
		$params7 = array($current_profile_id, $tab_id, $action_id, $permissions);
		$adb->pquery($sql7, $params7);
	}

	//Inserting values into Profile2Utility vtiger_table
	$sql8 = "select * from ".$table_prefix."_profile2utility where profileid=?";
	$params8 = array($parentProfileId);
	$result8= $adb->pquery($sql8, $params8);
	$p2util_rows = $adb->num_rows($result8);
	for($i=0; $i<$p2util_rows; $i++)
	{
		$tab_id=$adb->query_result($result8,$i,'tabid');
		$action_id=$adb->query_result($result8,$i,'activityid');
		$permissions=$adb->query_result($result8,$i,'permission');
		$sql9="insert into ".$table_prefix."_profile2utility values(?,?,?,?)";
		$params9 = array($current_profile_id, $tab_id, $action_id, $permissions);
		$adb->pquery($sql9, $params9);
	}

	//Inserting values into Profile2field vtiger_table
	$sql10 = "select * from ".$table_prefix."_profile2field where profileid=?";
	$params10 = array($parentProfileId);
	$result10= $adb->pquery($sql10, $params10);
	$p2field_rows = $adb->num_rows($result10);
	for($i=0; $i<$p2field_rows; $i++)
	{
		$tab_id=$adb->query_result($result10,$i,'tabid');
		$fieldid=$adb->query_result($result10,$i,'fieldid');
		$permissions=$adb->query_result($result10,$i,'visible');
		$readonly=$adb->query_result($result10,$i,'readonly');
		// crmv@39110
		$sequence = $adb->query_result($result10,$i,'sequence');
		$sql11="insert into ".$table_prefix."_profile2field (profileid, tabid, fieldid, visible, readonly, sequence) values(?,?,?,?,?,?)";
		$params11 = array($current_profile_id, $tab_id, $fieldid, $permissions ,$readonly, (empty($sequence) ? $i : $sequence));
		// crmv@39110e
		$adb->pquery($sql11, $params11);
	}
	if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_ADDPROFILE, $current_profile_id); // crmv@49398
	$log->debug("Exiting createProfile method ...");
}

/** Function to delete vtiger_profile
  * @param $transfer_profileid -- Profile Id to which the existing vtiger_role2profile relationships are to be transferred :: Type varchar
  * @param $prof_id -- Profile Id to be deleted:: Type integer
 */
function deleteProfile($prof_id,$transfer_profileid='')
{
	global $log, $metaLogs; // crmv@49398
$log->debug("Entering deleteProfile(".$prof_id.",".$transfer_profileid.") method ...");
	global $adb,$table_prefix;
	//delete from vtiger_profile2global permissions
	$sql4 = "delete from ".$table_prefix."_profile2globalperm where profileid=?";
	$adb->pquery($sql4, array($prof_id));

	//deleting from vtiger_profile 2 vtiger_tab;
	$sql4 = "delete from ".$table_prefix."_profile2tab where profileid=?";
	$adb->pquery($sql4, array($prof_id));

	//deleting from vtiger_profile2standardperm vtiger_table
	$sql5 = "delete from ".$table_prefix."_profile2standardperm where profileid=?";
	$adb->pquery($sql5, array($prof_id));

	//deleting from vtiger_profile2field
	$sql6 ="delete from ".$table_prefix."_profile2field where profileid=?";
	$adb->pquery($sql6, array($prof_id));

	//deleting from vtiger_profile2utility
	$sql7 ="delete from ".$table_prefix."_profile2utility where profileid=?";
	$adb->pquery($sql7, array($prof_id));

	// crmv@39110
	$sql7 ="delete from ".$table_prefix."_profile2related where profileid=?";
	$adb->pquery($sql7, array($prof_id));

	$sql7 ="delete from ".$table_prefix."_profile2entityname where profileid=?";
	$adb->pquery($sql7, array($prof_id));
	// crmv@39110e

	//updating vtiger_role2profile
        if(isset($transfer_profileid) && $transfer_profileid != '')
        {

                $sql8 = "select roleid from ".$table_prefix."_role2profile where profileid=?";
				$result = $adb->pquery($sql8, array($prof_id));
                $num_rows=$adb->num_rows($result);

                for($i=0;$i<$num_rows;$i++)
                {
                        $roleid=$adb->query_result($result,$i,'roleid');
                        $sql = "select profileid from ".$table_prefix."_role2profile where roleid=?";
                        $profresult=$adb->pquery($sql, array($roleid));
                        $num=$adb->num_rows($profresult);
                        if($num>1)
                        {
                                $sql10="delete from ".$table_prefix."_role2profile where roleid=? and profileid=?";
								$adb->pquery($sql10, array($roleid, $prof_id));
                        }
                        else
                        {
                                $sql8 = "update ".$table_prefix."_role2profile set profileid=? where profileid=? and roleid=?";
                                $adb->pquery($sql8, array($transfer_profileid, $prof_id, $roleid));
                        }

                }
        }

	//delete from vtiger_profile vtiger_table;
	$sql9 = "delete from ".$table_prefix."_profile where profileid=?";
	$adb->pquery($sql9, array($prof_id));

	if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_DELPROFILE, $prof_id); // crmv@49398
	$log->debug("Exiting deleteProfile method ...");
}

/** Function to get all  the vtiger_role information
  * @returns $allRoleDetailArray-- Array will contain the details of all the vtiger_roles. RoleId will be the key:: Type array
 */
function getAllRoleDetails()
{
global $log;
$log->debug("Entering getAllRoleDetails() method ...");
	global $adb,$table_prefix;
	$role_det = Array();
	$query = "select * from ".$table_prefix."_role";
	$result = $adb->pquery($query, array());
	$num_rows=$adb->num_rows($result);
	for($i=0; $i<$num_rows;$i++)
	{
		$each_role_det = Array();
		$roleid=$adb->query_result($result,$i,'roleid');
		$rolename=$adb->query_result($result,$i,'rolename');
		$roledepth=$adb->query_result($result,$i,'depth');
		$sub_roledepth=$roledepth + 1;
		$parentrole=$adb->query_result($result,$i,'parentrole');
		$sub_role='';

		//getting the immediate subordinates
		$query1="select * from ".$table_prefix."_role where parentrole like ? and depth=?";
		$res1 = $adb->pquery($query1, array($parentrole."::%", $sub_roledepth));
		$num_roles = $adb->num_rows($res1);
		if($num_roles > 0)
		{
			for($j=0; $j<$num_roles; $j++)
			{
				if($j == 0)
				{
					$sub_role .= $adb->query_result($res1,$j,'roleid');
				}
				else
				{
					$sub_role .= ','.$adb->query_result($res1,$j,'roleid');
				}
			}
		}


		$each_role_det[]=$rolename;
		$each_role_det[]=$roledepth;
		$each_role_det[]=$sub_role;
		$role_det[$roleid]=$each_role_det;

	}
	$log->debug("Exiting getAllRoleDetails method ...");
	return $role_det;
}


/** Function to get all  the vtiger_profile information
  * @returns $allProfileInfoArray-- Array will contain the details of all the vtiger_profiles. Profile ID will be the key:: Type array
 */
function getAllProfileInfo($onlymobile = false) { // crmv@39110
	global $log;
	$log->debug("Entering getAllProfileInfo() method ...");
	global $adb,$table_prefix;
	// crmv@39110
	if ($onlymobile) {
		$query="select * from ".$table_prefix."_profile where mobile = ?";
		$result = $adb->pquery($query, array(1));
	} else {
		$query="select * from ".$table_prefix."_profile";
		$result = $adb->query($query);
	}
	// crmv@39110
	$num_rows=$adb->num_rows($result);
	$prof_details=Array();
	for($i=0;$i<$num_rows;$i++)
	{
		$profileid=$adb->query_result($result,$i,'profileid');
		$profilename=$adb->query_result($result,$i,'profilename');
		$prof_details[$profileid]=$profilename;

	}
	$log->debug("Exiting getAllProfileInfo method ...");
	return $prof_details;
}

/** Function to get the vtiger_role information of the specified vtiger_role
  * @param $roleid -- RoleId :: Type varchar
  * @returns $roleInfoArray-- RoleInfoArray in the following format:
  *       $roleInfo=Array($roleId=>Array($rolename,$parentrole,$roledepth,$immediateParent));
 */
function getRoleInformation($roleid)
{
	global $log;
	$log->debug("Entering getRoleInformation(".$roleid.") method ...");
	global $adb,$table_prefix;
	$query = "select * from ".$table_prefix."_role where roleid=?";
	$result = $adb->pquery($query, array($roleid));
	$rolename=$adb->query_result($result,0,'rolename');
	$parentrole=$adb->query_result($result,0,'parentrole');
	$roledepth=$adb->query_result($result,0,'depth');
	$parentRoleArr=explode('::',$parentrole);
	$immediateParent=$parentRoleArr[sizeof($parentRoleArr)-2];
	$roleDet=Array();
	$roleDet[]=$rolename;
	$roleDet[]=$parentrole;
	$roleDet[]=$roledepth;
	$roleDet[]=$immediateParent;
	$roleInfo=Array();
	$roleInfo[$roleid]=$roleDet;
	$log->debug("Exiting getRoleInformation method ...");
	return $roleInfo;
}


/** Function to get the vtiger_role related vtiger_profiles
  * @param $roleid -- RoleId :: Type varchar
  * @returns $roleProfiles-- Role Related Profile Array in the following format:
  *       $roleProfiles=Array($profileId1=>$profileName,$profileId2=>$profileName,........,$profileIdn=>$profileName));
 */
function getRoleRelatedProfiles($roleId, $mobile=0) { // crmv@39110
	global $log, $adb,$table_prefix;
	$log->debug("Entering getRoleRelatedProfiles(".$roleId.") method ...");
	// crmv@39110
	$query = "select {$table_prefix}_role2profile.*,".$table_prefix."_profile.profilename from ".$table_prefix."_role2profile inner join ".$table_prefix."_profile on ".$table_prefix."_profile.profileid=".$table_prefix."_role2profile.profileid where roleid=? and {$table_prefix}_profile.mobile = ? and {$table_prefix}_role2profile.mobile = ?";
	$result = $adb->pquery($query, array($roleId, $mobile, $mobile));
	// crmv@39110e
	$num_rows=$adb->num_rows($result);
	$roleRelatedProfiles = Array();
	for($i=0; $i<$num_rows; $i++) {
		$roleRelatedProfiles[$adb->query_result($result,$i,'profileid')] = $adb->query_result($result,$i,'profilename');
	}
	$log->debug("Exiting getRoleRelatedProfiles method ...");
	return $roleRelatedProfiles;
}


/** Function to get the vtiger_role related vtiger_users
  * @param $roleid -- RoleId :: Type varchar
  * @returns $roleUsers-- Role Related User Array in the following format:
  *       $roleUsers=Array($userId1=>$userName,$userId2=>$userName,........,$userIdn=>$userName));
 */
function getRoleUsers($roleId)
{
	global $log;
	$log->debug("Entering getRoleUsers(".$roleId.") method ...");
	global $adb,$table_prefix;
	$query = "select ".$table_prefix."_user2role.*,".$table_prefix."_users.user_name from ".$table_prefix."_user2role inner join ".$table_prefix."_users on ".$table_prefix."_users.id=".$table_prefix."_user2role.userid where roleid=?";
	$result = $adb->pquery($query, array($roleId));
	$num_rows=$adb->num_rows($result);
	$roleRelatedUsers=Array();
	for($i=0; $i<$num_rows; $i++)
	{
		$roleRelatedUsers[$adb->query_result($result,$i,'userid')]=$adb->query_result($result,$i,'user_name');
	}
	$log->debug("Exiting getRoleUsers method ...");
	return $roleRelatedUsers;


}


/** Function to get the vtiger_role related user ids
  * @param $roleid -- RoleId :: Type varchar
  * @returns $roleUserIds-- Role Related User Array in the following format:
  *       $roleUserIds=Array($userId1,$userId2,........,$userIdn);
 */

function getRoleUserIds($roleId)
{
	global $log;
	$log->debug("Entering getRoleUserIds(".$roleId.") method ...");
	global $adb,$table_prefix;
	$query = "select ".$table_prefix."_user2role.*,".$table_prefix."_users.user_name from ".$table_prefix."_user2role inner join ".$table_prefix."_users on ".$table_prefix."_users.id=".$table_prefix."_user2role.userid where roleid=?";
	$result = $adb->pquery($query, array($roleId));
	$num_rows=$adb->num_rows($result);
	$roleRelatedUsers=Array();
	for($i=0; $i<$num_rows; $i++)
	{
		$roleRelatedUsers[]=$adb->query_result($result,$i,'userid');
	}
	$log->debug("Exiting getRoleUserIds method ...");
	return $roleRelatedUsers;


}

/** Function to get the vtiger_role and subordinate vtiger_users
  * @param $roleid -- RoleId :: Type varchar
  * @returns $roleSubUsers-- Role and Subordinates Related Users Array in the following format:
  *       $roleSubUsers=Array($userId1=>$userName,$userId2=>$userName,........,$userIdn=>$userName));
 */
function getRoleAndSubordinateUsers($roleId,$check_active = false) //crmv@66098
{
	global $log;
	$log->debug("Entering getRoleAndSubordinateUsers(".$roleId.") method ...");
	global $adb,$table_prefix;
	$roleInfoArr=getRoleInformation($roleId);
	$parentRole=$roleInfoArr[$roleId][1];
	//crmv@66098
	if ($check_active == true) {
		$userstatus = " AND ".$table_prefix."_users.status = 'Active' ";
	}
	//crmv@66098e
	$query = "select ".$table_prefix."_user2role.*,".$table_prefix."_users.user_name 
				from ".$table_prefix."_user2role 
				inner join ".$table_prefix."_users on ".$table_prefix."_users.id=".$table_prefix."_user2role.userid 
				inner join ".$table_prefix."_role on ".$table_prefix."_role.roleid=".$table_prefix."_user2role.roleid 
				where ".$table_prefix."_role.parentrole like ? $userstatus"; //crmv@66098
	$query .= " ORDER BY {$table_prefix}_users.user_name";
	$result = $adb->pquery($query, array($parentRole."%"));
	$num_rows=$adb->num_rows($result);
	$roleRelatedUsers=Array();
	for($i=0; $i<$num_rows; $i++)
	{
		$roleRelatedUsers[$adb->query_result($result,$i,'userid')]=$adb->query_result($result,$i,'user_name');
	}
	$log->debug("Exiting getRoleAndSubordinateUsers method ...");
	return $roleRelatedUsers;


}


/** Function to get the vtiger_role and subordinate user ids
  * @param $roleid -- RoleId :: Type varchar
  * @returns $roleSubUserIds-- Role and Subordinates Related Users Array in the following format:
  *       $roleSubUserIds=Array($userId1,$userId2,........,$userIdn);
 */
function getRoleAndSubordinateUserIds($roleId)
{
	global $log;
	$log->debug("Entering getRoleAndSubordinateUserIds(".$roleId.") method ...");
	global $adb,$table_prefix;
	$roleInfoArr=getRoleInformation($roleId);
	$parentRole=$roleInfoArr[$roleId][1];
	$query = "select ".$table_prefix."_user2role.*,".$table_prefix."_users.user_name from ".$table_prefix."_user2role inner join ".$table_prefix."_users on ".$table_prefix."_users.id=".$table_prefix."_user2role.userid inner join ".$table_prefix."_role on ".$table_prefix."_role.roleid=".$table_prefix."_user2role.roleid where ".$table_prefix."_role.parentrole like ?";
	$result = $adb->pquery($query, array($parentRole."%"));
	$num_rows=$adb->num_rows($result);
	$roleRelatedUsers=Array();
	for($i=0; $i<$num_rows; $i++)
	{
		$roleRelatedUsers[]=$adb->query_result($result,$i,'userid');
	}
	$log->debug("Exiting getRoleAndSubordinateUserIds method ...");
	return $roleRelatedUsers;


}

/** Function to get the vtiger_role and subordinate Information for the specified vtiger_roleId
  * @param $roleid -- RoleId :: Type varchar
  * @returns $roleSubInfo-- Role and Subordinates Information array in the following format:
  *       $roleSubInfo=Array($roleId1=>Array($rolename,$parentrole,$roledepth,$immediateParent), $roleId2=>Array($rolename,$parentrole,$roledepth,$immediateParent),.....);
 */
function getRoleAndSubordinatesInformation($roleId)
{
	global $log;
	$log->debug("Entering getRoleAndSubordinatesInformation(".$roleId.") method ...");
	global $adb,$table_prefix;
	$roleDetails=getRoleInformation($roleId);
	$roleInfo=$roleDetails[$roleId];
	$roleParentSeq=$roleInfo[1];

	$query="select * from ".$table_prefix."_role where parentrole like ? order by parentrole asc";
	$result=$adb->pquery($query, array($roleParentSeq."%"));
	$num_rows=$adb->num_rows($result);
	$roleInfo=Array();
	for($i=0;$i<$num_rows;$i++)
	{
		$roleid=$adb->query_result($result,$i,'roleid');
                $rolename=$adb->query_result($result,$i,'rolename');
                $roledepth=$adb->query_result($result,$i,'depth');
                $parentrole=$adb->query_result($result,$i,'parentrole');
		$roleDet=Array();
		$roleDet[]=$rolename;
		$roleDet[]=$parentrole;
		$roleDet[]=$roledepth;
		$roleInfo[$roleid]=$roleDet;

	}
	$log->debug("Exiting getRoleAndSubordinatesInformation method ...");
	return $roleInfo;

}


/** Function to get the vtiger_role and subordinate vtiger_role ids
  * @param $roleid -- RoleId :: Type varchar
  * @returns $roleSubRoleIds-- Role and Subordinates RoleIds in an Array in the following format:
  *       $roleSubRoleIds=Array($roleId1,$roleId2,........,$roleIdn);
 */
function getRoleAndSubordinatesRoleIds($roleId)
{
	global $log;
	$log->debug("Entering getRoleAndSubordinatesRoleIds(".$roleId.") method ...");
	global $adb,$table_prefix;
	$roleDetails=getRoleInformation($roleId);
	$roleInfo=$roleDetails[$roleId];
	$roleParentSeq=$roleInfo[1];

	$query="select * from ".$table_prefix."_role where parentrole like ? order by parentrole asc";
	$result=$adb->pquery($query, array($roleParentSeq."%"));
	$num_rows=$adb->num_rows($result);
	$roleInfo=Array();
	for($i=0;$i<$num_rows;$i++)
	{
		$roleid=$adb->query_result($result,$i,'roleid');
		$roleInfo[]=$roleid;

	}
	$log->debug("Exiting getRoleAndSubordinatesRoleIds method ...");
	return $roleInfo;

}

// crmv@49398
/** Function to get delete the spcified vtiger_role
  * @param $roleid -- RoleId :: Type varchar
  * @param $transferRoleId -- RoleId to which vtiger_users of the vtiger_role that is being deleted are transferred:: Type varchar
 */
function deleteRole($roleId,$transferRoleId) {
	global $log, $metaLogs;
	global $adb, $table_prefix;

	$log->debug("Entering deleteRole(".$roleId.",".$transferRoleId.") method ...");

	$roleInfo=getRoleAndSubordinatesInformation($roleId);
	foreach($roleInfo as $roleid=>$roleDetArr) {

		$sql1 = "update ".$table_prefix."_user2role set roleid=? where roleid=?";
		$adb->pquery($sql1, array($transferRoleId, $roleid));

		//Deleteing from vtiger_role2profile vtiger_table
		$sql2 = "delete from ".$table_prefix."_role2profile where roleid=?";
		$adb->pquery($sql2, array($roleid));

		//delete handling for vtiger_groups
		$sql10 = "delete from ".$table_prefix."_group2role where roleid=?";
		$adb->pquery($sql10, array($roleid));

		$sql11 = "delete from ".$table_prefix."_group2rs where roleandsubid=?";
		$adb->pquery($sql11, array($roleid));


		//delete handling for sharing rules
		deleteRoleRelatedSharingRules($roleid);

		//delete from vtiger_role vtiger_table;
		$sql9 = "delete from ".$table_prefix."_role where roleid=?";
		$adb->pquery($sql9, array($roleid));

		if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_DELROLE, 0, array('roleid'=>$roleid));

	}
	$log->debug("Exiting deleteRole method ...");
}
// crmv@49398

/** Function to delete the vtiger_role related sharing rules
  * @param $roleid -- RoleId :: Type varchar
 */
function deleteRoleRelatedSharingRules($roleId)
{
	global $log, $table_prefix;
	$log->debug("Entering deleteRoleRelatedSharingRules(".$roleId.") method ...");
        global $adb;
        $dataShareTableColArr=Array($table_prefix.'_datashare_grp2role'=>'to_roleid',
                                    $table_prefix.'_datashare_grp2rs'=>'to_roleandsubid',
                                    $table_prefix.'_datashare_role2group'=>'share_roleid',
                                    $table_prefix.'_datashare_role2role'=>'share_roleid::to_roleid',
                                    $table_prefix.'_datashare_role2rs'=>'share_roleid::to_roleandsubid',
                                    $table_prefix.'_datashare_rs2grp'=>'share_roleandsubid',
                                    $table_prefix.'_datashare_rs2role'=>'share_roleandsubid::to_roleid',
                                    $table_prefix.'_datashare_rs2rs'=>'share_roleandsubid::to_roleandsubid');

        foreach($dataShareTableColArr as $tablename=>$colname)
        {
                $colNameArr=explode('::',$colname);
                $query="select shareid from ".$tablename." where ".$colNameArr[0]."=?";
				$params = array($roleId);
                if(sizeof($colNameArr) >1)
                {
                        $query .=" or ".$colNameArr[1]."=?";
						array_push($params, $roleId);
                }

                $result=$adb->pquery($query, $params);
                $num_rows=$adb->num_rows($result);
                for($i=0;$i<$num_rows;$i++)
                {
                        $shareid=$adb->query_result($result,$i,'shareid');
                        deleteSharingRule($shareid);
                }

        }
	$log->debug("Exiting deleteRoleRelatedSharingRules method ...");
}

/** Function to delete the group related sharing rules
  * @param $roleid -- RoleId :: Type varchar
 */
function deleteGroupRelatedSharingRules($grpId)
{
	global $log;
	$log->debug("Entering deleteGroupRelatedSharingRules(".$grpId.") method ...");

        global $adb, $table_prefix;
        $dataShareTableColArr=Array($table_prefix.'_datashare_grp2grp'=>'share_groupid::to_groupid',
                                    $table_prefix.'_datashare_grp2role'=>'share_groupid',
                                    $table_prefix.'_datashare_grp2rs'=>'share_groupid',
                                    $table_prefix.'_datashare_role2group'=>'to_groupid',
                                    $table_prefix.'_datashare_rs2grp'=>'to_groupid');


        foreach($dataShareTableColArr as $tablename=>$colname)
        {
                $colNameArr=explode('::',$colname);
                $query="select shareid from ".$tablename." where ".$colNameArr[0]."=?";
				$params = array($grpId);
                if(sizeof($colNameArr) >1)
                {
                        $query .=" or ".$colNameArr[1]."=?";
						array_push($params, $grpId);
                }

                $result=$adb->pquery($query, $params);
                $num_rows=$adb->num_rows($result);
                for($i=0;$i<$num_rows;$i++)
                {
                        $shareid=$adb->query_result($result,$i,'shareid');
                        deleteSharingRule($shareid);
                }

        }
	$log->debug("Exiting deleteGroupRelatedSharingRules method ...");
}


/** Function to get userid and username of all vtiger_users
  * @returns $userArray -- User Array in the following format:
  * $userArray=Array($userid1=>$username, $userid2=>$username,............,$useridn=>$username);
 */
function getAllUserName()
{
	global $log;
	$log->debug("Entering getAllUserName() method ...");
	global $adb,$table_prefix;
	$query="select * from ".$table_prefix."_users where deleted=0 order by user_name";
	$result = $adb->pquery($query, array());
	$num_rows=$adb->num_rows($result);
	$user_details=Array();
	for($i=0;$i<$num_rows;$i++)
	{
		$userid=$adb->query_result($result,$i,'id');
		$username=$adb->query_result($result,$i,'user_name');
		$user_details[$userid]=$username;

	}
	$log->debug("Exiting getAllUserName method ...");
	return $user_details;

}


/** Function to get groupid and groupname of all vtiger_groups
  * @returns $grpArray -- Group Array in the following format:
  * $grpArray=Array($grpid1=>$grpname, $grpid2=>$grpname,............,$grpidn=>$grpname);
 */
function getAllGroupName()
{
	global $log;
	$log->debug("Entering getAllGroupName() method ...");
	global $adb,$table_prefix;
	$query="select groupid, groupname from ".$table_prefix."_groups"; // crmv@49398
	$result = $adb->pquery($query, array());
	$num_rows=$adb->num_rows($result);
	$group_details=Array();
	for($i=0;$i<$num_rows;$i++)
	{
		$grpid=$adb->query_result($result,$i,'groupid');
		$grpname=$adb->query_result($result,$i,'groupname');
		$group_details[$grpid]=$grpname;

	}
	$log->debug("Exiting getAllGroupName method ...");
	return $group_details;

}

/** Function to get groupid and groupname of all for the given groupid
  * @returns $grpArray -- Group Array in the following format:
  * $grpArray=Array($grpid1=>$grpname);
 */
function getGroupDetails($id)
{
	global $log;
	$log->debug("Entering getAllGroupDetails() method ...");
	global $adb,$table_prefix;
	$query="select * from ".$table_prefix."_groups where groupid = ?";
	$result = $adb->pquery($query, array($id));
	$num_rows=$adb->num_rows($result);
	if($num_rows < 1)
		return null;
	$group_details=Array();
	$grpid=$adb->query_result($result,0,'groupid');
	$grpname=$adb->query_result($result,0,'groupname');
	$grpdesc=$adb->query_result($result,0,'description');
	$group_details=Array($grpid,$grpname,$grpdesc);

	$log->debug("Exiting getAllGroupDetails method ...");
	return $group_details;

}
/** Function to get group information of all vtiger_groups
  * @returns $grpInfoArray -- Group Informaton array in the following format:
  * $grpInfoArray=Array($grpid1=>Array($grpname,description) $grpid2=>Array($grpname,description),............,$grpidn=>Array($grpname,description));
 */
function getAllGroupInfo()
{
	global $log;
	$log->debug("Entering getAllGroupInfo() method ...");
	global $adb,$table_prefix;
	$query="select * from ".$table_prefix."_groups order by groupname";
	$result = $adb->pquery($query, array());
	$num_rows=$adb->num_rows($result);
	$group_details=Array();
	for($i=0;$i<$num_rows;$i++)
	{
		$grpInfo=Array();
		$grpid=$adb->query_result($result,$i,'groupid');
		$grpname=$adb->query_result($result,$i,'groupname');
		$description=$adb->query_result($result,$i,'description');
		$grpInfo[0]=$grpname;
		$grpInfo[1]=$description;
		$group_details[$grpid]=$grpInfo;

	}
	$log->debug("Exiting getAllGroupInfo method ...");
	return $group_details;

}

/** Function to create a group
  * @param $groupName -- Group Name :: Type varchar
  * @param $groupMemberArray -- Group Members (Groups,Roles,RolesAndsubordinates,Users)
  * @param $groupName -- Group Name :: Type varchar
  * @returns $groupId -- Group Id :: Type integer
 */
function createGroup($groupName,$groupMemberArray,$description)
{
	global $log, $metaLogs; // crmv@49398
	$log->debug("Entering createGroup(".$groupName.",".$groupMemberArray.",".$description.") method ...");
	global $adb,$table_prefix;
	$groupId=$adb->getUniqueId($table_prefix."_users");
	//Insert into group vtiger_table
	// crmv@49398
	$now = date('Y-m-d H:i:s');
	$query = "insert into ".$table_prefix."_groups (groupid, groupname, description, date_entered, date_modified) values(?,?,?,?,?)";
	$adb->pquery($query, array($groupId, $groupName, $description, $now, $now));
	// crmv@49398e

	//Insert Group to Group Relation
	$groupArray=$groupMemberArray['groups'];
	$roleArray=$groupMemberArray['roles'];
	$rsArray=$groupMemberArray['rs'];
	$userArray=$groupMemberArray['users'];

	foreach($groupArray as $group_id)
	{
		insertGroupToGroupRelation($groupId,$group_id);
	}

	//Insert Group to Role Relation
	foreach($roleArray as $roleId)
	{
		insertGroupToRoleRelation($groupId,$roleId);
	}

	//Insert Group to RoleAndSubordinate Relation
	foreach($rsArray as $rsId)
	{
		insertGroupToRsRelation($groupId,$rsId);
	}

	//Insert Group to Role Relation
	foreach($userArray as $userId)
	{
		insertGroupToUserRelation($groupId,$userId);
	}
	if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_ADDGROUP, $groupId); // crmv@49398
	$log->debug("Exiting createGroup method ...");
	return $groupId;
}


/** Function to insert group to group relation
  * @param $groupId -- Group Id :: Type integer
  * @param $containsGroupId -- Group Id :: Type integer
 */
function insertGroupToGroupRelation($groupId,$containsGroupId)
{
	global $log;
	$log->debug("Entering insertGroupToGroupRelation(".$groupId.",".$containsGroupId.") method ...");
	global $adb,$table_prefix;
	$query="insert into ".$table_prefix."_group2grouprel values(?,?)";
	$adb->pquery($query, array($groupId, $containsGroupId));
	$log->debug("Exiting insertGroupToGroupRelation method ...");
}


/** Function to insert group to vtiger_role relation
  * @param $groupId -- Group Id :: Type integer
  * @param $roleId -- Role Id :: Type varchar
 */
function insertGroupToRoleRelation($groupId,$roleId)
{
	 global $log;
	$log->debug("Entering insertGroupToRoleRelation(".$groupId.",".$roleId.") method ...");
	global $adb,$table_prefix;
	$query="insert into ".$table_prefix."_group2role values(?,?)";
	$adb->pquery($query, array($groupId, $roleId));
	$log->debug("Exiting insertGroupToRoleRelation method ...");
}


/** Function to insert group to vtiger_role&subordinate relation
  * @param $groupId -- Group Id :: Type integer
  * @param $rsId -- Role Sub Id :: Type varchar
 */
function insertGroupToRsRelation($groupId,$rsId)
{
	global $log;
	$log->debug("Entering insertGroupToRsRelation(".$groupId.",".$rsId.") method ...");
	global $adb,$table_prefix;
	$query="insert into ".$table_prefix."_group2rs values(?,?)";
	$adb->pquery($query, array($groupId, $rsId));
	$log->debug("Exiting insertGroupToRsRelation method ...");
}

/** Function to insert group to user relation
  * @param $groupId -- Group Id :: Type integer
  * @param $userId -- User Id :: Type varchar
 */
function insertGroupToUserRelation($groupId,$userId)
{
	global $log;
	$log->debug("Entering insertGroupToUserRelation(".$groupId.",".$userId.") method ...");
	global $adb,$table_prefix;
	$query="insert into ".$table_prefix."_users2group values(?,?)";
	$adb->pquery($query, array($groupId, $userId));
	$log->debug("Exiting insertGroupToUserRelation method ...");
}


/** Function to get the group Information of the specified group
  * @param $groupId -- Group Id :: Type integer
  * @returns Group Detail Array in the following format:
  *   $groupDetailArray=Array($groupName,$description,$groupMembers);
 */
function getGroupInfo($groupId)
{
	global $log;
	$log->debug("Entering getGroupInfo(".$groupId.") method ...");
	global $adb,$table_prefix;
	$groupDetailArr=Array();
	$groupMemberArr=Array();
	//Retreving the group Info
	$query="select * from ".$table_prefix."_groups where groupid=?";
	$result = $adb->pquery($query, array($groupId));
	$groupName=$adb->query_result($result,0,'groupname');
	$description=$adb->query_result($result,0,'description');

	//Retreving the Group RelatedMembers
	$groupMemberArr=getGroupMembers($groupId);
	$groupDetailArr[]=$groupName;
	$groupDetailArr[]=$description;
	$groupDetailArr[]=$groupMemberArr;

	//Returning the Group Detail Array
	$log->debug("Exiting getGroupInfo method ...");
	return $groupDetailArr;


}

/** Function to fetch the group name of the specified group
  * @param $groupId -- Group Id :: Type integer
  * @returns Group Name :: Type varchar
 */
function fetchGroupName($groupId)
{
	global $log;
	$log->debug("Entering fetchGroupName(".$groupId.") method ...");

	global $adb,$table_prefix;
	//Retreving the group Info
	$query="select groupname from ".$table_prefix."_groups where groupid=?"; // crmv@49398
	$result = $adb->pquery($query, array($groupId));
	$groupName=decode_html($adb->query_result($result,0,'groupname'));
	$log->debug("Exiting fetchGroupName method ...");
	return $groupName;

}

/** Function to fetch the group members of the specified group
  * @param $groupId -- Group Id :: Type integer
  * @returns Group Member Array in the follwing format:
  *  $groupMemberArray=Array([groups]=>Array(groupid1,groupid2,groupid3,.....,groupidn),
  *                          [roles]=>Array(roleid1,roleid2,roleid3,.....,roleidn),
  *                          [rs]=>Array(roleid1,roleid2,roleid3,.....,roleidn),
  *                          [users]=>Array(useridd1,userid2,userid3,.....,groupidn))
 */
function getGroupMembers($groupId)
{
	global $log;
	$log->debug("Entering getGroupMembers(".$groupId.") method ...");
	$groupMemberArr=Array();
	$roleGroupArr=getGroupRelatedRoles($groupId);
	$rsGroupArr=getGroupRelatedRoleSubordinates($groupId);
	$groupGroupArr=getGroupRelatedGroups($groupId);
	$userGroupArr=getGroupRelatedUsers($groupId);

	$groupMemberArr['groups']=$groupGroupArr;
	$groupMemberArr['roles']=$roleGroupArr;
	$groupMemberArr['rs']=$rsGroupArr;
	$groupMemberArr['users']=$userGroupArr;

	$log->debug("Exiting getGroupMembers method ...");
	return($groupMemberArr);
}

/** Function to get the group related vtiger_roles of the specified group
  * @param $groupId -- Group Id :: Type integer
  * @returns Group Related Role Array in the follwing format:
  *  $groupRoles=Array(roleid1,roleid2,roleid3,.....,roleidn);
 */
function getGroupRelatedRoles($groupId)
{
	global $log;
	$log->debug("Entering getGroupRelatedRoles(".$groupId.") method ...");
	global $adb,$table_prefix;
	$roleGroupArr=Array();
	$query="select * from ".$table_prefix."_group2role where groupid=?";
	$result = $adb->pquery($query, array($groupId));
	$num_rows=$adb->num_rows($result);
	for($i=0;$i<$num_rows;$i++)
	{
		$roleId=$adb->query_result($result,$i,'roleid');
		$roleGroupArr[]=$roleId;
	}
	$log->debug("Exiting getGroupRelatedRoles method ...");
	return $roleGroupArr;

}


/** Function to get the group related vtiger_roles and subordinates of the specified group
  * @param $groupId -- Group Id :: Type integer
  * @returns Group Related Roles & Subordinate Array in the follwing format:
  *  $groupRoleSubordinates=Array(roleid1,roleid2,roleid3,.....,roleidn);
 */
function getGroupRelatedRoleSubordinates($groupId)
{
	global $log;
	$log->debug("Entering getGroupRelatedRoleSubordinates(".$groupId.") method ...");
	global $adb,$table_prefix;
	$rsGroupArr=Array();
	$query="select * from ".$table_prefix."_group2rs where groupid=?";
	$result = $adb->pquery($query, array($groupId));
	$num_rows=$adb->num_rows($result);
	for($i=0;$i<$num_rows;$i++)
	{
		$roleSubId=$adb->query_result($result,$i,'roleandsubid');
		$rsGroupArr[]=$roleSubId;
	}
	$log->debug("Exiting getGroupRelatedRoleSubordinates method ...");
	return $rsGroupArr;
}


/** Function to get the group related vtiger_groups
  * @param $groupId -- Group Id :: Type integer
  * @returns Group Related Groups Array in the follwing format:
  *  $groupGroups=Array(grpid1,grpid2,grpid3,.....,grpidn);
 */
function getGroupRelatedGroups($groupId)
{
	global $log;
	$log->debug("Entering getGroupRelatedGroups(".$groupId.") method ...");
	global $adb,$table_prefix;
	$groupGroupArr=Array();
	$query="select * from ".$table_prefix."_group2grouprel where groupid=?";
	$result = $adb->pquery($query, array($groupId));
	$num_rows=$adb->num_rows($result);
	for($i=0;$i<$num_rows;$i++)
	{
		$relGroupId=$adb->query_result($result,$i,'containsgroupid');
		$groupGroupArr[]=$relGroupId;
	}
	$log->debug("Exiting getGroupRelatedGroups method ...");
	return $groupGroupArr;

}

/** Function to get the group related vtiger_users
  * @param $userId -- User Id :: Type integer
  * @returns Group Related Users Array in the follwing format:
  *  $groupUsers=Array(userid1,userid2,userid3,.....,useridn);
 */
function getGroupRelatedUsers($groupId)
{
	global $log;
	$log->debug("Entering getGroupRelatedUsers(".$groupId.") method ...");
	global $adb,$table_prefix;
	$userGroupArr=Array();
	$query="SELECT ".$table_prefix."_users2group.* FROM ".$table_prefix."_users2group INNER JOIN ".$table_prefix."_users ON ".$table_prefix."_users.id=".$table_prefix."_users2group.userid WHERE groupid=? ORDER BY ".$table_prefix."_users.user_name";
	$result = $adb->pquery($query, array($groupId));
	$num_rows=$adb->num_rows($result);
	for($i=0;$i<$num_rows;$i++)
	{
		$userId=$adb->query_result($result,$i,'userid');
		$userGroupArr[]=$userId;
	}
	$log->debug("Exiting getGroupRelatedUsers method ...");
	return $userGroupArr;

}

/** Function to update the group
  * @param $groupId -- Group Id :: Type integer
  * @param $groupName -- Group Name :: Type varchar
  * @param $groupMemberArray -- Group Members Array :: Type array
  * @param $description -- Description :: Type text
 */
function updateGroup($groupId,$groupName,$groupMemberArray,$description)
{
	global $log, $metaLogs; // crmv@49398
	$log->debug("Entering updateGroup(".$groupId.",".$groupName.",".$groupMemberArray.",".$description.") method ...");
	global $adb,$table_prefix;
	// crmv@49398
	$query="update ".$table_prefix."_groups set groupname=?, description=?, date_modified=? where groupid=?";
	$adb->pquery($query, array($groupName, $description, date('Y-m-d H:i:s'), $groupId));
	// crmv@49398e

	//Deleting the Group Member Relation
	deleteGroupRelatedGroups($groupId);
	deleteGroupRelatedRoles($groupId);
	deleteGroupRelatedRolesAndSubordinates($groupId);
	deleteGroupRelatedUsers($groupId);

	//Inserting the Group Member Entries
	$groupArray=$groupMemberArray['groups'];
	$roleArray=$groupMemberArray['roles'];
	$rsArray=$groupMemberArray['rs'];
	$userArray=$groupMemberArray['users'];

	foreach($groupArray as $group_id)
	{
		insertGroupToGroupRelation($groupId,$group_id);
	}

	//Insert Group to Role Relation
	foreach($roleArray as $roleId)
	{
		insertGroupToRoleRelation($groupId,$roleId);
	}

	//Insert Group to RoleAndSubordinate Relation
	foreach($rsArray as $rsId)
	{
		insertGroupToRsRelation($groupId,$rsId);
	}

	//Insert Group to Role Relation
	foreach($userArray as $userId)
	{
		insertGroupToUserRelation($groupId,$userId);
	}
	
	if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_EDITGROUP, $groupId); // crmv@49398
	$log->debug("Exiting updateGroup method ...");
}

/** Function to delete the specified group
  * @param $groupId -- Group Id :: Type integer
  * @param $transferId --  Id of the group/user to which record ownership is to be transferred:: Type integer
  * @param $transferType -- It can have only two values namely 'Groups' or 'Users'. This determines whether the owneship is to be transferred to a group or user :: Type varchar
 */
function deleteGroup($groupId,$transferId)
{
	global $log, $metaLogs; // crmv@49398
	$log->debug("Entering deleteGroup(".$groupId.") method ...");
	global $adb,$table_prefix;

	tranferGroupOwnership($groupId,$transferId);
	deleteGroupRelatedSharingRules($groupId);

	$query="delete from ".$table_prefix."_groups where groupid=?";
	$adb->pquery($query, array($groupId));

	deleteGroupRelatedGroups($groupId);
	deleteGroupRelatedRoles($groupId);
	deleteGroupReportRelations($groupId);
	deleteGroupRelatedRolesAndSubordinates($groupId);
	deleteGroupRelatedUsers($groupId);
	
	if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_DELGROUP, $groupId); // crmv@49398
	$log->debug("Exiting deleteGroup method ...");
}


/** Function to transfer the ownership of records owned by a particular group to the specified group
  * @param $groupId -- Group Id of the group which's record ownership has to be transferred:: Type integer
  * @param $transferId --  Id of the group/user to which record ownership is to be transferred:: Type integer
  * @param $transferType -- It can have only two values namely 'Groups' or 'Users'. This determines whether the owneship is to be transferred to a group or user :: Type varchar
 */
function tranferGroupOwnership($groupId,$transferId)
{
	global $log;
	$log->debug("Entering tranferGroupOwnership(".$groupId.") method ...");
	global $adb,$table_prefix;

	$query = "update ".$table_prefix."_crmentity set smownerid=? where smownerid=?";
			$params = array($transferId, $groupId);
			$adb->pquery($query, $params);

	$log->debug("Exiting tranferGroupOwnership method ...");
}

/** Function to delete group to group relation of the  specified group
  * @param $groupId -- Group Id :: Type integer
 */
function deleteGroupRelatedGroups($groupId)
{
	global $log;
	$log->debug("Entering deleteGroupRelatedGroups(".$groupId.") method ...");
	global $adb,$table_prefix;
	$query="delete from ".$table_prefix."_group2grouprel where groupid=?";
	$adb->pquery($query, array($groupId));
	$log->debug("Exiting deleteGroupRelatedGroups method ...");
}


/** Function to delete group to vtiger_role relation of the  specified group
  * @param $groupId -- Group Id :: Type integer
 */
function deleteGroupRelatedRoles($groupId)
{
	global $log;
	$log->debug("Entering deleteGroupRelatedRoles(".$groupId.") method ...");
	global $adb,$table_prefix;
	$query="delete from ".$table_prefix."_group2role where groupid=?";
	$adb->pquery($query, array($groupId));
	$log->debug("Exiting deleteGroupRelatedRoles method ...");
}


/** Function to delete group to vtiger_role and subordinates relation of the  specified group
  * @param $groupId -- Group Id :: Type integer
 */
function deleteGroupRelatedRolesAndSubordinates($groupId)
{
	global $log;
	$log->debug("Entering deleteGroupRelatedRolesAndSubordinates(".$groupId.") method ...");
	global $adb,$table_prefix;
	$query="delete from ".$table_prefix."_group2rs where groupid=?";
	$adb->pquery($query, array($groupId));
	$log->debug("Exiting deleteGroupRelatedRolesAndSubordinates method ...");
}


/** Function to delete group to user relation of the  specified group
  * @param $groupId -- Group Id :: Type integer
 */
function deleteGroupRelatedUsers($groupId)
{
	global $log;
	$log->debug("Entering deleteGroupRelatedUsers(".$groupId.") method ...");
	global $adb,$table_prefix;
	$query="delete from ".$table_prefix."_users2group where groupid=?";
	$adb->pquery($query, array($groupId));
	$log->debug("Exiting deleteGroupRelatedUsers method ...");
}

/** This function returns the Default Organisation Sharing Action Name
  * @param $share_action_id -- It takes the Default Organisation Sharing ActionId as input :: Type Integer
  * @returns The sharing Action Name :: Type Varchar
  */
function getDefOrgShareActionName($share_action_id)
{
	global $log;
	$log->debug("Entering getDefOrgShareActionName(".$share_action_id.") method ...");
	global $adb,$table_prefix;
	$query="select * from ".$table_prefix."_org_share_act_mapping where share_action_id=?";
	$result=$adb->pquery($query, array($share_action_id));
	$share_action_name=$adb->query_result($result,0,"share_action_name");
	$log->debug("Exiting getDefOrgShareActionName method ...");
	return $share_action_name;


}
/** This function returns the Default Organisation Sharing Action Array for the specified Module
  * It takes the module tabid as input and constructs the array.
  * The output array consists of the 'Default Organisation Sharing Id'=>'Default Organisation Sharing Action' mapping for all the sharing actions available for the specifed module
  * The output Array will be in the following format:
  *    Array = (Default Org ActionId1=>Default Org ActionName1,
  *             Default Org ActionId2=>Default Org ActionName2,
  *			|
  *                     |
  *              Default Org ActionIdn=>Default Org ActionNamen)
  */
function getModuleSharingActionArray($tabid)
{
	global $log;
	$log->debug("Entering getModuleSharingActionArray(".$tabid.") method ...");
	global $adb,$table_prefix;
	$share_action_arr=Array();
	$query = "select ".$table_prefix."_org_share_act_mapping.share_action_name,".$table_prefix."_org_share_action2tab.share_action_id from ".$table_prefix."_org_share_action2tab inner join ".$table_prefix."_org_share_act_mapping on ".$table_prefix."_org_share_action2tab.share_action_id=".$table_prefix."_org_share_act_mapping.share_action_id where ".$table_prefix."_org_share_action2tab.tabid=?";
	$result=$adb->pquery($query, array($tabid));
	$num_rows=$adb->num_rows($result);
	for($i=0; $i<$num_rows; $i++)
	{
		$share_action_name=$adb->query_result($result,$i,"share_action_name");
		$share_action_id=$adb->query_result($result,$i,"share_action_id");
		$share_action_arr[$share_action_id] = $share_action_name;
	}
	$log->debug("Exiting getModuleSharingActionArray method ...");
	return $share_action_arr;

}

/** This function adds a organisation level sharing rule for the specified Module
  * It takes the following input parameters:
  * 	$tabid -- Module tabid - Datatype::Integer
  * 	$shareEntityType -- The Entity Type may be vtiger_groups,roles,rs and vtiger_users - Datatype::String
  * 	$toEntityType -- The Entity Type may be vtiger_groups,roles,rs and vtiger_users - Datatype::String
  * 	$shareEntityId -- The id of the group,role,rs,user to be shared
  * 	$toEntityId -- The id of the group,role,rs,user to which the specified entity is to be shared
  * 	$sharePermisson -- This can have the following values:
  *                       0 - Read Only
  *                       1 - Read/Write
  * This function will return the shareid as output
  */
function addSharingRule($tabid,$shareEntityType,$toEntityType,$shareEntityId,$toEntityId,$sharePermission)
{
	global $log, $metaLogs; // crmv@49398
	$log->debug("Entering addSharingRule(".$tabid.",".$shareEntityType.",".$toEntityType.",".$shareEntityId.",".$toEntityId.",".$sharePermission.") method ...");

	global $adb,$table_prefix;
	$shareid=$adb->getUniqueId($table_prefix."_datashare_mod_rel");


	if($shareEntityType == 'groups' && $toEntityType == 'groups')
	{
		$type_string='GRP::GRP';
		$query = "insert into ".$table_prefix."_datashare_grp2grp values(?,?,?,?)";
	}
	elseif($shareEntityType == 'groups' && $toEntityType == 'roles')
	{

		$type_string='GRP::ROLE';
		$query = "insert into ".$table_prefix."_datashare_grp2role values(?,?,?,?)";
	}
	elseif($shareEntityType == 'groups' && $toEntityType == 'rs')
	{

		$type_string='GRP::RS';
		$query = "insert into {$table_prefix}_datashare_grp2rs values(?,?,?,?)";
	}
	elseif($shareEntityType == 'roles' && $toEntityType == 'groups')
	{

		$type_string='ROLE::GRP';
		$query = "insert into ".$table_prefix."_datashare_role2group values(?,?,?,?)";
	}
	elseif($shareEntityType == 'roles' && $toEntityType == 'roles')
	{

		$type_string='ROLE::ROLE';
		$query = "insert into ".$table_prefix."_datashare_role2role values(?,?,?,?)";
	}
	elseif($shareEntityType == 'roles' && $toEntityType == 'rs')
	{

		$type_string='ROLE::RS';
		$query = "insert into ".$table_prefix."_datashare_role2rs values(?,?,?,?)";
	}
	elseif($shareEntityType == 'rs' && $toEntityType == 'groups')
	{

		$type_string='RS::GRP';
		$query = "insert into ".$table_prefix."_datashare_rs2grp values(?,?,?,?)";
	}
	elseif($shareEntityType == 'rs' && $toEntityType == 'roles')
	{

		$type_string='RS::ROLE';
		$query = "insert into ".$table_prefix."_datashare_rs2role values(?,?,?,?)";
	}
	elseif($shareEntityType == 'rs' && $toEntityType == 'rs')
	{

		$type_string='RS::RS';
		$query = "insert into ".$table_prefix."_datashare_rs2rs values(?,?,?,?)";
	}
//crmv@7222
	elseif($shareEntityType == 'user' && $toEntityType == 'user')
	{

		$type_string='USR::USR';
		$query = "insert into ".$table_prefix."_datashare_usr2usr values(?,?,?,?)";
	}
//crmv@7222e
	$query1 = "insert into ".$table_prefix."_datashare_mod_rel values(?,?,?)";
	$adb->pquery($query1, array($shareid, $tabid, $type_string));

	$params = array($shareid, $shareEntityId, $toEntityId, $sharePermission);
	$adb->pquery($query, $params);

	if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_ADDSHRULE, $shareid, array('module'=>getTabModuleName($tabid))); // crmv@49398
	$log->debug("Exiting addSharingRule method ...");
	return $shareid;

}


/** This function is to update the organisation level sharing rule
  * It takes the following input parameters:
  *     $shareid -- Id of the Sharing Rule to be updated
  * 	$tabid -- Module tabid - Datatype::Integer
  * 	$shareEntityType -- The Entity Type may be vtiger_groups,roles,rs and vtiger_users - Datatype::String
  * 	$toEntityType -- The Entity Type may be vtiger_groups,roles,rs and vtiger_users - Datatype::String
  * 	$shareEntityId -- The id of the group,role,rs,user to be shared
  * 	$toEntityId -- The id of the group,role,rs,user to which the specified entity is to be shared
  * 	$sharePermisson -- This can have the following values:
  *                       0 - Read Only
  *                       1 - Read/Write
  * This function will return the shareid as output
  */
function updateSharingRule($shareid,$tabid,$shareEntityType,$toEntityType,$shareEntityId,$toEntityId,$sharePermission)
{
	global $log, $metaLogs; // crmv@49398
	$log->debug("Entering updateSharingRule(".$shareid.",".$tabid.",".$shareEntityType.",".$toEntityType.",".$shareEntityId.",".$toEntityId.",".$sharePermission.") method ...");

	global $adb,$table_prefix;
	$query2="select * from ".$table_prefix."_datashare_mod_rel where shareid=?";
	$res=$adb->pquery($query2, array($shareid));
	$typestr=$adb->query_result($res,0,'relationtype');
	$tabname=getDSTableNameForType($typestr);
	$query3="delete from ".$tabname." where shareid=?";
	$adb->pquery($query3, array($shareid));


	if($shareEntityType == 'groups' && $toEntityType == 'groups')
	{
		$type_string='GRP::GRP';
		$query = "insert into ".$table_prefix."_datashare_grp2grp values(?,?,?,?)";
	}
	elseif($shareEntityType == 'groups' && $toEntityType == 'roles')
	{

		$type_string='GRP::ROLE';
		$query = "insert into ".$table_prefix."_datashare_grp2role values(?,?,?,?)";
	}
	elseif($shareEntityType == 'groups' && $toEntityType == 'rs')
	{

		$type_string='GRP::RS';
		$query = "insert into ".$table_prefix."_datashare_grp2rs values(?,?,?,?)";
	}
	elseif($shareEntityType == 'roles' && $toEntityType == 'groups')
	{

		$type_string='ROLE::GRP';
		$query = "insert into ".$table_prefix."_datashare_role2group values(?,?,?,?)";
	}
	elseif($shareEntityType == 'roles' && $toEntityType == 'roles')
	{

		$type_string='ROLE::ROLE';
		$query = "insert into ".$table_prefix."_datashare_role2role values(?,?,?,?)";
	}
	elseif($shareEntityType == 'roles' && $toEntityType == 'rs')
	{

		$type_string='ROLE::RS';
		$query = "insert into ".$table_prefix."_datashare_role2rs values(?,?,?,?)";
	}
	elseif($shareEntityType == 'rs' && $toEntityType == 'groups')
	{

		$type_string='RS::GRP';
		$query = "insert into ".$table_prefix."_datashare_rs2grp values(?,?,?,?)";
	}
	elseif($shareEntityType == 'rs' && $toEntityType == 'roles')
	{

		$type_string='RS::ROLE';
		$query = "insert into ".$table_prefix."_datashare_rs2role values(?,?,?,?)";
	}
	elseif($shareEntityType == 'rs' && $toEntityType == 'rs')
	{

		$type_string='RS::RS';
		$query = "insert into ".$table_prefix."_datashare_rs2rs values(?,?,?,?)";
	}
//crmv@7222
	elseif($shareEntityType == 'user' && $toEntityType == 'user')
	{

		$type_string='USR::USR';
		$query = "insert into ".$table_prefix."_datashare_usr2usr values(?,?,?,?)";
	}
//crmv@7222e
	$query1 = "update ".$table_prefix."_datashare_mod_rel set relationtype=? where shareid=?";
	$adb->pquery($query1, array($type_string, $shareid));

	$params = array($shareid, $shareEntityId, $toEntityId, $sharePermission);
	$adb->pquery($query, $params);

	if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_EDITSHRULE, $shareid, array('module'=>getTabModuleName($tabid))); // crmv@49398
	$log->debug("Exiting updateSharingRule method ...");
	return $shareid;

}


/** This function is to delete the organisation level sharing rule
  * It takes the following input parameters:
  *     $shareid -- Id of the Sharing Rule to be updated
  */
function deleteSharingRule($shareid)
{
	global $log, $metaLogs; // crmv@49398
	$log->debug("Entering deleteSharingRule(".$shareid.") method ...");
	global $adb,$table_prefix;
	$query2="select * from ".$table_prefix."_datashare_mod_rel where shareid=?";
	$res=$adb->pquery($query2, array($shareid));
	$tabid = $adb->query_result($res,0,'tabid'); // crmv@49398
	$typestr=$adb->query_result($res,0,'relationtype');
	$tabname=getDSTableNameForType($typestr);
	$query3="delete from $tabname where shareid=?";
	$adb->pquery($query3, array($shareid));
	$query4="delete from ".$table_prefix."_datashare_mod_rel where shareid=?";
	$adb->pquery($query4, array($shareid));

	//deleting the releated module sharing permission
	$query5="delete from ".$table_prefix."_datashare_relmod_perm where shareid=?";
	$adb->pquery($query5, array($shareid));
	
	if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_DELSHRULE, $shareid, array('module'=>getTabModuleName($tabid))); // crmv@49398
	$log->debug("Exiting deleteSharingRule method ...");
}

/** Function get the Data Share Table and their columns
  * @returns -- Data Share Table and Column Array in the following format:
  *  $dataShareTableColArr=Array('datashare_grp2grp'=>'share_groupid::to_groupid',
  *				    'datashare_grp2role'=>'share_groupid::to_roleid',
  *				    'datashare_grp2rs'=>'share_groupid::to_roleandsubid',
  * 				    'datashare_role2group'=>'share_roleid::to_groupid',
  *				    'datashare_role2role'=>'share_roleid::to_roleid',
  *				    'datashare_role2rs'=>'share_roleid::to_roleandsubid',
  *				    'datashare_rs2grp'=>'share_roleandsubid::to_groupid',
  *				    'datashare_rs2role'=>'share_roleandsubid::to_roleid',
  *				    'datashare_rs2rs'=>'share_roleandsubid::to_roleandsubid');
  */
function getDataShareTableandColumnArray()
{
global $log,$table_prefix;
	$log->debug("Entering getDataShareTableandColumnArray() method ...");
	$dataShareTableColArr=Array($table_prefix.'_datashare_grp2grp'=>'share_groupid::to_groupid',
				    $table_prefix.'_datashare_grp2role'=>'share_groupid::to_roleid',
				    $table_prefix.'_datashare_grp2rs'=>'share_groupid::to_roleandsubid',
				    $table_prefix.'_datashare_role2group'=>'share_roleid::to_groupid',
				    $table_prefix.'_datashare_role2role'=>'share_roleid::to_roleid',
				    $table_prefix.'_datashare_role2rs'=>'share_roleid::to_roleandsubid',
				    $table_prefix.'_datashare_rs2grp'=>'share_roleandsubid::to_groupid',
				    $table_prefix.'_datashare_rs2role'=>'share_roleandsubid::to_roleid',
				    $table_prefix.'_datashare_rs2rs'=>'share_roleandsubid::to_roleandsubid',
					 $table_prefix.'_datashare_usr2usr'=>'share_userid::to_userid');
	$log->debug("Exiting getDataShareTableandColumnArray method ...");
	return $dataShareTableColArr;

}



/** Function get the Data Share Column Names for the specified Table Name
 *  @param $tableName -- DataShare Table Name :: Type Varchar
 *  @returns Column Name -- Type Varchar
 *
 */
function getDSTableColumns($tableName)
{
	global $log;
	$log->debug("Entering getDSTableColumns(".$tableName.") method ...");
	$dataShareTableColArr=getDataShareTableandColumnArray();

	$dsTableCols=$dataShareTableColArr[$tableName];
	$dsTableColsArr=explode('::',$dsTableCols);
	$log->debug("Exiting getDSTableColumns method ...");
	return $dsTableColsArr;

}


/** Function get the Data Share Table Names
 *  @returns the following Date Share Table Name Array:
 *  $dataShareTableColArr=Array('GRP::GRP'=>'datashare_grp2grp',
 * 				    'GRP::ROLE'=>'datashare_grp2role',
 *				    'GRP::RS'=>'datashare_grp2rs',
 *				    'ROLE::GRP'=>'datashare_role2group',
 *				    'ROLE::ROLE'=>'datashare_role2role',
 *				    'ROLE::RS'=>'datashare_role2rs',
 *				    'RS::GRP'=>'datashare_rs2grp',
 *				    'RS::ROLE'=>'datashare_rs2role',
 *				    'RS::RS'=>'datashare_rs2rs');
 */
function getDataShareTableName()
{
	global $log, $table_prefix;
	$log->debug("Entering getDataShareTableName() method ...");
	$dataShareTableColArr=Array('GRP::GRP'=>$table_prefix.'_datashare_grp2grp',
				    'GRP::ROLE'=>$table_prefix.'_datashare_grp2role',
				    'GRP::RS'=>$table_prefix.'_datashare_grp2rs',
				    'ROLE::GRP'=>$table_prefix.'_datashare_role2group',
				    'ROLE::ROLE'=>$table_prefix.'_datashare_role2role',
				    'ROLE::RS'=>$table_prefix.'_datashare_role2rs',
				    'RS::GRP'=>$table_prefix.'_datashare_rs2grp',
				    'RS::ROLE'=>$table_prefix.'_datashare_rs2role',
				    'RS::RS'=>$table_prefix.'_datashare_rs2rs',
					 'USR::USR'=>$table_prefix.'_datashare_usr2usr');
	$log->debug("Exiting getDataShareTableName method ...");
	return $dataShareTableColArr;

}

/** Function to get the Data Share Table Name from the speciified type string
 *  @param $typeString -- Datashare Type Sting :: Type Varchar
 *  @returns Table Name -- Type Varchar
 *
 */
function getDSTableNameForType($typeString)
{
	global $log;
	$log->debug("Entering getDSTableNameForType(".$typeString.") method ...");
	$dataShareTableColArr=getDataShareTableName();
	$tableName=$dataShareTableColArr[$typeString];
	$log->debug("Exiting getDSTableNameForType method ...");
	return $tableName;

}

/** Function to get the Entity type from the specified DataShare Table Column Name
 *  @param $colname -- Datashare Table Column Name :: Type Varchar
 *  @returns The entity type. The entity type may be vtiger_groups or vtiger_roles or rs -- Type Varchar
 */
function getEntityTypeFromCol($colName)
{
	global $log;
	$log->debug("Entering getEntityTypeFromCol(".$colName.") method ...");

        if($colName == 'share_groupid' || $colName == 'to_groupid')
        {
                $entity_type='groups';
        }
        elseif($colName =='share_roleid' || $colName =='to_roleid')
        {
                $entity_type='roles';
        }
	elseif($colName == 'share_roleandsubid' || $colName == 'to_roleandsubid')
        {
                $entity_type='rs';
        }
//crmv@7222
	elseif($colName == 'share_userid' || $colName == 'to_userid')
        {
                $entity_type='usr';
        }
//crmv@7222e
	$log->debug("Exiting getEntityTypeFromCol method ...");
	return $entity_type;

}

/** Function to get the Entity Display Link
 *  @param $entityid -- Entity Id
 *  @params $entityType --  The entity type may be vtiger_groups or vtiger_roles or rs -- Type Varchar
 *  @returns the Entity Display link
 */
function getEntityDisplayLink($entityType,$entityid)
{
	global $log;
	$log->debug("Entering getEntityDisplayLink(".$entityType.",".$entityid.") method ...");
	if($entityType == 'groups')
	{
		$groupNameArr = getGroupInfo($entityid);
		$display_out = "<a href='index.php?module=Settings&action=GroupDetailView&returnaction=OrgSharingDetailView&groupId=".$entityid."'>Group::". $groupNameArr[0]." </a>";
	}
	elseif($entityType == 'roles')
	{
		$roleName=getRoleName($entityid);
		$display_out = "<a href='index.php?module=Settings&action=RoleDetailView&returnaction=OrgSharingDetailView&roleid=".$entityid."'>Role::".$roleName. "</a>";
	}
	elseif($entityType == 'rs')
	{
		$roleName=getRoleName($entityid);
		$display_out = "<a href='index.php?module=Settings&action=RoleDetailView&returnaction=OrgSharingDetailView&roleid=".$entityid."'>RoleAndSubordinate::".$roleName. "</a>";
	}
//crmv@7222
	elseif($entityType == 'usr')
	{
		$roleName=getUserName($entityid);
		$display_out = "<a href='index.php?module=Users&action=DetailView&parenttab=Settings&record=".$entityid."'>".$roleName."</a>";
	}
//crmv@7222e
	$log->debug("Exiting getEntityDisplayLink method ...");
	return $display_out;

}


/** Function to get the Sharing rule Info
 *  @param $shareId -- Sharing Rule Id
 *  @returns Sharing Rule Information Array in the following format:
 *    $shareRuleInfoArr=Array($shareId, $tabid, $type, $share_ent_type, $to_ent_type, $share_entity_id, $to_entity_id,$permission);
 */
function getSharingRuleInfo($shareId)
{
	global $log;
	$log->debug("Entering getSharingRuleInfo(".$shareId.") method ...");
	global $adb,$table_prefix;
	$shareRuleInfoArr=Array();
	$query="select * from ".$table_prefix."_datashare_mod_rel where shareid=?";
	$result=$adb->pquery($query, array($shareId));
	//Retreving the Sharing Tabid
	$tabid=$adb->query_result($result,0,'tabid');
	$type=$adb->query_result($result,0,'relationtype');

	//Retreiving the Sharing Table Name
	$tableName=getDSTableNameForType($type);

	//Retreiving the Sharing Col Names
	$dsTableColArr=getDSTableColumns($tableName);
	$share_ent_col=$dsTableColArr[0];
	$to_ent_col=$dsTableColArr[1];

	//Retreiving the Sharing Entity Col Types
	$share_ent_type=getEntityTypeFromCol($share_ent_col);
	$to_ent_type=getEntityTypeFromCol($to_ent_col);

	//Retreiving the Value from Table
	$query1="select * from $tableName where shareid=?";
	$result1=$adb->pquery($query1, array($shareId));
	$share_id=$adb->query_result($result1,0,$share_ent_col);
	$to_id=$adb->query_result($result1,0,$to_ent_col);
	$permission=$adb->query_result($result1,0,'permission');

	//Constructing the Array
	$shareRuleInfoArr[]=$shareId;
	$shareRuleInfoArr[]=$tabid;
	$shareRuleInfoArr[]=$type;
	$shareRuleInfoArr[]=$share_ent_type;
	$shareRuleInfoArr[]=$to_ent_type;
	$shareRuleInfoArr[]=$share_id;
	$shareRuleInfoArr[]=$to_id;
	$shareRuleInfoArr[]=$permission;

	$log->debug("Exiting getSharingRuleInfo method ...");
	return $shareRuleInfoArr;



}

/** This function is to retreive the list of related sharing modules for the specifed module
  * It takes the following input parameters:
  *     $tabid -- The module tabid:: Type Integer
  */

function getRelatedSharingModules($tabid)
{
	global $log;
	$log->debug("Entering getRelatedSharingModules(".$tabid.") method ...");
	global $adb,$table_prefix;
	$relatedSharingModuleArray=Array();
	$query="select * from ".$table_prefix."_datashare_relmod where tabid=?";
	$result=$adb->pquery($query, array($tabid));
	$num_rows=$adb->num_rows($result);
	for($i=0;$i<$num_rows;$i++)
	{
		$ds_relmod_id=$adb->query_result($result,$i,'datashare_relatedmodule_id');
		$rel_tabid=$adb->query_result($result,$i,'relatedto_tabid');
		$relatedSharingModuleArray[$rel_tabid]=$ds_relmod_id;

	}
	$log->debug("Exiting getRelatedSharingModules method ...");
	return $relatedSharingModuleArray;

}


/** This function is to add the related module sharing permission for a particulare Sharing Rule
  * It takes the following input parameters:
  *     $shareid -- The Sharing Rule Id:: Type Integer
  *     $tabid -- The module tabid:: Type Integer
  *     $relatedtabid -- The related module tabid:: Type Integer
  * 	$sharePermisson -- This can have the following values:
  *                       0 - Read Only
  *                       1 - Read/Write
  */

function addRelatedModuleSharingPermission($shareid,$tabid,$relatedtabid,$sharePermission)
{
	global $log;
	$log->debug("Entering addRelatedModuleSharingPermission(".$shareid.",".$tabid.",".$relatedtabid.",".$sharePermission.") method ...");
	global $adb,$table_prefix;
	$relatedModuleSharingId=getRelatedModuleSharingId($tabid,$relatedtabid);
	$query="insert into ".$table_prefix."_datashare_relmod_perm values(?,?,?)" ;
	$result=$adb->pquery($query, array($shareid, $relatedModuleSharingId, $sharePermission));
	$log->debug("Exiting addRelatedModuleSharingPermission method ...");
}

/** This function is to update the related module sharing permission for a particulare Sharing Rule
  * It takes the following input parameters:
  *     $shareid -- The Sharing Rule Id:: Type Integer
  *     $tabid -- The module tabid:: Type Integer
  *     $relatedtabid -- The related module tabid:: Type Integer
  * 	$sharePermisson -- This can have the following values:
  *                       0 - Read Only
  *                       1 - Read/Write
  */

function updateRelatedModuleSharingPermission($shareid,$tabid,$relatedtabid,$sharePermission)
{
	global $log;
	$log->debug("Entering updateRelatedModuleSharingPermission(".$shareid.",".$tabid.",".$relatedtabid.",".$sharePermission.") method ...");
	global $adb,$table_prefix;
	$relatedModuleSharingId=getRelatedModuleSharingId($tabid,$relatedtabid);
	$query="update ".$table_prefix."_datashare_relmod_perm set permission=? where shareid=? and datashare_relatedmodule_id=?";
	$result=$adb->pquery($query, array($sharePermission, $shareid, $relatedModuleSharingId));
	$log->debug("Exiting updateRelatedModuleSharingPermission method ...");
}

/** This function is to retreive the Related Module Sharing Id
  * It takes the following input parameters:
  *     $tabid -- The module tabid:: Type Integer
  *     $related_tabid -- The related module tabid:: Type Integer
  * This function returns the Related Module Sharing Id
  */

function getRelatedModuleSharingId($tabid,$related_tabid)
{
	global $log;
	$log->debug("Entering getRelatedModuleSharingId(".$tabid.",".$related_tabid.") method ...");
	global $adb,$table_prefix;
	$query="select datashare_relatedmodule_id from ".$table_prefix."_datashare_relmod where tabid=? and relatedto_tabid=?";
	$result=$adb->pquery($query, array($tabid, $related_tabid));
	$relatedModuleSharingId=$adb->query_result($result,0,'datashare_relatedmodule_id');
	$log->debug("Exiting getRelatedModuleSharingId method ...");
	return $relatedModuleSharingId;

}

/** This function is to retreive the Related Module Sharing Permissions for the specified Sharing Rule
  * It takes the following input parameters:
  *     $shareid -- The Sharing Rule Id:: Type Integer
  *This function will return the Related Module Sharing permissions in an Array in the following format:
  *     $PermissionArray=($relatedTabid1=>$sharingPermission1,
  *			  $relatedTabid2=>$sharingPermission2,
  *					|
  *                                     |
  *                       $relatedTabid-n=>$sharingPermission-n)
  */
function getRelatedModuleSharingPermission($shareid)
{
	global $log;
	$log->debug("Entering getRelatedModuleSharingPermission(".$shareid.") method ...");
	global $adb,$table_prefix;
	$relatedSharingModulePermissionArray=Array();
	$query="select ".$table_prefix."_datashare_relmod.*,".$table_prefix."_datashare_relmod_perm.permission from ".$table_prefix."_datashare_relmod inner join ".$table_prefix."_datashare_relmod_perm on ".$table_prefix."_datashare_relmod_perm.datashare_relatedmodule_id=".$table_prefix."_datashare_relmod.datashare_relatedmodule_id where ".$table_prefix."_datashare_relmod_perm.shareid=?";
	$result=$adb->pquery($query, array($shareid));
	$num_rows=$adb->num_rows($result);
	for($i=0;$i<$num_rows;$i++)
	{
		$relatedto_tabid=$adb->query_result($result,$i,'relatedto_tabid');
		$permission=$adb->query_result($result,$i,'permission');
		$relatedSharingModulePermissionArray[$relatedto_tabid]=$permission;


	}
	$log->debug("Exiting getRelatedModuleSharingPermission method ...");
	return $relatedSharingModulePermissionArray;

}


/** This function is to retreive the vtiger_profiles associated with the  the specified user
  * It takes the following input parameters:
  *     $userid -- The User Id:: Type Integer
  *     $mobile -- 1 or 0
  *This function will return the vtiger_profiles associated to the specified vtiger_users in an Array in the following format:
  *     $userProfileArray=(profileid1,profileid2,profileid3,...,profileidn);
  */
function getUserProfile($userId,$mobile=0) { // crmv@39110
	global $log;
	$log->debug("Entering getUserProfile(".$userId.") method ...");
	global $adb,$table_prefix;
	$roleId=fetchUserRole($userId);
	$profArr=Array();
	// crmv@39110
	$sql1 = "select profileid from ".$table_prefix."_role2profile where roleid=?";
	$params = array($roleId);
	$columns = array_keys($adb->datadict->MetaColumns("{$table_prefix}_role2profile"));
	if (in_array(strtoupper('mobile'),$columns)) {
		$sql1 .= " and mobile = ?";
		$params[] = $mobile;
	}
    $result1 = $adb->pquery($sql1,$params);
    // crmv@39110e
	$num_rows=$adb->num_rows($result1);
	for($i=0;$i<$num_rows;$i++) {
       	$profileid=  $adb->query_result($result1,$i,"profileid");
		$profArr[]=$profileid;
	}
	$log->debug("Exiting getUserProfile method ...");
	return $profArr;
}

/** To retreive the global permission of the specifed user from the various vtiger_profiles associated with the user
  * @param $userid -- The User Id:: Type Integer
  * @returns  user global permission  array in the following format:
  *     $gloabalPerrArray=(view all action id=>permission,
			   edit all action id=>permission)							);
  */
function getCombinedUserGlobalPermissions($userId, $mobile = 0)  // crmv@39110
{
	global $log;
	$log->debug("Entering getCombinedUserGlobalPermissions(".$userId.") method ...");
	global $adb;
	$profArr=getUserProfile($userId, $mobile);  // crmv@39110
	$no_of_profiles=sizeof($profArr);
	$userGlobalPerrArr=Array();

	$userGlobalPerrArr=getProfileGlobalPermission($profArr[0]);
	if($no_of_profiles != 1)
	{
			for($i=1;$i<$no_of_profiles;$i++)
		{
			$tempUserGlobalPerrArr=getProfileGlobalPermission($profArr[$i]);

			foreach($userGlobalPerrArr as $globalActionId=>$globalActionPermission)
			{
				if($globalActionPermission == 1)
				{
					$now_permission = $tempUserGlobalPerrArr[$globalActionId];
					if($now_permission == 0)
					{
						$userGlobalPerrArr[$globalActionId]=$now_permission;
					}


				}

			}

		}

	}

	$log->debug("Exiting getCombinedUserGlobalPermissions method ...");
	return $userGlobalPerrArr;

}

/** To retreive the vtiger_tab permissions of the specifed user from the various vtiger_profiles associated with the user
  * @param $userid -- The User Id:: Type Integer
  * @returns  user global permission  array in the following format:
  *     $tabPerrArray=(tabid1=>permission,
  *			   tabid2=>permission)							);
  */
function getCombinedUserTabsPermissions($userId, $mobile = 0)  // crmv@39110
{
	global $log;
	$log->debug("Entering getCombinedUserTabsPermissions(".$userId.") method ...");
	global $adb;
	$profArr=getUserProfile($userId, $mobile);  // crmv@39110
	$no_of_profiles=sizeof($profArr);
	$userTabPerrArr=Array();

	$userTabPerrArr=getProfileTabsPermission($profArr[0]);
	if($no_of_profiles != 1)
	{
		for($i=1;$i<$no_of_profiles;$i++)
		{
			$tempUserTabPerrArr=getProfileTabsPermission($profArr[$i]);

			foreach($userTabPerrArr as $tabId=>$tabPermission)
			{
				if($tabPermission == 1)
				{
					$now_permission = $tempUserTabPerrArr[$tabId];
					if($now_permission == 0)
					{
						$userTabPerrArr[$tabId]=$now_permission;
					}


				}

			}

		}

	}
	$log->debug("Exiting getCombinedUserTabsPermissions method ...");
	return $userTabPerrArr;

}

/** To retreive the vtiger_tab acion permissions of the specifed user from the various vtiger_profiles associated with the user
  * @param $userid -- The User Id:: Type Integer
  * @returns  user global permission  array in the following format:
  *     $actionPerrArray=(tabid1=>permission,
  *			   tabid2=>permission);
 */
function getCombinedUserActionPermissions($userId, $mobile=0)  // crmv@39110
{
	global $log;
	$log->debug("Entering getCombinedUserActionPermissions(".$userId.") method ...");
	global $adb;
	$profArr=getUserProfile($userId, $mobile);  // crmv@39110
	$no_of_profiles=sizeof($profArr);
	$actionPerrArr=Array();

	$actionPerrArr=getProfileAllActionPermission($profArr[0]);
	if($no_of_profiles != 1)
	{
		for($i=1;$i<$no_of_profiles;$i++)
		{
			$tempActionPerrArr=getProfileAllActionPermission($profArr[$i]);

			foreach($actionPerrArr as $tabId=>$perArr)
			{
				foreach($perArr as $actionid=>$per)
				{
					if($per == 1)
					{
						$now_permission = $tempActionPerrArr[$tabId][$actionid];
						if($now_permission == 0)
						{
							$actionPerrArr[$tabId][$actionid]=$now_permission;
						}


					}
				}

			}

		}

	}
	$log->debug("Exiting getCombinedUserActionPermissions method ...");
	return $actionPerrArr;

}

/** To retreive the parent vtiger_role of the specified vtiger_role
  * @param $roleid -- The Role Id:: Type varchar
  * @returns  parent vtiger_role array in the following format:
  *     $parentRoleArray=(roleid1,roleid2,.......,roleidn);
 */
function getParentRole($roleId)
{
	global $log;
	$log->debug("Entering getParentRole(".$roleId.") method ...");
	$roleInfo=getRoleInformation($roleId);
	$parentRole=$roleInfo[$roleId][1];
	$tempParentRoleArr=explode('::',$parentRole);
	$parentRoleArr=Array();
	foreach($tempParentRoleArr as $role_id)
	{
		if($role_id != $roleId)
		{
			$parentRoleArr[]=$role_id;
		}
	}
	$log->debug("Exiting getParentRole method ...");
	return $parentRoleArr;

}

/** To retreive the subordinate vtiger_roles of the specified parent vtiger_role
  * @param $roleid -- The Role Id:: Type varchar
  * @returns  subordinate vtiger_role array in the following format:
  *     $subordinateRoleArray=(roleid1,roleid2,.......,roleidn);
 */
function getRoleSubordinates($roleId)
{
	global $log,$table_prefix;
	$log->debug("Entering getRoleSubordinates(".$roleId.") method ...");

	// Look at cache first for information
	$roleSubordinates = VTCacheUtils::lookupRoleSubordinates($roleId);

	if($roleSubordinates === false) {
		global $adb,$table_prefix;
		$roleDetails=getRoleInformation($roleId);
		$roleInfo=$roleDetails[$roleId];
		$roleParentSeq=$roleInfo[1];

		$query="select * from ".$table_prefix."_role where parentrole like ? order by parentrole asc";
		$result=$adb->pquery($query, array($roleParentSeq."::%"));
		$num_rows=$adb->num_rows($result);
		$roleSubordinates=Array();
		for($i=0;$i<$num_rows;$i++)
		{
			$roleid=$adb->query_result($result,$i,'roleid');

			$roleSubordinates[]=$roleid;

		}
		// Update cache for re-use
		VTCacheUtils::updateRoleSubordinates($roleId, $roleSubordinates);
	}

	$log->debug("Exiting getRoleSubordinates method ...");
	return $roleSubordinates;

}

/** To retreive the subordinate vtiger_roles and vtiger_users of the specified parent vtiger_role
  * @param $roleid -- The Role Id:: Type varchar
  * @returns  subordinate vtiger_role array in the following format:
  *     $subordinateRoleUserArray=(roleid1=>Array(userid1,userid2,userid3),
                               vtiger_roleid2=>Array(userid1,userid2,userid3)
				                |
						|
			       vtiger_roleidn=>Array(userid1,userid2,userid3));
 */
function getSubordinateRoleAndUsers($roleId)
{
	global $log;
	$log->debug("Entering getSubordinateRoleAndUsers(".$roleId.") method ...");
	global $adb;
	$subRoleAndUsers=Array();
	$subordinateRoles=getRoleSubordinates($roleId);
	foreach($subordinateRoles as $subRoleId)
	{
		$userArray=getRoleUsers($subRoleId);
		$subRoleAndUsers[$subRoleId]=$userArray;

	}
	$log->debug("Exiting getSubordinateRoleAndUsers method ...");
	return $subRoleAndUsers;

}

function getCurrentUserProfileList($userid='')	//crmv@63421
{
	global $log;
	$log->debug("Entering getCurrentUserProfileList({$userid}) method ...");
	global $current_user;
	require('user_privileges/requireUserPrivileges.php'); // crmv@39110
	//crmv@49510
	if (empty($current_user_profiles)) {
		$current_user_profiles = getUserProfile($current_user->id);
	}
	//crmv@49510e
	$profList = array();
	$i=0;
	foreach ($current_user_profiles as $profid) {
		array_push($profList, $profid);
		$i++;
	}
	$log->debug("Exiting getCurrentUserProfileList method ...");
	return $profList;
}

function getCurrentUserGroupList()
{
	global $log;
	$log->debug("Entering getCurrentUserGroupList() method ...");
	global $current_user;
	require('user_privileges/requireUserPrivileges.php'); // crmv@39110
	$grpList= array();
	if(sizeof($current_user_groups) > 0)
	{
		$i=0;
		foreach ($current_user_groups as $grpid)
		{
			array_push($grpList, $grpid);
			$i++;
		}
	}
	$log->debug("Exiting getCurrentUserGroupList method ...");
	return $grpList;
}

function getSubordinateUsersList()
{
	global $log;
	$log->debug("Entering getSubordinateUsersList() method ...");
	global $current_user;
	$user_array=Array();
	require('user_privileges/requireUserPrivileges.php'); // crmv@39110

	if(sizeof($subordinate_roles_users) > 0)
	{
		foreach ($subordinate_roles_users as $roleid => $userArray)
		{
			foreach($userArray as $userid)
			{
				if(! in_array($userid,$user_array))
				{
					$user_array[]=$userid;
				}
			}
		}
	}
	$subUserList = constructList($user_array,'INTEGER');
	$log->debug("Exiting getSubordinateUsersList method ...");
	return $subUserList;
}

function getReadSharingUsersList($module)
{
	global $log;
	$log->debug("Entering getReadSharingUsersList(".$module.") method ...");
	global $adb,$table_prefix;
	global $current_user;
	$user_array=Array();
	$tabid=getTabid($module);
	$query = "select shareduserid from ".$table_prefix."_tmp_read_u_per where userid=? and tabid=?";
	$result=$adb->pquery($query, array($current_user->id, $tabid));
	$num_rows=$adb->num_rows($result);
	for($i=0;$i<$num_rows;$i++)
	{
		$user_id=$adb->query_result($result,$i,'shareduserid');
		$user_array[]=$user_id;
	}
	$shareUserList=constructList($user_array,'INTEGER');
	$log->debug("Exiting getReadSharingUsersList method ...");
	return $shareUserList;
}

function getReadSharingGroupsList($module)
{
	global $log;
	$log->debug("Entering getReadSharingGroupsList(".$module.") method ...");
	global $adb,$table_prefix;
	global $current_user;
	$grp_array=Array();
	$tabid=getTabid($module);
	$query = "select sharedgroupid from ".$table_prefix."_tmp_read_g_per where userid=? and tabid=?";
	$result=$adb->pquery($query, array($current_user->id, $tabid));
	$num_rows=$adb->num_rows($result);
	for($i=0;$i<$num_rows;$i++)
	{
		$grp_id=$adb->query_result($result,$i,'sharedgroupid');
		$grp_array[]=$grp_id;
	}
	$shareGrpList=constructList($grp_array,'INTEGER');
	$log->debug("Exiting getReadSharingGroupsList method ...");
	return $shareGrpList;
}

function getWriteSharingGroupsList($module)
{
	global $log;
	$log->debug("Entering getWriteSharingGroupsList(".$module.") method ...");
	global $adb,$table_prefix;
	global $current_user;
	$grp_array=Array();
	$tabid=getTabid($module);
	$query = "select sharedgroupid from ".$table_prefix."_tmp_write_g_per where userid=? and tabid=?";
	$result=$adb->pquery($query, array($current_user->id, $tabid));
	$num_rows=$adb->num_rows($result);
	for($i=0;$i<$num_rows;$i++)
	{
		$grp_id=$adb->query_result($result,$i,'sharedgroupid');
		$grp_array[]=$grp_id;
	}
	$shareGrpList=constructList($grp_array,'INTEGER');
	$log->debug("Exiting getWriteSharingGroupsList method ...");
	return $shareGrpList;
}


function constructList($array,$data_type)
{
	global $log;
	$log->debug("Entering constructList(".$array.",".$data_type.") method ...");
	$list= array();
	if(sizeof($array) > 0)
	{
		$i=0;
		foreach($array as $value)
		{
			if($data_type == "INTEGER")
			{
				array_push($list, $value);
			}
			elseif($data_type == "VARCHAR")
			{
				array_push($list, "'".$value."'");
			}
			$i++;
		}
	}
	$log->debug("Exiting constructList method ...");
	return $list;
}

function getListViewSecurityParameter($module)
{
	global $log;
	$log->debug("Entering getListViewSecurityParameter(".$module.") method ...");
	global $adb,$table_prefix;
	global $current_user;

	$tabid=getTabid($module);
	global $current_user;
	if($current_user)
	{
        	require('user_privileges/requireUserPrivileges.php'); // crmv@39110
        	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
	}

	if($module == 'Leads')
	{
		$sec_query .= " and (
						".$table_prefix."_crmentity.smownerid in($current_user->id)
						or ".$table_prefix."_crmentity.smownerid in(select ".$table_prefix."_user2role.userid from ".$table_prefix."_user2role inner join ".$table_prefix."_users on ".$table_prefix."_users.id=".$table_prefix."_user2role.userid inner join ".$table_prefix."_role on ".$table_prefix."_role.roleid=".$table_prefix."_user2role.roleid where ".$table_prefix."_role.parentrole like '".$current_user_parent_role_seq."::%')
						or ".$table_prefix."_crmentity.smownerid in(select shareduserid from ".$table_prefix."_tmp_read_u_per where userid=".$current_user->id." and tabid=".$tabid.")
						or (";

                        if(sizeof($current_user_groups) > 0)
                        {
                              $sec_query .= $table_prefix."_groups.groupid in (". implode(",", $current_user_groups) .") or ";
                        }
                         $sec_query .= $table_prefix."_groups.groupid in(select ".$table_prefix."_tmp_read_g_per.sharedgroupid from ".$table_prefix."_tmp_read_g_per where userid=".$current_user->id." and tabid=".$tabid.")) ";
	}
	elseif($module == 'Accounts')
	{
		$sec_query .= " and (".$table_prefix."_crmentity.smownerid in($current_user->id) " .
				"or ".$table_prefix."_crmentity.smownerid in(select ".$table_prefix."_user2role.userid from ".$table_prefix."_user2role inner join ".$table_prefix."_users on ".$table_prefix."_users.id=".$table_prefix."_user2role.userid inner join ".$table_prefix."_role on ".$table_prefix."_role.roleid=".$table_prefix."_user2role.roleid where ".$table_prefix."_role.parentrole like '".$current_user_parent_role_seq."::%') " .
				"or ".$table_prefix."_crmentity.smownerid in(select shareduserid from ".$table_prefix."_tmp_read_u_per where userid=".$current_user->id." and tabid=".$tabid.") or (";

                if(sizeof($current_user_groups) > 0)
                {
                	$sec_query .= $table_prefix."_groups.groupid in (". implode(",", $current_user_groups) .") or ";
                }
		$sec_query .= $table_prefix."_groups.groupid in(select ".$table_prefix."_tmp_read_g_per.sharedgroupid from ".$table_prefix."_tmp_read_g_per where userid=".$current_user->id." and tabid=".$tabid.")) ";

	}
	elseif($module == 'Contacts')
	{
		$sec_query .= " and (".$table_prefix."_crmentity.smownerid in($current_user->id) " .
				"or ".$table_prefix."_crmentity.smownerid in(select ".$table_prefix."_user2role.userid from ".$table_prefix."_user2role inner join ".$table_prefix."_users on ".$table_prefix."_users.id=".$table_prefix."_user2role.userid inner join ".$table_prefix."_role on ".$table_prefix."_role.roleid=".$table_prefix."_user2role.roleid where ".$table_prefix."_role.parentrole like '".$current_user_parent_role_seq."::%') " .
				"or ".$table_prefix."_crmentity.smownerid in(select shareduserid from ".$table_prefix."_tmp_read_u_per where userid=".$current_user->id." and tabid=".$tabid.") or (";

                if(sizeof($current_user_groups) > 0)
                {
                	$sec_query .= $table_prefix."_groups.groupid in (". implode(",", $current_user_groups) .") or ";
                }
		$sec_query .= $table_prefix."_groups.groupid in(select ".$table_prefix."_tmp_read_g_per.sharedgroupid from ".$table_prefix."_tmp_read_g_per where userid=".$current_user->id." and tabid=".$tabid."))";

	}
	elseif($module == 'Potentials')
	{
		$sec_query .= " and (".$table_prefix."_crmentity.smownerid in($current_user->id) " .
				"or ".$table_prefix."_crmentity.smownerid in(select ".$table_prefix."_user2role.userid from ".$table_prefix."_user2role inner join ".$table_prefix."_users on ".$table_prefix."_users.id=".$table_prefix."_user2role.userid inner join ".$table_prefix."_role on ".$table_prefix."_role.roleid=".$table_prefix."_user2role.roleid where ".$table_prefix."_role.parentrole like '".$current_user_parent_role_seq."::%') " .
				"or ".$table_prefix."_crmentity.smownerid in(select shareduserid from ".$table_prefix."_tmp_read_u_per where userid=".$current_user->id." and tabid=".$tabid.")";

		$sec_query .= " or (";

        if(sizeof($current_user_groups) > 0)
        {
        	$sec_query .= $table_prefix."_groups.groupid in (". implode(",", $current_user_groups) .") or ";
        }
		$sec_query .= $table_prefix."_groups.groupid in(select ".$table_prefix."_tmp_read_g_per.sharedgroupid from ".$table_prefix."_tmp_read_g_per where userid=".$current_user->id." and tabid=".$tabid.")) ";

	}
	elseif($module == 'HelpDesk')
	{
		$sec_query .= " and (".$table_prefix."_crmentity.smownerid in($current_user->id) or ".$table_prefix."_crmentity.smownerid in(select ".$table_prefix."_user2role.userid from ".$table_prefix."_user2role inner join ".$table_prefix."_users on ".$table_prefix."_users.id=".$table_prefix."_user2role.userid inner join ".$table_prefix."_role on ".$table_prefix."_role.roleid=".$table_prefix."_user2role.roleid where ".$table_prefix."_role.parentrole like '".$current_user_parent_role_seq."::%') or ".$table_prefix."_crmentity.smownerid in(select shareduserid from ".$table_prefix."_tmp_read_u_per where userid=".$current_user->id." and tabid=".$tabid.") ";

		$sec_query .= " or (";
                if(sizeof($current_user_groups) > 0)
                {
                	$sec_query .= $table_prefix."_groups.groupid in (". implode(",", $current_user_groups) .") or ";
                }
		$sec_query .= $table_prefix."_groups.groupid in(select ".$table_prefix."_tmp_read_g_per.sharedgroupid from ".$table_prefix."_tmp_read_g_per where userid=".$current_user->id." and tabid=".$tabid.")) ";

	}
	elseif($module == 'Emails')
	{
		$sec_query .= " and ".$table_prefix."_crmentity.smownerid=".$current_user->id." ";

	}
	elseif($module == 'Calendar')
	{
		require_once('modules/Calendar/CalendarCommon.php');
		$shared_ids = getSharedCalendarId($current_user->id);
		if(isset($shared_ids) && $shared_ids != '')
			$condition = " or (".$table_prefix."_crmentity.smownerid in($shared_ids))"; // crmv@70053
		else
			$condition = null;
		$sec_query .= " and (".$table_prefix."_crmentity.smownerid in($current_user->id) $condition or ".$table_prefix."_crmentity.smownerid in(select ".$table_prefix."_user2role.userid from ".$table_prefix."_user2role inner join ".$table_prefix."_users on ".$table_prefix."_users.id=".$table_prefix."_user2role.userid inner join ".$table_prefix."_role on ".$table_prefix."_role.roleid=".$table_prefix."_user2role.roleid where ".$table_prefix."_role.parentrole like '".$current_user_parent_role_seq."::%')";

		//crmv@17001
		if(sizeof($current_user_groups) > 0)
		{
			$sec_query .= " or ".$table_prefix."_groups.groupid in (". implode(",", $current_user_groups) .")";	//crmv@22282	//crmv@22452
		}
		$sec_query .= " OR ".$table_prefix."_activity.activityid IN(SELECT activityid FROM ".$table_prefix."_invitees WHERE inviteeid = $current_user->id)";
		//crmv@17001e
	}
	elseif($module == 'Quotes')
	{
		$sec_query .= " and (".$table_prefix."_crmentity.smownerid in($current_user->id) or ".$table_prefix."_crmentity.smownerid in(select ".$table_prefix."_user2role.userid from ".$table_prefix."_user2role inner join ".$table_prefix."_users on ".$table_prefix."_users.id=".$table_prefix."_user2role.userid inner join ".$table_prefix."_role on ".$table_prefix."_role.roleid=".$table_prefix."_user2role.roleid where ".$table_prefix."_role.parentrole like '".$current_user_parent_role_seq."::%') or ".$table_prefix."_crmentity.smownerid in(select shareduserid from ".$table_prefix."_tmp_read_u_per where userid=".$current_user->id." and tabid=".$tabid.")";

		//Adding crteria for group sharing
		 $sec_query .= " or ((";

                if(sizeof($current_user_groups) > 0)
                {
                	$sec_query .= $table_prefix."_groups.groupid in (". implode(",", $current_user_groups) .") or ";
                }
		$sec_query .= $table_prefix."_groups.groupid in(select ".$table_prefix."_tmp_read_g_per.sharedgroupid from ".$table_prefix."_tmp_read_g_per where userid=".$current_user->id." and tabid=".$tabid."))) ";

	}
	elseif($module == 'PurchaseOrder')
	{
		$sec_query .= " and (".$table_prefix."_crmentity.smownerid in($current_user->id) or ".$table_prefix."_crmentity.smownerid in(select ".$table_prefix."_user2role.userid from ".$table_prefix."_user2role inner join ".$table_prefix."_users on ".$table_prefix."_users.id=".$table_prefix."_user2role.userid inner join ".$table_prefix."_role on ".$table_prefix."_role.roleid=".$table_prefix."_user2role.roleid where ".$table_prefix."_role.parentrole like '".$current_user_parent_role_seq."::%') or ".$table_prefix."_crmentity.smownerid in(select shareduserid from ".$table_prefix."_tmp_read_u_per where userid=".$current_user->id." and tabid=".$tabid.") or (";

                if(sizeof($current_user_groups) > 0)
                {
                	$sec_query .= $table_prefix."_groups.groupid in (". implode(",", $current_user_groups) .") or ";
                }
		$sec_query .= $table_prefix."_groups.groupid in(select ".$table_prefix."_tmp_read_g_per.sharedgroupid from ".$table_prefix."_tmp_read_g_per where userid=".$current_user->id." and tabid=".$tabid.")) ";

	}
	elseif($module == 'SalesOrder')
	{
		$sec_query .= " and (".$table_prefix."_crmentity.smownerid in($current_user->id) or ".$table_prefix."_crmentity.smownerid in(select ".$table_prefix."_user2role.userid from ".$table_prefix."_user2role inner join ".$table_prefix."_users on ".$table_prefix."_users.id=".$table_prefix."_user2role.userid inner join ".$table_prefix."_role on ".$table_prefix."_role.roleid=".$table_prefix."_user2role.roleid where ".$table_prefix."_role.parentrole like '".$current_user_parent_role_seq."::%') or ".$table_prefix."_crmentity.smownerid in(select shareduserid from ".$table_prefix."_tmp_read_user_sharing_per where userid=".$current_user->id." and tabid=".$tabid.")";

		//Adding crteria for group sharing
		 $sec_query .= " or (";

                if(sizeof($current_user_groups) > 0)
                {
                	$sec_query .= $table_prefix."_groups.groupid in (". implode(",", $current_user_groups) .") or ";
                }
		$sec_query .= $table_prefix."_groups.groupid in(select ".$table_prefix."_tmp_read_g_per.sharedgroupid from ".$table_prefix."_tmp_read_g_per where userid=".$current_user->id." and tabid=".$tabid.")) ";

	}
	elseif($module == 'Invoice')
	{
		$sec_query .= " and (".$table_prefix."_crmentity.smownerid in($current_user->id) or ".$table_prefix."_crmentity.smownerid in(select ".$table_prefix."_user2role.userid from ".$table_prefix."_user2role inner join ".$table_prefix."_users on ".$table_prefix."_users.id=".$table_prefix."_user2role.userid inner join ".$table_prefix."_role on ".$table_prefix."_role.roleid=".$table_prefix."_user2role.roleid where ".$table_prefix."_role.parentrole like '".$current_user_parent_role_seq."::%') or ".$table_prefix."_crmentity.smownerid in(select shareduserid from ".$table_prefix."_tmp_read_u_per where userid=".$current_user->id." and tabid=".$tabid.")";

		//Adding crteria for group sharing
		 $sec_query .= " or ((";

                if(sizeof($current_user_groups) > 0)
                {
                	$sec_query .= $table_prefix."_groups.groupid in (". implode(",", $current_user_groups) .") or ";
                }
		$sec_query .= $table_prefix."_groups.groupid in(select ".$table_prefix."_tmp_read_g_per.sharedgroupid from ".$table_prefix."_tmp_read_g_per where userid=".$current_user->id." and tabid=".$tabid."))) ";

	}
	elseif($module == 'Campaigns')
	{

		$sec_query .= " and (".$table_prefix."_crmentity.smownerid in($current_user->id) or ".$table_prefix."_crmentity.smownerid in(select ".$table_prefix."_user2role.userid from ".$table_prefix."_user2role inner join ".$table_prefix."_users on ".$table_prefix."_users.id=".$table_prefix."_user2role.userid inner join ".$table_prefix."_role on ".$table_prefix."_role.roleid=".$table_prefix."_user2role.roleid where ".$table_prefix."_role.parentrole like '".$current_user_parent_role_seq."::%') or ".$table_prefix."_crmentity.smownerid in(select shareduserid from ".$table_prefix."_tmp_read_u_per where userid=".$current_user->id." and tabid=".$tabid.") or ((";

		if(sizeof($current_user_groups) > 0)
		{
			$sec_query .= $table_prefix."_groups.groupid in (". implode(",", $current_user_groups) .") or ";
		}
		$sec_query .= $table_prefix."_groups.groupid in(select ".$table_prefix."_tmp_read_g_per.sharedgroupid from ".$table_prefix."_tmp_read_g_per where userid=".$current_user->id." and tabid=".$tabid."))) ";


	}

	elseif($module == 'Documents')
	{
		$sec_query .= " and (".$table_prefix."_crmentity.smownerid in($current_user->id) or ".$table_prefix."_crmentity.smownerid in(select ".$table_prefix."_user2role.userid from ".$table_prefix."_user2role inner join ".$table_prefix."_users on ".$table_prefix."_users.id=".$table_prefix."_user2role.userid inner join ".$table_prefix."_role on ".$table_prefix."_role.roleid=".$table_prefix."_user2role.roleid where ".$table_prefix."_role.parentrole like '".$current_user_parent_role_seq."::%') or ".$table_prefix."_crmentity.smownerid in(select shareduserid from ".$table_prefix."_tmp_read_u_per where userid=".$current_user->id." and tabid=".$tabid.") or ((";

                if(sizeof($current_user_groups) > 0)
                {
                	$sec_query .= $table_prefix."_groups.groupid in (". implode(",", $current_user_groups) .") or ";
                }
		$sec_query .= $table_prefix."_groups.groupid in(select ".$table_prefix."_tmp_read_g_per.sharedgroupid from ".$table_prefix."_tmp_read_g_per where userid=".$current_user->id." and tabid=".$tabid."))) ";

	}

	//ds@26
	elseif($module == 'Visitreport')
	{
		$sec_query .= " and (".$table_prefix."_crmentity.smownerid in($current_user->id) or ".$table_prefix."_crmentity.smownerid in(select ".$table_prefix."_user2role.userid from ".$table_prefix."_user2role inner join ".$table_prefix."_users on ".$table_prefix."_users.id=".$table_prefix."_user2role.userid inner join ".$table_prefix."_role on ".$table_prefix."_role.roleid=".$table_prefix."_user2role.roleid where ".$table_prefix."_role.parentrole like '".$current_user_parent_role_seq."::%') or ".$table_prefix."_crmentity.smownerid in(select shareduserid from ".$table_prefix."_tmp_read_u_per where userid=".$current_user->id." and tabid=".$tabid.") or (".$table_prefix."_crmentity.smownerid in (0) and (";

                if(sizeof($current_user_groups) > 0)
                {
                	$sec_query .= $table_prefix."_groups.groupid in (". implode(",", $current_user_groups) .") or ";
                }
		$sec_query .= $table_prefix."_groups.groupid in(select ".$table_prefix."_tmp_read_g_per.sharedgroupid from ".$table_prefix."_tmp_read_g_per where userid=".$current_user->id." and tabid=".$tabid."))) ";
	}
	//ds@26e
	else
	{
		$modObj = CRMEntity::getInstance($module);
		$sec_query = $modObj->getListViewSecurityParameter($module);
		$default = true;
	}
	if (!$default){
	//crmv@7221
		$modObj = CRMEntity::getInstance($module);
		$sec_query.= $modObj->getListViewAdvSecurityParameter_list($module);
	//crmv@7221e
	}
	$log->debug("Exiting getListViewSecurityParameter method ...");
	return $sec_query;
}
function get_current_user_access_groups($module)
{
	global $log;
	$log->debug("Entering get_current_user_access_groups(".$module.") method ...");
	global $adb,$noof_group_rows,$table_prefix;
	$current_user_group_list=getCurrentUserGroupList();
	$sharing_write_group_list=getWriteSharingGroupsList($module);
	$query ="select groupname,groupid from ".$table_prefix."_groups";
	$params = array();
	if(count($current_user_group_list) > 0 && count($sharing_write_group_list) > 0)
	{
		$query .= " where (groupid in (". generateQuestionMarks($current_user_group_list) .") or groupid in (". generateQuestionMarks($sharing_write_group_list) ."))";
		array_push($params, $current_user_group_list, $sharing_write_group_list);
		$result = $adb->pquery($query, $params);
		$noof_group_rows=$adb->num_rows($result);
	}
	elseif(count($current_user_group_list) > 0)
	{
		$query .= " where groupid in (". generateQuestionMarks($current_user_group_list) .")";
		array_push($params, $current_user_group_list);
		$result = $adb->pquery($query, $params);
		$noof_group_rows=$adb->num_rows($result);
	}
	elseif(count($sharing_write_group_list) > 0)
	{
		$query .= " where groupid in (". generateQuestionMarks($sharing_write_group_list) .")";
		array_push($params, $sharing_write_group_list);
		$result = $adb->pquery($query, $params);
		$noof_group_rows=$adb->num_rows($result);
	}
	$log->debug("Exiting get_current_user_access_groups method ...");
	return $result;
}
/** Function to get the Group Id for a given group groupname
 *  @param $groupname -- Groupname
 *  @returns Group Id -- Type Integer
 */

function getGrpId($groupname)
{
	global $log;
	$log->debug("Entering getGrpId(".$groupname.") method ...");
	global $adb,$table_prefix;

	$result = $adb->pquery("select groupid from ".$table_prefix."_groups where groupname=?", array($groupname));
	$groupid = $adb->query_result($result,0,'groupid');
	$log->debug("Exiting getGrpId method ...");
	return $groupid;
}

/** Function to check permission to access a vtiger_field for a given user
  * @param $fld_module -- Module :: Type String
  * @param $userid -- User Id :: Type integer
  * @param $fieldname -- Field Name :: Type varchar
  * @returns $rolename -- Role Name :: Type varchar
  *
 */
function getFieldVisibilityPermission($fld_module, $userid, $fieldname)
{
	global $log;
	$log->debug("Entering getFieldVisibilityPermission(".$fld_module.",". $userid.",". $fieldname.") method ...");

	global $adb,$table_prefix;
	global $current_user;

	// Check if field is in-active
	$fieldActive = isFieldActive($fld_module,$fieldname);
	if($fieldActive == false) {
		return '1';
	}

	require('user_privileges/requireUserPrivileges.php'); // crmv@39110

	/* Asha: Fix for ticket #4508. Users with View all and Edit all permission will also have visibility permission for all fields */
	if($is_admin || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] ==0)
	{
		$log->debug("Exiting getFieldVisibilityPermission method ...");
		return '0';
	}
	else
	{
		//get vtiger_profile list using userid
		$profilelist = getCurrentUserProfileList($userid);	//crmv@63421

		//get tabid
		$tabid = getTabid($fld_module);

 		$query = "SELECT count(*) as cnt FROM ".$table_prefix."_field INNER JOIN ".$table_prefix."_def_org_field ON ".$table_prefix."_def_org_field.fieldid=".$table_prefix."_field.fieldid  WHERE ".$table_prefix."_field.tabid=? AND ".$table_prefix."_def_org_field.visible=0 and ".$table_prefix."_field.presence in (0,2) ";
 		$query.=" AND EXISTS(SELECT * FROM ".$table_prefix."_profile2field WHERE ".$table_prefix."_profile2field.fieldid = ".$table_prefix."_field.fieldid AND ".$table_prefix."_profile2field.profileid IN (". generateQuestionMarks($profilelist) .") AND ".$table_prefix."_profile2field.visible = 0) ";
 		$query.=" AND ".$table_prefix."_field.fieldname= ? and ".$table_prefix."_field.presence in (0,2)";
 		$params = array($tabid,$profilelist,$fieldname);
		$result = $adb->pquery($query, $params);
		$ret_val = 1;
		if ($result){
			if ($adb->query_result($result,0,'cnt') == 1)
				$ret_val = 0;
		}
		$log->debug("Exiting getFieldVisibilityPermission method ...");
		return $ret_val;
	}
}
/** Function to check permission to access the column for a given user
 * @param $userid -- User Id :: Type integer
 * @param $tablename -- tablename :: Type String
 * @param $columnname -- columnname :: Type String
 * @param $module -- Module Name :: Type varchar
 */
function getColumnVisibilityPermission($userid,$columnname, $module)
{
	global $adb,$log,$table_prefix;
	$log->debug("in function getcolumnvisibilitypermission $columnname -$userid");
	$tabid = getTabid($module);

	// Look at cache if information is available.
	$cacheFieldInfo = VTCacheUtils::lookupFieldInfoByColumn($tabid, $columnname);
	$fieldname = false;
	if($cacheFieldInfo === false) {
		$res = $adb->pquery("select fieldname from ".$table_prefix."_field where tabid=? and columnname=? and ".$table_prefix."_field.presence in (0,2)", array($tabid, $columnname));
		$fieldname = $adb->query_result($res, 0, 'fieldname');
	} else {
		$fieldname = $cacheFieldInfo['fieldname'];
	}

	return getFieldVisibilityPermission($module,$userid,$fieldname);
}

/** Function to get the vtiger_field access module array
  * @returns The vtiger_field Access module Array :: Type Array
  *
 */
function getFieldModuleAccessArray()
{
	global $log;
	global $adb;
	global $table_prefix;
	$log->debug("Entering getFieldModuleAccessArray() method ...");

	$fldModArr=Array();
	$query = 'select distinct(name) from '.$table_prefix.'_profile2field inner join '.$table_prefix.'_tab on '.$table_prefix.'_tab.tabid='.$table_prefix.'_profile2field.tabid';
	$result = $adb->pquery($query, array());
	$num_rows=$adb->num_rows($result);
	for($i=0;$i<$num_rows;$i++)
	{
		$fldModArr[$adb->query_result($result,$i,'name')]=$adb->query_result($result,$i,'name');
	}
	$log->debug("Exiting getFieldModuleAccessArray method ...");
	return $fldModArr;
}

/** Function to get the permitted module name Array with presence as 0
  * @returns permitted module name Array :: Type Array
  *
 */
function getPermittedModuleNames()
{
	global $log;
	$log->debug("Entering getPermittedModuleNames() method ...");
	global $current_user;
	$permittedModules=Array();

	require('user_privileges/requireUserPrivileges.php'); // crmv@39110
	include('tabdata.php');

	if($is_admin == false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1)
	{
		foreach($tab_seq_array as $tabid=>$seq_value)
		{
			if($seq_value ==0 && $profileTabsPermission[$tabid] == 0)
			{
				$permittedModules[]=getTabModuleName($tabid);
			}

		}
	}
	else
	{
		foreach($tab_seq_array as $tabid=>$seq_value)
		{
			if($seq_value ==0)
			{
				$permittedModules[]=getTabModuleName($tabid);
			}

		}
	}
	$log->debug("Exiting getPermittedModuleNames method ...");
	return $permittedModules;
}

/**
 * Function to get the permitted module id Array with presence as 0
 * @global Users $current_user
 * @return Array Array of accessible tabids.
 */
function getPermittedModuleIdList() {
	global $current_user;
	$permittedModules=Array();

	require('user_privileges/requireUserPrivileges.php'); // crmv@39110
	include('tabdata.php');

	if($is_admin == false && $profileGlobalPermission[1] == 1 &&
			$profileGlobalPermission[2] == 1) {
		foreach($tab_seq_array as $tabid=>$seq_value) {
			if($seq_value === 0 && $profileTabsPermission[$tabid] === 0) {
				$permittedModules[]=($tabid);
			}
		}
	} else {
		foreach($tab_seq_array as $tabid=>$seq_value) {
			if($seq_value === 0) {
				$permittedModules[]=($tabid);
			}
		}
	}
	return $permittedModules;
}

/** 
 * Function to recalculate the Sharing Rules for all the users,
 * or only for the ones specified in the first parameter
 */
// crmv@74560
function RecalculateSharingRules($array_users='', $checkFn = null) {
	global $adb,$table_prefix;
	global $log, $metaLogs; //crmv@49398
	$log->debug("Entering RecalculateSharingRules() method ...");
	
	require_once('modules/Users/CreateUserPrivilegeFile.php');
	$query="select id from ".$table_prefix."_users where deleted=0 ";
	//crmv@20209
	if(is_array($array_users)) {
		if(sizeof($array_users) > 0){
			$ultimo = sizeof($array_users) - 1;
			unset($array_users[$ultimo]);
			$query .= " AND id IN (".implode(',',$array_users).")";
		}
	}
	//crmv@20209e
	$result=$adb->pquery($query, array());
	$num_rows=$adb->num_rows($result);
	for ($i=0; $i<$num_rows; ++$i) {
		// userid
		$id=$adb->query_result_no_html($result,$i,'id');
		
		// check if I should interrupt the recalc
		if ($checkFn && is_callable($checkFn)) {
			if (call_user_func($checkFn, $id) === false) {
				$log->debug("RecalculateSharingRules method interrupted.");
				return false;
			}
		}
		
		// generate the files
		createUserPrivilegesfile($id);
		createUserPrivilegesfile($id, 1); // crmv@39110
		createUserSharingPrivilegesfile($id);
	}
	//if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_REBUILDSHARES); // crmv@49398
	$log->debug("Exiting RecalculateSharingRules method ...");
	return true;
}
// crmv@74560e


/** Function to get the list of module for which the user defined sharing rules can be defined
  * @returns Array:: Type array
  *
  */
function getSharingModuleList($eliminateModules=false)
{
	global $log;

	$sharingModuleArray = Array();

	global $adb,$table_prefix;
	if(empty($eliminateModules)) $eliminateModules = Array();

	// Module that needs to be eliminated explicitly
	if(!in_array('Calendar', $eliminateModules)) $eliminateModules[] = 'Calendar';
	if(!in_array('Events', $eliminateModules)) $eliminateModules[] = 'Events';
	//crmv@47243
	if(!in_array('ChangeLog', $eliminateModules)) $eliminateModules[] = 'ChangeLog';
	if(!in_array('Sms', $eliminateModules)) $eliminateModules[] = 'Sms';
	if(!in_array('ModComments', $eliminateModules)) $eliminateModules[] = 'ModComments';
	if(!in_array('ModNotifications', $eliminateModules)) $eliminateModules[] = 'ModNotifications';
	if(!in_array('MyNotes', $eliminateModules)) $eliminateModules[] = 'MyNotes';
	if(!in_array('MyFiles', $eliminateModules)) $eliminateModules[] = 'MyFiles';
	//crmv@47243e

	$query = "SELECT name FROM ".$table_prefix."_tab WHERE presence=0 AND ownedby = 0 AND isentitytype = 1";
	$query .= " AND name NOT IN('" . implode("','", $eliminateModules) . "')";

	$result = $adb->query($query);
	while($resrow = $adb->fetch_array($result)) {
		$sharingModuleArray[] = $resrow['name'];
	}

	return $sharingModuleArray;
}


function isCalendarPermittedBySharing($recordId)
{
	global $adb,$table_prefix;
	global $current_user;
	$permission = 'no';
	// crmv@70053
	$query = "
		select userid
		from ".$table_prefix."_sharedcalendar 
		where userid in (
			select smownerid 
			from ".$table_prefix."_activity 
			inner join ".$table_prefix."_crmentity on ".$table_prefix."_crmentity.crmid=".$table_prefix."_activity.activityid 
			where activityid=? and visibility != 'Private' and smownerid != 0
		) and sharedid=?";
	// crmv@70053e
	$result=$adb->pquery($query, array($recordId, $current_user->id));
	if($adb->num_rows($result) >0)
	{
		$permission = 'yes';
	}
	return $permission;
}
/*
 *  * Function to populate default entries for the picklist while creating a new role--vashni
 *   */
function insertRole2Picklist($roleid,$parentroleid)
{
	global $adb,$log,$table_prefix;
	$log->debug("Entering into the function insertRole2Picklist($roleid,$parentroleid)");
	$sql = "insert into ".$table_prefix."_role2picklist select '".$roleid."',picklistvalueid,picklistid,sortid from ".$table_prefix."_role2picklist where roleid=?";
	$adb->pquery($sql, array($parentroleid));
	$log->debug("Exiting from the function insertRole2Picklist($roleid,$parentroleid)");
}
//end

//ds@8 project tool
/**
 * @deprecated
 */
function isProjectAdmin()
{
	global $current_user;

	require('user_privileges/requireUserPrivileges.php'); // crmv@39110
	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');

	if ((isset($profileActionPermission[getTabid('Projects')]["1"]) && $profileActionPermission[getTabid('Projects')]["1"] < 1) || is_admin($current_user))
		return TRUE;
  	else
    	return FALSE;

}

/**
 * @deprecated
 */
function isProjectLeader()
{
	global $current_user;
	require('user_privileges/requireUserPrivileges.php'); // crmv@39110
	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');

	if (isset($profileActionPermission[getTabid('Projects')]["2"]) && $profileActionPermission[getTabid('Projects')]["2"] < 1)
    	return TRUE;
  	else
   		return FALSE;
}
//ds@8e


/** Function get the Data Share Table and their columns
  * @returns -- Data Share Table and Column Array in the following format:
  *  $dataShareTableColArr=Array('datashare_grp2grp'=>'share_groupid::to_groupid',
  *				    'datashare_usr2usr'=>'share_userid::to_userid');
  */
function getDataShareTableandColumnArrayUser()
{
	global $log,$table_prefix;
	$log->debug("Entering getDataShareTableandColumnArrayUser() method ...");
	$dataShareTableColArr=Array($table_prefix.'_datashare_usr2usr'=>'share_userid::to_userid');
	$log->debug("Exiting getDataShareTableandColumnArrayUser method ...");
	return $dataShareTableColArr;

}
//crmv@7222
/** returns the list of sharing rules for the specified module
  * @param $module -- Module Name:: Type varchar
  * @param $userid -- User Id:: Type int
  * @returns $access_permission -- sharing rules list info array:: Type array
  *
 */
function getSharingRuleListUser($module,$userid)
{
	global $adb,$mod_strings,$table_prefix;

	$tabid=getTabid($module);
	$dataShareTableArray=getDataShareTableandColumnArrayUser();

	$i=1;
	$access_permission = array();
	foreach($dataShareTableArray as $table_name => $colName)
	{

		$colNameArr=explode("::",$colName);
		$query = "select ".$table_name.".* from ".$table_name." inner join ".$table_prefix."_datashare_mod_rel on ".$table_name.".shareid=".$table_prefix."_datashare_mod_rel.shareid where ".$table_prefix."_datashare_mod_rel.tabid=? and to_userid =?";
		$result=$adb->pquery($query, array($tabid,$userid));
		$num_rows=$adb->num_rows($result);

		$share_colName=$colNameArr[0];
		$share_modType=getEntityTypeFromCol($share_colName);

		$to_colName=$colNameArr[1];
		$to_modType=getEntityTypeFromCol($to_colName);

		for($j=0;$j<$num_rows;$j++)
		{
			$shareid=$adb->query_result($result,$j,"shareid");
			$share_id=$adb->query_result($result,$j,$share_colName);
			$to_id=$adb->query_result($result,$j,$to_colName);
			$permission = $adb->query_result($result,$j,'permission');

			$share_ent_disp = getEntityDisplayLink($share_modType,$share_id);
			$to_ent_disp = getEntityDisplayLink($to_modType,$to_id);

			if($permission == 0)
			{
				$perr_out = $mod_strings['Read Only '];
			}
			elseif($permission == 1)
			{
				$perr_out = $mod_strings['Read/Write'];
			}

			$access_permission [] = $shareid;
			$access_permission [] = $share_ent_disp;
			$access_permission [] = $to_ent_disp;
			$access_permission [] = $perr_out;

			$i++;
		}

	}
	if(is_array($access_permission))
		$access_permission = array_chunk($access_permission,4);
	return $access_permission;
}

/** Function to get all  the vtiger_role information
  * @returns $allRoleDetailArray-- Array will contain the details of all the vtiger_roles. RoleId will be the key:: Type array
 */
function getAllRoleDetailsUser()
{
global $log;
$log->debug("Entering getAllRoleDetailsUser() method ...");
	global $adb,$table_prefix;
	$role_det = Array();
	$query = "select * from ".$table_prefix."_users";
	$result = $adb->pquery($query, array());
	$num_rows=$adb->num_rows($result);
	for($i=0; $i<$num_rows;$i++)
	{
		$each_role_det = Array();
		$roleid=$adb->query_result($result,$i,'id');
		$rolename=$adb->query_result($result,$i,'user_name');
		$each_role_det[]=$rolename;
		$each_role_det[]=$roledepth;
		$each_role_det[]=$sub_role;
		$role_det[$roleid]=$each_role_det;

	}
	$log->debug("Exiting getAllRoleDetailsUser method ...");
	return $role_det;
}
//crmv@7222e
//crmv@7221

/** Function to get the permission for view/modify entities for requested module according advanced filters for module sharing
  * @param $id -- record id:: Type int
  * @param $module -- Module name:: Type varchar
  * @param $mode -- Permission Mode:: Type varchar
  * @returns $permission -- the response of the security request:: Type varchar
 */
function isAdvancedShared($id,$module,$mode){
	global $adb;
	$resid= getAdvancedresid($id,$module,$mode);
	if ($id == $resid) return 'yes';
	else return 'no';
}

/** Function to get the permission for view/modify entities for requested paent module related to module according advanced filters for module sharing
  * @param $id -- record id:: Type int
  * @param $module -- Module name:: Type varchar
  * @returns $permission -- the response of the security request:: Type varchar
 */
function isParentAdvancedShared($id,$module,$parModId,$mode){
	return 'no';
	global $adb;
	$resid= getParentAdvancedresid($id,$module,$parModId,$mode);
	if ($id == $resid) return 'yes';
	else return 'no';
}

/** Function to get the id of the record that soddisfy the advanced filters for module sharing
  * @param $id -- record id:: Type int
  * @param $module -- Module name:: Type varchar
  * @param $mode -- Permission Mode:: Type varchar
  * @returns $resid -- the resulting entity id:: Type int
 */
function getAdvancedresid($id,$module,$mode){
	global $adb,$current_user;
	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
	//crmv@17317
	if ($module == 'Contacts')
		$mod = "Accounts_".$module;
	else
		$mod = $module;
	if (${$mod."_adv_role"}[$mode] != ''){
		$backup_id = $current_user->id;
		$current_user->id = 1;
		$obj = CRMEntity::getInstance($module);
		$LVU = ListViewUtils::getInstance();
		$module_table=$obj->table_name;
		$module_pk=$obj->tab_name_index[$obj->table_name];
		$where = "and $module_table.$module_pk = $id ".${$mod."_adv_role"}[$mode];
		$query = $LVU->getListQuery($module, $where);
		$current_user->id = $backup_id;
		$result = $adb->query(replaceSelectQuery($query,"$module_table.$module_pk"));
		if ($result)
			$resid=$adb->query_result($result,0,$module_pk);
		return $resid;
	}
	//crmv@17317 end
	return null;
}

/** Function to get the id of the record that soddisfy the advanced filters for module related to module sharing
  * @param $id -- record id:: Type int
  * @param $module -- Module name:: Type varchar
  * @param $mode -- Permission Mode:: Type varchar
  * @returns $resid -- the resulting entity id:: Type int
 */
function getParentAdvancedresid($id,$module,$parModId,$mode){
	global $adb,$current_user;
	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
	$parentmodule=getTabname($parModId);
	if ( ${$parentmodule."_".$module."_adv_role"}[$mode] != ''){
		$backup_id = $current_user->id;
		$current_user->id = 1;
		$obj = CRMEntity::getInstance($module);
		$LVU = ListViewUtils::getInstance();
		$module_table=$obj->table_name;
		$module_pk=$obj->tab_name_index[$obj->table_name];
		$relobj = CRMEntity::getInstance($parentmodule);
		$module_table=$obj->table_name;
		$relmodule_pk=$relobj->tab_name_index[$relobj->table_name];
		$where = "and $module_table.$module_pk = $id  ".${$parentmodule."_".$module."_adv_role"}[$mode];
		$query = $LVU->getListQuery($module, $where);
		$current_user->id = $backup_id;
		$result = $adb->query(replaceSelectQuery($query,"$module_table.$module_pk"));
		if ($result)
			$resid=$adb->query_result($result,0,$module_pk);
		return $resid;
	}
	return null;
}

/** Function to get the query to append to SecurityParameter that extends the resultset according advanced filters for module sharing
  * @param $module -- Module name:: Type varchar
  * @param $mode -- Permission Mode:: Type varchar
  * @returns $query -- the resulting sql conditions:: Type varchar
 */
function getAdvancedresList($module,$mode){
	global $adb,$current_user;
	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
	//crmv@17317
	if ($module == 'Contacts') {
    	$module= "Accounts_".$module;
	}
	//crmv@17317 end
	if (${$module."_adv_role"}[$mode] != ''){
		if ($mode == 'columns')
			$query= ${$module."_adv_role"}[$mode];
		else
			$query= " or ".${$module."_adv_role"}[$mode];
		return $query;
	}
	//crmv@27824
	if ($mode == 'columns')
		return '';
	else
	//crmv@27824e
	return null;
}

/** Function to get the query to append to SecurityParameter that extends the resultset according advanced filters for module related to module sharing
  * @param $module -- Module name:: Type varchar
  * @param $parentmodule -- Parent Module name:: Type varchar
  * @returns $mode -- the resulting sql conditions:: Type varchar
 */
function getParentAdvancedresList($module,$parentmodule,$mode){
	global $adb,$current_user;
	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
	//crmv@17317
	if ($parentmodule == 'Contacts') {
    	$parentmodule= "Accounts_".$parentmodule;
	}
	//crmv@17317 end
	if (${$parentmodule."_adv_role"}[$mode] != ''){
		if ($mode == 'columns')
			$query= ${$parentmodule."_adv_role"}[$mode];
		else{
			//TODO use all rel tables
			$backup_id = $current_user->id;
			$current_user->id = 1;
			$obj = CRMEntity::getInstance($module);
			$LVU = ListViewUtils::getInstance();
			$module_table=$obj->table_name;
			$module_pk=$obj->tab_name_index[$obj->table_name];
			$where = "and ".${$parentmodule."_adv_role"}[$mode];
			$query = " or $module_table.$module_pk in ( ".replaceSelectQuery($LVU->getListQuery($module, $where),"$module_table.$module_pk")." )";
			$current_user->id = $backup_id;
		}
		return $query;
	}
	return null;
}
//crmv@7221e
/** Function to delete group to report relation of the  specified group
  * @param $groupId -- Group Id :: Type integer
 */
function deleteGroupReportRelations($groupId)
{
	global $log;
	$log->debug("Entering deleteGroupReportRelations(".$groupId.") method ...");
	global $adb,$table_prefix;
	$query="delete from ".$table_prefix."_reportsharing where shareid=? and setype='groups'";
	$adb->pquery($query, array($groupId));
	$log->debug("Exiting deleteGroupReportRelations method ...");
}
//end

/** Function to check if the field is Active
 *  @params  $modulename -- Module Name :: String Type
 *   		 $fieldname  -- Field Name  :: String Type
 */
function isFieldActive($modulename,$fieldname){
	$fieldid = getFieldid(getTabid($modulename), $fieldname, true);
	return ($fieldid !== false);
}

/**
 *
 * @param String $module - module name for which query needs to be generated.
 * @param Users $user - user for which query needs to be generated.
 * @return String Access control Query for the user.
 */
function getNonAdminAccessControlQuery($module,$user,$scope='',$join_cond=''){	//crmv@31775
	$instance = CRMEntity::getInstance($module);
	return $instance->getNonAdminAccessControlQuery($module,$user,$scope,$join_cond);	//crmv@31775
}

function appendFromClauseToQuery($query,$fromClause) {
	$query = preg_replace('/\s+/', ' ', $query);
	//crmv@16643 : stripos al posto di strripos
	$condition = substr($query, stripos($query,' where '),strlen($query));
	$newQuery = substr($query, 0, stripos($query,' where '));
	//crmv@16643e
	$query = $newQuery.$fromClause.$condition;
	return $query;
}

//crmv@17001 : Inviti	//crmv@20324
function isCalendarInvited($ownerId,$record_id) {
	global $adb,$current_user,$table_prefix;
	$permission = 'no';
	$answer = '';
	$activitytype = '--';
	$query = "SELECT * FROM ".$table_prefix."_invitees WHERE inviteeid = ? AND activityid = ?";
	$result=$adb->pquery($query, array($ownerId, $record_id));
	if($adb->num_rows($result) >0)
	{
		$permission = 'yes';
		switch($adb->query_result($result,0,'partecipation')) {
			case 0: $answer = 'pending'; break;
			case 1: $answer = 'no'; break;
			case 2: $answer = 'yes'; break;
			case 3: $answer = 'maybe'; break;
		}
		$activityid = $adb->query_result($result,0,'activityid');
		$queryAct = "SELECT activitytype FROM ".$table_prefix."_activity WHERE activityid = ?";
		$resultAct=$adb->pquery($queryAct, array($activityid));
		if($adb->num_rows($resultAct) >0) {
			$activitytype = $adb->query_result($resultAct,0,'activitytype');
		}
	}
	return array($permission,$answer,$activitytype);
}
//crmv@17001e	//crmv@20324e

