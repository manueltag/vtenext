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


//5.0.3 RC2 to 5.0.3 database changes - added on 29-03-07
//we have to use the current object (stored in PatchApply.php) to execute the queries
$adb = $_SESSION['adodb_current_object'];
$conn = $_SESSION['adodb_current_object'];

$migrationlog->debug("\n\nDB Changes from 5.0.3RC2 to 5.0.3 -------- Starts \n\n");

$query_array = Array(
			//description field added in vtiger_inventoryproductrel
			"alter table ".$table_prefix."_inventoryproductrel add column description TEXT default NULL after comment",

			//size increased for comment field
			"alter table ".$table_prefix."_inventoryproductrel change comment comment varchar(250)",

			//size increased for tax and discount related fields in Inventory modules
			"alter table ".$table_prefix."_purchaseorder change salestax salestax decimal(25,3)",
			"alter table ".$table_prefix."_purchaseorder change adjustment adjustment decimal(25,3)",
			"alter table ".$table_prefix."_purchaseorder change salescommission salescommission decimal(25,3)",
			"alter table ".$table_prefix."_purchaseorder change exciseduty exciseduty decimal(25,3)",
			"alter table ".$table_prefix."_purchaseorder change total total decimal(25,3)",
			"alter table ".$table_prefix."_purchaseorder change subtotal subtotal decimal(25,3)",
			"alter table ".$table_prefix."_purchaseorder change discount_percent discount_percent decimal(25,3)",
			"alter table ".$table_prefix."_purchaseorder change discount_amount discount_amount decimal(25,3)",
			"alter table ".$table_prefix."_purchaseorder change s_h_amount s_h_amount decimal(25,3)",

			"alter table ".$table_prefix."_salesorder change salestax salestax decimal(25,3)",
			"alter table ".$table_prefix."_salesorder change adjustment adjustment decimal(25,3)",
			"alter table ".$table_prefix."_salesorder change salescommission salescommission decimal(25,3)",
			"alter table ".$table_prefix."_salesorder change exciseduty exciseduty decimal(25,3)",
			"alter table ".$table_prefix."_salesorder change total total decimal(25,3)",
			"alter table ".$table_prefix."_salesorder change subtotal subtotal decimal(25,3)",
			"alter table ".$table_prefix."_salesorder change discount_percent discount_percent decimal(25,3)",
			"alter table ".$table_prefix."_salesorder change discount_amount discount_amount decimal(25,3)",
			"alter table ".$table_prefix."_salesorder change s_h_amount s_h_amount decimal(25,3)",

			"alter table ".$table_prefix."_invoice change salestax salestax decimal(25,3)",
			"alter table ".$table_prefix."_invoice change adjustment adjustment decimal(25,3)",
			"alter table ".$table_prefix."_invoice change salescommission salescommission decimal(25,3)",
			"alter table ".$table_prefix."_invoice change exciseduty exciseduty decimal(25,3)",
			"alter table ".$table_prefix."_invoice change total total decimal(25,3)",
			"alter table ".$table_prefix."_invoice change subtotal subtotal decimal(25,3)",
			"alter table ".$table_prefix."_invoice change discount_percent discount_percent decimal(25,3)",
			"alter table ".$table_prefix."_invoice change discount_amount discount_amount decimal(25,3)",
			"alter table ".$table_prefix."_invoice change s_h_amount s_h_amount decimal(25,3)",

			"alter table ".$table_prefix."_quotes change subtotal subtotal decimal(25,3)",
			"alter table ".$table_prefix."_quotes change tax tax decimal(25,3)",
			"alter table ".$table_prefix."_quotes change adjustment adjustment decimal(25,3)",
			"alter table ".$table_prefix."_quotes change total total decimal(25,3)",
			"alter table ".$table_prefix."_quotes change discount_percent discount_percent decimal(25,3)",
			"alter table ".$table_prefix."_quotes change discount_amount discount_amount decimal(25,3)",
			"alter table ".$table_prefix."_quotes change s_h_amount s_h_amount decimal(25,3)",

			"alter table ".$table_prefix."_pricebookproductrel change listprice listprice decimal(25,3)",

			"alter table ".$table_prefix."_inventoryproductrel change listprice listprice decimal(25,3)",

			"alter table ".$table_prefix."_products change unit_price unit_price decimal(25,2)",

			"alter table ".$table_prefix."_campaign change expectedrevenue expectedrevenue decimal(25,3)",
			"alter table ".$table_prefix."_campaign change budgetcost budgetcost decimal(25,3)",
			"alter table ".$table_prefix."_campaign change actualcost actualcost decimal(25,3)",
			"alter table ".$table_prefix."_campaign change expectedroi expectedroi decimal(25,3)",
			"alter table ".$table_prefix."_campaign change actualroi actualroi decimal(25,3)",

			//unwanted currency column removed from lead and contact
			"alter table ".$table_prefix."_leadsubdetails drop column currency",

			"alter table ".$table_prefix."_contactdetails drop column currency",

			//Amount and probability field datatype modified.(http://forums.vtiger.com/viewtopic.php?t=14006)

			"alter table ".$table_prefix."_potential change probability probability decimal(5,2)",
			"alter table ".$table_prefix."_potential change amount amount decimal(12,2)",

			//Homepage order has been changed
			"update ".$table_prefix."_users set homeorder = 'HDB,ALVT,PLVT,QLTQ,CVLVT,HLT,OLV,GRT,OLTSO,ILTI,MNL,OLTPO,LTFAQ'",

		    );

foreach($query_array as $query)
{
	ExecuteQuery($query);
}

//Added to avoid migration error.
//Check for the table availability before alter.
$exists1=$adb->query("show create table ".$table_prefix."_opportunitystage");
if($exists1)
{
        ExecuteQuery("alter table ".$table_prefix."_opportunitystage change probability probability decimal(5,2)");
}

$exists2=$adb->query("show create table ".$table_prefix."_dealintimation");
if($exists2)
{
        ExecuteQuery("alter table ".$table_prefix."_dealintimation change dealprobability dealprobability decimal(5,2)");
}

$exists3=$adb->query("show create table ".$table_prefix."_potstagehistory");
if($exists3)
{
        ExecuteQuery("alter table ".$table_prefix."_potstagehistory change probability probability decimal(5,2)");
        ExecuteQuery("alter table ".$table_prefix."_potstagehistory change amount amount decimal(12,2)");
}
//Check ends

//Added for Custom Invoice Number, No need for security population
//Invoice Number has been set the uitype as 3 which is a new UI type. user can configure but non editable
$newfieldid = $adb->getUniqueID($table_prefix."_field");
ExecuteQuery("insert into ".$table_prefix."_field values(23,".$newfieldid.",'invoice_no','".$table_prefix."_invoice',1,'3','invoice_no','Invoice No',1,0,0,100,3,69,1,'V~M',1,NULL,'BAS')");
//Populate security entries for this new field
$profileresult = $adb->query("select * from ".$table_prefix."_profile");
$countprofiles = $adb->num_rows($profileresult);
for($i=0;$i<$countprofiles;$i++)
{
	$profileid = $adb->query_result($profileresult,$i,'profileid');
	$sqlProf2FieldInsert[$i] = 'insert into '.$table_prefix.'_profile2field values ('.$profileid.',23,'.$newfieldid.',0,1)';
	ExecuteQuery($sqlProf2FieldInsert[$i]);
}
$def_query = "insert into ".$table_prefix."_def_org_field values (23,".$newfieldid.",0,1)";
ExecuteQuery($def_query);

ExecuteQuery("alter table ".$table_prefix."_invoice add column (invoice_no varchar(50) UNIQUE default NULL)");

$res = $adb->query("select cvid from ".$table_prefix."_customview where entitytype='Invoice' and viewname='All'");
$cvid = $adb->query_result($res,0,'cvid');

ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnindex=6 where columnindex=5 and cvid=$cvid");
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnindex=5 where columnindex=4 and cvid=$cvid");
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnindex=4 where columnindex=3 and cvid=$cvid");
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnindex=3 where columnindex=2 and cvid=$cvid");
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnindex=2 where columnindex=1 and cvid=$cvid");
ExecuteQuery("insert into ".$table_prefix."_cvcolumnlist values($cvid,1,'".$table_prefix."_invoice:invoice_no:invoice_no:Invoice_invoice_no:V')");

//Added for product custom view taxclass issue Ticket #3364
ExecuteQuery("update ".$table_prefix."_field set tablename='".$table_prefix."_products' where tablename='".$table_prefix."_producttaxrel' and columnname='taxclass'");
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_products:taxclass:taxclass:Products_Tax_Class:V' where columnname='".$table_prefix."_producttaxrel:taxclass:taxclass:Products_Tax_Class:V'");




//Display type 3 added in profile & default org tables

$profileresult = $adb->query("select * from ".$table_prefix."_profile");
$countprofiles = $adb->num_rows($profileresult);

$res = $adb->query("select * from ".$table_prefix."_field where fieldid not in (select fieldid from ".$table_prefix."_profile2field) and generatedtype=1 and displaytype=3 and tabid!=29");
//$res = $adb->query("select * from vtiger_field where generatedtype=1 and displaytype=3 and tabid!=29");
$num_fields = $adb->num_rows($res);
for($i=0;$i<$num_fields;$i++)
{
	$tabid = $adb->query_result($res,$i,'tabid');
	$fieldid = $adb->query_result($res,$i,'fieldid');

	//For each profile, we have to enter the current fields
	for ($j=0;$j<$countprofiles;$j++)
	{
        	$profileid = $adb->query_result($profileresult,$j,'profileid');
	        ExecuteQuery('insert into ".$table_prefix."_profile2field values ('.$profileid.','.$tabid.','.$fieldid.',0,1)');
	}

	$def_query = "insert into ".$table_prefix."_def_org_field values (".$tabid.",".$fieldid.",0,1)";
	ExecuteQuery($def_query);
}


