<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

$new_tables = 0;

require_once('config.php');
require_once('include/logging.php');
require_once('modules/Leads/Leads.php');
require_once('modules/Contacts/Contacts.php');
require_once('modules/Accounts/Accounts.php');
require_once('modules/Potentials/Potentials.php');
require_once('modules/Calendar/Activity.php');
require_once('modules/Documents/Documents.php');
require_once('modules/Emails/Emails.php');
require_once('modules/Users/Users.php');
require_once('modules/Users/LoginHistory.php');
require_once('data/Tracker.php');
require_once('include/utils/utils.php');
require_once('modules/Users/DefaultDataPopulator.php');
require_once('modules/Users/CreateUserPrivilegeFile.php');

// load the config_override.php file to provide default user settings
if (is_file("config_override.php")) {
	require_once("config_override.php");
}

$adb = PearDatabase::getInstance();
$log =& LoggerManager::getLogger('INSTALL');

function create_default_users_access() {
	global $log, $adb, $table_prefix;
	global $admin_email;
	global $admin_password;
	global $default_language, $default_timezone; // crmv@25610

	$role1_id = $adb->getUniqueID($table_prefix."_role");
	$role2_id = $adb->getUniqueID($table_prefix."_role");
	$role3_id = $adb->getUniqueID($table_prefix."_role");

	$profile1_id = $adb->getUniqueID($table_prefix."_profile");
	$profile2_id = $adb->getUniqueID($table_prefix."_profile");

	$adb->query("insert into ".$table_prefix."_role values('H".$role1_id."','Organisation','H".$role1_id."',0)");
	$adb->query("insert into ".$table_prefix."_role values('H".$role2_id."','Manager','H".$role1_id."::H".$role2_id."',1)");
	$adb->query("insert into ".$table_prefix."_role values('H".$role3_id."','Agent','H".$role1_id."::H".$role2_id."::H".$role3_id."',2)");

	//Insert into vtiger_role2profile
	// crmv@39110
	$adb->query("insert into ".$table_prefix."_role2profile (roleid,profileid) values ('H".$role2_id."',".$profile1_id.")");
	$adb->query("insert into ".$table_prefix."_role2profile (roleid,profileid) values ('H".$role3_id."',".$profile2_id.")");
	// crmv@39110e

	//New Security Start
	//Inserting into vtiger_profile vtiger_table
	// crmv@39110
	$adb->query("insert into ".$table_prefix."_profile (profileid, profilename, description) values ('".$profile1_id."','Administrator','Admin Profile')");
	$adb->query("insert into ".$table_prefix."_profile (profileid, profilename, description) values ('".$profile2_id."','Sales Profile','Profile Related to Sales')");
	// crmv@39110e

	//Inserting into vtiger_profile2gloabal permissions
	$adb->query("insert into ".$table_prefix."_profile2globalperm values ('".$profile1_id."',1,0)");
	$adb->query("insert into ".$table_prefix."_profile2globalperm values ('".$profile1_id."',2,0)");
	$adb->query("insert into ".$table_prefix."_profile2globalperm values ('".$profile2_id."',1,1)");
	$adb->query("insert into ".$table_prefix."_profile2globalperm values ('".$profile2_id."',2,1)");

	//Inserting into vtiger_profile2tab
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile1_id.",1,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile1_id.",2,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile1_id.",3,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile1_id.",4,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile1_id.",6,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile1_id.",7,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile1_id.",8,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile1_id.",9,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile1_id.",10,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile1_id.",13,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile1_id.",14,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile1_id.",15,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile1_id.",16,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile1_id.",18,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile1_id.",19,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile1_id.",20,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile1_id.",21,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile1_id.",22,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile1_id.",23,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile1_id.",24,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile1_id.",25,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile1_id.",26,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile1_id.",27,0)");

	//Inserting into vtiger_profile2tab
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile2_id.",1,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile2_id.",2,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile2_id.",3,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile2_id.",4,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile2_id.",6,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile2_id.",7,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile2_id.",8,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile2_id.",9,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile2_id.",10,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile2_id.",13,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile2_id.",14,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile2_id.",15,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile2_id.",16,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile2_id.",18,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile2_id.",19,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile2_id.",20,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile2_id.",21,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile2_id.",22,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile2_id.",23,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile2_id.",24,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile2_id.",25,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile2_id.",26,0)");
	$adb->query("insert into ".$table_prefix."_profile2tab values (".$profile2_id.",27,0)");

	//Inserting into vtiger_profile2standardperm  Adminsitrator
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",2,0,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",2,1,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",2,2,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",2,3,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",2,4,0)");

	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",4,0,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",4,1,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",4,2,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",4,3,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",4,4,0)");

	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",6,0,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",6,1,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",6,2,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",6,3,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",6,4,0)");

	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",7,0,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",7,1,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",7,2,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",7,3,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",7,4,0)");

	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",8,0,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",8,1,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",8,2,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",8,3,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",8,4,0)");

	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",9,0,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",9,1,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",9,2,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",9,3,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",9,4,0)");

	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",13,0,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",13,1,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",13,2,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",13,3,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",13,4,0)");

	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",14,0,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",14,1,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",14,2,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",14,3,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",14,4,0)");

	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",15,0,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",15,1,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",15,2,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",15,3,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",15,4,0)");

	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",16,0,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",16,1,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",16,2,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",16,3,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",16,4,0)");

	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",18,0,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",18,1,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",18,2,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",18,3,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",18,4,0)");

	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",19,0,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",19,1,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",19,2,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",19,3,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",19,4,0)");

	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",20,0,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",20,1,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",20,2,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",20,3,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",20,4,0)");

	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",21,0,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",21,1,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",21,2,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",21,3,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",21,4,0)");

	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",22,0,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",22,1,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",22,2,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",22,3,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",22,4,0)");

	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",23,0,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",23,1,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",23,2,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",23,3,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",23,4,0)");

	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",26,0,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",26,1,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",26,2,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",26,3,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile1_id.",26,4,0)");

	//Insert into Profile 2 std permissions for Sales User
	//Help Desk Create/Delete not allowed. Read-Only
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",2,0,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",2,1,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",2,2,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",2,3,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",2,4,0)");

	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",4,0,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",4,1,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",4,2,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",4,3,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",4,4,0)");

	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",6,0,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",6,1,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",6,2,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",6,3,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",6,4,0)");

	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",7,0,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",7,1,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",7,2,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",7,3,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",7,4,0)");

	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",8,0,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",8,1,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",8,2,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",8,3,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",8,4,0)");

	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",9,0,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",9,1,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",9,2,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",9,3,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",9,4,0)");

	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",13,0,1)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",13,1,1)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",13,2,1)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",13,3,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",13,4,0)");

	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",14,0,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",14,1,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",14,2,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",14,3,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",14,4,0)");

	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",15,0,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",15,1,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",15,2,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",15,3,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",15,4,0)");

	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",16,0,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",16,1,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",16,2,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",16,3,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",16,4,0)");

	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",18,0,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",18,1,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",18,2,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",18,3,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",18,4,0)");

	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",19,0,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",19,1,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",19,2,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",19,3,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",19,4,0)");

	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",20,0,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",20,1,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",20,2,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",20,3,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",20,4,0)");

	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",21,0,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",21,1,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",21,2,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",21,3,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",21,4,0)");

	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",22,0,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",22,1,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",22,2,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",22,3,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",22,4,0)");

	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",23,0,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",23,1,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",23,2,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",23,3,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",23,4,0)");

	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",26,0,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",26,1,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",26,2,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",26,3,0)");
	$adb->query("insert into ".$table_prefix."_profile2standardperm values (".$profile2_id.",26,4,0)");

	//Inserting into vtiger_profile 2 utility Admin
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile1_id.",2,5,0)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile1_id.",2,6,0)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile1_id.",4,5,0)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile1_id.",4,6,0)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile1_id.",6,5,0)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile1_id.",6,6,0)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile1_id.",7,5,0)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile1_id.",7,6,0)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile1_id.",8,6,0)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile1_id.",7,8,0)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile1_id.",6,8,0)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile1_id.",4,8,0)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile1_id.",13,5,0)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile1_id.",13,6,0)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile1_id.",13,8,0)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile1_id.",14,5,0)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile1_id.",14,6,0)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile1_id.",7,9,0)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile1_id.",18,5,0)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile1_id.",18,6,0)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile1_id.",7,10,0)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile1_id.",6,10,0)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile1_id.",4,10,0)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile1_id.",2,10,0)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile1_id.",13,10,0)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile1_id.",14,10,0)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile1_id.",18,10,0)");

	//Inserting into vtiger_profile2utility Sales Profile
	//Import Export Not Allowed.
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile2_id.",2,5,1)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile2_id.",2,6,1)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile2_id.",4,5,1)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile2_id.",4,6,1)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile2_id.",6,5,1)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile2_id.",6,6,1)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile2_id.",7,5,1)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile2_id.",7,6,1)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile2_id.",8,6,1)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile2_id.",7,8,0)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile2_id.",6,8,0)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile2_id.",4,8,0)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile2_id.",13,5,1)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile2_id.",13,6,1)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile2_id.",13,8,0)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile2_id.",14,5,1)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile2_id.",14,6,1)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile2_id.",7,9,0)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile2_id.",18,5,1)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile2_id.",18,6,1)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile2_id.",7,10,0)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile2_id.",6,10,0)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile2_id.",4,10,0)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile2_id.",2,10,0)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile2_id.",13,10,0)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile2_id.",14,10,0)");
	$adb->query("insert into ".$table_prefix."_profile2utility values (".$profile2_id.",18,10,0)");

	// Invalidate any cached information
	VTCacheUtils::clearRoleSubordinates();

	// create default admin user
	$user = CRMEntity::getInstance('Users');
	$user->column_fields["last_name"] = 'Administrator';
	$user->column_fields["user_name"] = 'admin';
	$user->column_fields["status"] = 'Active';
	$user->column_fields["is_admin"] = 'on';
	$user->column_fields["user_password"] = $admin_password;
	$user->column_fields["tz"] = 'Europe/Berlin';
	$user->column_fields["holidays"] = 'de,en_uk,fr,it,us,';
	$user->column_fields["workdays"] = '0,1,2,3,4,5,6,';
	$user->column_fields["weekstart"] = '1';
	$user->column_fields["namedays"] = '';
	$user->column_fields["currency_id"] = 1;
	$user->column_fields["reminder_interval"] = '1 Minute';
	$user->column_fields["reminder_next_time"] = date('Y-m-d H:i');
	//crmv@17001	//crmv@25390
	$user->column_fields["date_format"] = 'dd-mm-yyyy';
	$user->column_fields["hour_format"] = '24';
	$user->column_fields["start_hour"] = '08:00';
	$user->column_fields["end_hour"] = '';
	$user->column_fields["menu_view"] = 'Large Menu';
	$user->column_fields["no_week_sunday"] = 1;
	//crmv@17001e	//crmv@25390e
	$user->column_fields["imagename"] = '';
	$user->column_fields["internal_mailer"] = '1';
	$user->column_fields["activity_view"] = 'This Week';
	$user->column_fields["lead_view"] = 'Today';
	//added by philip for default admin emailid
	if($admin_email == '')
		$admin_email ="admin@vteruser.com";
	$user->column_fields["email1"] = $admin_email;
	$role_query = "select roleid from ".$table_prefix."_role where rolename='Manager'";
	$role_result = $adb->query($role_query);
	$role_id = $adb->query_result($role_result,0,"roleid");
	$user->column_fields["roleid"] = $role_id;
	//crmv@29506
	$user->column_fields["allow_generic_talks"] = 1;
	$user->column_fields["receive_public_talks"] = 1;
	//crmv@29506e
	$user->column_fields["notify_me_via"] = 'ModNotifications';	//crmv@29617
	$user->column_fields["user_timezone"] = $default_timezone;	//crmv@25610
	$user->column_fields["default_language"] = $default_language;
	// crmv@42024
	global $default_decimal_separator, $default_thousands_separator, $default_decimals_num;
	$user->column_fields["decimal_separator"] = $default_decimal_separator;
	$user->column_fields["thousands_separator"] = $default_thousands_separator;
	$user->column_fields["decimals_num"] = $default_decimals_num;
	// crmv@42024e

	$user->save("Users");
	$admin_user_id = $user->id;

	$user->saveLastChangePassword($admin_user_id);	//crmv@28327

	updateUser2RoleMapping($role_id,$admin_user_id);

	$adb->pquery('update '.$table_prefix.'_users set cal_color=? where id=?',array(calculateCalColor(),$admin_user_id));	//crmv@20047

	//Inserting into vtiger_groups table
	$group2_id = $adb->getUniqueID($table_prefix."_users");
	$group3_id = $adb->getUniqueID($table_prefix."_users");

	// crmv@49398
	$now = date('Y-m-d H:i:s');
	$grp1 = array($group2_id, 'Marketing Group', $now, $now, 'Group Related to Marketing Activities');
	$adb->pquery("insert into ".$table_prefix."_groups (groupid,groupname,date_entered,date_modified,description) values (?,?,?,?,?)", $grp1);
	$adb->query("insert into ".$table_prefix."_group2role values ('".$group2_id."','H".$role2_id."')");
	$adb->query("insert into ".$table_prefix."_group2rs values ('".$group2_id."','H".$role3_id."')");

	$grp2 = array($group3_id, 'Support Group', $now, $now, 'Group Related to providing Support to Customers');
	$adb->pquery("insert into ".$table_prefix."_groups (groupid,groupname,date_entered,date_modified,description) values (?,?,?,?,?)", $grp2);
	$adb->query("insert into ".$table_prefix."_group2role values ('".$group3_id."','H".$role3_id."')");
	$adb->query("insert into ".$table_prefix."_group2rs values ('".$group3_id."','H".$role3_id."')");
	// crmv@49398e

	// Setting user group relation for admin user
	$adb->pquery("insert into ".$table_prefix."_users2group values (?,?)", array($group2_id, $admin_user_id));

	//Creating the flat files for admin user
	createUserPrivilegesfile($admin_user_id);
	createUserSharingPrivilegesfile($admin_user_id);

	//Insert into vtiger_profile2field
	insertProfile2field($profile1_id);
	insertProfile2field($profile2_id);

	insert_def_org_field();

	//crmv@20209
	$adb->pquery("insert into tbl_s_showncalendar (userid,shownid,selected) values (?,?,1)", array(1,'mine'));
	$adb->pquery("insert into tbl_s_showncalendar (userid,shownid,selected) values (?,?,0)", array(1,'all'));
	$adb->pquery("insert into tbl_s_showncalendar (userid,shownid,selected) values (?,?,0)", array(1,'others'));
	//crmv@20209e

	//crmv@3079m
	$adb->pquery("update {$table_prefix}_homestuff set visible = ?, stuffsequence = ? where userid = ? and stufftitle = ?", array(1,15,1,'HELPVTE'));
	$adb->pquery("update {$table_prefix}_homestuff set visible = ?, stuffsequence = ? where userid = ? and stufftitle = ?", array(0,16,1,'CRMVNEWS'));
	$adb->pquery("update {$table_prefix}_homestuff set visible = ?, stuffsequence = ? where userid = ? and stufftitle = ?", array(0,17,1,'My files'));
	$adb->pquery("update {$table_prefix}_homestuff set visible = ?, stuffsequence = ? where userid = ? and stufftitle = ?", array(0,18,1,'MODCOMMENTS'));
	//crmv@3079me
}

