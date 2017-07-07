<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the 
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Accounts/Save.php,v 1.7 2005/03/15 09:55:31 shaw Exp $
 * Description:  Saves an Account record and then redirects the browser to the 
 * defined return URL.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('modules/Accounts/Accounts.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');
global $currentModule;
global $table_prefix;
//added to fix 4600
$search=vtlib_purify($_REQUEST['search_url']);
if(isset($_REQUEST['dup_check']) && $_REQUEST['dup_check'] != ''){
	
	check_duplicate(vtlib_purify($_REQUEST['module']),
	vtlib_purify($_REQUEST['colnames']),vtlib_purify($_REQUEST['fieldnames']),
	vtlib_purify($_REQUEST['fieldvalues']));
	die;
}

//crmv@7213
if(isset($_REQUEST['EXT_CODE']) && $_REQUEST['EXT_CODE'] != '')
{
	$value = $_REQUEST['external_code'];
	$query = "SELECT accountid FROM ".$table_prefix."_account,".$table_prefix."_crmentity WHERE ".$table_prefix."_account.external_code ='".$value."' and ".$table_prefix."_account.accountid = ".$table_prefix."_crmentity.crmid and ".$table_prefix."_crmentity.deleted ='1'";
	$result = $adb->query($query);
	if($adb->num_rows($result) > 0)
	{
		echo "duplicate";
	}
	else
	{
		$query2 = "SELECT accountid FROM ".$table_prefix."_account,".$table_prefix."_crmentity WHERE ".$table_prefix."_account.external_code ='".$value."' and ".$table_prefix."_account.accountid = ".$table_prefix."_crmentity.crmid and ".$table_prefix."_crmentity.deleted != '1'";
		$result2 = $adb->query($query2);
		if($adb->num_rows($result2) > 0)
		{
			require_once('modules/Accounts/MergeCodeAccount.php');
			$ext_code=$adb->query_result($result2,0,0);
			if (check_merge_permission_CODE($_REQUEST['record'],$ext_code)){
				echo $ext_code;
			}
			else echo "owner";
		}
		else
		{
			echo "false";
		}
	}
	die;	
}

if(isset($_REQUEST['MergeCode']) && $_REQUEST['MergeCode'] != ''){
	include ('modules/Accounts/MergeCodeAccount.php');
	$focus2=CRMEntity::getInstance('Accounts');
	$focus2->mode = "edit";
	$focus2->retrieve_entity_info($_REQUEST['idEXT'],"Accounts");
	$focus=CRMEntity::getInstance('Accounts');
	$focus->mode = "edit";
	$focus->retrieve_entity_info($_REQUEST['idCRM'],"Accounts");
	if ($focus2->crmv_compare_column_fields($focus2->column_fields,$focus->column_fields)) {
		$focus->crmv_save_ajax_code($focus2->column_fields['external_code']);
		$focus->id=$_REQUEST['idCRM'];
		$focus->save("Accounts");
		$focus2->id=$_REQUEST['idEXT'];
		$focus2->save("Accounts");
		crmv_merge_account($_REQUEST['idCRM'],$_REQUEST['idEXT']);
		echo "true";
	}
	else echo "false";
	die;
}
//crmv@7213e

$local_log =& LoggerManager::getLogger('index');
global $log;
$focus = CRMEntity::getInstance('Accounts');
global $current_user;
$currencyid=fetchCurrency($current_user->id);
$rate_symbol = getCurrencySymbolandCRate($currencyid);
$rate = $rate_symbol['rate'];
$curr_symbol = $rate_symbol['symbol'];
if(isset($_REQUEST['record']))
{
	$focus->id = $_REQUEST['record'];
$log->info("id is ".$focus->id);
}
if(isset($_REQUEST['mode']))
{
	$focus->mode = $_REQUEST['mode'];
}

foreach($focus->column_fields as $fieldname => $val)
{
	if(isset($_REQUEST[$fieldname]))
	{
		if(is_array($_REQUEST[$fieldname]))
			$value = $_REQUEST[$fieldname];
		else
			$value = trim($_REQUEST[$fieldname]);
		$log->DEBUG($fieldname."=Field Name &first& Value =".$value);
		$focus->column_fields[$fieldname] = $value;
	}
}

if(isset($_REQUEST['annual_revenue']))
{
	// crmv@83877
	$value = convertToDollar(parseUserNumber($_REQUEST['annual_revenue']),$rate);
	$focus->column_fields['annual_revenue'] = formatUserNumber($value);
	// crmv@83877e
}

if($_REQUEST['assigntype'] == 'U')  {
	$focus->column_fields['assigned_user_id'] = $_REQUEST['assigned_user_id'];
} elseif($_REQUEST['assigntype'] == 'T') {
	$focus->column_fields['assigned_user_id'] = $_REQUEST['assigned_group_id'];
}

//When changing the Account Address Information  it should also change the related contact address - dina
if($focus->mode == 'edit' && $_REQUEST['address_change'] == 'yes')
{
	$query = "update ".$table_prefix."_contactaddress set mailingcity=?,mailingstreet=?,mailingcountry=?,mailingzip=?,mailingpobox=?,mailingstate=?,othercountry=?,othercity=?,otherstate=?,otherzip=?,otherstreet=?,otherpobox=?  where contactaddressid in (select contactid from ".$table_prefix."_contactdetails where accountid=?)" ;
    $params = array($focus->column_fields['bill_city'], $focus->column_fields['bill_street'], $focus->column_fields['bill_country'], 
				$focus->column_fields['bill_code'], $focus->column_fields['bill_pobox'], $focus->column_fields['bill_state'], $focus->column_fields['ship_country'], 
				$focus->column_fields['ship_city'], $focus->column_fields['ship_state'], $focus->column_fields['ship_code'], $focus->column_fields['ship_street'], 
				$focus->column_fields['ship_pobox'], $focus->id);
	$adb->pquery($query, $params);
}
//Changing account address - Ends