$query_array2 = Array(

			//Added To fix Duplicate items in Report's Select Column(ticket #3665)

			"update ".$table_prefix."_field set fieldlabel='Adjustment' where tabid=22 and columnname='adjustment'",

			"update ".$table_prefix."_field set fieldlabel='Sub Total' where tabid=22 and columnname='subtotal'",

			"update ".$table_prefix."_field set fieldlabel='Adjustment' where tabid=23 and columnname='adjustment'",

			"update ".$table_prefix."_field set fieldlabel='Sales Tax' where tabid=20 and columnname='tax'",

			// Changes made to make discontinued column in vtiger_products '0' during deactivation.

			"alter table ".$table_prefix."_products modify discontinued int(1) NOT NULL default 0",

			"UPDATE ".$table_prefix."_products SET discontinued=1 WHERE discontinued IS NULL",

			//Ref : ticket#3278, 3309, 3461
			"update ".$table_prefix."_field set typeofdata='E~O' where fieldname in ('yahooid','yahoo_id')",
			"alter table ".$table_prefix."_leaddetails modify noofemployees int(50)",
			"update ".$table_prefix."_field set typeofdata='I~O' where fieldname ='noofemployees' && tabid='7'",

			//Ref : ticket#3521
			"update ".$table_prefix."_field set typeofdata ='D~O' where tabid=21 && fieldname='duedate'",


			//Changes made to add an email Id for standarduser since a user must have an Email Id.Changes for 5.0.3.
			"update ".$table_prefix."_users set email1='standarduser@".$table_prefix."user.com' where id = '2' and email1 = ''",


			//#3668, this query is already available in the file modules/Migration/DBChanges/42P2_to_50.php
			"update ".$table_prefix."_crmentity set setype='Calendar' where setype='Activities'",

			//we don't have field security for Emails module, so we can delete the existing entries
			"delete from ".$table_prefix."_profile2field where tabid=10",
			"delete from ".$table_prefix."_def_org_field where tabid=10",
		     );

foreach($query_array2 as $query)
{
	ExecuteQuery($query);
}


//change the picklist - presence value ie., if presence = 0 then you cannot edit, if presence = 1 then you can edit
$noneditable_tables = Array("ticketstatus","taskstatus","eventstatus","eventstatus","faqstatus","quotestage","postatus","sostatus","invoicestatus");
$noneditable_values = Array(
				"sales_stage"=>"Closed Won",
			   );
foreach($noneditable_tables as $picklistname)
{
	//we have to interchange 0 and 1, so change 0->2, 1->0, 2->1
	ExecuteQuery("UPDATE ".$table_prefix."_".$picklistname." SET PRESENCE=2 WHERE PRESENCE=0");
	ExecuteQuery("UPDATE ".$table_prefix."_".$picklistname." SET PRESENCE=0 WHERE PRESENCE=1");
	ExecuteQuery("UPDATE ".$table_prefix."_".$picklistname." SET PRESENCE=1 WHERE PRESENCE=2");
}
foreach($noneditable_values as $picklistname => $value)
{
	ExecuteQuery("UPDATE ".$table_prefix."_".$picklistname." SET PRESENCE=0 WHERE $picklistname='".$value."'");
}

//Assigned To value is shown as empty in Accounts, Emails and PO listviews because of uitype 52
ExecuteQuery("update ".$table_prefix."_field set uitype=53 where fieldname='assigned_user_id' and tabid in (6,10,21)");

//AccountName is shown as empty in SO/Quotes/Invoice listview because of account details in vtiger_cvcolumnlist.columnname
$modules_array = Array("SalesOrder","Quotes","Invoice","Contacts","Potentials");
foreach($modules_array as $module)
{
	ExecuteQuery("update ".$table_prefix."_cvcolumnlist inner join ".$table_prefix."_customview on ".$table_prefix."_customview.cvid=".$table_prefix."_cvcolumnlist.cvid set columnname='".$table_prefix."_account:accountname:accountname:".$module."_Account_Name:V' where columnname like '%:accountid:account_id:%' and ".$table_prefix."_customview.entitytype='".$module."'");
}

//Previously PurchaseOrder was named as Order. Now we have to change the entitytype from Orders to PurchaseOrder
ExecuteQuery("update ".$table_prefix."_customview set entitytype='PurchaseOrder' where entitytype='Orders'");

//ContactName is shown as empty in SO/Quotes/Invoice listview because of contact details in vtiger_cvcolumnlist.columnname
$modules_array = Array("SalesOrder","Quotes","Invoice","PurchaseOrder","Notes","Calendar");
foreach($modules_array as $module)
{
	ExecuteQuery("update ".$table_prefix."_cvcolumnlist inner join ".$table_prefix."_customview on ".$table_prefix."_customview.cvid=".$table_prefix."_cvcolumnlist.cvid set columnname='".$table_prefix."_contactdetails:lastname:lastname:".$module."_Contact_Name:V' where columnname like '%:contactid:contact_id:%' and ".$table_prefix."_customview.entitytype='".$module."'");
}

/*
$res = $adb->query("select vtiger_cvcolumnlist.*, vtiger_customview.viewname from vtiger_cvcolumnlist inner join vtiger_customview on vtiger_customview.cvid=vtiger_cvcolumnlist.cvid where columnname like '%:accountid:account_id:%' and vtiger_customview.entitytype='SalesOrder'");
for($i=0;$i<$adb->num_rows($res);$i++)
{
	$cvid = $adb->query_result($res,$i,'cvid');
	$columnindex = $adb->query_result($res,$i,'columnindex');
	ExecuteQuery("update vtiger_cvcolumnlist set columnname='vtiger_account:accountname:accountname:SalesOrder_Account_Name:V' where cvid=$cvid and columnindex=$columnindex");
}
*/

//ContactName in Calendar listview is a link but record id is empty in link so when we click the link fatal error comes
//ExecuteQuery("update vtiger_cvcolumnlist inner join vtiger_customview on vtiger_customview.cvid=vtiger_cvcolumnlist.cvid set columnname = 'vtiger_cntactivityrel:contactid:contact_id:Calendar_Contact_Name:V' where columnname = 'vtiger_contactdetails:lastname:lastname:Calendar_Contact_Name:V' and vtiger_customview.entitytype='Calendar'");

//Related To is not displayed in Calendar Listview
ExecuteQuery("update ".$table_prefix."_cvcolumnlist inner join ".$table_prefix."_customview on ".$table_prefix."_customview.cvid=".$table_prefix."_cvcolumnlist.cvid set columnname = '".$table_prefix."_seactivityrel:crmid:parent_id:Calendar_Related_to:V' where columnname = '".$table_prefix."_seactivityrel:crmid:parent_id:Calendar_Related_To:V' and ".$table_prefix."_customview.entitytype='Calendar'");

/* Owner id is set to 1 for modules other than Leads, Helpdesk and Calendar irrespective of whether it is assigned to a group
	[ This is done to fix an issue with 4.2.x versions. So resetting the owner id to 0 for other modules if it is assigned to a group */
