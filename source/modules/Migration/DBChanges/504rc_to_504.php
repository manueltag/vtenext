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

global $table_prefix;
//5.0.4 RC to 5.0.4 database changes

//we have to use the current object (stored in PatchApply.php) to execute the queries
$adb = $_SESSION['adodb_current_object'];
$conn = $_SESSION['adodb_current_object'];

$migrationlog->debug("\n\nDB Changes from 5.0.4rc to 5.0.4 -------- Starts \n\n");

//Increased the size of salution field for Leads module
ExecuteQuery("alter table ".$table_prefix."_leaddetails modify column salutation varchar(50)");

//Added to handle the crypt_type in users table. From 5.0.4 onwards the default crypt type will be MD5. But for existing users crypt type will be empty untill they change their password. Once the existing users change the password then their crypt type will be set as MD5
ExecuteQuery("alter table ".$table_prefix."_users add column crypt_type varchar(20) not null default 'MD5'");
ExecuteQuery("update ".$table_prefix."_users set crypt_type=''");

//In 503 to 504rc release we have included the role based picklist migration but the sequence tables for corresponding picklists are not handled. Now we are handling the sequence tables
//Popullating arry with picklist field names
$picklist_arr = array('leadsource'=>'leadsourceid','accounttype'=>'accounttypeid','industry'=>'industryid','leadstatus'=>'leadstatusid','rating'=>'rating_id','opportunity_type'=>'opptypeid','salutationtype'=>'salutationid','sales_stage'=>'sales_stage_id','ticketstatus'=>'ticketstatus_id','ticketpriorities'=>'ticketpriorities_id','ticketseverities'=>'ticketseverities_id','ticketcategories'=>'ticketcategories_id','eventstatus'=>'eventstatusid','taskstatus'=>'taskstatusid','taskpriority'=>'taskpriorityid','manufacturer'=>'manufacturerid','productcategory'=>'productcategoryid','faqcategories'=>'faqcategories_id','usageunit'=>'usageunitid','glacct'=>'glacctid','quotestage'=>'quotestageid','carrier'=>'carrierid','faqstatus'=>'faqstatus_id','invoicestatus'=>'invoicestatusid','postatus'=>'postatusid','sostatus'=>'sostatusid','campaigntype'=>'campaigntypeid','campaignstatus'=>'campaignstatusid','expectedresponse'=>'expectedresponseid');

$custom_result = $adb->query("select fieldname from ".$table_prefix."_field where (uitype=15 or uitype=33) and fieldname like '%cf_%'");
$numrow = $adb->num_rows($custom_result);
for($i=0; $i < $numrow; $i++)
{
	$fieldname=$adb->query_result($custom_result,$i,'fieldname');
	$picklist_arr[$fieldname] = $adb->query_result($custom_result,$i,'fieldname')."id";
}

