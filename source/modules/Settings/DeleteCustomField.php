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
require_once('include/database/PearDatabase.php');
require_once('modules/Reports/Reports.php');

$fld_module = $_REQUEST["fld_module"];
global $table_prefix;
$id = intval($_REQUEST["fld_id"]);

$colName = $_REQUEST["colName"];
$uitype = $_REQUEST["uitype"];

//Deleting the CustomField from the Custom Field Table
$query='delete from '.$table_prefix.'_field where fieldid=?';
$adb->pquery($query, array($id));

//Deleting from vtiger_profile2field table
$query='delete from '.$table_prefix.'_profile2field where fieldid=?';
$adb->pquery($query, array($id));

//Deleting from vtiger_def_org_field table
$query='delete from '.$table_prefix.'_def_org_field where fieldid=?';
$adb->pquery($query, array($id));

//Drop the column in the corresponding module table
$delete_module_tables = Array(
				"Leads"=>$table_prefix."_leadscf",
				"Accounts"=>$table_prefix."_accountscf",
				"Contacts"=>$table_prefix."_contactscf",
				"Potentials"=>$table_prefix."_potentialscf",
				"HelpDesk"=>$table_prefix."_ticketcf",
				"Products"=>$table_prefix."_productcf",
				"Vendors"=>$table_prefix."_vendorcf",
				"PriceBooks"=>$table_prefix."_pricebookcf",
				"PurchaseOrder"=>$table_prefix."_purchaseordercf",
				"SalesOrder"=>$table_prefix."_salesordercf",
				"Quotes"=>$table_prefix."_quotescf",
				"Invoice"=>$table_prefix."_invoicecf",
				"Campaigns"=>$table_prefix."_campaignscf",
				"Calendar"=>$table_prefix."_activitycf",	//crmv@19780
			     );

// vtlib customization: Hook added to allow action for custom modules too
$cftablename = $delete_module_tables[$fld_module];
if(empty($cftablename)) {
	include_once('data/CRMEntity.php');
	$focus = CRMEntity::getInstance($fld_module);
	$cftablename = $focus->customFieldTable[0];
}

//crmv@59051
if($adb->isMssql()){
	$const_sql = "select name from sys.default_constraints where parent_object_id = object_id(?) AND type = 'D'
					 AND parent_column_id = (
					  select column_id 
					  from sys.columns 
					  where object_id = object_id(?)
					  and name = ?)";
	$res = $adb->pquery($const_sql,array($adb->sql_escape_string($delete_module_tables[$fld_module]),$adb->sql_escape_string($delete_module_tables[$fld_module]),$adb->sql_escape_string($colName)));
	if($res && $adb->num_rows($res) > 0){
		$constraint_name = $adb->query_result($res,0,'name');
		$adb->query('alter table '. $adb->sql_escape_string($delete_module_tables[$fld_module]) .' drop constraint '.$constraint_name);
	}
}
//crmv@59051e
			     
$dbquery = 'alter table '. $adb->sql_escape_string($delete_module_tables[$fld_module]) .' drop column '. $adb->sql_escape_string($colName);
$adb->pquery($dbquery, array());

//To remove customfield entry from vtiger_field table
$dbquery = 'delete from '.$table_prefix.'_field where tablename= ? and fieldname=?';
$adb->pquery($dbquery, array($delete_module_tables[$fld_module], $colName));
//we have to remove the entries in customview and report related tables which have this field ($colName)
$adb->pquery("delete from ".$table_prefix."_cvcolumnlist where columnname like ?", array('%'.$colName.'%'));
$adb->pquery("delete from ".$table_prefix."_cvstdfilter where columnname like ?", array('%'.$colName.'%'));
$adb->pquery("delete from ".$table_prefix."_cvadvfilter where columnname like ?", array('%'.$colName.'%'));

// crmv@101691
$reports = Reports::getInstance();
$reports->deleteFieldFromAll($id);
// crmv@101691e

//Deleting from convert lead mapping vtiger_table- Jaguar
if($fld_module=="Leads")
{
	$deletequery = 'delete from '.$table_prefix.'_convertleadmapping where leadfid=?';
	$adb->pquery($deletequery, array($id));
}elseif($fld_module=="Accounts" || $fld_module=="Contacts" || $fld_module=="Potentials")
{
	$map_del_id = array("Accounts"=>"accountfid","Contacts"=>"contactfid","Potentials"=>"potentialfid");
	$map_del_q = "update ".$table_prefix."_convertleadmapping set ".$map_del_id[$fld_module]."=0 where ".$map_del_id[$fld_module]."=?";
	$adb->pquery($map_del_q, array($id));
}

//HANDLE HERE - we have to remove the table for other picklist type values which are text area and multiselect combo box 
if($uitype == 15)
{
	$deltablequery = 'drop table '.$table_prefix.'_'.$adb->sql_escape_string($colName);
	$adb->pquery($deltablequery, array());
}

header("Location:index.php?module=Settings&action=CustomFieldList&fld_module=".$fld_module."&parenttab=Settings");
?>