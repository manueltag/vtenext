<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
*
 ********************************************************************************/


/**	function used to get the permitted blocks
 *	@param string $module - module name
 *	@param string $disp_view - view name, this may be create_view, edit_view or detail_view
 *	@return string $blockid_list - list of block ids within the paranthesis with comma seperated
 */
function getPermittedBlocks($module, $disp_view)
{
	global $adb, $log, $table_prefix;
	$log->debug("Entering into the function getPermittedBlocks($module, $disp_view)");
	
        $tabid = getTabid($module);
        $block_detail = Array();
        $query="select blockid,blocklabel,show_title from ".$table_prefix."_blocks where tabid=? and $disp_view=0 and visible = 0 order by sequence";
        $result = $adb->pquery($query, array($tabid));
        $noofrows = $adb->num_rows($result);
	$blockid_list ='(';
	for($i=0; $i<$noofrows; $i++)
	{
		$blockid = $adb->query_result($result,$i,"blockid");
		if($i != 0)
			$blockid_list .= ', ';
		$blockid_list .= $blockid;
		$block_label[$blockid] = $adb->query_result($result,$i,"blocklabel");
	}
	$blockid_list .= ')';

	$log->debug("Exit from the function getPermittedBlocks($module, $disp_view). Return value = $blockid_list");
	return $blockid_list;
}

/**	function used to get the query which will list the permitted fields 
 *	@param string $module - module name
 *	@param string $disp_view - view name, this may be create_view, edit_view or detail_view
 *	@return string $sql - query to get the list of fields which are permitted to the current user
 */
function getPermittedFieldsQuery($module, $disp_view)
{
	global $adb, $log, $table_prefix;
	$log->debug("Entering into the function getPermittedFieldsQuery($module, $disp_view)");

	global $current_user;
	require('user_privileges/user_privileges_'.$current_user->id.'.php');

	//To get the permitted blocks
	$blockid_list = getPermittedBlocks($module, $disp_view);
	
        $tabid = getTabid($module);
	if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0 || $module == "Users")
	{
 		$sql = "SELECT ".$table_prefix."_field.columnname, ".$table_prefix."_field.fieldlabel, ".$table_prefix."_field.tablename FROM ".$table_prefix."_field WHERE ".$table_prefix."_field.tabid=".$tabid." AND ".$table_prefix."_field.block IN $blockid_list AND ".$table_prefix."_field.displaytype IN (1,2,4) and ".$table_prefix."_field.presence in (0,2) ORDER BY block,sequence";
  	}
  	else
  	{
		$profileList = getCurrentUserProfileList();
		$sql = "SELECT ".$table_prefix."_field.columnname, ".$table_prefix."_field.fieldlabel, ".$table_prefix."_field.tablename FROM ".$table_prefix."_field INNER JOIN ".$table_prefix."_def_org_field ON ".$table_prefix."_def_org_field.fieldid=".$table_prefix."_field.fieldid WHERE ".$table_prefix."_field.tabid=".$tabid." AND ".$table_prefix."_field.block IN ".$blockid_list." AND ".$table_prefix."_field.displaytype IN (1,2,4) AND ".$table_prefix."_def_org_field.visible=0 and ".$table_prefix."_field.presence in (0,2) ";
	    $sql.=" AND EXISTS(SELECT * FROM ".$table_prefix."_profile2field WHERE ".$table_prefix."_profile2field.fieldid = ".$table_prefix."_field.fieldid ";
	        if (count($profileList) > 0) {
		  	 	$sql.=" AND ".$table_prefix."_profile2field.profileid IN (". implode(",", $profileList) .") ";
		} 			  
	    $sql.=" AND ".$table_prefix."_profile2field.visible = 0) "; 
		$sql.=" ORDER BY block,sequence";
	}
	$log->debug("Exit from the function getPermittedFieldsQuery($module, $disp_view). Return value = $sql");
	return $sql;
}

/**	function used to get the list of fields from the input query as a comma seperated string 
 *	@param string $query - field table query which contains the list of fields 
 *	@return string $fields - list of fields as a comma seperated string
 */