foreach($picklist_arr as $picklistname => $picklistidname)
{
	$result = $adb->query("select max(".$picklistidname.") as id from ".$table_prefix."_".$picklistname);
	$max_count = 1;
	if ($adb->num_rows($result) > 0) {
		$max_count = $adb->query_result($result,0,'id');
		if ($max_count <= 0) $max_count = 1;
	}
	$adb->query("drop table if exists ".$table_prefix."_".$picklistname."_seq");
	$adb->query("create table ".$table_prefix."_".$picklistname."_seq (id integer(11))");
	$adb->query("insert into ".$table_prefix."_".$picklistname."_seq (id) values(".$max_count.")");

	//In 5.0.3 to 5.0.4 RC migration, for some utf8 character picklist values, picklist_valueid is set as 0 because of query instead of pquery
	$result = $adb->query("select * from ".$table_prefix."_$picklistname where picklist_valueid=0");
	$numrow = $adb->num_rows($result);
	for($i=0; $i < $numrow; $i++)
	{
		$picklist_array_values[$picklistname][] = decode_html($adb->query_result($result,$i,$picklistname));
	}

	//we have retrieved the picklist values to which the picklist_valueid is 0. So we can delete those entries
	$adb->query("delete from ".$table_prefix."_$picklistname where picklist_valueid=0");

	$temp_array = $picklist_array_values[$picklistname];
	if(is_array($temp_array))
	foreach($temp_array as $ind => $picklist_value)
	{
		$picklist_autoincrementid = $adb->getUniqueID($picklistname);//auto increment for each picklist table
		$picklist_valueid = getUniquePicklistID();//unique value id for each picklist value

		$picklistquery = "insert into ".$table_prefix."_$picklistname values(?,?,?,?) ";
		$adb->pquery($picklistquery, array($picklist_autoincrementid, $picklist_value, 1, $picklist_valueid));

		//get the picklist's unique id from vtiger_picklist table
		$res = $adb->query("select * from ".$table_prefix."_picklist where name='$picklistname'");
		$picklistid = $adb->query_result($res, 0, 'picklistid');

		//we have to insert the picklist value in vtiger_role2picklist table for each available roles
		$sql="select roleid from ".$table_prefix."_role";
		$role_result = $adb->query($sql);
		$numrows = $adb->num_rows($role_result);

		for($k=0; $k < $numrows; $k++)
		{
			$roleid = $adb->query_result($role_result,$k,'roleid');

			//get the max sortid for each picklist
			$res = $adb->query("select max(sortid)+1 sortid from ".$table_prefix."_role2picklist where roleid = '$roleid' and picklistid ='$picklistid'");
			$sortid = $adb->query_result($res, 0, 'sortid');

			$query = "insert into ".$table_prefix."_role2picklist values(?,?,?,?)";
			$adb->pquery($query, array($roleid, $picklist_valueid, $picklistid, $sortid));
		}
	}
}

//When we change the ticket description from troubletickets table to crmentity table we have handled in customview but missed in reports - #4968
ExecuteQuery("update ".$table_prefix."_selectcolumn set columnname='".$table_prefix."_crmentity:description:HelpDesk_Description:description:V' where columnname='".$table_prefix."_troubletickets:description:HelpDesk_Description:description:V'");
ExecuteQuery("update ".$table_prefix."_relcriteria set columnname='".$table_prefix."_crmentityHelpDesk:description:HelpDesk_Description:description:V' where columnname='".$table_prefix."_troubletickets:description:HelpDesk_Description:description:V'");
ExecuteQuery("update ".$table_prefix."_reportsortcol set columnname='".$table_prefix."_crmentityHelpDesk:description:HelpDesk_Description:description:V' where columnname='".$table_prefix."_troubletickets:description:HelpDesk_Description:description:V'");

