<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

// crmv@37463
global $adb, $table_prefix;
global $current_user;
$del_id =  intval($_REQUEST['delete_user_id']);
$tran_id = intval($_REQUEST['transfer_user_id']);

if (isPermitted('Users', 'Delete') != 'yes') {
	header("Location: index.php?action=ListView&module=Users");
	die();
}
// crmv@37463e

//Updating the smcreatorid,smownerid, modifiedby in vtiger_crmentity
$sql1 = "update ".$table_prefix."_crmentity set smcreatorid=? where smcreatorid=?";
$adb->pquery($sql1, array($tran_id, $del_id));
$sql2 = "update ".$table_prefix."_crmentity set smownerid=? where smownerid=?";
$adb->pquery($sql2, array($tran_id, $del_id));
$sql3 = "update ".$table_prefix."_crmentity set modifiedby=? where modifiedby=?";
$adb->pquery($sql3, array($tran_id, $del_id));

//deleting from vtiger_tracker
$sql4 = "delete from ".$table_prefix."_tracker where user_id=?";
$adb->pquery($sql4, array($del_id));

//updating created by in vtiger_lar
$sql5 = "update ".$table_prefix."_lar set createdby=? where createdby=?";
$adb->pquery($sql5, array($tran_id, $del_id));

//updating the vtiger_import_maps
$sql6 ="update ".$table_prefix."_import_maps set assigned_user_id=? where assigned_user_id=?";
$adb->pquery($sql6, array($tran_id, $del_id));

//update assigned_user_id in vtiger_files
$sql7 ="update ".$table_prefix."_files set assigned_user_id=? where assigned_user_id=?";
$adb->pquery($sql7, array($tran_id, $del_id));

//update assigned_user_id in vtiger_users_last_import
$sql8 = "update ".$table_prefix."_users_last_import set assigned_user_id=? where assigned_user_id=?";
$adb->pquery($sql8, array($tran_id, $del_id));

//updating handler in vtiger_products
$sql9 = "update ".$table_prefix."_products set handler=? where handler=?";
$adb->pquery($sql9, array($tran_id, $del_id));

//updating inventorymanager in vtiger_quotes
$sql10 = "update ".$table_prefix."_quotes set inventorymanager=? where inventorymanager=?";
$adb->pquery($sql10, array($tran_id, $del_id));

//updating reports_to_id in vtiger_users
$sql11 = "update ".$table_prefix."_users set reports_to_id=? where reports_to_id=?";
$adb->pquery($sql11, array($tran_id, $del_id));

//updating user_id in vtiger_moduleowners
$sql12 = "update ".$table_prefix."_moduleowners set user_id=? where user_id=?";
$adb->pquery($sql12, array($tran_id, $del_id));

//delete from vtiger_users to group vtiger_table
$sql13 = "delete from ".$table_prefix."_user2role where userid=?";
$adb->pquery($sql13, array($del_id));

//delete from vtiger_users to vtiger_role vtiger_table
$sql14 = "delete from ".$table_prefix."_users2group where userid=?";
$adb->pquery($sql14, array($del_id));


//delete from user vtiger_table;
$sql15 = "delete from ".$table_prefix."_users where id=?";
$adb->pquery($sql15, array($del_id));

// crmv@49398
global $metaLogs;
if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_DELUSER, $del_id);
// crmv@49398e

//crmv@42329
if (isModuleInstalled('PBXManager')){
	$sql16 = "delete from ".$table_prefix."_asteriskextensions where userid=?";
	$adb->pquery($sql16, array($del_id));
}
//crmv@42329 e
//crmv@7220
// www.digital-worx.de - start Asterix 5 Integration
//remove user
//$sql16 = "DELETE FROM vtiger_asterisk WHERE extension NOT IN (SELECT extension FROM vtiger_users WHERE extension IS NOT NULL)";
//$adb->query($sql16);
// www.digital-worx.de - end Asterix 5 Integration

//if check to delete user from detail view
if(isset($_REQUEST["ajax_delete"]) && $_REQUEST["ajax_delete"] == 'false')
	header("Location: index.php?action=ListView&module=Users");
else
	header("Location: index.php?action=UsersAjax&module=Users&file=ListView&ajax=true&deleteuser=true");
?>