$modules = array("DefaultDataPopulator");
$focus=0;
$success = $adb->createTables("schema/DatabaseSchema.xml");

// TODO HTML
if($success==0)
	die("Error: Tables not created.  Table creation failed.\n");
elseif ($success==1)
	die("Error: Tables partially created.  Table creation failed.\n");
	
global $table_prefix;
$adb->database->GenID($table_prefix.'_ws_entity_extra_seq',1000); //crmv@OPER4380 start from 1000 don't overlap with standard module ids

foreach ($modules as $module ) {
	$focus = new $module();
	$focus->create_tables();
}

create_default_users_access();

// create and populate combo tables
require_once('include/PopulateComboValues.php');
$combo = new PopulateComboValues();
$combo->create_tables();
$combo->create_nonpicklist_tables();
//Writing tab data in flat file
create_tab_data_file();
create_parenttab_data_file();

/* crmv@32357
//to get the users lists
$query = 'select id from '.$table_prefix.'_users';
$result=$adb->pquery($query,array());

//crmv@25314
//creating home page widgets
$defaultWidgets = array(array('Top Accounts', 1, 'ALVT', 'Accounts', 'Default'),
						array('Home Page Dashboard', 1, 'HDB', 'Dashboard', 'Default'),
						array('Top Potentials', 1, 'PLVT','Potentials', 'Default'),
						array('Top Quotes', 1,'QLTQ','Quotes', 'Default'),
						array('Key Metrics', 1,'CVLVT','NULL', 'Default'),
						array('Top Trouble Tickets', 1,'HLT','HelpDesk', 'Default'),
						array('Upcoming Activities', 1,'UA','Calendar', 'Default'),
						array('My Group Allocation', 1,'GRT','NULL', 'Default'),
						array('Top Sales Orders', 1,'OLTSO','SalesOrder', 'Default'),
						array('Top Invoices', 1,'ILTI','Invoice', 'Default'),
						array('My New Leads', 1,'MNL','Leads', 'Default'),
						array('Top Purchase Orders', 1,'OLTPO','PurchaseOrder', 'Default'),
						array('Pending Activities', 1,'PA','Calendar', 'Default'),
						array('My Recent FAQs', 1,'LTFAQ','Faq', 'Default'),
						array('MODCOMMENTS', 0,'MODCOMMENTS','NULL', 'Iframe'),	//crmv@29079
						array('News CRMVILLAGE.BIZ', 0,'CRMVNEWS','NULL', 'Iframe'),
						array('Help VTE', 1,'HELPVTE','NULL', 'Iframe'),
						);

$defaultWidgets = array_reverse($defaultWidgets);

for($u=0;$u<$adb->num_rows($result);$u++){
	$userid = $adb->query_result($result,$u,'id');

	for($i=0; $i<count($defaultWidgets); $i++){
		$stuffid = $adb->getUniqueID($table_prefix."_homestuff");
		$widgetTitle = $defaultWidgets[$i][0];
		$visible = $defaultWidgets[$i][1];
		$type = $defaultWidgets[$i][2];
		$module = $defaultWidgets[$i][3];
		$stufftype = $defaultWidgets[$i][4];
		$sequence = $i+1;

		// crmv@30014
		$sql="insert into ".$table_prefix."_homestuff values(?, ?, ?, ?, ?, ?, ?)";
		$res=$adb->pquery($sql,array($stuffid, $sequence, $stufftype, $userid, $visible, 1, $widgetTitle));
		// crmv@30014e

		$sql="insert into ".$table_prefix."_homedefault values($stuffid, '$type', 5, '$module')";
		$adb->pquery($sql,array());
	}

	$stuffid = $adb->getUniqueID($table_prefix."_homestuff");
	$widgetTitle = "Tag Cloud";
	$visible = 1;
	$sequence = $i+1;
	// crmv@30014
	$sql="insert into ".$table_prefix."_homestuff values(?, ?, 'Tag Cloud', ?, ?, ?, ?)";
	$res=$adb->pquery($sql,array($stuffid, $sequence, $userid, $visible, 1, $widgetTitle));
	// crmv@30014e
}
//crmv@25314e
crmv@32357e */