//Some fields in customview related tables are changed in latest version but not handled in migration
//Array format is -- oldvalue => newvalue - old values will be updated with new values
//customview related tables to be changed - cvcolumnlist, cvadvfilter
$cv_fields_list = Array(
			//campaigns module
			$table_prefix."_campaign:product_id:product_id:Campaigns_Product:V"=>$table_prefix."_products:productname:productname:Campaigns_Product:V",
			$table_prefix."_campaign:targetsize:targetsize:Campaigns_TargetSize:N"=>$table_prefix."_campaign:targetsize:targetsize:Campaigns_TargetSize:I",
			$table_prefix."_campaign:budgetcost:budgetcost:Campaigns_Budget_Cost:I"=>$table_prefix."_campaign:budgetcost:budgetcost:Campaigns_Budget_Cost:N",
			$table_prefix."_campaign:actualcost:actualcost:Campaigns_Actual_Cost:I"=>$table_prefix."_campaign:actualcost:actualcost:Campaigns_Actual_Cost:N",
			$table_prefix."_campaign:expectedrevenue:expectedrevenue:Campaigns_Expected_Revenue:I"=>$table_prefix."_campaign:expectedrevenue:expectedrevenue:Campaigns_Expected_Revenue:N",
			$table_prefix."_campaign:expectedsalescount:expectedsalescount:Campaigns_Expected_Sales_Count:N"=>$table_prefix."_campaign:expectedsalescount:expectedsalescount:Campaigns_Expected_Sales_Count:I",
			$table_prefix."_campaign:actualsalescount:actualsalescount:Campaigns_Actual_Sales_Count:N"=>$table_prefix."_campaign:actualsalescount:actualsalescount:Campaigns_Actual_Sales_Count:I",
			//calendar module
			$table_prefix."_recurringevents:recurringtype:recurringtype:Calendar_Recurrence:V"=>$table_prefix."_activity:recurringtype:recurringtype:Calendar_Recurrence:O",
			$table_prefix."_activity:time_start::Calendar_Start_Time:V"=>$table_prefix."_activity:time_start::Calendar_Start_Time:I",
			$table_prefix."_activity:time_end:time_end:Calendar_End_Time:V"=>$table_prefix."_activity:time_end:time_end:Calendar_End_Time:T",
			"activity:date_start:date_start:Activities_Start_Date_&_Time:DT"=>$table_prefix."_activity:date_start:date_start:Calendar_Start_Date_&_Time:DT",
			//Calendar Module
			$table_prefix."_activity:activitytype:activitytype:Calendar_Activity_Type:C"=>$table_prefix."_activity:activitytype:activitytype:Calendar_Activity_Type:V",
			//Campaign Module
			$table_prefix."_campaign:product_id:product_id:Campaigns_Product:I"=>$table_prefix."_products:productname:productname:Campaigns_Product:V",
			$table_prefix."_campaign:expectedresponsecount:expectedresponsecount:Campaigns_Expected_Response_Count:N"=>$table_prefix."_campaign:expectedresponsecount:expectedresponsecount:Campaigns_Expected_Response_Count:I",
			$table_prefix."_campaign:actualresponsecount:actualresponsecount:Campaigns_Actual_Response_Count:N"=>$table_prefix."_campaign:actualresponsecount:actualresponsecount:Campaigns_Actual_Response_Count:I",
			//Contacts Module
			$table_prefix."_contactsubdetails:birthday:birthday:Contacts_Birthdate:V"=>$table_prefix."_contactsubdetails:birthday:birthday:Contacts_Birthdate:D",
			//Leads Module
			$table_prefix."_leaddetails:noofemployees:noofemployees:Leads_No_Of_Employees:V"=>$table_prefix."_leaddetails:noofemployees:noofemployees:Leads_No_Of_Employees:I",
			//Potentials Module
			$table_prefix."_potential:campaignid:campaignid:Potentials_Campaign_Source:N"=>$table_prefix."_potential:campaignid:campaignid:Potentials_Campaign_Source:V",
			//FAQ Module
			$table_prefix."_faq:product_id:product_id:Faq_Product_Name:I"=>$table_prefix."_faq:product_id:product_id:Faq_Product_Name:V",
			//Products Module
			$table_prefix."_products:qtyinstock:qtyinstock:Products_Qty_In_Stock:I"=>$table_prefix."_products:qtyinstock:qtyinstock:Products_Qty_In_Stock:NN",
			$table_prefix."_products:handler:assigned_user_id:Products_Handler:I"=>$table_prefix."_products:handler:assigned_user_id:Products_Handler:V",
			//Vendors Module
			$table_prefix."_vendor:email:email:Vendors_Email:E"=>$table_prefix."_vendor:email:email:Vendors_Email:V",
			//Price Books Module
			$table_prefix."_pricebook:active:active:PriceBooks_Active:V"=>$table_prefix."_pricebook:active:active:PriceBooks_Active:C",
			//Quotes Module
			$table_prefix."_quotes:potentialid:potential_id:Quotes_Potential_Name:I"=>$table_prefix."_quotes:potentialid:potential_id:Quotes_Potential_Name:V",
			$table_prefix."_quotes:inventorymanager:assigned_user_id1:Quotes_Inventory_Manager:I"=>$table_prefix."_quotes:inventorymanager:assigned_user_id1:Quotes_Inventory_Manager:V",
		  );

foreach($cv_fields_list as $oldval => $newval)
{
	ExecuteQuery("update ".$table_prefix."_cvcolumnlist set columnname='$newval' where columnname = '$oldval'");
	ExecuteQuery("update ".$table_prefix."_cvadvfilter set columnname='$newval' where columnname = '$oldval'");
}

