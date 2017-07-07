<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

function dashBoardDisplayChart()
{

require_once("modules/Dashboard/Entity_charts.php");
global $tmp_dir,$adb,$table_prefix;
global $mod_strings,$app_strings;
global $current_user;
require('user_privileges/user_privileges_'.$current_user->id.'.php');
global $current_language;
$mod_strings = return_module_language($current_language, 'Dashboard');


$period=($_REQUEST['period'])?$_REQUEST['period']:"tmon"; // Period >> lmon- Last Month, tmon- This Month, lweek-LastWeek, tweek-ThisWeek; lday- Last Day 
$type=($_REQUEST['type'])?$_REQUEST['type']:"leadsource";
$dates_values=start_end_dates($period); //To get the stating and End dates for a given period 
$date_start=$dates_values[0]; //Starting date 
$end_date=$dates_values[1]; // Ending Date
$period_type=$dates_values[2]; //Period type as MONTH,WEEK,LDAY
$width=$dates_values[3];
$height=$dates_values[4];

//It gives all the dates in between the starting and ending dates and also gives the number of days,declared in utils.php
$no_days_dates=get_days_n_dates($date_start,$end_date);
$days=$no_days_dates[0];
$date_array=$no_days_dates[1]; //Array containig all the dates 
$user_id=$current_user->id;

//crmv@sdk-28873
$sdk_dash = SDK::getDashboard($type);
if (!empty($sdk_dash) ) {
	$module = $_REQUEST['module'];
	$home_chart_type = $_REQUEST['Chart_Type'];
	require($sdk_dash['file']);
	return;
}
//crmv@sdk-28873e

// Query for Leads
$leads_query="select ".$table_prefix."_crmentity.crmid,".$table_prefix."_crmentity.createdtime, ".$table_prefix."_leaddetails.*, ".$table_prefix."_crmentity.smownerid, ".$table_prefix."_leadscf.* from ".$table_prefix."_leaddetails inner join ".$table_prefix."_crmentity on ".$table_prefix."_crmentity.crmid=".$table_prefix."_leaddetails.leadid inner join ".$table_prefix."_leadsubdetails on ".$table_prefix."_leadsubdetails.leadsubscriptionid=".$table_prefix."_leaddetails.leadid inner join ".$table_prefix."_leadaddress on ".$table_prefix."_leadaddress.leadaddressid=".$table_prefix."_leadsubdetails.leadsubscriptionid inner join ".$table_prefix."_leadscf on ".$table_prefix."_leaddetails.leadid = ".$table_prefix."_leadscf.leadid left join ".$table_prefix."_groups on ".$table_prefix."_groups.groupid=".$table_prefix."_crmentity.smownerid where ".$table_prefix."_crmentity.deleted=0 and ".$table_prefix."_leaddetails.converted=0 ";


//Query for Accounts
$account_query="select ".$table_prefix."_crmentity.*, ".$table_prefix."_account.*, ".$table_prefix."_accountscf.* from ".$table_prefix."_account inner join ".$table_prefix."_crmentity on ".$table_prefix."_crmentity.crmid=".$table_prefix."_account.accountid inner join ".$table_prefix."_accountbillads on ".$table_prefix."_account.accountid=".$table_prefix."_accountbillads.accountaddressid inner join ".$table_prefix."_accountshipads on ".$table_prefix."_account.accountid=".$table_prefix."_accountshipads.accountaddressid inner join ".$table_prefix."_accountscf on ".$table_prefix."_account.accountid = ".$table_prefix."_accountscf.accountid left join ".$table_prefix."_groups on ".$table_prefix."_groups.groupid=".$table_prefix."_crmentity.smownerid left join ".$table_prefix."_users on ".$table_prefix."_users.id=".$table_prefix."_crmentity.smownerid where ".$table_prefix."_crmentity.deleted=0 ";

//Query for Products by PO
$probyPO = "select ".$table_prefix."_purchaseorder.*,".$table_prefix."_crmentity.* from ".$table_prefix."_purchaseorder inner join ".$table_prefix."_crmentity on ".$table_prefix."_crmentity.crmid=".$table_prefix."_purchaseorder.purchaseorderid inner join ".$table_prefix."_inventoryproductrel on ".$table_prefix."_purchaseorder.purchaseorderid = ".$table_prefix."_inventoryproductrel.id LEFT JOIN ".$table_prefix."_groups ON ".$table_prefix."_groups.groupid = ".$table_prefix."_crmentity.smownerid LEFT JOIN ".$table_prefix."_users ON ".$table_prefix."_users.id = ".$table_prefix."_crmentity.smownerid where ".$table_prefix."_inventoryproductrel.id=".$table_prefix."_purchaseorder.purchaseorderid and ".$table_prefix."_crmentity.deleted=0";

//Query for Products by Quotes
$probyQ = "select ".$table_prefix."_quotes.*,".$table_prefix."_crmentity.* from ".$table_prefix."_quotes inner join ".$table_prefix."_crmentity on ".$table_prefix."_crmentity.crmid=".$table_prefix."_quotes.quoteid inner join ".$table_prefix."_inventoryproductrel on ".$table_prefix."_quotes.quoteid = ".$table_prefix."_inventoryproductrel.id LEFT JOIN ".$table_prefix."_groups ON ".$table_prefix."_groups.groupid = ".$table_prefix."_crmentity.smownerid LEFT JOIN ".$table_prefix."_users ON ".$table_prefix."_users.id = ".$table_prefix."_crmentity.smownerid where ".$table_prefix."_inventoryproductrel.id=".$table_prefix."_quotes.quoteid and ".$table_prefix."_crmentity.deleted=0";

//Query for Products by Invoices
$probyInv = "select ".$table_prefix."_invoice.*,".$table_prefix."_crmentity.* from ".$table_prefix."_invoice inner join ".$table_prefix."_crmentity on ".$table_prefix."_crmentity.crmid=".$table_prefix."_invoice.invoiceid inner join ".$table_prefix."_inventoryproductrel on ".$table_prefix."_invoice.invoiceid = ".$table_prefix."_inventoryproductrel.id LEFT JOIN ".$table_prefix."_groups ON ".$table_prefix."_groups.groupid = ".$table_prefix."_crmentity.smownerid LEFT JOIN ".$table_prefix."_users ON ".$table_prefix."_users.id = ".$table_prefix."_crmentity.smownerid where ".$table_prefix."_inventoryproductrel.id=".$table_prefix."_invoice.invoiceid and ".$table_prefix."_crmentity.deleted=0";

//Query For Products qty in stock
$products_query="select distinct(".$table_prefix."_crmentity.crmid),".$table_prefix."_crmentity.createdtime,".$table_prefix."_products.* from ".$table_prefix."_products inner join ".$table_prefix."_crmentity on ".$table_prefix."_crmentity.crmid=".$table_prefix."_products.productid left join ".$table_prefix."_inventoryproductrel on ".$table_prefix."_products.productid = ".$table_prefix."_inventoryproductrel.id where ".$table_prefix."_crmentity.deleted=0 and ".$table_prefix."_products.qtyinstock > 0";

//Query for Potential
$potential_query= "select  ".$table_prefix."_crmentity.*,".$table_prefix."_account.accountname, ".$table_prefix."_potential.*, ".$table_prefix."_potentialscf.*, ".$table_prefix."_groups.groupname from ".$table_prefix."_potential inner join ".$table_prefix."_crmentity on ".$table_prefix."_crmentity.crmid=".$table_prefix."_potential.potentialid left join ".$table_prefix."_account on ".$table_prefix."_potential.related_to = ".$table_prefix."_account.accountid inner join ".$table_prefix."_potentialscf on ".$table_prefix."_potentialscf.potentialid = ".$table_prefix."_potential.potentialid left join ".$table_prefix."_groups on ".$table_prefix."_groups.groupid=".$table_prefix."_crmentity.smownerid left join ".$table_prefix."_users on ".$table_prefix."_users.id=".$table_prefix."_crmentity.smownerid where ".$table_prefix."_crmentity.deleted=0 ";

//Query for Sales Order
$so_query="select ".$table_prefix."_crmentity.*,".$table_prefix."_salesorder.*,".$table_prefix."_account.accountid,".$table_prefix."_quotes.quoteid from ".$table_prefix."_salesorder inner join ".$table_prefix."_crmentity on ".$table_prefix."_crmentity.crmid=".$table_prefix."_salesorder.salesorderid inner join ".$table_prefix."_sobillads on ".$table_prefix."_salesorder.salesorderid=".$table_prefix."_sobillads.sobilladdressid inner join ".$table_prefix."_soshipads on ".$table_prefix."_salesorder.salesorderid=".$table_prefix."_soshipads.soshipaddressid left join ".$table_prefix."_salesordercf on ".$table_prefix."_salesordercf.salesorderid = ".$table_prefix."_salesorder.salesorderid left outer join ".$table_prefix."_quotes on ".$table_prefix."_quotes.quoteid=".$table_prefix."_salesorder.quoteid left outer join ".$table_prefix."_account on ".$table_prefix."_account.accountid=".$table_prefix."_salesorder.accountid left join ".$table_prefix."_groups on ".$table_prefix."_groups.groupid=".$table_prefix."_crmentity.smownerid left join ".$table_prefix."_users on ".$table_prefix."_users.id=".$table_prefix."_crmentity.smownerid where ".$table_prefix."_crmentity.deleted=0 ";


//Query for Purchase Order

$po_query="select ".$table_prefix."_crmentity.*,".$table_prefix."_purchaseorder.* from ".$table_prefix."_purchaseorder inner join ".$table_prefix."_crmentity on ".$table_prefix."_crmentity.crmid=".$table_prefix."_purchaseorder.purchaseorderid left outer join ".$table_prefix."_vendor on ".$table_prefix."_purchaseorder.vendorid=".$table_prefix."_vendor.vendorid inner join ".$table_prefix."_pobillads on ".$table_prefix."_purchaseorder.purchaseorderid=".$table_prefix."_pobillads.pobilladdressid inner join ".$table_prefix."_poshipads on ".$table_prefix."_purchaseorder.purchaseorderid=".$table_prefix."_poshipads.poshipaddressid left join ".$table_prefix."_purchaseordercf on ".$table_prefix."_purchaseordercf.purchaseorderid = ".$table_prefix."_purchaseorder.purchaseorderid left join ".$table_prefix."_groups on ".$table_prefix."_groups.groupid=".$table_prefix."_crmentity.smownerid left join ".$table_prefix."_users on ".$table_prefix."_users.id=".$table_prefix."_crmentity.smownerid where ".$table_prefix."_crmentity.deleted=0 ";

// Query for Quotes
$quotes_query="select ".$table_prefix."_crmentity.*,".$table_prefix."_quotes.* from ".$table_prefix."_quotes inner join ".$table_prefix."_crmentity on ".$table_prefix."_crmentity.crmid=".$table_prefix."_quotes.quoteid inner join ".$table_prefix."_quotesbillads on ".$table_prefix."_quotes.quoteid=".$table_prefix."_quotesbillads.quotebilladdressid inner join ".$table_prefix."_quotesshipads on ".$table_prefix."_quotes.quoteid=".$table_prefix."_quotesshipads.quoteshipaddressid left join ".$table_prefix."_quotescf on ".$table_prefix."_quotes.quoteid = ".$table_prefix."_quotescf.quoteid left outer join ".$table_prefix."_account on ".$table_prefix."_account.accountid=".$table_prefix."_quotes.accountid left outer join ".$table_prefix."_potential on ".$table_prefix."_potential.potentialid=".$table_prefix."_quotes.potentialid left join ".$table_prefix."_groups on ".$table_prefix."_groups.groupid=".$table_prefix."_crmentity.smownerid left join ".$table_prefix."_users on ".$table_prefix."_users.id=".$table_prefix."_crmentity.smownerid where ".$table_prefix."_crmentity.deleted=0 ";

//Query for Invoice
$invoice_query="select ".$table_prefix."_crmentity.*,".$table_prefix."_invoice.* from ".$table_prefix."_invoice inner join ".$table_prefix."_crmentity on ".$table_prefix."_crmentity.crmid=".$table_prefix."_invoice.invoiceid inner join ".$table_prefix."_invoicebillads on ".$table_prefix."_invoice.invoiceid=".$table_prefix."_invoicebillads.invoicebilladdressid inner join ".$table_prefix."_invoiceshipads on ".$table_prefix."_invoice.invoiceid=".$table_prefix."_invoiceshipads.invoiceshipaddressid left outer join ".$table_prefix."_salesorder on ".$table_prefix."_salesorder.salesorderid=".$table_prefix."_invoice.salesorderid inner join ".$table_prefix."_invoicecf on ".$table_prefix."_invoice.invoiceid = ".$table_prefix."_invoicecf.invoiceid left join ".$table_prefix."_groups on ".$table_prefix."_groups.groupid=".$table_prefix."_crmentity.smownerid left join ".$table_prefix."_users on ".$table_prefix."_users.id=".$table_prefix."_crmentity.smownerid where ".$table_prefix."_crmentity.deleted=0 ";

//Query for tickets
$helpdesk_query=" select ".$table_prefix."_troubletickets.status ticketstatus, ".$table_prefix."_groups.groupname ticketgroupname, ".$table_prefix."_troubletickets.*,".$table_prefix."_crmentity.* from ".$table_prefix."_troubletickets inner join ".$table_prefix."_ticketcf on ".$table_prefix."_ticketcf.ticketid = ".$table_prefix."_troubletickets.ticketid inner join ".$table_prefix."_crmentity on ".$table_prefix."_crmentity.crmid=".$table_prefix."_troubletickets.ticketid left join ".$table_prefix."_groups on ".$table_prefix."_groups.groupid=".$table_prefix."_crmentity.smownerid left join ".$table_prefix."_contactdetails on ".$table_prefix."_troubletickets.parent_id=".$table_prefix."_contactdetails.contactid left join ".$table_prefix."_account on ".$table_prefix."_account.accountid=".$table_prefix."_troubletickets.parent_id left join ".$table_prefix."_users on ".$table_prefix."_crmentity.smownerid=".$table_prefix."_users.id and ".$table_prefix."_troubletickets.ticketid = ".$table_prefix."_ticketcf.ticketid where ".$table_prefix."_crmentity.deleted=0";


//Query for Contacts by Campaign
$contByCampaign = "select crmid from ".$table_prefix."_contactdetails inner join ".$table_prefix."_campaigncontrel on ".$table_prefix."_campaigncontrel.contactid = ".$table_prefix."_contactdetails.contactid inner join ".$table_prefix."_crmentity on ".$table_prefix."_crmentity.crmid = ".$table_prefix."_contactdetails.contactid left join ".$table_prefix."_groups on ".$table_prefix."_groups.groupid=".$table_prefix."_crmentity.smownerid left join ".$table_prefix."_users on ".$table_prefix."_crmentity.smownerid=".$table_prefix."_users.id where ".$table_prefix."_crmentity.deleted=0 ";
$cont_Q = getDashboardQuery($contByCampaign,"Contacts");

$result = $adb->pquery($cont_Q, array());
$num_conts = $adb->num_rows($result);
$cont_Array = array();
for($z=0;$z<$num_conts;$z++)
{
	$cont_ID=$adb->query_result($result,$z,'crmid');
	if(!in_array($cont_ID, $cont_Array))
		array_push($cont_Array, $cont_ID);
}
$cont_checkQ = " and ".$table_prefix."_crmentityContacts.crmid in(0)";
if(count($cont_Array) > 0)
	$cont_checkQ = " and ".$table_prefix."_crmentityContacts.crmid in(".implode(", ", $cont_Array).")";
	
$campaign_query="select ".$table_prefix."_campaign.*,".$table_prefix."_crmentity.* from ".$table_prefix."_campaign inner join ".$table_prefix."_crmentity on ".$table_prefix."_crmentity.crmid=".$table_prefix."_campaign.campaignid inner join ".$table_prefix."_campaigncontrel on ".$table_prefix."_campaigncontrel.campaignid=".$table_prefix."_campaign.campaignid left join ".$table_prefix."_groups on ".$table_prefix."_groups.groupid=".$table_prefix."_crmentity.smownerid left join ".$table_prefix."_users on ".$table_prefix."_crmentity.smownerid=".$table_prefix."_users.id left join ".$table_prefix."_contactdetails on ".$table_prefix."_contactdetails.contactid = ".$table_prefix."_campaigncontrel.contactid left join ".$table_prefix."_crmentity ".$table_prefix."_crmentityContacts on ".$table_prefix."_crmentityContacts.crmid = ".$table_prefix."_contactdetails.contactid where ".$table_prefix."_campaigncontrel.campaignid=".$table_prefix."_campaign.campaignid and ".$table_prefix."_crmentity.deleted=0 and ".$table_prefix."_crmentityContacts.deleted=0".$cont_checkQ;


//Query for tickets by account
$tickets_by_account="select ".$table_prefix."_troubletickets.*, ".$table_prefix."_groups.groupname ticketgroupname, ".$table_prefix."_crmentity.*, ".$table_prefix."_account.* from ".$table_prefix."_troubletickets inner join ".$table_prefix."_crmentity on ".$table_prefix."_crmentity.crmid=".$table_prefix."_troubletickets.ticketid inner join ".$table_prefix."_account on ".$table_prefix."_account.accountid=".$table_prefix."_troubletickets.parent_id left join ".$table_prefix."_groups on ".$table_prefix."_groups.groupid=".$table_prefix."_crmentity.smownerid left join ".$table_prefix."_users on ".$table_prefix."_crmentity.smownerid=".$table_prefix."_users.id where ".$table_prefix."_crmentity.deleted=0";
 
//Query for tickets by contact
$tickets_by_contact="select ".$table_prefix."_troubletickets.*, ".$table_prefix."_groups.groupname ticketgroupname, ".$table_prefix."_crmentity.*, ".$table_prefix."_contactdetails.* from ".$table_prefix."_troubletickets inner join ".$table_prefix."_crmentity on ".$table_prefix."_crmentity.crmid=".$table_prefix."_troubletickets.ticketid inner join ".$table_prefix."_contactdetails on ".$table_prefix."_contactdetails.contactid=".$table_prefix."_troubletickets.parent_id left join ".$table_prefix."_groups on ".$table_prefix."_groups.groupid=".$table_prefix."_crmentity.smownerid left join ".$table_prefix."_users on ".$table_prefix."_crmentity.smownerid=".$table_prefix."_users.id where ".$table_prefix."_crmentity.deleted=0";

//Query for product by category

$product_category = "select ".$table_prefix."_products.*,".$table_prefix."_crmentity.deleted from ".$table_prefix."_products inner join ".$table_prefix."_crmentity on ".$table_prefix."_crmentity.crmid = ".$table_prefix."_products.productid where ".$table_prefix."_crmentity.deleted=0";

$graph_array = Array(
	  "DashboardHome" => $mod_strings['DashboardHome'],
          "leadsource" => $mod_strings['leadsource'],
          "leadstatus" => $mod_strings['leadstatus'],
          "leadindustry" => $mod_strings['leadindustry'],
          "salesbyleadsource" => $mod_strings['salesbyleadsource'],
          "salesbyaccount" => $mod_strings['salesbyaccount'],
	  "salesbyuser" => $mod_strings['salesbyuser'],
	  "salesbyteam" => $mod_strings['salesbyteam'],
          "accountindustry" => $mod_strings['accountindustry'],
          "productcategory" => $mod_strings['productcategory'],
	  "productbyqtyinstock" => $mod_strings['productbyqtyinstock'],
	  "productbypo" => $mod_strings['productbypo'],
	  "productbyquotes" => $mod_strings['productbyquotes'],
	  "productbyinvoice" => $mod_strings['productbyinvoice'],
          "sobyaccounts" => $mod_strings['sobyaccounts'],
          "sobystatus" => $mod_strings['sobystatus'],
          "pobystatus" => $mod_strings['pobystatus'],
          "quotesbyaccounts" => $mod_strings['quotesbyaccounts'],
          "quotesbystage" => $mod_strings['quotesbystage'],
          "invoicebyacnts" => $mod_strings['invoicebyacnts'],
          "invoicebystatus" => $mod_strings['invoicebystatus'],
          "ticketsbystatus" => $mod_strings['ticketsbystatus'],
          "ticketsbypriority" => $mod_strings['ticketsbypriority'],
	  "ticketsbycategory" => $mod_strings['ticketsbycategory'], 
	  "ticketsbyuser" => $mod_strings['ticketsbyuser'],
	  "ticketsbyteam" => $mod_strings['ticketsbyteam'],
	  "ticketsbyproduct"=> $mod_strings['ticketsbyproduct'],
	  "contactbycampaign"=> $mod_strings['contactbycampaign'],
	  "ticketsbyaccount"=> $mod_strings['ticketsbyaccount'],
	  "ticketsbycontact"=> $mod_strings['ticketsbycontact'],
  );
	if(isset($_REQUEST['from_page']) && $_REQUEST['from_page'] == 'HomePage')
	{

		//Charts for Lead Source
                    if($profileTabsPermission[getTabid("Leads")] == 0 && ($type == "leadsource") && (getFieldVisibilityPermission('Leads',$user_id,'leadsource') == "0"))
                    {
                    	$graph_by="leadsource";
                    	$graph_title= $mod_strings['leadsource'];
                    	$module="Leads";
                    	$where="";
                    	$query=getDashboardQuery($leads_query,$module);
                    	return get_graph_by_type($graph_by,$graph_title,$module,$where,$query,"210","210","forhomepage");
                    
                    }
                    // To display the charts  for Lead status                   
                    elseif ($profileTabsPermission[getTabid("Leads")] == 0 && ($type == "leadstatus")&& (getFieldVisibilityPermission('Leads',$user_id,'leadstatus') == "0"))
                    {
                    	$graph_by="leadstatus";
                    	$graph_title= $mod_strings['leadstatus'];
                    	$module="Leads";
                    	$where="";
                    	$query=getDashboardQuery($leads_query,$module);
			if(!$is_admin)
				$query .= ' and ".$table_prefix."_leaddetails.leadsource '.picklist_check($module,$graph_by);
                    	return get_graph_by_type($graph_by,$graph_title,$module,$where,$query,"210","210","forhomepage");
                    }
                    //Charts for Lead Industry
                    elseif ($profileTabsPermission[getTabid("Leads")] == 0 && ($type == "leadindustry") && (getFieldVisibilityPermission('Leads',$user_id,'industry') == "0"))
                    {
                    	$graph_by="industry";
                            $graph_title=$mod_strings['leadindustry'];
                            $module="Leads";
                            $where="";
                            $query=getDashboardQuery($leads_query,$module);
                            return get_graph_by_type($graph_by,$graph_title,$module,$where,$query,"210","210","forhomepage");
                    }
                    //Sales by Lead Source
                    elseif ($profileTabsPermission[getTabid("Potentials")] == 0 && ($type == "salesbyleadsource")&& (getFieldVisibilityPermission('Potentials',$user_id,'leadsource') == "0"))
                    {
                            $graph_by="leadsource";
                            $graph_title=$mod_strings['salesbyleadsource'];
                            $module="Potentials";
                            $where=" and ".$table_prefix."_potential.sales_stage like '%Closed Won%' ";
                            $query=getDashboardQuery($potential_query,$module);
                            return get_graph_by_type($graph_by,$graph_title,$module,$where,$query,"210","210","forhomepage");
                    }
                    //Sales by Account
                    elseif ($profileTabsPermission[getTabid("Potentials")] == 0 && ($type == "salesbyaccount") && (getFieldVisibilityPermission('Potentials',$user_id,'related_to') == "0"))
                    {
                    	 $graph_by="related_to";
                         $graph_title=$mod_strings['salesbyaccount'];
                         $module="Potentials";
                         $where=" and ".$table_prefix."_potential.sales_stage like '%Closed Won%' ";
                         $query=getDashboardQuery($potential_query,$module);
                         return get_graph_by_type($graph_by,$graph_title,$module,$where,$query,"210","210","forhomepage");
                    }
		    //Sales by User
		    elseif ($profileTabsPermission[getTabid("Potentials")] == 0 && ($type == "salesbyuser"))
		    {
				$graph_by="smownerid";
				$graph_title=$mod_strings['salesbyuser'];
				$module="Potentials";
				$where=" and ".$table_prefix."_potential.sales_stage like '%Closed Won%' and (".$table_prefix."_groups.groupname is NULL)";
				$query=getDashboardQuery($potential_query,$module);
				return get_graph_by_type($graph_by,$graph_title,$module,$where,$query,"210","210","forhomepage");
		    }
		    //Sales by team
		    elseif ($profileTabsPermission[getTabid("Potentials")] == 0 && ($type == "salesbyteam"))
		    {
				$graph_by="smownerid";
				$graph_title=$mod_strings['salesbyteam'];
				$module="Potentials";
				$where=" and ".$table_prefix."_potential.sales_stage like '%Closed Won%' and (".$table_prefix."_groups.groupname != NULL || ".$table_prefix."_groups.groupname != '')";
				$query=getDashboardQuery($potential_query,$module);
				return get_graph_by_type($graph_by,$graph_title,$module,$where,$query,"210","210","forhomepage");
		    }
                    //Charts for Account by Industry
                    elseif ($profileTabsPermission[getTabid("Accounts")] == 0 && ($type == "accountindustry") && (getFieldVisibilityPermission('Accounts',$user_id,'industry') == "0"))
                    {
                    	$graph_by="industry";
                            $graph_title=$mod_strings['accountindustry'];
                            $module="Accounts";
                            $where="";
                            $query=getDashboardQuery($account_query,$module);
                            return get_graph_by_type($graph_by,$graph_title,$module,$where,$query,"210","210","forhomepage");
                    }
                    //Charts for Products by Category
                    elseif ($profileTabsPermission[getTabid("Products")] == 0 && ($type == "productcategory") && (getFieldVisibilityPermission('Products',$user_id,'productcategory') == "0"))
                    {
                    	$graph_by="productcategory";
                            $graph_title=$mod_strings['productcategory'];
                            $module="Products";
                            $where="";
                            $query=getDashboardQuery($product_category, $module);
                            return get_graph_by_type($graph_by,$graph_title,$module,$where,$query,"210","210","forhomepage");
                    }
		    //Charts for Products by Quantity in stock
		    elseif ($profileTabsPermission[getTabid("Products")] == 0 && ($type == "productbyqtyinstock") && (getFieldVisibilityPermission('Products',$user_id,'qtyinstock') == "0"))
		    {
			$graph_by="productname";
			    $graph_title=$mod_strings['productbyqtyinstock'];
			    $module="Products";
			    $where="";
			    $query=getDashboardQuery($products_query,$module);
			    return get_graph_by_type($graph_by,$graph_title,$module,$where,$query,"210","210","forhomepage");
		    }
		    //Charts for Products by PO
		    elseif ($profileTabsPermission[getTabid("Products")] == 0 && ($type == "productbypo") && $profileTabsPermission[getTabid("PurchaseOrder")] == 0)
		    { 
			    $graph_by="purchaseorderid";
			    $graph_title=$mod_strings['productbypo'];
			    $module="Products";
			    $where="";
			    $query=getDashboardQuery($probyPO,$module);
			    return get_graph_by_type($graph_by,$graph_title,$module,$where,$query,"210","210","forhomepage");
		    }
		    //Charts for Products by Quotes
		    elseif ($profileTabsPermission[getTabid("Products")] == 0 && ($type == "productbyquotes") && $profileTabsPermission[getTabid("Quotes")] == 0)
		    { 
                            $graph_by="quoteid";
   			    $graph_title=$mod_strings['productbyquotes'];
			    $module="Products";
			    $where=""; 
			    $query=getDashboardQuery($probyQ, $module);
			    return get_graph_by_type($graph_by,$graph_title,$module,$where,$query,"210","210","forhomepage");
		    }
		    //Charts for Products by Invoice
		    elseif ($profileTabsPermission[getTabid("Products")] == 0 && ($type == "productbyinvoice") && $profileTabsPermission[getTabid("Invoice")] == 0)
		    {
		            $graph_by="invoiceid";
			    $graph_title=$mod_strings['productbyinvoice'];
			    $module="Products";
			    $where="";
			    $query=getDashboardQuery($probyInv, $module);
			    return get_graph_by_type($graph_by,$graph_title,$module,$where,$query,"210","210","forhomepage");
		    }

                    // Sales Order by Accounts
                    elseif ($profileTabsPermission[getTabid("SalesOrder")] == 0 && ($type == "sobyaccounts") && (getFieldVisibilityPermission('SalesOrder',$user_id,'account_id') == "0"))
                    {
                    	$graph_by="accountid";
                            $graph_title=$mod_strings['sobyaccounts'];
                            $module="SalesOrder";
                            $where="";
                            $query=getDashboardQuery($so_query,$module);
                            return get_graph_by_type($graph_by,$graph_title,$module,$where,$query,"210","210","forhomepage");
                    }
                    //Sales Order by Status
                    elseif ($profileTabsPermission[getTabid("SalesOrder")] == 0 && ($type == "sobystatus") && (getFieldVisibilityPermission('SalesOrder',$user_id,'sostatus') == "0"))
                    {
                            $graph_by="sostatus";
                            $graph_title=$mod_strings['sobystatus'];
                            $module="SalesOrder";
                            $where="";
                            $query=getDashboardQuery($so_query,$module);
                            return get_graph_by_type($graph_by,$graph_title,$module,$where,$query,"210","210","forhomepage");
                    }
                    //Purchase Order by Status
                    elseif ($profileTabsPermission[getTabid("PurchaseOrder")] == 0 && ($type == "pobystatus") && (getFieldVisibilityPermission('PurchaseOrder',$user_id,'postatus') == "0"))
                    {
                            $graph_by="postatus";
                            $graph_title=$mod_strings['pobystatus'];
                            $module="PurchaseOrder";
                            $where="";
                            $query=getDashboardQuery($po_query,$module);
                            return get_graph_by_type($graph_by,$graph_title,$module,$where,$query,"210","210","forhomepage");
                    }
                    //Quotes by Accounts
                    elseif ($profileTabsPermission[getTabid("Quotes")] == 0 && ($type == "quotesbyaccounts") && (getFieldVisibilityPermission('Quotes',$user_id,'account_id') == "0"))
                    {
                            $graph_by="accountid";
                            $graph_title= $mod_strings['quotesbyaccounts'];
                            $module="Quotes";
                            $where="";
                            $query=getDashboardQuery($quotes_query,$module);
                            return get_graph_by_type($graph_by,$graph_title,$module,$where,$query,"210","210","forhomepage");
                    }
                    //Quotes by Stage
                    elseif ($profileTabsPermission[getTabid("Quotes")] == 0 && ($type == "quotesbystage") && (getFieldVisibilityPermission('Quotes',$user_id,'quotestage') == "0"))
                    {
                            $graph_by="quotestage";
                            $graph_title=$mod_strings['quotesbystage'];
                            $module="Quotes";
                            $where="";
                            $query=getDashboardQuery($quotes_query,$module);
                            return get_graph_by_type($graph_by,$graph_title,$module,$where,$query,"210","210","forhomepage");
                    }
                    //Invoice by Accounts
                    elseif ($profileTabsPermission[getTabid("Invoice")] == 0 && ($type == "invoicebyacnts") && (getFieldVisibilityPermission('Invoice',$user_id,'account_id') == "0"))
                    {
                            $graph_by="accountid";
                            $graph_title=$mod_strings['invoicebyacnts'];
                            $module="Invoice";
                            $where="";
                            $query=getDashboardQuery($invoice_query,$module);
                            return get_graph_by_type($graph_by,$graph_title,$module,$where,$query,"210","210","forhomepage");
                    }
                    //Invoices by status
                    elseif ($profileTabsPermission[getTabid("Invoice")] == 0 && ($type == "invoicebystatus") && (getFieldVisibilityPermission('Invoice',$user_id,'invoicestatus') == "0"))
                    {
                            $graph_by="invoicestatus";
                            $graph_title=$mod_strings['invoicebystatus'];
                            $module="Invoice";
                            $where="";
                            $query=getDashboardQuery($invoice_query,$module);
                            return get_graph_by_type($graph_by,$graph_title,$module,$where,$query,"210","210","forhomepage");
                    }
                    //Tickets by Status
                    elseif ($profileTabsPermission[getTabid("HelpDesk")] == 0 && ($type == "ticketsbystatus") && (getFieldVisibilityPermission('HelpDesk',$user_id,'ticketstatus') == "0"))
                    {
                            $graph_by="ticketstatus";
                            $graph_title=$mod_strings['ticketsbystatus'];
                            $module="HelpDesk";
                            $where="";
                            $query=getDashboardQuery($helpdesk_query,$module);
                            return get_graph_by_type($graph_by,$graph_title,$module,$where,$query,"210","210","forhomepage");
                    }
                    //Tickets by Priority
                    elseif ($profileTabsPermission[getTabid("HelpDesk")] == 0 && ($type == "ticketsbypriority") && (getFieldVisibilityPermission('HelpDesk',$user_id,'ticketpriorities') == "0"))
                    {
                            $graph_by="priority";
                            $graph_title=$mod_strings['ticketsbypriority'];
                            $module="HelpDesk";
                            $where="";
                            $query=getDashboardQuery($helpdesk_query,$module);
                            return get_graph_by_type($graph_by,$graph_title,$module,$where,$query,"210","210","forhomepage");
                    }
		    //Tickets by Category
		    elseif ($profileTabsPermission[getTabid("HelpDesk")] == 0 && ($type == "ticketsbycategory") && (getFieldVisibilityPermission('HelpDesk',$user_id,'ticketcategories') == "0"))
		    {
			    $graph_by="category";
			    $graph_title=$mod_strings['ticketsbycategory'];
			    $module="HelpDesk";
			    $where="";
			    $query=getDashboardQuery($helpdesk_query,$module);
			    return get_graph_by_type($graph_by,$graph_title,$module,$where,$query,"210","210","forhomepage");
		    }
		    //Tickets by User   
		    elseif ($profileTabsPermission[getTabid("HelpDesk")] == 0 && ($type == "ticketsbyuser"))
		    {
			    $graph_by="smownerid";
			    $graph_title=$mod_strings['ticketsbyuser'];
			    $module="HelpDesk";
			    $where=" and (".$table_prefix."_groups.groupname is NULL)";
			    $query=getDashboardQuery($helpdesk_query,$module);
			    return get_graph_by_type($graph_by,$graph_title,$module,$where,$query,"210","210","forhomepage");
		    }
		    //Tickets by Team
		    elseif ($profileTabsPermission[getTabid("HelpDesk")] == 0 && ($type == "ticketsbyteam"))
		    {
			    $graph_by="smownerid";
			    $graph_title=$mod_strings['ticketsbyteam'];
			    $module="HelpDesk";
			    $where=" and (".$table_prefix."_groups.groupname != NULL || ".$table_prefix."_groups.groupname != ' ')";
			    $query=getDashboardQuery($helpdesk_query,$module);
			    return get_graph_by_type($graph_by,$graph_title,$module,$where,$query,"210","210","forhomepage");
		    }    
		    //Tickets by Product
		    elseif ($profileTabsPermission[getTabid("HelpDesk")] == 0 && ($type == "ticketsbyproduct") && (getFieldVisibilityPermission('HelpDesk',$user_id,'product_id') == "0"))
		    {
			    $graph_by="product_id";
			    $graph_title=$mod_strings['ticketsbyproduct'];
			    $module="HelpDesk";
			    $where="";
			    $query=getDashboardQuery($helpdesk_query,$module);
			    return get_graph_by_type($graph_by,$graph_title,$module,$where,$query,"210","210","forhomepage");
		    }
		    //Campaigns by Contact
		    elseif ($profileTabsPermission[getTabid("Contacts")] == 0 && ($type == "contactbycampaign") && $profileTabsPermission[getTabid("Campaigns")] == 0)
		    {
			    $graph_by="campaignid";
			    $graph_title=$mod_strings['contactbycampaign'];
			    $module="Contacts";
			    $where="";
			    $query=getDashboardQuery($campaign_query,$module);
			    return get_graph_by_type($graph_by,$graph_title,$module,$where,$query,"210","210","forhomepage");
		    }
		    //Tickets by Account
		    elseif ($profileTabsPermission[getTabid("HelpDesk")] == 0 && ($type == "ticketsbyaccount") && (getFieldVisibilityPermission('HelpDesk',$user_id,'parent_id') == "0"))
		    {
			    $graph_by="parent_id";
			    $graph_title=$mod_strings['ticketsbyaccount'];
			    $module="HelpDesk";
			    $where="";
			    $query=getDashboardQuery($tickets_by_account,$module);
			    return get_graph_by_type($graph_by,$graph_title,$module,$where,$query,"210","210","forhomepage");
		    }
		    //Tickets by Contact
		    elseif ($profileTabsPermission[getTabid("HelpDesk")] == 0 && ($type == "ticketsbycontact") && (getFieldVisibilityPermission('HelpDesk',$user_id,'parent_id') == "0"))
			    {
				    $graph_by="parent_id";
				    $graph_title=$mod_strings['ticketsbycontact'];
				    $module="HelpDesk";
				    $where="";
				    $query=getDashboardQuery($tickets_by_contact,$module);
				    return get_graph_by_type($graph_by,$graph_title,$module,$where,$query,"210","210","forhomepage");
				    }
		    else
                    {
                        //echo $mod_strings['LBL_NO_PERMISSION_FIELD'];
			sleep(1);
                        return '<h3>'.$mod_strings['LBL_NO_PERMISSION_FIELD'].'</h3>';
		    }
	}
	else
	{
		$html='<table width="100%"  border="0" cellspacing="0" cellpadding="0">';
		//Charts for Lead Source
                    if($profileTabsPermission[getTabid("Leads")] == 0 && ($type == "leadsource") && (getFieldVisibilityPermission('Leads',$user_id,'leadsource') == "0"))
                    {
                    	$graph_by="leadsource";
                    	$graph_title= $mod_strings['leadsource'];
                    	$module="Leads";
                    	$where="";
                    	$query=getDashboardQuery($leads_query,$module);
                    	//$html .= get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
                            echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
                    }
                    // To display the charts  for Lead status                   
                    elseif ($profileTabsPermission[getTabid("Leads")] == 0 && ($type == "leadstatus")&& (getFieldVisibilityPermission('Leads',$user_id,'leadstatus') == "0"))
                    {
                    	$graph_by="leadstatus";
                    	$graph_title= $mod_strings['leadstatus'];
                    	$module="Leads";
                    	$where="";
                    	$query=getDashboardQuery($leads_query,$module);
						if(!$is_admin)
							$query .= ' and '.$table_prefix.'_leaddetails.leadstatus '.picklist_check($module,$graph_by);
			                    	echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
                    }
                    //Charts for Lead Industry
                    elseif ($profileTabsPermission[getTabid("Leads")] == 0 && ($type == "leadindustry") && (getFieldVisibilityPermission('Leads',$user_id,'industry') == "0"))
                    {
                    	$graph_by="industry";
                            $graph_title=$mod_strings['leadindustry'];
                            $module="Leads";
                            $where="";
                            $query=getDashboardQuery($leads_query,$module);
			    if(!$is_admin)
			    	$query .= ' and '.$table_prefix.'_leaddetails.industry '.picklist_check($module,$graph_by);
                            echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
                    }
                    //Sales by Lead Source
                    elseif ($profileTabsPermission[getTabid("Potentials")] == 0 && ($type == "salesbyleadsource")&& (getFieldVisibilityPermission('Potentials',$user_id,'leadsource') == "0"))
                    {
                            $graph_by="leadsource";
                            $graph_title=$mod_strings['salesbyleadsource'];
                            $module="Potentials";
                            $where=" and ".$table_prefix."_potential.sales_stage like 'Closed Won' ";
                            $query=getDashboardQuery($potential_query,$module);
			    if(!$is_admin)
			    	$query .= ' and '.$table_prefix.'_potential.leadsource '.picklist_check($module,$graph_by);
                            echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
                    }
                    //Sales by Account
                    elseif ($profileTabsPermission[getTabid("Potentials")] == 0 && ($type == "salesbyaccount") && (getFieldVisibilityPermission('Potentials',$user_id,'related_to') == "0"))
                    {
                    	 $graph_by="related_to";
                         $graph_title=$mod_strings['salesbyaccount'];
                         $module="Potentials";
                         $where=" and ".$table_prefix."_potential.sales_stage like 'Closed Won' ";
                         $query=getDashboardQuery($potential_query,$module);
                         echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
                    }
		    //Sales by User
		    elseif ($profileTabsPermission[getTabid("Potentials")] == 0 && ($type == "salesbyuser"))
		    {
			$graph_by="smownerid";
			$graph_title=$mod_strings['salesbyuser'];
			$module="Potentials";
			$where=" and ".$table_prefix."_potential.sales_stage like 'Closed Won' and (".$table_prefix."_groups.groupname is NULL)";
			$query=getDashboardQuery($potential_query,$module);
			echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
		    }
		    //Sales by team
		    elseif ($profileTabsPermission[getTabid("Potentials")] == 0 && ($type == "salesbyteam"))
		    {
			$graph_by="smownerid";
			$graph_title=$mod_strings['salesbyteam'];
			$module="Potentials";
			$where=" and ".$table_prefix."_potential.sales_stage like 'Closed Won' and (".$table_prefix."_groups.groupname != NULL || ".$table_prefix."_groups.groupname != '')";
			$query=getDashboardQuery($potential_query,$module);
			echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
		    }
                    //Charts for Account by Industry
                    elseif ($profileTabsPermission[getTabid("Accounts")] == 0 && ($type == "accountindustry") && (getFieldVisibilityPermission('Accounts',$user_id,'industry') == "0"))
                    {
                    	$graph_by="industry";
                            $graph_title=$mod_strings['accountindustry'];
                            $module="Accounts";
                            $where="";
                            $query=getDashboardQuery($account_query,$module);
			    if(!$is_admin)
			    	$query .= ' and '.$table_prefix.'_account.industry '.picklist_check($module,$graph_by);
                            echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
                    }
                    //Charts for Products by Category
                    elseif ($profileTabsPermission[getTabid("Products")] == 0 && ($type == "productcategory") && (getFieldVisibilityPermission('Products',$user_id,'productcategory') == "0"))
                    {
                    	$graph_by="productcategory";
                            $graph_title=$mod_strings['productcategory'];
                            $module="Products";
                            $where="";
                            $query=getDashboardQuery($product_category,$module);
			    if(!$is_admin)
			    	$query .= ' and '.$table_prefix.'_products.productcategory '.picklist_check($module,$graph_by);
                            echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
                    }
		    //Charts for Products by Quantity in stock
		    elseif ($profileTabsPermission[getTabid("Products")] == 0 && ($type == "productbyqtyinstock") && (getFieldVisibilityPermission('Products',$user_id,'qtyinstock') == "0"))
		    {
			$graph_by="productname";
			    $graph_title=$mod_strings['productbyqtyinstock'];
			    $module="Products";
			    $where="";
			    $query=getDashboardQuery($products_query,$module);
			    echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
		    }
		    //Charts for Products by PO
		    elseif ($profileTabsPermission[getTabid("Products")] == 0 && ($type == "productbypo") && $profileTabsPermission[getTabid("PurchaseOrder")] == 0)
		    { 
			    $graph_by="purchaseorderid";
			    $graph_title=$mod_strings['productbypo'];
			    $module="Products";
			    $where="";
			    $query=getDashboardQuery($probyPO,"PurchaseOrder");
			    echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
		    }
		    //Charts for Products by Quotes
		    elseif ($profileTabsPermission[getTabid("Products")] == 0 && ($type == "productbyquotes") && $profileTabsPermission[getTabid("Quotes")] == 0)
		    { 
                            $graph_by="quoteid";
   			    $graph_title=$mod_strings['productbyquotes'];
			    $module="Products";
			    $where=""; 
			    $query=getDashboardQuery($probyQ,"Quotes");
			    echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
		    }
		    //Charts for Products by Invoice
		    elseif ($profileTabsPermission[getTabid("Products")] == 0 && ($type == "productbyinvoice") && $profileTabsPermission[getTabid("Invoice")] == 0)
		    {
		            $graph_by="invoiceid";
			    $graph_title=$mod_strings['productbyinvoice'];
			    $module="Products";
			    $where="";
			    $query=getDashboardQuery($probyInv,"Invoice");
			    echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
		    }

                    // Sales Order by Accounts
                    elseif ($profileTabsPermission[getTabid("SalesOrder")] == 0 && ($type == "sobyaccounts") && (getFieldVisibilityPermission('SalesOrder',$user_id,'account_id') == "0"))
                    {
                    	$graph_by="accountid";
                            $graph_title=$mod_strings['sobyaccounts'];
                            $module="SalesOrder";
                            $where="";
                            $query=getDashboardQuery($so_query,$module);
                            echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
                    }
                    //Sales Order by Status
                    elseif ($profileTabsPermission[getTabid("SalesOrder")] == 0 && ($type == "sobystatus") && (getFieldVisibilityPermission('SalesOrder',$user_id,'sostatus') == "0"))
                    {
                            $graph_by="sostatus";
                            $graph_title=$mod_strings['sobystatus'];
                            $module="SalesOrder";
                            $where="";
                            $query=getDashboardQuery($so_query,$module);
			    if(!$is_admin)
			    	$query .= ' and '.$table_prefix.'_salesorder.sostatus '.picklist_check($module,$graph_by);
                            echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
                    }
                    //Purchase Order by Status
                    elseif ($profileTabsPermission[getTabid("PurchaseOrder")] == 0 && ($type == "pobystatus") && (getFieldVisibilityPermission('PurchaseOrder',$user_id,'postatus') == "0"))
                    {
                            $graph_by="postatus";
                            $graph_title=$mod_strings['pobystatus'];
                            $module="PurchaseOrder";
                            $where="";
                            $query=getDashboardQuery($po_query,$module);
			    if(!$is_admin)
			    	$query .= ' and '.$table_prefix.'_purchaseorder.postatus '.picklist_check($module,$graph_by);
                            echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
                    }
                    //Quotes by Accounts
                    elseif ($profileTabsPermission[getTabid("Quotes")] == 0 && ($type == "quotesbyaccounts") && (getFieldVisibilityPermission('Quotes',$user_id,'account_id') == "0"))
                    {
                            $graph_by="accountid";
                            $graph_title= $mod_strings['quotesbyaccounts'];
                            $module="Quotes";
                            $where="";
                            $query=getDashboardQuery($quotes_query,$module);
                            echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
                    }
                    //Quotes by Stage
                    elseif ($profileTabsPermission[getTabid("Quotes")] == 0 && ($type == "quotesbystage") && (getFieldVisibilityPermission('Quotes',$user_id,'quotestage') == "0"))
                    {
                            $graph_by="quotestage";
                            $graph_title=$mod_strings['quotesbystage'];
                            $module="Quotes";
                            $where="";
                            $query=getDashboardQuery($quotes_query,$module);
			    if(!$is_admin)
			    	$query .= ' and '.$table_prefix.'_quotes.quotestage '.picklist_check($module,$graph_by);
                            echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
                    }
                    //Invoice by Accounts
                    elseif ($profileTabsPermission[getTabid("Invoice")] == 0 && ($type == "invoicebyacnts") && (getFieldVisibilityPermission('Invoice',$user_id,'account_id') == "0"))
                    {
                            $graph_by="accountid";
                            $graph_title=$mod_strings['invoicebyacnts'];
                            $module="Invoice";
                            $where="";
                            $query=getDashboardQuery($invoice_query,$module);
                            echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
                    }
                    //Invoices by status
                    elseif ($profileTabsPermission[getTabid("Invoice")] == 0 && ($type == "invoicebystatus") && (getFieldVisibilityPermission('Invoice',$user_id,'invoicestatus') == "0"))
                    {
                            $graph_by="invoicestatus";
                            $graph_title=$mod_strings['invoicebystatus'];
                            $module="Invoice";
                            $where="";
                            $query=getDashboardQuery($invoice_query,$module);
			    if(!$is_admin)
			    	$query .= ' and '.$table_prefix.'_invoice.invoicestatus '.picklist_check($module,$graph_by);
                            echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
                    }
                    //Tickets by Status
                    elseif ($profileTabsPermission[getTabid("HelpDesk")] == 0 && ($type == "ticketsbystatus") && (getFieldVisibilityPermission('HelpDesk',$user_id,'ticketstatus') == "0"))
                    {
                        $graph_by="ticketstatus";
                        $graph_title=$mod_strings['ticketsbystatus'];
                        $module="HelpDesk";
                        $where="";
					    $query=getDashboardQuery($helpdesk_query,$module);
					    if(!$is_admin)
					    	$query .= ' and '.$table_prefix.'_troubletickets.status '.picklist_check($module,$graph_by);
                            echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
                    }
                    //Tickets by Priority
                    elseif ($profileTabsPermission[getTabid("HelpDesk")] == 0 && ($type == "ticketsbypriority") && (getFieldVisibilityPermission('HelpDesk',$user_id,'ticketpriorities') == "0"))
                    {
                            $graph_by="priority";
                            $graph_title=$mod_strings['ticketsbypriority'];
                            $module="HelpDesk";
                            $where="";
                            $query=getDashboardQuery($helpdesk_query,$module);
			    if(!$is_admin)
			    	$query .= ' and '.$table_prefix.'_troubletickets.priority '.picklist_check($module,$graph_by);
                            echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
                    }
		    //Tickets by Category
		    elseif ($profileTabsPermission[getTabid("HelpDesk")] == 0 && ($type == "ticketsbycategory") && (getFieldVisibilityPermission('HelpDesk',$user_id,'ticketcategories') == "0"))
		    {
			    $graph_by="category";
			    $graph_title=$mod_strings['ticketsbycategory'];
			    $module="HelpDesk";
			    $where="";
			    $query=getDashboardQuery($helpdesk_query,$module);
			    if(!$is_admin)
			    	$query .= ' and '.$table_prefix.'_troubletickets.category '.picklist_check($module,$graph_by);
			    echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
		    }
		    //Tickets by User   
		    elseif ($profileTabsPermission[getTabid("HelpDesk")] == 0 && ($type == "ticketsbyuser"))
		    {
			    $graph_by="smownerid";
			    $graph_title=$mod_strings['ticketsbyuser'];
			    $module="HelpDesk";
			    $where=" and (".$table_prefix."_groups.groupname is NULL)";
			    $query=getDashboardQuery($helpdesk_query,$module);
			    echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
		    }
		    //Tickets by Team
		    elseif ($profileTabsPermission[getTabid("HelpDesk")] == 0 && ($type == "ticketsbyteam"))
		    {
			    $graph_by="smownerid";
			    $graph_title=$mod_strings['ticketsbyteam'];
			    $module="HelpDesk";
			    $where=" and (".$table_prefix."_groups.groupname != NULL || ".$table_prefix."_groups.groupname != ' ')";
			    $query=getDashboardQuery($helpdesk_query,$module);
			    echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
		    }    
		    //Tickets by Product
		    elseif ($profileTabsPermission[getTabid("HelpDesk")] == 0 && ($type == "ticketsbyproduct") && (getFieldVisibilityPermission('HelpDesk',$user_id,'product_id') == "0"))
		    {
			    $graph_by="product_id";
			    $graph_title=$mod_strings['ticketsbyproduct'];
			    $module="HelpDesk";
			    $where="";
			    $query=getDashboardQuery($helpdesk_query,$module);
			    echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
		    }
		    //Campaigns by Contact
		    elseif ($profileTabsPermission[getTabid("Contacts")] == 0 && ($type == "contactbycampaign") && $profileTabsPermission[getTabid("Campaigns")] == 0)
		    {
			    $graph_by="campaignid";
			    $graph_title=$mod_strings['contactbycampaign'];
			    $module="Contacts";
			    $where="";
			    $query=getDashboardQuery($campaign_query,"Campaigns");
			    echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
		    }
		    //Tickets by Account
		    elseif ($profileTabsPermission[getTabid("HelpDesk")] == 0 && ($type == "ticketsbyaccount") && (getFieldVisibilityPermission('HelpDesk',$user_id,'parent_id') == "0"))
		    {
			    $graph_by="parent_id";
			    $graph_title=$mod_strings['ticketsbyaccount'];
			    $module="HelpDesk";
			    $where="";
			    $query=getDashboardQuery($tickets_by_account, $module);
			    echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
		    }
		    //Tickets by Contact
		    elseif ($profileTabsPermission[getTabid("HelpDesk")] == 0 && ($type == "ticketsbycontact") && (getFieldVisibilityPermission('HelpDesk',$user_id,'parent_id') == "0"))
		    {
				    $graph_by="parent_id";
				    $graph_title=$mod_strings['ticketsbycontact'];
				    $module="HelpDesk";
				    $where="";
				    $query=getDashboardQuery($tickets_by_contact, $module);
				    echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
		    }
		    else
                    {
                        //echo $mod_strings['LBL_NO_PERMISSION_FIELD'];
			sleep(1);
                        echo '<h3>'.$mod_strings['LBL_NO_PERMISSION_FIELD'].'</h3>';
		    }
	}
}