// default report population
require_once('modules/Reports/PopulateReports.php');

// default customview population
require_once('modules/CustomView/PopulateCustomView.php');

// crmv@OPER6288 - populate default kanban view
require_once('include/utils/KanbanView.php');
$kanbanLib = KanbanLib::getInstance();
$kanbanLib->populateDefault();
// crmv@OPER6288e

// ensure required sequences are created (adodb creates them as needed, but if
// creation occurs within a transaction we get problems
$adb->getUniqueID($table_prefix."_crmentity");
$adb->getUniqueID($table_prefix."_seactivityrel");
$adb->getUniqueID($table_prefix."_freetags");

//Master currency population
//Insert into vtiger_currency vtiger_table
$adb->pquery("insert into ".$table_prefix."_currency_info values(?,?,?,?,?,?,?,?)", array($adb->getUniqueID($table_prefix."_currency_info"),$currency_name,$currency_code,$currency_symbol,1,'Active','-11','0'));

// Register All the Events
registerEvents($adb);

// Register All the Entity Methods
registerEntityMethods($adb);

// Populate Default Workflows
populateDefaultWorkflows($adb);

// Populate Links
populateLinks();

// Populate default Crons
populateCrons(); // crmv@47611

// Set Help Information for Fields
setFieldHelpInfo();