//Some fields in report related tables are changed in latest version but not handled in migration
//Report related tables to be changed - selectcolumn, relcriteria, reportsortcol
//Array format is -- oldvalue => newvalue - old values will be updated with new values
$report_fields_list = Array(
			//Calendar module
			$table_prefix."_recurringevents:recurringtype:Calendar_Recurrence:recurringtype:O"=>$table_prefix."_activity:recurringtype:Calendar_Recurrence:recurringtype:O",
			//Campaign module
			$table_prefix."_campaign:targetsize:Campaigns_TargetSize:targetsize:N"=>$table_prefix."_campaign:targetsize:Campaigns_TargetSize:targetsize:I",
			$table_prefix."_campaign:budgetcost:Campaigns_Budget_Cost:budgetcost:I"=>$table_prefix."_campaign:budgetcost:Campaigns_Budget_Cost:budgetcost:N",
			$table_prefix."_campaign:actualcost:Campaigns_Actual_Cost:actualcost:I"=>$table_prefix."_campaign:actualcost:Campaigns_Actual_Cost:actualcost:N",
			$table_prefix."_campaign:expectedrevenue:Campaigns_Expected_Revenue:expectedrevenue:I"=>$table_prefix."_campaign:expectedrevenue:Campaigns_Expected_Revenue:expectedrevenue:N",
			$table_prefix."_campaign:expectedsalescount:Campaigns_Expected_Sales_Count:expectedsalescount:N"=>$table_prefix."_campaign:expectedsalescount:Campaigns_Expected_Sales_Count:expectedsalescount:I",
			$table_prefix."_campaign:actualsalescount:Campaigns_Actual_Sales_Count:actualsalescount:N"=>$table_prefix."_campaign:actualsalescount:Campaigns_Actual_Sales_Count:actualsalescount:I",
			$table_prefix."_campaign:expectedresponsecount:Campaigns_Expected_Response_Count:expectedresponsecount:N"=>$table_prefix."_campaign:expectedresponsecount:Campaigns_Expected_Response_Count:expectedresponsecount:I",
			$table_prefix."_campaign:actualresponsecount:Campaigns_Actual_Response_Count:actualresponsecount:N"=>$table_prefix."_campaign:actualresponsecount:Campaigns_Actual_Response_Count:actualresponsecount:I",
			$table_prefix."_crmentityRelCalendar:setype:Calendar_Related_To:parent_id:I"=>$table_prefix."_crmentityRelCalendar:setype:Calendar_Related_To:parent_id:V",
			$table_prefix."_contactdetailsCalendar:lastname:Calendar_Contact_Name:contact_id:I"=>$table_prefix."_contactdetailsCalendar:lastname:Calendar_Contact_Name:contact_id:V",
			//Calendar Module
			"activity:date_start:Activities_Start_Date_&_Time:date_start:DT"=>$table_prefix."_activity:date_start:Calendar_Start_Date_&_Time:date_start:DT",
			$table_prefix."_activity:activitytype:Calendar_Activity_Type:activitytype:C"=>$table_prefix."_activity:activitytype:Calendar_Activity_Type:activitytype:V",
			//$table_prefix."_activity:status:Calendar_Status:taskstatus:V"=>$table_prefix."_activity:status:Calendar_Status:taskstatus:V",
			//Campaign Module
			$table_prefix."_campaign:product_id:Campaigns_Product:product_id:I"=>$table_prefix."_products:productname:Campaigns_Product:productname:V",
			$table_prefix."_campaign:expectedresponsecount:Campaigns_Expected_Response_Count:expectedresponsecount:N"=>$table_prefix."_campaign:expectedresponsecount:Campaigns_Expected_Response_Count:expectedresponsecount:I",
			$table_prefix."_campaign:actualresponsecount:Campaigns_Actual_Response_Count:actualresponsecount:N"=>$table_prefix."_campaign:actualresponsecount:Campaigns_Actual_Response_Count:actualresponsecount:I",
			//Contacts Module
			$table_prefix."_contactsubdetails:birthday:Contacts_Birthdate:birthday:V"=>$table_prefix."_contactsubdetails:birthday:Contacts_Birthdate:birthday:D",
			//Leads Module
			$table_prefix."_leaddetails:noofemployees:Leads_No_Of_Employees:noofemployees:V"=>$table_prefix."_leaddetails:noofemployees:Leads_No_Of_Employees:noofemployees:I",
			//Potentials Module
			$table_prefix."_potential:campaignid:Potentials_Campaign_Source:campaignid:N"=>$table_prefix."_potential:campaignid:Potentials_Campaign_Source:campaignid:V",
			//FAQ Module
			$table_prefix."_faq:product_id:Faq_Product_Name:product_id:I"=>$table_prefix."_faq:product_id:Faq_Product_Name:product_id:V",
			//Products Module
			$table_prefix."_products:qtyinstock:Products_Qty_In_Stock:qtyinstock:I"=>$table_prefix."_products:qtyinstock:Products_Qty_In_Stock:qtyinstock:NN",
			$table_prefix."_products:handler:Products_Handler:assigned_user_id:I"=>$table_prefix."_products:handler:Products_Handler:assigned_user_id:V",
			//Quotes Module
			$table_prefix."_quotes:potentialid:Quotes_Potential_Name:potential_id:I"=>$table_prefix."_quotes:potentialid:Quotes_Potential_Name:potential_id:V",
			$table_prefix."_quotes:inventorymanager:Quotes_Inventory_Manager:assigned_user_id1:I"=>$table_prefix."_quotes:inventorymanager:Quotes_Inventory_Manager:assigned_user_id1:V",
			   );