$focus->save("Accounts");
if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] == "Campaigns")
{
	if(isset($_REQUEST['return_id']) && $_REQUEST['return_id'] != "")
	{
		$campAccStatusResult = $adb->pquery("select campaignrelstatusid from ".$table_prefix."_campaignaccountrel where campaignid=? AND accountid=?",array($_REQUEST['return_id'], $focus->id));
		$accountStatus = $adb->query_result($campAccStatusResult,0,'campaignrelstatusid');
		$sql = "delete from ".$table_prefix."_campaignaccountrel where accountid = ?";
		$adb->pquery($sql, array($focus->id));
		if(isset($accountStatus) && $accountStatus!=''){
			$sql = "insert into ".$table_prefix."_campaignaccountrel values (?,?,?)";
			$adb->pquery($sql, array($_REQUEST['return_id'], $focus->id,$accountStatus));
		}
		else{
			$sql = "insert into ".$table_prefix."_campaignaccountrel values (?,?,1)";
			$adb->pquery($sql, array($_REQUEST['return_id'], $focus->id));		
		}
	}
}
$return_id = $focus->id;

$parenttab = getParentTab();
if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] != "") $return_module = vtlib_purify($_REQUEST['return_module']);
else $return_module = "Accounts";
if(isset($_REQUEST['return_action']) && $_REQUEST['return_action'] != "") $return_action = vtlib_purify($_REQUEST['return_action']);
else $return_action = "DetailView";
if(isset($_REQUEST['return_id']) && $_REQUEST['return_id'] != "") $return_id = vtlib_purify($_REQUEST['return_id']);

//crmv@54375
if($_REQUEST['return2detail'] == 'yes') {
	$return_module = $currentModule;
	$return_action = 'DetailView';
	$return_id = $focus->id;
}
//crmv@54375e

$local_log->debug("Saved record with id of ".$return_id);

//code added for returning back to the current view after edit from list view
if($_REQUEST['return_viewname'] == '') $return_viewname='0';
if($_REQUEST['return_viewname'] != '') $return_viewname=vtlib_purify($_REQUEST['return_viewname']);

$url = "index.php?action=$return_action&module=$return_module&parenttab=$parenttab&record=$return_id&viewname=$return_viewname&start=".vtlib_purify($_REQUEST['pagenumber']).$search;

$from_module = vtlib_purify($_REQUEST['module']);
if (!empty($from_module)) $url .= "&from_module=$from_module";

header("Location: $url");

/** Function to save Accounts custom field info into database
* @param integer $entity_id - accountid
*/
function save_customfields($entity_id)
{
	global $log;
	$log->debug("Entering save_customfields(".$entity_id.") method ...");
	$log->info("save customfields invoked");
	global $adb;
	global $table_prefix;
	$dbquery = "SELECT * FROM customfields WHERE module = 'Accounts'";
	$result = $adb->pquery($dbquery, array());
	
	$custquery = "SELECT * FROM ".$table_prefix."_accountcf WHERE ".$table_prefix."_accountid = ?";
    $cust_result = $adb->pquery($custquery, array($entity_id));
	if($adb->num_rows($result) != 0)
	{
		
		$columns='';
		$values='';
		$ins_params = array();
		$update='';
		$upd_params = array();
                
        $noofrows = $adb->num_rows($result);
		for($i=0; $i<$noofrows; $i++)
		{
			$fldName=$adb->query_result($result,$i,"fieldlabel");
			$colName=$adb->query_result($result,$i,"column_name");
			if(isset($_REQUEST[$colName]))
			{
				$fldvalue=$_REQUEST[$colName];
				if(get_magic_quotes_gpc() == 1)
                {
                	$fldvalue = stripslashes($fldvalue);
                }
			}
			else
			{
				$fldvalue = '';
			}
			
            if(isset($_REQUEST['record']) && $_REQUEST['record'] != '' && $adb->num_rows($cust_result) !=0)
			{
				//Update Block
				if($i == 0)
				{
               		$update = $colName."=?";
					array_push($upd_params, $fldvalue);
				}
				else
				{
					$update .= ', '.$colName."=?";
					array_push($upd_params, $fldvalue);
				}
			}
			else
			{
				//Insert Block
				if($i == 0)
				{
					$columns='accountid, '.$colName;
					array_push($ins_params, $entity_id, $fldvalue);
				}
				else
				{
					$columns .= ', '.$colName;
					array_push($ins_params, $fldvalue);
				}
			}				
		}
        if(isset($_REQUEST['record']) && $_REQUEST['record'] != '' && $adb->num_rows($cust_result) !=0)
		{
			//Update Block
            $query = "UPDATE ".$table_prefix."_accountcf SET $update WHERE ".$table_prefix."_accountid=?";
			array_push($upd_params, $entity_id);			 
			$adb->pquery($query, $upd_params);
		}
		else
		{
			//Insert Block
			$query = "INSERT INTO ".$table_prefix."_accountcf ($columns) VALUES(" . generateQuestionMarks($ins_params) .")";
            $adb->pquery($query, $ins_params);
		}
		
	}
	$log->debug("Exiting save_customfields method ...");
}
?>