// Register all the events here
function registerEvents($adb) {
	require_once('include/events/include.inc');
	$em = new VTEventsManager($adb);

	// Registering event for Recurring Invoices
	$em->registerHandler('vtiger.entity.aftersave', 'modules/SalesOrder/RecurringInvoiceHandler.php', 'RecurringInvoiceHandler');

	// Workflow manager
	$em->registerHandler('vtiger.entity.aftersave', 'modules/com_vtiger_workflow/VTEventHandler.inc', 'VTWorkflowEventHandler');

	//Registering events for On modify
	$em->registerHandler('vtiger.entity.afterrestore', 'modules/com_vtiger_workflow/VTEventHandler.inc', 'VTWorkflowEventHandler');

	$em->registerHandler('vtiger.entity.beforesave','modules/Users/MenuViewHandler.php','MenuViewHandler');	//crmv@22622

	$em->registerHandler('vtiger.entity.beforesave','modules/Calendar/CalendarHandler.php','CalendarHandler');	//crmv@26030m
}

// Register all the entity methods here
function registerEntityMethods($adb) {
	require_once("modules/com_vtiger_workflow/include.inc");
	require_once("modules/com_vtiger_workflow/tasks/VTEntityMethodTask.inc");
	require_once("modules/com_vtiger_workflow/VTEntityMethodManager.inc");
	$emm = new VTEntityMethodManager($adb);

	// Registering method for Updating Inventory Stock
	$emm->addEntityMethod("SalesOrder","UpdateInventory","include/InventoryHandler.php","handleInventoryProductRel");//Adding EntityMethod for Updating Products data after creating SalesOrder
	$emm->addEntityMethod("Invoice","UpdateInventory","include/InventoryHandler.php","handleInventoryProductRel");//Adding EntityMethod for Updating Products data after creating Invoice
}