foreach($report_fields_list as $oldval => $newval)
{
	ExecuteQuery("update ".$table_prefix."_selectcolumn set columnname='$newval' where columnname='$oldval'");
	ExecuteQuery("update ".$table_prefix."_relcriteria set columnname='$newval' where columnname='$oldval'");
	ExecuteQuery("update ".$table_prefix."_reportsortcol set columnname='$newval' where columnname='$oldval'");
}


//we have removed the Team field in quotes and added a new custom field for Team. So we can remove that field from reports (we have changed this field name in customview related tables in 503 - 504rc migration)
ExecuteQuery("delete from ".$table_prefix."_selectcolumn where columnname='".$table_prefix."_quotes:team:Quotes_Team:team:V'");
ExecuteQuery("delete from ".$table_prefix."_relcriteria where columnname='".$table_prefix."_quotes:team:Quotes_Team:team:V'");
ExecuteQuery("delete from ".$table_prefix."_reportsortcol where columnname='".$table_prefix."_quotes:team:Quotes_Team:team:V'");

//Update the webmail password with encryption
update_webmail_password();
function update_webmail_password()
{
	global $adb,$migrationlog,$table_prefix;
	$migrationlog->debug("\nInside update_webmail_password() function starts\n\n");
	require_once("modules/Users/Users.php");
	$res_set = $adb->query('select * from '.$table_prefix.'_mail_accounts');
	$user_obj = new Users();
	while($row = $adb->fetchByAssoc($res_set))
	{
		$adb->query("update ".$table_prefix."_mail_accounts set mail_password = '".$user_obj->changepassword($row['mail_password'])."' where mail_username='".$row['mail_username']."'");
	}
	$migrationlog->debug("\nInside update_webmail_password() function ends\n");
}

//Modified to increase the length of the outgoinfg server(smtp) servername, username and password
ExecuteQuery("alter table ".$table_prefix."_systems change  column server_username server_username varchar(100)");
ExecuteQuery("alter table ".$table_prefix."_systems change  column server server varchar(100)");
ExecuteQuery("alter table ".$table_prefix."_systems change  column server_password server_password varchar(100)");