function getFieldsListFromQuery($query)
{
	global $adb, $log, $table_prefix;
	$log->debug("Entering into the function getFieldsListFromQuery($query)");
	
	$result = $adb->query($query);
	$num_rows = $adb->num_rows($result);
	for($i=0; $i < $num_rows;$i++)
	{
		$columnName = $adb->query_result($result,$i,"columnname");
		$fieldlabel = $adb->query_result($result,$i,"fieldlabel");
		$tablename = $adb->query_result($result,$i,"tablename");
		//crmv@fix names > 30 chars
		if($adb->isOracle()) //crmv@63765
			$fieldlabel = substr($fieldlabel,0,29);
		//crmv@fix names > 30 chars end
		//HANDLE HERE - Mismatch fieldname-tablename in field table, in future we have to avoid these if elses
		if($columnName == 'smownerid')//for all assigned to user name
		{
			$fields .= "case when (".$table_prefix."_users.user_name is not null) then ".$table_prefix."_users.user_name else ".$table_prefix."_groups.groupname end as \"".$fieldlabel."\",";
		}
		elseif($tablename == $table_prefix.'_account' && $columnName == 'parentid')//Account - Member Of
		{
			 $fields .= $table_prefix."_account2.accountname as \"".$fieldlabel."\",";
		}
		elseif($tablename == $table_prefix.'_contactdetails' && $columnName == 'accountid')//Contact - Account Name
		{
			$fields .= $table_prefix."_account.accountname as \"".$fieldlabel."\",";
		}
		elseif($tablename == $table_prefix.'_contactdetails' && $columnName == 'reportsto')//Contact - Reports To
		{
			$fields .= " ".$adb->sql_concat(Array($table_prefix.'_contactdetails2.lastname',"' '",$table_prefix.'_contactdetails2.firstname'))." as \"Reports To Contact\",";
		}
		elseif($tablename == $table_prefix.'_potential' && $columnName == 'related_to')//Potential - Related to (changed for B2C model support)
		{
			$fields .= $table_prefix."_potential.related_to as \"".$fieldlabel."\",";
		}
		elseif($tablename == $table_prefix.'_potential' && $columnName == 'campaignid')//Potential - Campaign Source
		{
			$fields .= $table_prefix."_campaign.campaignname as \"".$fieldlabel."\",";
		}
		elseif($tablename == $table_prefix.'_seproductsrel' && $columnName == 'crmid')//Product - Related To
		{
			
			$fields .= "case ".$table_prefix."_crmentityRelatedTo.setype 
					when 'Leads' then ".$adb->sql_concat(Array("'Leads ::: '",$table_prefix.'_ProductRelatedToLead.lastname',"' '",$table_prefix.'_ProductRelatedToLead.firstname'))."
					when 'Accounts' then ".$adb->sql_concat(Array("'Accounts ::: '",$table_prefix.'_ProductRelatedToAccount.accountname'))." 
					when 'Potentials' then ".$adb->sql_concat(Array("'Potentials ::: '",$table_prefix.'_ProductRelatedToPotential.potentialname'))."
				    End as \"Related To\",";
		}
		elseif($tablename == $table_prefix.'_products' && $columnName == 'contactid')//Product - Contact
		{
			$fields .= " ".$adb->sql_concat(Array($table_prefix.'_contactdetails.lastname',"' '",$table_prefix.'_contactdetails.firstname'))." as \"Contact Name\",";
		}
		elseif($tablename == $table_prefix.'_products' && $columnName == 'vendor_id')//Product - Vendor Name
		{
			$fields .= $table_prefix."_vendor.vendorname as \"".$fieldlabel."\",";
		}
		//Pavani- Handling product handler
		elseif($tablename == $table_prefix.'_products' && $columnName == 'handler')//Product - Handler
		{
			$fields .= $table_prefix."_users.user_name as \"".$fieldlabel."\",";
		}
		elseif($tablename == $table_prefix.'_producttaxrel' && $columnName == 'taxclass')//avoid product - taxclass
		{
			$fields .= "";
		}
		elseif($tablename == $table_prefix.'_attachments' && $columnName == 'name')//Emails filename
		{
			$fields .= $tablename.".name as \"".$fieldlabel."\",";
		}
		//By Pavani...Handling mismatch field and table name for trouble tickets
      	elseif($tablename == $table_prefix.'_troubletickets' && $columnName == 'product_id')//Ticket - Product
        {
                 $fields .= $table_prefix."_products.productname as \"".$fieldlabel."\",";
        }
        elseif($tablename == $table_prefix.'_troubletickets' && $columnName == 'parent_id')//Ticket - Related To
        {
			//crmv@92596
			$fields .= "case ".$table_prefix."_crmentityRelatedTo.setype
				when 'Accounts' then ".$adb->sql_concat(Array("'Accounts ::: '",$table_prefix.'_account.accountname'))." 
				when 'Contacts' then ".$adb->sql_concat(Array("'Contacts ::: '",$table_prefix.'_contactdetails.lastname',"' '",$table_prefix.'_contactdetails.firstname'))."
				when 'Leads' then ".$adb->sql_concat(Array("'Leads ::: '",$table_prefix.'_leaddetails.lastname',"' '",$table_prefix.'_leaddetails.firstname'))." 
			End as \"Related To\",";
			//crmv@92596
        }
		elseif($tablename == $table_prefix.'_notes' && ($columnName == 'filename' || $columnName == 'filetype' || $columnName == 'filesize' || $columnName == 'filelocationtype' || $columnName == 'filestatus' || $columnName == 'filedownloadcount' ||$columnName == 'folderid')){
			continue;
		}
		//crmv@61280 
		elseif ($columnName == 'newsletter_unsubscrpt') {
			if ($tablename == $table_prefix."_leaddetails") {
				$fields .= $tablename.".email as \"" .$fieldlabel."\",";
			}elseif ($tablename == $table_prefix."_account") {
				$fields .= $tablename.".email1 as \"" .$fieldlabel."\",";
			}elseif ($tablename == $table_prefix."_contactdetails") {
				$fields .= $tablename.".email as \"" .$fieldlabel."\",";
			}
		}
		//crmv@61280 e	
		else
		{
			$fields .= $tablename.".".$columnName. " as \"" .$fieldlabel."\",";
		}
	}
	$fields = trim($fields,",");

	$log->debug("Exit from the function getFieldsListFromQuery($query). Return value = $fields");
	return $fields;
}



?>