function populateDefaultWorkflows($adb) {
	//crmv@20799
	require_once("modules/com_vtiger_workflow/include.inc");
	require_once("modules/com_vtiger_workflow/tasks/VTEntityMethodTask.inc");
	require_once("modules/com_vtiger_workflow/VTEntityMethodManager.inc");

	// Creating Workflow for Updating Inventory Stock for Invoice
	$vtWorkFlow = new VTWorkflowManager($adb);
	$invWorkFlow = $vtWorkFlow->newWorkFlow("Invoice");
	$invWorkFlow->test = '[{"fieldname":"subject","operation":"does not contain","value":"`!`"}]';
	$invWorkFlow->description = "Aggiorna inventario prodotti ad ogni salvataggio";
	$vtWorkFlow->save($invWorkFlow);

	$tm = new VTTaskManager($adb);
	$task = $tm->createTask('VTEntityMethodTask', $invWorkFlow->id);
	$task->active=true;
	$task->summary="Aggiorna inventario prodotti ad ogni salvataggio";
	$task->methodName = "UpdateInventory";
	$tm->saveTask($task);
	//crmv@20799e
}

// Function to populate Links
function populateLinks() {
	include_once('vtlib/Vtecrm/Module.php');
	// crmv@43147
	$docInstance = Vtecrm_Module::getInstance('Documents');
	Vtecrm_Link::addLink($docInstance->id, 'DETAILVIEWBASIC', 'ShareDocument', "javascript:openShareRecord('\$RECORD\$', '')", '', 1);
	Vtecrm_Link::addLink($docInstance->id, 'DETAILVIEWBASIC','LBL_ADD_DOCREVISION',"javascript:AddDocRevision('\$RECORD\$');",'vteicon:note_add',0,'checkPermittedLink:include/utils/crmv_utils.php');	//crmv@63483
	Vtecrm_Link::addLink($docInstance->id, 'DETAILVIEWWIDGET', 'DOC REVISION', 'module=Documents&action=DocumentsAjax&file=RevisionTab&record=$RECORD$');
	Vtecrm_Link::addLink($docInstance->id, 'DETAILVIEWBASIC', 'LBL_SHOW_METADATA', "javascript:showMetadata('\$RECORD\$')", '', 2, 'checkMetadata:modules/Documents/storage/StorageBackendUtils.php'); // crmv@95157
	// crmv@43147e
	// crmv@43611
	$camInstance = Vtecrm_Module::getInstance('Campaigns');
	Vtecrm_Link::addLink($camInstance->id, 'LISTVIEWBASIC', 'OpenNewsletterWizard', "openNewsletterWizard('\$MODULE\$', '');", '', 1);
	Vtecrm_Link::addLink($camInstance->id, 'DETAILVIEWBASIC', 'OpenNewsletterWizard', "javascript:openNewsletterWizard('\$MODULE\$', '\$RECORD\$');", '', 1);
	// crmv@43611e

	// crmv@44323
	$Quotes = Vtecrm_Module::getInstance('Quotes');
	Vtecrm_Link::addLink($Quotes->id,'DETAILVIEWBASIC','ReviewQuote','javascript:ReviewQuote(\'$RECORD$\')', '', 1);
	// crmv@44323e

	/* crmv@26896
	// Links for Accounts module
	$accountInstance = Vtecrm_Module::getInstance('Accounts');
	// Detail View Custom link
	$accountInstance->addLink(
		'DETAILVIEWBASIC', 'LBL_ADD_NOTE',
		'index.php?module=Documents&action=EditView&return_module=$MODULE$&return_action=DetailView&return_id=$RECORD$&parent_id=$RECORD$',
		'vteicon:note_add'
	);

	$leadInstance = Vtecrm_Module::getInstance('Leads');
	$leadInstance->addLink(
		'DETAILVIEWBASIC', 'LBL_ADD_NOTE',
		'index.php?module=Documents&action=EditView&return_module=$MODULE$&return_action=DetailView&return_id=$RECORD$&parent_id=$RECORD$',
		'vteicon:note_add'
	);

	$contactInstance = Vtecrm_Module::getInstance('Contacts');
	$contactInstance->addLink(
		'DETAILVIEWBASIC', 'LBL_ADD_NOTE',
		'index.php?module=Documents&action=EditView&return_module=$MODULE$&return_action=DetailView&return_id=$RECORD$&parent_id=$RECORD$',
		'vteicon:note_add'
	);
	crmv@26896e */
}

