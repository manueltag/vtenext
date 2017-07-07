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


//5.0.2 database changes - added on 27-10-06
//we have to use the current object (stored in PatchApply.php) to execute the queries
$adb = $_SESSION['adodb_current_object'];
$conn = $_SESSION['adodb_current_object'];
global $table_prefix;
$migrationlog->debug("\n\nDB Changes from 5.0.1 to 5.0.2 -------- Starts \n\n");

//Query added to show Manufacturer field in Products module
ExecuteQuery("update ".$table_prefix."_field set displaytype=1,block=31 where tabid=14 and block=1");
ExecuteQuery("update ".$table_prefix."_field set block=23,displaytype=1 where block=1 and displaytype=23 and tabid=10");
ExecuteQuery("update ".$table_prefix."_field set block=22,displaytype=1 where block=1 and displaytype=22 and tabid=10");

//Added to rearange the attachment in HelpDesk
ExecuteQuery(" update ".$table_prefix."_field set block=25,sequence=12 where tabid=13 and fieldname='filename'");

//Query added to as entityname,its tablename,its primarykey are saved in a table
ExecuteQuery(" CREATE TABLE `".$table_prefix."_entityname` (
	`tabid` int(19) NOT NULL default '0',
	`modulename` varchar(50) NOT NULL,
	`tablename` varchar(50) NOT NULL,
	`fieldname` varchar(150) NOT NULL,
	`entityidfield` varchar(150) NOT NULL,
	PRIMARY KEY (`tabid`),
	KEY `entityname_tabid_idx` (`tabid`)
)");

//Data Populated for the existing modules
ExecuteQuery("insert into ".$table_prefix."_entityname values(7,'Leads','".$table_prefix."_leaddetails','lastname,firstname','leadid')");
ExecuteQuery("insert into ".$table_prefix."_entityname values(6,'Accounts','".$table_prefix."_account','accountname','accountid')");
ExecuteQuery("insert into ".$table_prefix."_entityname values(4,'Contacts','".$table_prefix."_contactdetails','lastname,firstname','contactid')");
ExecuteQuery("insert into ".$table_prefix."_entityname values(2,'Potentials','".$table_prefix."_potential','potentialname','potentialid')");
ExecuteQuery("insert into ".$table_prefix."_entityname values(8,'Notes','".$table_prefix."_notes','title','notesid')");
ExecuteQuery("insert into ".$table_prefix."_entityname values(13,'HelpDesk','".$table_prefix."_troubletickets','title','ticketid')");
ExecuteQuery("insert into ".$table_prefix."_entityname values(9,'Calendar','".$table_prefix."_activity','subject','activityid')");
ExecuteQuery("insert into ".$table_prefix."_entityname values(10,'Emails','".$table_prefix."_activity','subject','activityid')");
ExecuteQuery("insert into ".$table_prefix."_entityname values(14,'Products','".$table_prefix."_products','productname','productid')");
ExecuteQuery("insert into ".$table_prefix."_entityname values(29,'Users','".$table_prefix."_users','last_name,first_name','id')");
ExecuteQuery("insert into ".$table_prefix."_entityname values(23,'Invoice','".$table_prefix."_invoice','subject','invoiceid')");
ExecuteQuery("insert into ".$table_prefix."_entityname values(20,'Quotes','".$table_prefix."_quotes','subject','quoteid')");
ExecuteQuery("insert into ".$table_prefix."_entityname values(21,'PurchaseOrder','".$table_prefix."_purchaseorder','subject','purchaseorderid')");
ExecuteQuery("insert into ".$table_prefix."_entityname values(22,'SalesOrder','".$table_prefix."_salesorder','subject','salesorderid')");
ExecuteQuery("insert into ".$table_prefix."_entityname values(18,'Vendors','".$table_prefix."_vendor','vendorname','vendorid')");
ExecuteQuery("insert into ".$table_prefix."_entityname values(19,'PriceBooks','".$table_prefix."_pricebook','bookname','pricebookid')");
ExecuteQuery("insert into ".$table_prefix."_entityname values(26,'Campaigns','".$table_prefix."_campaign','campaignname','campaignid')");
ExecuteQuery("insert into ".$table_prefix."_entityname values(15,'Faq','".$table_prefix."_faq','question','id')");

//added quantity in stock in product default listview - All
$res = $adb->query("select ".$table_prefix."_cvcolumnlist.cvid from ".$table_prefix."_cvcolumnlist inner join ".$table_prefix."_customview on ".$table_prefix."_cvcolumnlist.cvid=".$table_prefix."_customview.cvid where entitytype='Products' and viewname='All'");
if($adb->num_rows != 0)
{
	$cvid = $adb->query_result($res,0,'cvid');
	$adb->query("insert into ".$table_prefix."_cvcolumnlist values($cvid,5,'".$table_prefix."_products:qtyinstock:qtyinstock:Products_Quantity_In_Stock:V')");
}


//echo "<br><font color='red'>&nbsp; 5.0/5.0.1 ==> 5.0.2 Database changes has been done.</font><br>";

$migrationlog->debug("\n\nDB Changes from 5.0.1 to 5.0.2 -------- Ends \n\n");

?>