//In our whole product, the picklist table columns and the corresponding picklists storage column in entity tables are changed to varchar(200)
$picklist_query_array = Array(
				"alter table ".$table_prefix."_account modify account_type varchar(200) default NULL",
				"alter table ".$table_prefix."_activity modify activitytype varchar(200) default NULL",
				"alter table ".$table_prefix."_users modify activity_view varchar(200) default NULL",
				"alter table ".$table_prefix."_campaign modify campaignstatus varchar(200) default NULL",
				"alter table ".$table_prefix."_campaign modify campaigntype varchar(200) default NULL",
				"alter table ".$table_prefix."_quotes modify carrier varchar(200) default NULL",
				"alter table ".$table_prefix."_purchaseorder modify carrier varchar(200) default NULL",
				"alter table ".$table_prefix."_salesorder modify carrier varchar(200) default NULL",
				"alter table ".$table_prefix."_users modify date_format varchar(200) default NULL",
				"alter table ".$table_prefix."_activity modify duration_minutes varchar(200) default NULL",
				"alter table ".$table_prefix."_activity drop key activity_status_eventstatus_idx, add key activity_status_idx(status)",
				"alter table ".$table_prefix."_activity modify eventstatus varchar(200) default NULL",
				"alter table ".$table_prefix."_campaign modify expectedresponse varchar(200) default NULL",
				"alter table ".$table_prefix."_faqcategories modify faqcategories varchar(200) default NULL",
				"alter table ".$table_prefix."_faq modify category varchar(200) default NULL",
				"alter table ".$table_prefix."_faqstatus modify faqstatus varchar(200) default NULL",
				"alter table ".$table_prefix."_faq modify status varchar(200) default NULL",
				"alter table ".$table_prefix."_vendor modify glacct varchar(200) default NULL",
				"alter table ".$table_prefix."_account modify industry varchar(200) default NULL",
				"alter table ".$table_prefix."_leaddetails modify industry varchar(200) default NULL",
				"alter table ".$table_prefix."_leaddetails modify leadsource varchar(200) default NULL",
				"alter table ".$table_prefix."_contactsubdetails modify leadsource varchar(200) default NULL",
				"alter table ".$table_prefix."_potential modify leadsource varchar(200) default NULL",
				"alter table ".$table_prefix."_users modify lead_view varchar(200) default NULL",
				"alter table ".$table_prefix."_products modify manufacturer varchar(200) default NULL",
				"alter table ".$table_prefix."_potential modify potentialtype varchar(200) default NULL",
				"alter table ".$table_prefix."_products modify productcategory varchar(200) default NULL",
				"alter table ".$table_prefix."_account modify rating varchar(200) default NULL",
				"alter table ".$table_prefix."_leaddetails modify rating varchar(200) default NULL",
				"alter table ".$table_prefix."_activity modify recurringtype varchar(200) default NULL",
				"alter table ".$table_prefix."_potential modify sales_stage varchar(200) default NULL",
				"alter table ".$table_prefix."_leaddetails modify salutation varchar(200) default NULL",
				"alter table ".$table_prefix."_contactdetails modify salutation varchar(200) default NULL",
				"alter table ".$table_prefix."_taskpriority modify taskpriority varchar(200) default NULL",
				"alter table ".$table_prefix."_activity modify priority varchar(200) default NULL",
				"alter table ".$table_prefix."_taskstatus modify taskstatus varchar(200) default NULL",
				"alter table ".$table_prefix."_activity modify status varchar(200) default NULL",
				"alter table ".$table_prefix."_ticketcategories modify ticketcategories varchar(200) default NULL",
				"alter table ".$table_prefix."_troubletickets modify category varchar(200) default NULL",
				"alter table ".$table_prefix."_ticketpriorities modify ticketpriorities varchar(200) default NULL",
				"alter table ".$table_prefix."_troubletickets modify priority varchar(200) default NULL",
				"alter table ".$table_prefix."_ticketseverities modify ticketseverities varchar(200) default NULL",
				"alter table ".$table_prefix."_troubletickets modify severity varchar(200) default NULL",
				"alter table ".$table_prefix."_ticketstatus modify ticketstatus varchar(200) default NULL",
				"alter table ".$table_prefix."_troubletickets modify status varchar(200) default NULL",
			     );
foreach($picklist_query_array as $query)
{
	ExecuteQuery($query);
}

// Modified to change the comparison datatype from Integer to Varchar for Account name
ExecuteQuery("update ".$table_prefix."_relcriteria set columnname='".$table_prefix."_accountContacts:accountname:Contacts_Account_Name:account_id:V' where columnname='".$table_prefix."_accountContacts:accountname:Contacts_Account_Name:account_id:I'");
ExecuteQuery("update ".$table_prefix."_selectcolumn set columnname='".$table_prefix."_accountContacts:accountname:Contacts_Account_Name:account_id:V' where columnname='".$table_prefix."_accountContacts:accountname:Contacts_Account_Name:account_id:I'");

// Modified to change the typeofdata for hour_format, start_hour and end_hour to 'V~O' instead of 'I~O'
ExecuteQuery("update ".$table_prefix."_field set typeofdata = 'V~O' where tablename='".$table_prefix."_users' and fieldname in ('hour_format','start_hour','end_hour')");