// crmv@47611
// insert the default VTE cron jobs
// modules in .zip files add their cron by themselves
function populateCrons() {
	require_once('include/utils/CronUtils.php');

	$CU = CronUtils::getInstance();

	$cj = new CronJob();
	$cj->name = 'Workflow';
	$cj->active = 1;
	$cj->singleRun = false;
	$cj->fileName = 'cron/modules/com_vtiger_workflow/com_vtiger_workflow.service.php';
	$cj->timeout = 300;             // 5min timeout
	$cj->repeat = 300;              // run every 5 min
	$CU->insertCronJob($cj);

	$cj = new CronJob();
	$cj->name = 'ScheduledImport';
	$cj->active = 1;
	$cj->singleRun = false;
	$cj->fileName = 'cron/modules/Import/ScheduledImport.service.php';
	$cj->timeout = 1200;			// 20min timeout
	$cj->repeat = 600;				// run every 10 min
	$CU->insertCronJob($cj);

	$cj = new CronJob();
	$cj->name = 'RecurringInvoice';
	$cj->active = 1;
	$cj->singleRun = false;
	$cj->fileName = 'cron/modules/SalesOrder/RecurringInvoice.service.php';
	$cj->timeout = 600;				// 10min timeout
	$cj->repeat = 3600*6;			// run every 6 hours
	$CU->insertCronJob($cj);

	$cj = new CronJob();
	$cj->name = 'SendReminder';
	$cj->active = 1;
	$cj->singleRun = false;
	$cj->fileName = 'cron/modules/Reminder/SendReminder.service.php';
	$cj->timeout = 600;				// 10min timeout
	$cj->repeat = 300;				// run every 5 min
	$CU->insertCronJob($cj);

	$cj = new CronJob();
	$cj->name = 'MailScanner';
	$cj->active = 0;
	$cj->singleRun = false;
	$cj->fileName = 'cron/modules/MailScanner/MailScanner.service.php';
	$cj->timeout = 600;				// 10min timeout
	$cj->repeat = 1200;				// run every 20 min
	$CU->insertCronJob($cj);

	$cj = new CronJob();
	$cj->name = 'SupportNotification';
	$cj->active = 0;
	$cj->singleRun = false;
	$cj->fileName = 'cron/modules/Reminder/SendSupportNotification.service.php';
	$cj->timeout = 600;				// 10min timeout
	$cj->repeat = 1800;				// run every 30 min
	$CU->insertCronJob($cj);

	$cj = new CronJob();
	$cj->name = 'TaskStatus';
	$cj->active = 0;
	$cj->singleRun = false;
	$cj->fileName = 'cron/modules/Reminder/intimateTaskStatus.service.php';
	$cj->timeout = 600;				// 10min timeout
	$cj->repeat = 1800;				// run every 30 min
	$CU->insertCronJob($cj);

	// crmv@65455
	$cj = new CronJob();
	$cj->name = 'DataImporterCheck';
	$cj->active = 1;
	$cj->singleRun = false;
	$cj->timeout = 1200;	// 20 min
	$cj->repeat = 600;		// 10min
	$cj->fileName = 'cron/modules/DataImporter/DataImporterCheck.service.php';
	$CU->insertCronJob($cj);
	// crmv@65455e

	// crmv@74560
	$cj = new CronJob();
	$cj->name = 'RecalcPrivileges';
	$cj->active = 1;
	$cj->singleRun = false;
	$cj->maxAttempts = 0;	// disable attempts check
	$cj->timeout = 1800;	// 30 min
	$cj->repeat = 60;		// 1min
	$cj->fileName = 'cron/modules/Users/RecalcPrivileges.service.php';
	$CU->insertCronJob($cj);
	// crmv@74560e

	// crmv@91571
	$cj = new CronJob();
	$cj->name = 'MassEdit';
	$cj->active = 1;
	$cj->singleRun = false;
	$cj->timeout = 1800;	// 30 min
	$cj->repeat = 120;		// 2 min
	$cj->fileName = 'cron/modules/MassEdit/MassEdit.service.php';
	$CU->insertCronJob($cj);
	// crmv@91571e

	// crmv@106069
	$cj = new CronJob();
	$cj->name = 'Cleaner';
	$cj->active = 1;
	$cj->singleRun = false;
	$cj->maxAttempts = 0;	// disable attempts check
	$cj->timeout = 600;		// 10 minutes timeout
	$cj->repeat = 21600;	// repeat every 6 hours
	$cj->fileName = 'cron/Cleaner.service.php';
	$CU->insertCronJob($cj);
	// crmv@106069e

}
// crmv@47611e

function setFieldHelpInfo() {
	// Added Help Info for Hours and Days fields of HelpDesk module.
	require_once('vtlib/Vtecrm/Module.php');
	$tt_module = Vtecrm_Module::getInstance('HelpDesk');
	$field1 = Vtecrm_Field::getInstance('hours',$tt_module);
	$field2 = Vtecrm_Field::getInstance('days',$tt_module);
	//crmv@18166
	$field1->setHelpInfo('LBL_HELPINFO_HOURS');
	$field2->setHelpInfo('LBL_HELPINFO_DAYS');
	//crmv@18166e
}

?>