ExecuteQuery("update ".$table_prefix."_crmentity set smownerid=0 where crmid in
	(select accountid from ".$table_prefix."_accountgrouprelation union select campaignid from ".$table_prefix."_campaigngrouprelation
	union select contactid from ".$table_prefix."_contactgrouprelation union select invoiceid from ".$table_prefix."_invoicegrouprelation
	union select purchaseorderid from ".$table_prefix."_pogrouprelation union select potentialid from ".$table_prefix."_potentialgrouprelation
	union select quoteid from ".$table_prefix."_quotegrouprelation union select salesorderid from ".$table_prefix."_sogrouprelation)");

//Change Emails to Webmails in main tabs - My Home Page, Marketing, Support drop down menu
ExecuteQuery("update ".$table_prefix."_parenttabrel set tabid=28 where tabid=10");

//we have to put invoiceid as default Invoice Number for all invoices otherwise we cannot edit the invoices
ExecuteQuery("update ".$table_prefix."_invoice set invoice_no=invoiceid");

//change the typeofdata for Emails fields(Custom Fields) from V~O to E~O
ExecuteQuery("update ".$table_prefix."_field set typeofdata='E~O' where uitype=13 and typeofdata='V~O'");

//set the visible to 0 for the mandatory fields for both profile2field and def_org_share_field
ExecuteQuery("update ".$table_prefix."_profile2field inner join ".$table_prefix."_field on ".$table_prefix."_field.fieldid = ".$table_prefix."_profile2field.fieldid set visible=0 where uitype in (2,6,22,73,24,81,50,23,16,53,20) or displaytype=3");
ExecuteQuery("update ".$table_prefix."_def_org_field inner join ".$table_prefix."_field on ".$table_prefix."_field.fieldid = ".$table_prefix."_def_org_field.fieldid set visible=0 where uitype in (2,6,22,73,24,81,50,23,16,53,20) or displaytype=3");

//remove http:// from the website in Accounts (In 5.x we will handle this in code instead of db)
ExecuteQuery("update ".$table_prefix."_account set website = REPLACE(website,'http://','')");

//Change the invoice_no as Invoice No - fieldlabel in field table
ExecuteQuery("update ".$table_prefix."_field set fieldlabel='Invoice No' where fieldlabel='invoice_no' and tabid='23'");

//Change the filter value for Account - Member Of in advance filter
ExecuteQuery("update ".$table_prefix."_cvadvfilter set columnname='".$table_prefix."_account:parentid:account_id:Accounts_Member_Of:V' where columnname='".$table_prefix."_account:accountname:accountname:Accounts_Member_Of:V'");

//Drop the salestax from Quotes/PO/SO/Invoice which is unwanted field
ExecuteQuery("delete from ".$table_prefix."_field where fieldname in ('txtTax') and tabid in (20,21,22,23)");
ExecuteQuery("alter table ".$table_prefix."_quotes drop column tax");
ExecuteQuery("alter table ".$table_prefix."_purchaseorder drop column salestax");
ExecuteQuery("alter table ".$table_prefix."_salesorder drop column salestax");
ExecuteQuery("alter table ".$table_prefix."_invoice drop column salestax");

//Contact Name is not shown in Notes ListView because of cvcolumnlist entry as now we need lastname in the query result
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_contactdetails:lastname:lastname:Notes_Contact_Name:V' where columnname='".$table_prefix."_notes:contact_id:contact_id:Notes_Contact_Name:I'");

//Missed Activity History entry in Potential related list has been added
ExecuteQuery("insert into ".$table_prefix."_relatedlists values(".$adb->getUniqueID($table_prefix.'_relatedlists').",2,9,'get_history',8,'Activity History',0)");

//Change the commission rate from decimal(3,3) to decimal(7,3) in products
ExecuteQuery("alter table ".$table_prefix."_products modify column commissionrate decimal(7,3)");

//In inventory notification mails, line breaks are not propter. we have to replace \n with <br>
$res = $adb->query("select notificationid, notificationbody from ".$table_prefix."_inventorynotification");
for($i=0;$i<$adb->num_rows($res);$i++)
{
	$notificationid = $adb->query_result($res,$i,'notificationid');
	$notificationbody = $adb->query_result($res,$i,'notificationbody');
	//Replace \n with <br>
	$notificationbody = str_replace("\n","<br>",$notificationbody);
	ExecuteQuery("update ".$table_prefix."_inventorynotification set notificationbody='$notificationbody' where notificationid=$notificationid");
}

//Move all the Potential custom fields into corresponding block(2) as they are placed in description block
ExecuteQuery("update ".$table_prefix."_field set block=(select blockid from ".$table_prefix."_blocks where tabid=2 and blocklabel='LBL_CUSTOM_INFORMATION') where tabid=2 and fieldname like 'cf_%'");

//Calendar - End Time fieldlabel has one extra space so that it comes twice in columns list in customview creation
ExecuteQuery("update ".$table_prefix."_field set fieldlabel='End Time' where tabid=9 and fieldname='time_end'");

//change the cntactivityrel table primary key, add relatedlist entries then only related to, contact will be displayed
$adb->query("alter table ".$table_prefix."_cntactivityrel drop primary key");
$adb->query("alter table ".$table_prefix."_cntactivityrel add primary key (contactid, activityid), ENGINE=InnoDB");
ExecuteQuery("insert into ".$table_prefix."_relatedlists values(".$adb->getUniqueID($table_prefix.'_relatedlists').",9,0,'get_users',1,'Users',0)");
ExecuteQuery("insert into ".$table_prefix."_relatedlists values(".$adb->getUniqueID($table_prefix.'_relatedlists').",9,4,'get_contacts',2,'Contacts',0)");

//Added activity history and Invoice status history in Invoice relatedlist
ExecuteQuery("insert into ".$table_prefix."_relatedlists values (".$adb->getUniqueID($table_prefix.'_relatedlists').",23,9,'get_history',3,'Activity History',0), (".$adb->getUniqueID($table_prefix.'_relatedlists').",23,0,'get_invoicestatushistory',4,'Invoice Status History',0)");

//Changed the activity reminder notification as active
ExecuteQuery("update ".$table_prefix."_notificationscheduler set active=1 where schedulednotificationname='LBL_ACTIVITY_REMINDER_DESCRIPTION'");

//Change Event Status values Planned, Held, Not Held as non editable
ExecuteQuery("update ".$table_prefix."_eventstatus set presence=0 where eventstatus in ('Planned','Held','Not Held')");

//If engine is MyISAM then order is changing regularly so we have to change the engine to InnoDB
ExecuteQuery("alter table ".$table_prefix."_industry engine=InnoDB");
ExecuteQuery("alter table ".$table_prefix."_industry modify column industry varchar(200) NOT NULL");
$adb->query("alter table ".$table_prefix."_industry drop index Industry_UK0");
$adb->query("alter table ".$table_prefix."_industry add UNIQUE index industry_industry_idx(industry)");

//we have removed contactid from products so that in cvcolumnlist we have to remove this column
ExecuteQuery("delete from ".$table_prefix."_cvcolumnlist where columnname='".$table_prefix."_products:contactid:contact_id:Products_Contact_Name:I'");

//Make the Closed Lost as non editable in Sales Stage
ExecuteQuery("update ".$table_prefix."_sales_stage set presence=0 where sales_stage='Closed Lost'");

//Added to fix the issues in group to entity relationship
$adb->query("DELETE FROM ".$table_prefix."_leadgrouprelation where groupname is NULL or groupname = ''");
$adb->query("ALTER TABLE ".$table_prefix."_leadgrouprelation DROP FOREIGN KEY fk_1_".$table_prefix."_leadgrouprelation");
$adb->query("ALTER TABLE ".$table_prefix."_leadgrouprelation DROP FOREIGN KEY fk_2_".$table_prefix."_leadgrouprelation");

$adb->query("DELETE FROM ".$table_prefix."_accountgrouprelation where groupname is NULL or groupname = ''");
$adb->query("ALTER TABLE ".$table_prefix."_accountgrouprelation DROP FOREIGN KEY fk_1_".$table_prefix."_accountgrouprelation");
$adb->query("ALTER TABLE ".$table_prefix."_accountgrouprelation DROP FOREIGN KEY fk_2_".$table_prefix."_accountgrouprelation");

$adb->query("DELETE FROM ".$table_prefix."_contactgrouprelation where groupname is NULL or groupname = ''");
$adb->query("ALTER TABLE ".$table_prefix."_contactgrouprelation DROP FOREIGN KEY fk_1_".$table_prefix."_contactgrouprelation");
$adb->query("ALTER TABLE ".$table_prefix."_contactgrouprelation DROP FOREIGN KEY fk_2_".$table_prefix."_contactgrouprelation");

$adb->query("DELETE FROM ".$table_prefix."_potentialgrouprelation where groupname is NULL or groupname = ''");
$adb->query("ALTER TABLE ".$table_prefix."_potentialgrouprelation DROP FOREIGN KEY fk_1_".$table_prefix."_potentialgrouprelation");
$adb->query("ALTER TABLE ".$table_prefix."_potentialgrouprelation DROP FOREIGN KEY fk_2_".$table_prefix."_potentialgrouprelation");

$adb->query("DELETE FROM ".$table_prefix."_campaigngrouprelation where groupname is NULL or groupname = ''");
$adb->query("ALTER TABLE ".$table_prefix."_campaigngrouprelation DROP FOREIGN KEY fk_1_".$table_prefix."_campaigngrouprelation");
$adb->query("ALTER TABLE ".$table_prefix."_campaigngrouprelation DROP FOREIGN KEY fk_2_".$table_prefix."_campaigngrouprelation");

$adb->query("DELETE FROM ".$table_prefix."_activitygrouprelation where groupname is NULL or groupname = ''");
$adb->query("ALTER TABLE ".$table_prefix."_activitygrouprelation DROP FOREIGN KEY fk_1_".$table_prefix."_activitygrouprelation");
$adb->query("ALTER TABLE ".$table_prefix."_activitygrouprelation DROP FOREIGN KEY fk_2_".$table_prefix."_activitygrouprelation");

$adb->query("DELETE FROM ".$table_prefix."_ticketgrouprelation where groupname is NULL or groupname = ''");
$adb->query("ALTER TABLE ".$table_prefix."_ticketgrouprelation DROP FOREIGN KEY fk_1_".$table_prefix."_ticketgrouprelation");
$adb->query("ALTER TABLE ".$table_prefix."_ticketgrouprelation DROP FOREIGN KEY fk_2_".$table_prefix."_ticketgrouprelation");

$adb->query("DELETE FROM ".$table_prefix."_sogrouprelation where groupname is NULL or groupname = ''");
$adb->query("ALTER TABLE ".$table_prefix."_sogrouprelation DROP FOREIGN KEY fk_1_".$table_prefix."_sogrouprelation");
$adb->query("ALTER TABLE ".$table_prefix."_sogrouprelation DROP FOREIGN KEY fk_2_".$table_prefix."_sogrouprelation");

$adb->query("DELETE FROM ".$table_prefix."_quotegrouprelation where groupname is NULL or groupname = ''");
$adb->query("ALTER TABLE ".$table_prefix."_quotegrouprelation DROP FOREIGN KEY fk_1_".$table_prefix."_quotegrouprelation");
$adb->query("ALTER TABLE ".$table_prefix."_quotegrouprelation DROP FOREIGN KEY fk_2_".$table_prefix."_quotegrouprelation");

$adb->query("DELETE FROM ".$table_prefix."_pogrouprelation where groupname is NULL or groupname = ''");
$adb->query("ALTER TABLE ".$table_prefix."_pogrouprelation DROP FOREIGN KEY fk_1_".$table_prefix."_pogrouprelation");
$adb->query("ALTER TABLE ".$table_prefix."_pogrouprelation DROP FOREIGN KEY fk_2_".$table_prefix."_pogrouprelation");

$adb->query("DELETE FROM ".$table_prefix."_invoicegrouprelation where groupname is NULL or groupname = ''");
$adb->query("ALTER TABLE ".$table_prefix."_invoicegrouprelation DROP FOREIGN KEY fk_1_".$table_prefix."_invoicegrouprelation");
$adb->query("ALTER TABLE ".$table_prefix."_invoicegrouprelation DROP FOREIGN KEY fk_2_".$table_prefix."_invoicegrouprelation");




ExecuteQuery("ALTER TABLE ".$table_prefix."_leadgrouprelation ADD CONSTRAINT fk_1_".$table_prefix."_leadgrouprelation FOREIGN KEY (groupname) REFERENCES ".$table_prefix."_groups(groupname) ON UPDATE CASCADE");
ExecuteQuery("ALTER TABLE ".$table_prefix."_leadgrouprelation ADD CONSTRAINT fk_2_".$table_prefix."_leadgrouprelation FOREIGN KEY (leadid) REFERENCES ".$table_prefix."_leaddetails(leadid) ON DELETE CASCADE");

ExecuteQuery("ALTER TABLE ".$table_prefix."_accountgrouprelation ADD CONSTRAINT fk_1_".$table_prefix."_accountgrouprelation FOREIGN KEY (accountid) REFERENCES ".$table_prefix."_account(accountid) ON DELETE CASCADE");
ExecuteQuery("ALTER TABLE ".$table_prefix."_accountgrouprelation ADD CONSTRAINT fk_2_".$table_prefix."_accountgrouprelation FOREIGN KEY (groupname) REFERENCES ".$table_prefix."_groups(groupname) ON UPDATE CASCADE");

ExecuteQuery("ALTER TABLE ".$table_prefix."_contactgrouprelation ADD CONSTRAINT fk_1_".$table_prefix."_contactgrouprelation FOREIGN KEY (contactid) REFERENCES ".$table_prefix."_contactdetails(contactid) ON DELETE CASCADE");
ExecuteQuery("ALTER TABLE ".$table_prefix."_contactgrouprelation ADD CONSTRAINT fk_2_".$table_prefix."_contactgrouprelation FOREIGN KEY (groupname) REFERENCES ".$table_prefix."_groups(groupname) ON UPDATE CASCADE");

ExecuteQuery("ALTER TABLE ".$table_prefix."_potentialgrouprelation ADD CONSTRAINT fk_1_".$table_prefix."_potentialgrouprelation FOREIGN KEY (groupname) REFERENCES ".$table_prefix."_groups(groupname) ON UPDATE CASCADE");
ExecuteQuery("ALTER TABLE ".$table_prefix."_potentialgrouprelation ADD CONSTRAINT fk_2_".$table_prefix."_potentialgrouprelation FOREIGN KEY (potentialid) REFERENCES ".$table_prefix."_potential(potentialid) ON DELETE CASCADE");

ExecuteQuery("ALTER TABLE ".$table_prefix."_campaigngrouprelation ADD CONSTRAINT fk_1_".$table_prefix."_campaigngrouprelation FOREIGN KEY (campaignid) REFERENCES ".$table_prefix."_campaign(campaignid) ON DELETE CASCADE");
ExecuteQuery("ALTER TABLE ".$table_prefix."_campaigngrouprelation ADD CONSTRAINT fk_2_".$table_prefix."_campaigngrouprelation FOREIGN KEY (groupname) REFERENCES ".$table_prefix."_groups(groupname) ON UPDATE CASCADE");

ExecuteQuery("ALTER TABLE ".$table_prefix."_activitygrouprelation ADD CONSTRAINT fk_1_".$table_prefix."_activitygrouprelation FOREIGN KEY (activityid) REFERENCES ".$table_prefix."_activity(activityid) ON DELETE CASCADE");
ExecuteQuery("ALTER TABLE ".$table_prefix."_activitygrouprelation ADD CONSTRAINT fk_2_".$table_prefix."_activitygrouprelation FOREIGN KEY (groupname) REFERENCES ".$table_prefix."_groups(groupname) ON UPDATE CASCADE");

ExecuteQuery("ALTER TABLE ".$table_prefix."_ticketgrouprelation ADD CONSTRAINT fk_1_".$table_prefix."_ticketgrouprelation FOREIGN KEY (groupname) REFERENCES ".$table_prefix."_groups(groupname) ON UPDATE CASCADE");
ExecuteQuery("ALTER TABLE ".$table_prefix."_ticketgrouprelation ADD CONSTRAINT fk_2_".$table_prefix."_ticketgrouprelation FOREIGN KEY (ticketid) REFERENCES ".$table_prefix."_troubletickets(ticketid) ON DELETE CASCADE");

ExecuteQuery("ALTER TABLE ".$table_prefix."_sogrouprelation ADD CONSTRAINT fk_1_".$table_prefix."_sogrouprelation FOREIGN KEY (groupname) REFERENCES ".$table_prefix."_groups(groupname) ON UPDATE CASCADE");
ExecuteQuery("ALTER TABLE ".$table_prefix."_sogrouprelation ADD CONSTRAINT fk_2_".$table_prefix."_sogrouprelation FOREIGN KEY (salesorderid) REFERENCES ".$table_prefix."_salesorder(salesorderid) ON DELETE CASCADE");

ExecuteQuery("ALTER TABLE ".$table_prefix."_quotegrouprelation ADD CONSTRAINT fk_1_".$table_prefix."_quotegrouprelation FOREIGN KEY (groupname) REFERENCES ".$table_prefix."_groups(groupname) ON UPDATE CASCADE");
ExecuteQuery("ALTER TABLE ".$table_prefix."_quotegrouprelation ADD CONSTRAINT fk_2_".$table_prefix."_quotegrouprelation FOREIGN KEY (quoteid) REFERENCES ".$table_prefix."_quotes(quoteid) ON DELETE CASCADE");


ExecuteQuery("ALTER TABLE ".$table_prefix."_pogrouprelation ADD CONSTRAINT fk_1_".$table_prefix."_pogrouprelation FOREIGN KEY (groupname) REFERENCES ".$table_prefix."_groups(groupname) ON UPDATE CASCADE");
ExecuteQuery("ALTER TABLE ".$table_prefix."_pogrouprelation ADD CONSTRAINT fk_2_".$table_prefix."_pogrouprelation FOREIGN KEY (purchaseorderid) REFERENCES ".$table_prefix."_purchaseorder(purchaseorderid) ON DELETE CASCADE");


ExecuteQuery("ALTER TABLE ".$table_prefix."_invoicegrouprelation ADD CONSTRAINT fk_1_".$table_prefix."_invoicegrouprelation FOREIGN KEY (groupname) REFERENCES ".$table_prefix."_groups(groupname) ON UPDATE CASCADE");
ExecuteQuery("ALTER TABLE ".$table_prefix."_invoicegrouprelation ADD CONSTRAINT fk_2_".$table_prefix."_invoicegrouprelation FOREIGN KEY (invoiceid) REFERENCES ".$table_prefix."_invoice(invoiceid) ON DELETE CASCADE");

//For emails listview, we have to update the column sender
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_crmentity:smownerid:assigned_user_id:Emails_Sender:V' where columnname='".$table_prefix."_crmentity:smownerid:assigned_user_id:Emails_Assigned_To:V'");

//if is_admin is set as 0 then we have to update as off and update status as Active if the status is NULL
ExecuteQuery("update ".$table_prefix."_users set is_admin='off' where is_admin='0'");
ExecuteQuery("update ".$table_prefix."_users set status='Active' where status is NULL");

//Update the City, State, Zip and Country to Mailing City, State, Zip and Country
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_contactaddress:mailingcity:mailingcity:Contacts_Mailing_City:V' where columnname='".$table_prefix."_contactaddress:mailingcity:mailingcity:Contacts_City:V'");
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_contactaddress:mailingstate:mailingstate:Contacts_Mailing_State:V' where columnname='".$table_prefix."_contactaddress:mailingstate:mailingstate:Contacts_State:V'");
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_contactaddress:mailingzip:mailingzip:Contacts_Mailing_Zip:V' where columnname='".$table_prefix."_contactaddress:mailingzip:mailingzip:Contacts_Zip:V'");
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_contactaddress:mailingcountry:mailingcountry:Contacts_Mailing_Country:V' where columnname='".$table_prefix."_contactaddress:mailingcountry:mailingcountry:Contacts_Country:V'");

//Added Attachments and Quote stage history in Quotes relatedlist
ExecuteQuery("update ".$table_prefix."_relatedlists set sequence=4 where tabid=20 and name='get_history'");
ExecuteQuery("insert into ".$table_prefix."_relatedlists values (".$adb->getUniqueID($table_prefix.'_relatedlists').",20,0,'get_attachments',3,'Attachments',0), (".$adb->getUniqueID($table_prefix.'_relatedlists').",20,0,'get_quotestagehistory',5,'Quote Stage History',0)");

//Added SalesOrder Status History in SalesOrder relatedlist
ExecuteQuery("insert into ".$table_prefix."_relatedlists values (".$adb->getUniqueID($table_prefix.'_relatedlists').",22,0,'get_sostatushistory',5,'SalesOrder Status History',0)");

//Added PurchaseOrder Status History in PurchaseOrder relatedlist
ExecuteQuery("insert into ".$table_prefix."_relatedlists values (".$adb->getUniqueID($table_prefix.'_relatedlists').",21,0,'get_postatushistory',4,'PurchaseOrder Status History',0)");

//Update SalesOrder Id as SalesOrder No for default All SalesOrder customview (only All customview has this field)
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_crmentity:crmid::SalesOrder_Order_No:I' where columnname='".$table_prefix."_crmentity:crmid::SalesOrder_Order_Id:I'");

//Update PurchaseOrder Id as Purchase No for default All PurchaseOrder customview (only All customview has this field)
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_crmentity:crmid::PurchaseOrder_Order_No:I' where columnname='".$table_prefix."_crmentity:crmid::PurchaseOrder_Order_Id:I'");






$query = "select * from ".$table_prefix."_emaildetails";
$result = $adb->query($query);
$find_array = Array('<','>');
$replace_array = Array('&lt;','&gt;');
for($i = 0; $i < $adb->num_rows($result); $i++)
{
	$emailID = $adb->query_result($result,$i,'emailid');
	$from_email = $adb->query_result($result,$i,'from_email');
	$to_email = $adb->query_result($result,$i,'to_email');
	$cc_email = $adb->query_result($result,$i,'cc_email');
	$bcc_email = $adb->query_result($result,$i,'bcc_email');
	$assigned_user_email = $adb->query_result($result,$i,'assigned_user_email');
	$replace_from_email = str_replace($find_array,$replace_array,$from_email);
	$replace_to_email = str_replace($find_array,$replace_array,$to_email);
	$replace_cc_email = str_replace($find_array,$replace_array,$cc_email);
	$replace_bcc_email = str_replace($find_array,$replace_array,$bcc_email);
	$replace_assigned_user_email = str_replace($find_array,$replace_array,$assigned_user_email);
	$query = "update ".$table_prefix."_emaildetails set from_email='".$replace_from_email."', to_email='".$replace_to_email."', cc_email='".$replace_cc_email."' ,bcc_email='".$assigned_user_email."' where emailid=".$emailID;
	//echo $query.'<br>';
	ExecuteQuery($query);
}

ExecuteQuery("delete from ".$table_prefix."_selectcolumn where columnname='".$table_prefix."_contactdetailsProducts:lastname:Products_Contact_Name:contact_id:I'");
ExecuteQuery("delete from ".$table_prefix."_selectcolumn where columnname='".$table_prefix."_invoice:notes:Invoice_Notes:notes:V'");
ExecuteQuery("delete from ".$table_prefix."_selectcolumn where columnname='".$table_prefix."_invoice:invoiceterms:Invoice_Invoice_Terms:invoiceterms:V'");

ExecuteQuery("update ".$table_prefix."_selectcolumn set columnname='".$table_prefix."_leaddetails:email:Leads_Email:email:V' where columnname='".$table_prefix."_leaddetails:email:Leads_Email:email:E'");



//to remove Export from Emails Module Under Profiles
ExecuteQuery("delete from ".$table_prefix."_profile2utility where tabid=10");


//ALTER TABLE Queries

ExecuteQuery("alter table ".$table_prefix."_accountbillads change column city bill_city varchar(30)");
ExecuteQuery("alter table ".$table_prefix."_accountbillads change column code bill_code varchar(30)");
ExecuteQuery("alter table ".$table_prefix."_accountbillads change column country bill_country varchar(30)");
ExecuteQuery("alter table ".$table_prefix."_accountbillads change column state bill_state varchar(30)");
ExecuteQuery("alter table ".$table_prefix."_accountbillads change column street bill_street varchar(250)");
ExecuteQuery("alter table ".$table_prefix."_accountbillads change column pobox bill_pobox varchar(30)");

ExecuteQuery("alter table ".$table_prefix."_accountshipads change column city ship_city varchar(30)");
ExecuteQuery("alter table ".$table_prefix."_accountshipads change column code ship_code varchar(30)");
ExecuteQuery("alter table ".$table_prefix."_accountshipads change column country ship_country varchar(30)");
ExecuteQuery("alter table ".$table_prefix."_accountshipads change column state ship_state varchar(30)");
ExecuteQuery("alter table ".$table_prefix."_accountshipads change column street ship_street varchar(250)");
ExecuteQuery("alter table ".$table_prefix."_accountshipads change column pobox ship_pobox varchar(30)");


//Field Table Update Queries
ExecuteQuery("update ".$table_prefix."_field set columnname='bill_city' where columnname='city' and tablename='".$table_prefix."_accountbillads'");
ExecuteQuery("update ".$table_prefix."_field set columnname='bill_code' where columnname='code' and tablename='".$table_prefix."_accountbillads'");
ExecuteQuery("update ".$table_prefix."_field set columnname='bill_country' where columnname='country' and tablename='".$table_prefix."_accountbillads'");
ExecuteQuery("update ".$table_prefix."_field set columnname='bill_state' where columnname='state' and tablename='".$table_prefix."_accountbillads'");
ExecuteQuery("update ".$table_prefix."_field set columnname='bill_street' where columnname='street' and tablename='".$table_prefix."_accountbillads'");
ExecuteQuery("update ".$table_prefix."_field set columnname='bill_pobox' where columnname='pobox' and tablename='".$table_prefix."_accountbillads'");


ExecuteQuery("update ".$table_prefix."_field set columnname='ship_city' where columnname='city' and tablename='".$table_prefix."_accountshipads'");
ExecuteQuery("update ".$table_prefix."_field set columnname='ship_code' where columnname='code' and tablename='".$table_prefix."_accountshipads'");
ExecuteQuery("update ".$table_prefix."_field set columnname='ship_country' where columnname='country' and tablename='".$table_prefix."_accountshipads'");
ExecuteQuery("update ".$table_prefix."_field set columnname='ship_state' where columnname='state' and tablename='".$table_prefix."_accountshipads'");
ExecuteQuery("update ".$table_prefix."_field set columnname='ship_street' where columnname='street' and tablename='".$table_prefix."_accountshipads'");
ExecuteQuery("update ".$table_prefix."_field set columnname='ship_pobox' where columnname='pobox' and tablename='".$table_prefix."_accountshipads'");


//CustomView Queries
//Available in Populate CustomView.php so migration for that particular field
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_accountbillads:bill_city:bill_city:Accounts_Billing_City:V' where columnname='".$table_prefix."_accountbillads:city:bill_city:Accounts_City:V'");

ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_accountbillads:bill_city:bill_city:Accounts_Billing_City:V' where columnname='".$table_prefix."_accountbillads:city:bill_city:Accounts_Billing_City:V'");
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_accountbillads:bill_code:bill_code:Accounts_Billing_Code:V' where columnname='".$table_prefix."_accountbillads:code:bill_code:Accounts_Billing_Code:V'");
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_accountbillads:bill_country:bill_country:Accounts_Billing_Country:V' where  columnname='".$table_prefix."_accountbillads:country:bill_country:Accounts_Billing_Country:V'");
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_accountbillads:bill_state:bill_state:Accounts_Billing_State:V' where columnname='".$table_prefix."_accountbillads:state:bill_state:Accounts_Billing_State:V'");
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_accountbillads:bill_street:bill_street:Accounts_Billing_Address:V' where columnname='".$table_prefix."_accountbillads:street:bill_street:Accounts_Billing_Address:V'");
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_accountbillads:bill_pobox:bill_pobox:Accounts_Billing_Po_Box:V' where columnname='".$table_prefix."_accountbillads:pobox:bill_pobox:Accounts_Billing_Po_Box:V'");



ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_accountshipads:ship_street:ship_street:Accounts_Shipping_Address:V' where columnname='".$table_prefix."_accountshipads:street:ship_street:Accounts_Shipping_Address:V'");
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_accountshipads:ship_pobox:ship_pobox:Accounts_Shipping_Po_Box:V' where columnname='".$table_prefix."_accountshipads:pobox:ship_pobox:Accounts_Shipping_Po_Box:V'");
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_accountshipads:ship_city:ship_city:Accounts_Shipping_City:V' where columnname='".$table_prefix."_accountshipads:city:ship_city:Accounts_Shipping_City:V'");
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_accountshipads:ship_state:ship_state:Accounts_Shipping_State:V' where columnname='".$table_prefix."_accountshipads:state:ship_state:Accounts_Shipping_State:V'");
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_accountshipads:ship_code:ship_code:Accounts_Shipping_Code:V' where columnname='".$table_prefix."_accountshipads:code:ship_code:Accounts_Shipping_Code:V'");
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_accountshipads:ship_country:ship_country:Accounts_Shipping_Country:V' where columnname='".$table_prefix."_accountshipads:country:ship_country:Accounts_Shipping_Country:V'");

//CustomView Advanced Filter

ExecuteQuery("update ".$table_prefix."_cvadvfilter set columnname='".$table_prefix."_accountbillads:bill_city:bill_city:Accounts_Billing_City:V' where columnname='".$table_prefix."_accountbillads:city:bill_city:Accounts_Billing_City:V'");
ExecuteQuery("update ".$table_prefix."_cvadvfilter set columnname='".$table_prefix."_accountbillads:bill_code:bill_code:Accounts_Billing_Code:V' where columnname='".$table_prefix."_accountbillads:code:bill_code:Accounts_Billing_Code:V'");
ExecuteQuery("update ".$table_prefix."_cvadvfilter set columnname='".$table_prefix."_accountbillads:bill_country:bill_country:Accounts_Billing_Country:V' where  columnname='".$table_prefix."_accountbillads:country:bill_country:Accounts_Billing_Country:V'");
ExecuteQuery("update ".$table_prefix."_cvadvfilter set columnname='".$table_prefix."_accountbillads:bill_state:bill_state:Accounts_Billing_State:V' where columnname='".$table_prefix."_accountbillads:state:bill_state:Accounts_Billing_State:V'");
ExecuteQuery("update ".$table_prefix."_cvadvfilter set columnname='".$table_prefix."_accountbillads:bill_street:bill_street:Accounts_Billing_Address:V' where columnname='".$table_prefix."_accountbillads:street:bill_street:Accounts_Billing_Address:V'");
ExecuteQuery("update ".$table_prefix."_cvadvfilter set columnname='".$table_prefix."_accountbillads:bill_pobox:bill_pobox:Accounts_Billing_Po_Box:V' where columnname='".$table_prefix."_accountbillads:pobox:bill_pobox:Accounts_Billing_Po_Box:V'");



ExecuteQuery("update ".$table_prefix."_cvadvfilter set columnname='".$table_prefix."_accountshipads:ship_street:ship_street:Accounts_Shipping_Address:V' where columnname='".$table_prefix."_accountshipads:street:ship_street:Accounts_Shipping_Address:V'");
ExecuteQuery("update ".$table_prefix."_cvadvfilter set columnname='".$table_prefix."_accountshipads:ship_pobox:ship_pobox:Accounts_Shipping_Po_Box:V' where columnname='".$table_prefix."_accountshipads:pobox:ship_pobox:Accounts_Shipping_Po_Box:V'");
ExecuteQuery("update ".$table_prefix."_cvadvfilter set columnname='".$table_prefix."_accountshipads:ship_city:ship_city:Accounts_Shipping_City:V' where columnname='".$table_prefix."_accountshipads:city:ship_city:Accounts_Shipping_City:V'");
ExecuteQuery("update ".$table_prefix."_cvadvfilter set columnname='".$table_prefix."_accountshipads:ship_state:ship_state:Accounts_Shipping_State:V' where columnname='".$table_prefix."_accountshipads:state:ship_state:Accounts_Shipping_State:V'");
ExecuteQuery("update ".$table_prefix."_cvadvfilter set columnname='".$table_prefix."_accountshipads:ship_code:ship_code:Accounts_Shipping_Code:V' where columnname='".$table_prefix."_accountshipads:code:ship_code:Accounts_Shipping_Code:V'");
ExecuteQuery("update ".$table_prefix."_cvadvfilter set columnname='".$table_prefix."_accountshipads:ship_country:ship_country:Accounts_Shipping_Country:V' where columnname='".$table_prefix."_accountshipads:country:ship_country:Accounts_Shipping_Country:V'");


//Reports Columns
ExecuteQuery("update ".$table_prefix."_selectcolumn set columnname='".$table_prefix."_accountbillads:bill_country:Accounts_Billing_Country:bill_country:V' where columnname='".$table_prefix."_accountbillads:country:Accounts_Billing_Country:bill_country:V'");
ExecuteQuery("update ".$table_prefix."_selectcolumn set columnname='".$table_prefix."_accountbillads:bill_city:Accounts_Billing_City:bill_city:V' where columnname='".$table_prefix."_accountbillads:city:Accounts_Billing_City:bill_city:V'");
ExecuteQuery("update ".$table_prefix."_selectcolumn set columnname='".$table_prefix."_accountbillads:bill_pobox:Accounts_Billing_Po_Box:bill_pobox:V' where columnname='".$table_prefix."_accountbillads:pobox:Accounts_Billing_Po_Box:bill_pobox:V'");
ExecuteQuery("update ".$table_prefix."_selectcolumn set columnname='".$table_prefix."_accountbillads:bill_street:Accounts_Billing_Address:bill_street:V' where columnname='".$table_prefix."_accountbillads:street:Accounts_Billing_Address:bill_street:V'");
ExecuteQuery("update ".$table_prefix."_selectcolumn set columnname='".$table_prefix."_accountbillads:bill_code:Accounts_Billing_Code:bill_code:V' where columnname='".$table_prefix."_accountbillads:code:Accounts_Billing_Code:bill_code:V'");
ExecuteQuery("update ".$table_prefix."_selectcolumn set columnname='".$table_prefix."_accountbillads:bill_state:Accounts_Billing_State:bill_state:V' where columnname='".$table_prefix."_accountbillads:state:Accounts_Billing_State:bill_state:V'");


ExecuteQuery("update ".$table_prefix."_selectcolumn set columnname='".$table_prefix."_accountshipads:ship_city:Accounts_Shipping_City:ship_city:V' where columnname='".$table_prefix."_accountshipads:city:Accounts_Shipping_City:ship_city:V'");
ExecuteQuery("update ".$table_prefix."_selectcolumn set columnname='".$table_prefix."_accountshipads:ship_pobox:Accounts_Shipping_Po_Box:ship_pobox:V' where columnname='".$table_prefix."_accountshipads:pobox:Accounts_Shipping_Po_Box:ship_pobox:V'");
ExecuteQuery("update ".$table_prefix."_selectcolumn set columnname='".$table_prefix."_accountshipads:ship_street:Accounts_Shipping_Address:ship_street:V' where columnname='".$table_prefix."_accountshipads:street:Accounts_Shipping_Address:ship_street:V'");
ExecuteQuery("update ".$table_prefix."_selectcolumn set columnname='".$table_prefix."_accountshipads:ship_country:Accounts_Shipping_Country:ship_country:V' where columnname='".$table_prefix."_accountshipads:country:Accounts_Shipping_Country:ship_country:V'");
ExecuteQuery("update ".$table_prefix."_selectcolumn set columnname='".$table_prefix."_accountshipads:ship_code:Accounts_Shipping_Code:ship_code:V' where columnname='".$table_prefix."_accountshipads:code:Accounts_Shipping_Code:ship_code:V'");
ExecuteQuery("update ".$table_prefix."_selectcolumn set columnname='".$table_prefix."_accountshipads:ship_state:Accounts_Shipping_State:ship_state:V' where columnname='".$table_prefix."_accountshipads:state:Accounts_Shipping_State:ship_state:V'");

//Reports Advanced Filter
ExecuteQuery("update ".$table_prefix."_relcriteria set columnname='".$table_prefix."_accountbillads:bill_country:Accounts_Billing_Country:bill_country:V' where columnname='".$table_prefix."_accountbillads:country:Accounts_Billing_Country:bill_country:V'");
ExecuteQuery("update ".$table_prefix."_relcriteria set columnname='".$table_prefix."_accountbillads:bill_city:Accounts_Billing_City:bill_city:V' where columnname='".$table_prefix."_accountbillads:city:Accounts_Billing_City:bill_city:V'");
ExecuteQuery("update ".$table_prefix."_relcriteria set columnname='".$table_prefix."_accountbillads:bill_pobox:Accounts_Billing_Po_Box:bill_pobox:V' where columnname='".$table_prefix."_accountbillads:pobox:Accounts_Billing_Po_Box:bill_pobox:V'");
ExecuteQuery("update ".$table_prefix."_relcriteria set columnname='".$table_prefix."_accountbillads:bill_street:Accounts_Billing_Address:bill_street:V' where columnname='".$table_prefix."_accountbillads:street:Accounts_Billing_Address:bill_street:V'");
ExecuteQuery("update ".$table_prefix."_relcriteria set columnname='".$table_prefix."_accountbillads:bill_code:Accounts_Billing_Code:bill_code:V' where columnname='".$table_prefix."_accountbillads:code:Accounts_Billing_Code:bill_code:V'");
ExecuteQuery("update ".$table_prefix."_relcriteria set columnname='".$table_prefix."_accountbillads:bill_state:Accounts_Billing_State:bill_state:V' where columnname='".$table_prefix."_accountbillads:state:Accounts_Billing_State:bill_state:V'");


ExecuteQuery("update ".$table_prefix."_relcriteria set columnname='".$table_prefix."_accountshipads:ship_city:Accounts_Shipping_City:ship_city:V' where columnname='".$table_prefix."_accountshipads:city:Accounts_Shipping_City:ship_city:V'");
ExecuteQuery("update ".$table_prefix."_relcriteria set columnname='".$table_prefix."_accountshipads:ship_pobox:Accounts_Shipping_Po_Box:ship_pobox:V' where columnname='".$table_prefix."_accountshipads:pobox:Accounts_Shipping_Po_Box:ship_pobox:V'");
ExecuteQuery("update ".$table_prefix."_relcriteria set columnname='".$table_prefix."_accountshipads:ship_street:Accounts_Shipping_Address:ship_street:V' where columnname='".$table_prefix."_accountshipads:street:Accounts_Shipping_Address:ship_street:V'");
ExecuteQuery("update ".$table_prefix."_relcriteria set columnname='".$table_prefix."_accountshipads:ship_country:Accounts_Shipping_Country:ship_country:V' where columnname='".$table_prefix."_accountshipads:country:Accounts_Shipping_Country:ship_country:V'");
ExecuteQuery("update ".$table_prefix."_relcriteria set columnname='".$table_prefix."_accountshipads:ship_code:Accounts_Shipping_Code:ship_code:V' where columnname='".$table_prefix."_accountshipads:code:Accounts_Shipping_Code:ship_code:V'");
ExecuteQuery("update ".$table_prefix."_relcriteria set columnname='".$table_prefix."_accountshipads:ship_state:Accounts_Shipping_State:ship_state:V' where columnname='".$table_prefix."_accountshipads:state:Accounts_Shipping_State:ship_state:V'");


//FieldLabel of Phone Field for Contacts has been changed to Other Phone

ExecuteQuery("update ".$table_prefix."_field set fieldlabel = 'Other Phone' where tablename='".$table_prefix."_contactsubdetails' and columnname='otherphone'");

ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_contactsubdetails:otherphone:otherphone:Contacts_Other_Phone:V' where columnname='".$table_prefix."_contactsubdetails:otherphone:otherphone:Contacts_Phone:V'");
ExecuteQuery("update ".$table_prefix."_cvadvfilter set columnname='".$table_prefix."_contactsubdetails:otherphone:otherphone:Contacts_Other_Phone:V' where columnname='".$table_prefix."_contactsubdetails:otherphone:otherphone:Contacts_Phone:V'");


//get the values from vtiger_selectcolumn with columnname like 'vtiger_contactdetails%' get the columnname value check for the field Phone and change the FieldLabel from Contacts_Phone to Contacts_Other_Phone -- To be done

$query1 = "select columnname from ".$table_prefix."_selectcolumn where columnname like '".$table_prefix."_contactdetails%' and columnname like '%Contacts_Phone%'";
$result = $adb->query($query1);
for($i = 0;$i < $adb->num_rows($result);$i++)
{
	$columnname = $adb->query_result($result,$i,'columnname');
	$update_columnname = str_replace('Contacts_Phone','Contacts_Other_Phone',$columnname);
	$query = "update ".$table_prefix."_selectcolumn set columnname='$update_columnname' where columnname='$columnname'";
	$adb->query($query);
}

//get the values from vtiger_relcriteria with columnname like 'vtiger_contactdetails%' get the columnname value check for the field Phone and change the FieldLabel from Contacts_Phone to Contacts_Other_Phone-- To be done
$query1 = "select columnname from ".$table_prefix."_relcriteria where columnname like '".$table_prefix."_contactdetails%' and columnname like '%Contacts_Phone%'";
$result = $adb->query($query1);
for($i = 0;$i < $adb->num_rows($result);$i++)
{
	$columnname = $adb->query_result($result,$i,'columnname');
	$update_columnname = str_replace('Contacts_Phone','Contacts_Other_Phone',$columnname);
	$query = "update ".$table_prefix."_relcriteria set columnname='$update_columnname' where columnname='$columnname'";
	$adb->query($query);
}

//Removed the default value Mr in salutation
ExecuteQuery("alter table ".$table_prefix."_contactdetails change column salutation salutation varchar(50)");
ExecuteQuery("update ".$table_prefix."_campaignstatus set campaignstatus='Completed' where campaignstatus='Complete'");
ExecuteQuery("update ".$table_prefix."_crmentity set setype='PurchaseOrder' where setype='Orders'");
ExecuteQuery("update ".$table_prefix."_crmentity set setype='PurchaseOrder Attachment' where setype='Orders Attachment'");
ExecuteQuery("update ".$table_prefix."_crmentity set setype='Vendors' where setype='Vendor'");
ExecuteQuery("update ".$table_prefix."_customview set entitytype='Vendors' where entitytype='Vendor'");

//Fixed customview related changes
//Calendar - Related To
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_seactivityrel:crmid:parent_id:Calendar_Related_to:V' where columnname='".$table_prefix."_seactivityrel:crmid:parent_id:Calendar_Related_to:I'");
ExecuteQuery("update ".$table_prefix."_cvadvfilter set columnname='".$table_prefix."_seactivityrel:crmid:parent_id:Calendar_Related_to:V' where columnname='".$table_prefix."_seactivityrel:crmid:parent_id:Calendar_Related_to:I'");

//Calendar - Start Date (Date & Time)
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_activity:date_start:date_start:Calendar_Start_Date:DT' where columnname='".$table_prefix."_activity:date_start:date_start:Calendar_Start_Date_&_Time:DT' or columnname = 'activity:date_start:date_start:Activities_Start_Date_&_Time:DT'");
ExecuteQuery("update ".$table_prefix."_cvadvfilter set columnname='".$table_prefix."_activity:date_start:date_start:Calendar_Start_Date:DT' where columnname='".$table_prefix."_activity:date_start:date_start:Calendar_Start_Date_&_Time:DT'  or columnname = 'activity:date_start:date_start:Activities_Start_Date_&_Time:DT'");

//Notes - Related To
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_senotesrel:crmid:parent_id:Notes_Related_to:V' where columnname='".$table_prefix."_senotesrel:crmid:parent_id:Notes_Related_to:I'");
ExecuteQuery("update ".$table_prefix."_cvadvfilter set columnname='".$table_prefix."_senotesrel:crmid:parent_id:Notes_Related_to:V' where columnname='".$table_prefix."_senotesrel:crmid:parent_id:Notes_Related_to:I'");

//Notes - Contact Name
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_contactdetails:lastname:lastname:Notes_Contact_Name:V' where columnname='".$table_prefix."_notes:contact_id:contact_id:Notes_Contact_Name:V'");
ExecuteQuery("update ".$table_prefix."_cvadvfilter set columnname='".$table_prefix."_contactdetails:lastname:lastname:Notes_Contact_Name:V' where columnname='".$table_prefix."_notes:contact_id:contact_id:Notes_Contact_Name:V'");

//Notes - title
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_notes:title:notes_title:Notes_Title:V' where columnname='".$table_prefix."_notes:title:title:Notes_Subject:V'");
ExecuteQuery("update ".$table_prefix."_cvadvfilter set columnname='".$table_prefix."_notes:title:notes_title:Notes_Title:V' where columnname='".$table_prefix."_notes:title:title:Notes_Subject:V'");

//In Products - Related To and Contact should be deleted
ExecuteQuery("delete from ".$table_prefix."_cvcolumnlist where columnname='".$table_prefix."_seproductsrel:crmid:parent_id:Products_Related_to:I'");
ExecuteQuery("delete from ".$table_prefix."_cvadvfilter where columnname='".$table_prefix."_seproductsrel:crmid:parent_id:Products_Related_to:I'");
ExecuteQuery("delete from ".$table_prefix."_cvcolumnlist where columnname='".$table_prefix."_products:contactid:contact_id:Products_Contact_Name:I'");
ExecuteQuery("delete from ".$table_prefix."_cvadvfilter where columnname='".$table_prefix."_products:contactid:contact_id:Products_Contact_Name:I'");

//For vendors module we have to update the module name as Vendors from Vendor
$result = $adb->query("select {$table_prefix}_cvcolumnlist.cvid, vtiger_cvcolumnlist.columnname from {$table_prefix}_customview inner join {$table_prefix}_cvcolumnlist on {$table_prefix}_customview.cvid = {$table_prefix}_cvcolumnlist.cvid where {$table_prefix}_customview.entitytype='Vendors' and columnname != ''");
for($i=0;$i<$adb->num_rows($result);$i++)
{
	$cvid = $adb->query_result($result,$i,'cvid');
	$columnname = $adb->query_result($result,$i,'columnname');
	$new_columnname = str_replace(':Vendor_',':Vendors_',$columnname);

	$adb->query("update ".$table_prefix."_cvcolumnlist set columnname='$new_columnname' where columnname='$columnname'");
	$adb->query("update ".$table_prefix."_cvadvfilter set columnname='$new_columnname' where columnname='$columnname'");
}

//Vendor - instead of street, treet has been saved previously
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_vendor:street:street:Vendors_Street:V' where columnname='".$table_prefix."_vendor:street:treet:Vendors_Street:V'");

//Accounts - Other Email
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_account:email2:email2:Accounts_Other_Email:V' where columnname='".$table_prefix."_account:email2:email2:Accounts_Other_Email:E'");
ExecuteQuery("update ".$table_prefix."_cvadvfilter set columnname='".$table_prefix."_account:email2:email2:Accounts_Other_Email:V' where columnname='".$table_prefix."_account:email2:email2:Accounts_Other_Email:E'");

//Accounts - SIC Code
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_account:siccode:siccode:Accounts_SIC_Code:V' where columnname='".$table_prefix."_account:siccode:siccode:Accounts_SIC_Code:I'");
ExecuteQuery("update ".$table_prefix."_cvadvfilter set columnname='".$table_prefix."_account:siccode:siccode:Accounts_SIC_Code:V' where columnname='".$table_prefix."_account:siccode:siccode:Accounts_SIC_Code:I'");

//Account - Member Of
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_account:parentid:account_id:Accounts_Member_Of:V' where columnname='".$table_prefix."_account:parentid:account_id:Accounts_Member_Of:I'");
ExecuteQuery("update ".$table_prefix."_cvadvfilter set columnname='".$table_prefix."_account:parentid:account_id:Accounts_Member_Of:V' where columnname='".$table_prefix."_account:parentid:account_id:Accounts_Member_Of:I'");

//Account - Email
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_account:email1:email1:Accounts_Email:V' where columnname='".$table_prefix."_account:email1:email1:Accounts_Email:E'");
ExecuteQuery("update ".$table_prefix."_cvadvfilter set columnname='".$table_prefix."_account:email1:email1:Accounts_Email:V' where columnname='".$table_prefix."_account:email1:email1:Accounts_Email:E'");

//Contact - Email
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_contactdetails:email:email:Contacts_Email:V' where columnname='".$table_prefix."_contactdetails:email:email:Contacts_Email:E'");
ExecuteQuery("update ".$table_prefix."_cvadvfilter set columnname='".$table_prefix."_contactdetails:email:email:Contacts_Email:V' where columnname='".$table_prefix."_contactdetails:email:email:Contacts_Email:E'");

//Leads - Email
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_leaddetails:email:email:Leads_Email:V' where columnname='".$table_prefix."_leaddetails:email:email:Leads_Email:E'");
ExecuteQuery("update ".$table_prefix."_cvadvfilter set columnname='".$table_prefix."_leaddetails:email:email:Leads_Email:V' where columnname='".$table_prefix."_leaddetails:email:email:Leads_Email:E'");

//Tickect - Related To
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_troubletickets:parent_id:parent_id:HelpDesk_Related_to:V' where columnname='".$table_prefix."_troubletickets:parent_id:parent_id:HelpDesk_Related_to:I'");
ExecuteQuery("update ".$table_prefix."_cvadvfilter set columnname='".$table_prefix."_troubletickets:parent_id:parent_id:HelpDesk_Related_to:V' where columnname='".$table_prefix."_troubletickets:parent_id:parent_id:HelpDesk_Related_to:I'");

//Ticket - Product Name
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_troubletickets:product_id:product_id:HelpDesk_Product_Name:V' where columnname='".$table_prefix."_troubletickets:product_id:product_id:HelpDesk_Product_Name:I'");
ExecuteQuery("update ".$table_prefix."_cvadvfilter set columnname='".$table_prefix."_troubletickets:product_id:product_id:HelpDesk_Product_Name:V' where columnname='".$table_prefix."_troubletickets:product_id:product_id:HelpDesk_Product_Name:I'");

//Invoice - Sales Order
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_invoice:salesorderid:salesorder_id:Invoice_Sales_Order:V' where columnname='".$table_prefix."_invoice:salesorderid:salesorder_id:Invoice_Sales_Order:I'");
ExecuteQuery("update ".$table_prefix."_cvadvfilter set columnname='".$table_prefix."_invoice:salesorderid:salesorder_id:Invoice_Sales_Order:V' where columnname='".$table_prefix."_invoice:salesorderid:salesorder_id:Invoice_Sales_Order:I'");

//Product - Active
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_products:discontinued:discontinued:Products_Product_Active:C' where columnname='".$table_prefix."_products:discontinued:discontinued:Products_Product_Active:V'");
ExecuteQuery("update ".$table_prefix."_cvadvfilter set columnname='".$table_prefix."_products:discontinued:discontinued:Products_Product_Active:C' where columnname='".$table_prefix."_products:discontinued:discontinued:Products_Product_Active:V'");

//Product - Vendor Name
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_products:vendor_id:vendor_id:Products_Vendor_Name:V' where columnname='".$table_prefix."_products:vendor_id:vendor_id:Products_Vendor_Name:I'");
ExecuteQuery("update ".$table_prefix."_cvadvfilter set columnname='".$table_prefix."_products:vendor_id:vendor_id:Products_Vendor_Name:V' where columnname='".$table_prefix."_products:vendor_id:vendor_id:Products_Vendor_Name:I'");

//Product - Product Code (Part Number)
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_products:productcode:productcode:Products_Part_Number:V' where columnname='".$table_prefix."_products:productcode:productcode:Products_Product_Code:V'");
ExecuteQuery("update ".$table_prefix."_cvadvfilter set columnname='".$table_prefix."_products:productcode:productcode:Products_Part_Number:V' where columnname='".$table_prefix."_products:productcode:productcode:Products_Product_Code:V'");

//Purchase Order - Vendor Name
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_purchaseorder:vendorid:vendor_id:PurchaseOrder_Vendor_Name:V' where columnname='".$table_prefix."_purchaseorder:vendorid:vendor_id:PurchaseOrder_Vendor_Name:I'");
ExecuteQuery("update ".$table_prefix."_cvadvfilter set columnname='".$table_prefix."_purchaseorder:vendorid:vendor_id:PurchaseOrder_Vendor_Name:V' where columnname='".$table_prefix."_purchaseorder:vendorid:vendor_id:PurchaseOrder_Vendor_Name:I'");

//Purchase Order - Due Date
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_purchaseorder:duedate:duedate:PurchaseOrder_Due_Date:D' where columnname='".$table_prefix."_purchaseorder:duedate:duedate:PurchaseOrder_Due_Date:V'");
ExecuteQuery("update ".$table_prefix."_cvadvfilter set columnname='".$table_prefix."_purchaseorder:duedate:duedate:PurchaseOrder_Due_Date:D' where columnname='".$table_prefix."_purchaseorder:duedate:duedate:PurchaseOrder_Due_Date:V'");

//SalesOrder - Potential Name
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_salesorder:potentialid:potential_id:SalesOrder_Potential_Name:V' where columnname='".$table_prefix."_salesorder:potentialid:potential_id:SalesOrder_Potential_Name:I'");
ExecuteQuery("update ".$table_prefix."_cvadvfilter set columnname='".$table_prefix."_salesorder:potentialid:potential_id:SalesOrder_Potential_Name:V' where columnname='".$table_prefix."_salesorder:potentialid:potential_id:SalesOrder_Potential_Name:I'");

//SalesOrder - Quote Name
ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='".$table_prefix."_salesorder:quoteid:quote_id:SalesOrder_Quote_Name:V' where columnname='".$table_prefix."_salesorder:quoteid:quote_id:SalesOrder_Quote_Name:I'");
ExecuteQuery("update ".$table_prefix."_cvadvfilter set columnname='".$table_prefix."_salesorder:quoteid:quote_id:SalesOrder_Quote_Name:V' where columnname='".$table_prefix."_salesorder:quoteid:quote_id:SalesOrder_Quote_Name:I'");


//Products Related To field deleted from ".$table_prefix."_selectcolumn
ExecuteQuery("delete from ".$table_prefix."_selectcolumn where columnname ='".$table_prefix."_seproductsrel:crmid:Products_Related_To:parent_id:I'");

//cvstdfilter table is in MyISAM engine so that the order of diplay will not be same in all time
$adb->query("alter table ".$table_prefix."_cvstdfilter engine=InnoDB");

//we have to set set permission as 3 in ".$table_prefix."_def_org_share as the Calendar is private
ExecuteQuery("update ".$table_prefix."_def_org_share set permission=3 where tabid=16");


//Query added to delete custom field entries available only in field table and not in customfield tables
$customtables_array = Array('".$table_prefix."_accountscf','".$table_prefix."_campaignscf','".$table_prefix."_contactscf','".$table_prefix."_invoicecf','".$table_prefix."_leadscf','".$table_prefix."_potentialscf','".$table_prefix."_pricebookcf','".$table_prefix."_productcf','".$table_prefix."_purchaseordercf','".$table_prefix."_quotescf','".$table_prefix."_salesordercf','".$table_prefix."_ticketcf','".$table_prefix."_vendorcf');

foreach($customtables_array as $customfieldtable)
{

	$sql = "select fieldname,tablename,columnname,fieldid from ".$table_prefix."_field where tablename='$customfieldtable'";
	$result = $adb->query($sql);

	$columns=$adb->getColumnNames($customfieldtable);
	for($i=0;$i < $adb->num_rows($result); $i++)
	{
		$columnname = $adb->query_result($result,$i,'columnname');
		if(!in_array($columnname,$columns))
		{
			$fieldID = $adb->query_result($result,$i,'fieldid');
			$query = "delete from ".$table_prefix."_field where fieldid=".$fieldID;
			$result1 = $adb->query($query);
		}
	}
}


$tables_array = Array(
			$table_prefix."_cvcolumnlist"=>"columnname",
			$table_prefix."_cvstdfilter"=>"columnname",
			$table_prefix."_cvadvfilter"=>"columnname",
			$table_prefix."_selectcolumn"=>"columnname",
			$table_prefix."_relcriteria"=>"columnname",
			$table_prefix."_reportsortcol"=>"columnname",
			$table_prefix."_reportdatefilter"=>"datecolumnname",
			$table_prefix."_reportsummary"=>"columnname",
		     );


foreach($tables_array as $tablename => $columnname)
{
	$query = "select $columnname from $tablename where $columnname like '%:cf_%'";
	$result = $adb->query($query);
	$noofrows = $adb->num_rows($result);

	for($i=0;$i<$noofrows;$i++)
	{
		//First get the fieldname from the result
		$col_value = $adb->query_result($result,$i,$columnname);
		$fieldname = substr($col_value,strpos($col_value,':cf_')+1,6);

		//Now check whether this field is available in field table
		$sql1 = "select fieldid from {$table_prefix}_field where fieldname='".$fieldname."'";
		$result1 = $adb->query($sql1);
		$noofrows1 = $adb->num_rows($result1);
		$fieldid = $adb->query_result($result1,0,"fieldid");

		//if there is no field then we have to delete that field entries
		if($noofrows1 == 0 && !isset($fieldid))
		{
			//Now we have to delete that customfield from the $tablename
			$adb->query("delete from $tablename where $columnname like '%:".$fieldname.":%'");
		}
	}
}







$migrationlog->debug("\n\nDB Changes from 5.0.3RC2 to 5.0.3 -------- Ends \n\n");



?>