/**This function generates the security parameters for a given module based on the assigned profile 
*Param $module - module name
*Returns an string value
*/

function getDashboardQuery($query, $module) {
	global $current_user;
	$secQuery = getNonAdminAccessControlQuery($module, $current_user);
	if(strlen($secQuery) > 1) {
		$query = appendFromClauseToQuery($query, $secQuery);
	}
	return $query;
}

/**This function generates the security parameters for a given user base picklist values
*Param $graph - name of the graph
*Returns an string value
*/

function picklist_check($module,$graph_by)
{
	global $current_user,$adb,$table_prefix;
	$pick_query = '';
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	$roleid=$current_user->roleid;
	$subrole = getRoleSubordinates($roleid);
	if(count($subrole)> 0)
	{
		$roleids = $subrole;
		array_push($roleids, $roleid);
	}
	else
	{
		$roleids = $roleid;
	}
	if($graph_by == 'sostatus' || $graph_by == 'leadsource' || $graph_by == 'leadstatus' ||$graph_by == 'industry' || $graph_by == 'productcategory' || $graph_by == 'postatus' || $graph_by == 'invoicestatus' || $graph_by == 'ticketstatus' || $graph_by == 'priority' || $graph_by == 'category' || $graph_by == 'quotestage')
	{
		$temp_fieldname = $graph_by;
		if($graph_by == 'priority')
			$temp_fieldname = 'ticketpriorities';
		if($graph_by == 'category')
			$temp_fieldname = 'ticketcategories';

		if(count($roleids) > 1)
			$pick_query = " in (select distinct $temp_fieldname from ".$table_prefix."_".$temp_fieldname."  inner join ".$table_prefix."_role2picklist on ".$table_prefix."_role2picklist.picklistvalueid = ".$table_prefix."_".$temp_fieldname.".picklist_valueid where roleid in (\"". implode($roleids,"\",\"") ."\")) ";
		else
			$pick_query = " in (select distinct $temp_fieldname from ".$table_prefix."_".$temp_fieldname."  inner join ".$table_prefix."_role2picklist on ".$table_prefix."_role2picklist.picklistvalueid = ".$table_prefix."_".$temp_fieldname.".picklist_valueid where roleid in ('$roleids')) ";
	}
	return $pick_query;
}
?>