//Since we don't have field level access for Users and RSS modules we have to delete if there is any entry for these modules in vtiger_profile2field table
$adb->query("delete from ".$table_prefix."_profile2field where tabid=29");
$adb->query("delete from ".$table_prefix."_profile2field where tabid=24");

// Modified  the typeofdata for all module email field & custom email field in Custom View & Reports.
typeOfDataChanges();
function typeOfDataChanges()
{
    global $adb,$migrationlog,$table_prefix;
    $migrationlog->debug("\nInside typeOfDataChanges() function Starts\n\n");
    
    $field_table_sql="select columnname,fieldname from ".$table_prefix."_field where uitype=13";
    $result=$adb->query($field_table_sql);        
    $num_rows = $adb->num_rows($result);
    for($k=0; $k < $num_rows; $k++)
    {
	$columnname=$adb->query_result($result,$k,'columnname');
	$fieldname=$adb->query_result($result,$k,'fieldname');
	$tablename_array = array($table_prefix.'_cvcolumnlist',$table_prefix.'_cvadvfilter',$table_prefix.'_selectcolumn',$table_prefix.'_relcriteria',$table_prefix.'_reportsortcol');
	foreach($tablename_array as $tablename)
	{
	    $custom_sql="select columnname from  ".$tablename."  where columnname like '%cf%' or columnname like '%email%'";
	    $custom_result = $adb->query($custom_sql);
	    $num_rows2 = $adb->num_rows($custom_result);
 	    for($l=0; $l < $num_rows2; $l++)
 	    {	
		$table_columnname=$adb->query_result($custom_result,$l,'columnname');
		$values = explode(':',$table_columnname);
		if($columnname == $values[1] && $fieldname == $values[2])
		{
			ExecuteQuery("update ".$tablename." set columnname='".$values[0].":".$values[1].":".$values[2].":".$values[3].":E'   where columnname='".$values[0].":".$values[1].":".$values[2].":".$values[3].":V'");
		}
		if($columnname == $values[1] && $fieldname == $values[3])
		{
			ExecuteQuery("update ".$tablename." set columnname='".$values[0].":".$values[1].":".$values[2].":".$values[3].":E'   where columnname='".$values[0].":".$values[1].":".$values[2].":".$values[3].":V'");
		}
	    }
	}
    }
    $migrationlog->debug("\nInside typeOfDataChanges() function Ends\n\n");
}
//Added to remove the unwanted \n characters from inventory notification schedulers
$result=$adb->query("select notificationid,notificationbody from ".$table_prefix."_inventorynotification");

for($i=0;$i<$adb->num_rows($result);$i++)
{
	$body=decode_html($adb->query_result($result,$i,'notificationbody'));
	$body=str_replace('\n','', $body);
	$notificationid=$adb->query_result($result,$i,'notificationid');
	$adb->pquery("update ".$table_prefix."_inventorynotification set notificationbody=? where notificationid=?", array($body, $notificationid));
}
//In 5.0.4, support start and end date notification scheduler should be defaultly active. If it is inactive previouly then we have to change them as active
ExecuteQuery("update ".$table_prefix."_notificationscheduler set active=1 where schedulednotificationid in (5,6)");

//Query added to modify the date_format field length in vtiger_users table
ExecuteQuery("alter table ".$table_prefix."_users modify date_format varchar(200) default NULL");
// Updated the sequence number of taskstatus for the ticket #5027
ExecuteQuery("update ".$table_prefix."_field set sequence = 8 where columnname = 'status' and tablename = '{$table_prefix}_activity' and fieldname = 'taskstatus' and uitype = 111");

$arr=$adb->getColumnNames($table_prefix."_users");
if(!in_array("internal_mailer", $arr))
{
	$adb->pquery("alter table ".$table_prefix."_users add column internal_mailer int(3) NOT NULL default '1'", array());
}

global $dbname;
include("modules/Migration/HTMLtoUTF8Conversion.php");

$migrationlog->debug("\n\nDB Changes from 5.0.4rc to 5.0.4 -------- Ends \n\n");

?>