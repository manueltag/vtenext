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
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header$
 * Description:  Includes generic helper functions used throughout the application.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('include/utils/utils.php');
require_once('include/utils/RecurringType.php');
require_once('include/utils/EmailTemplate.php'); //crmv@16312
require_once('include/utils/crmv_utils.php');
require_once('include/QueryGenerator/QueryGenerator.php');
require_once('include/ListView/ListViewController.php');
require_once('include/utils/FileStorage.php'); // crmv@95157
require_once('vtlib/Vtecrm/Module.php'); // crmv@104568

/**
 * Check if user id belongs to a system admin.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 */
function is_admin($user) {
    global $log;
    $log->debug("Entering is_admin(".$user->user_name.") method ...");

    if ($user->is_admin == 'on')
    {
        $log->debug("Exiting is_admin method ...");
        return true;
    }
    else
    {
        $log->debug("Exiting is_admin method ...");
         return false;
    }
}

/**
 * THIS FUNCTION IS DEPRECATED AND SHOULD NOT BE USED; USE get_select_options_with_id()
 * Create HTML to display select options in a dropdown list.  To be used inside
 * of a select statement in a form.
 * param $option_list - the array of strings to that contains the option list
 * param $selected - the string which contains the default value
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 */
function get_select_options (&$option_list, $selected, $advsearch='false') {
    global $log;
    $log->debug("Entering get_select_options (".$option_list.",".$selected.",".$advsearch.") method ...");
    $log->debug("Exiting get_select_options  method ...");
    return get_select_options_with_id($option_list, $selected, $advsearch);
}

/**
 * Create HTML to display select options in a dropdown list.  To be used inside
 * of a select statement in a form.   This method expects the option list to have keys and values.  The keys are the ids.  The values is an array of the datas
 * param $option_list - the array of strings to that contains the option list
 * param $selected - the string which contains the default value
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 */
function get_select_options_with_id (&$option_list, $selected_key, $advsearch='false') {
	global $log;
	$log->debug("Entering get_select_options_with_id (".$option_list.",".$selected_key.",".$advsearch.") method ...");
	$log->debug("Exiting get_select_options_with_id  method ...");
	return get_select_options_with_id_separate_key($option_list, $option_list, $selected_key, $advsearch);
}
function get_select_options_with_value (&$option_list, $selected_key, $advsearch='false') {
	global $log;
	$log->debug("Entering get_select_options_with_id (".$option_list.",".$selected_key.",".$advsearch.") method ...");
	$log->debug("Exiting get_select_options_with_id  method ...");
	return get_select_options_with_value_separate_key($option_list, $option_list, $selected_key, $advsearch);
}
/**
 * Create HTML to display select options in a dropdown list.  To be used inside
 * of a select statement in a form.   This method expects the option list to have keys and values.  The keys are the ids.
 * The values are the display strings.
 */
function get_select_options_array (&$option_list, $selected_key, $advsearch='false') {
	global $log;
	$log->debug("Entering get_select_options_array (".$option_list.",".$selected_key.",".$advsearch.") method ...");
	$log->debug("Exiting get_select_options_array  method ...");
        return get_options_array_seperate_key($option_list, $option_list, $selected_key, $advsearch);
}

/**
 * Create HTML to display select options in a dropdown list.  To be used inside
 * of a select statement in a form.   This method expects the option list to have keys and values.  The keys are the ids.  The value is an array of data
 * param $label_list - the array of strings to that contains the option list
 * param $key_list - the array of strings to that contains the values list
 * param $selected - the string which contains the default value
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 */
function get_options_array_seperate_key (&$label_list, &$key_list, $selected_key, $advsearch='false') {
    global $log;
    $log->debug("Entering get_options_array_seperate_key (".$label_list.",".$key_list.",".$selected_key.",".$advsearch.") method ...");
    global $app_strings;
    if($advsearch=='true')
    $select_options = "\n<OPTION value=''>--NA--</OPTION>";
    else
    $select_options = "";

    //for setting null selection values to human readable --None--
    $pattern = "/'0?'></";
    $replacement = "''>".$app_strings['LBL_NONE']."<";
    if (!is_array($selected_key)) $selected_key = array($selected_key);

    //create the type dropdown domain and set the selected value if $opp value already exists
    foreach ($key_list as $option_key=>$option_value) {
        $selected_string = '';
        // the system is evaluating $selected_key == 0 || '' to true.  Be very careful when changing this.  Test all cases.
        // The vtiger_reported bug was only happening with one of the vtiger_users in the drop down.  It was being replaced by none.
        if (($option_key != '' && $selected_key == $option_key) || ($selected_key == '' && $option_key == '') || (in_array($option_key, $selected_key)))
        {
            $selected_string = 'selected';
        }

        $html_value = $option_key;

        $select_options .= "\n<OPTION ".$selected_string."value='$html_value'>$label_list[$option_key]</OPTION>";
        $options[$html_value]=array($label_list[$option_key]=>$selected_string);
    }
    $select_options = preg_replace($pattern, $replacement, $select_options);

    $log->debug("Exiting get_options_array_seperate_key  method ...");
    return $options;
}

/**
 * Create HTML to display select options in a dropdown list.  To be used inside
 * of a select statement in a form.   This method expects the option list to have keys and values.  The keys are the ids.
 * The values are the display strings.
 */

function get_select_options_with_id_separate_key(&$label_list, &$key_list, $selected_key, $advsearch='false')
{
    global $log;
    $log->debug("Entering get_select_options_with_id_separate_key(".$label_list.",".$key_list.",".$selected_key.",".$advsearch.") method ...");
    global $app_strings;
    if($advsearch=='true')
    $select_options = "\n<OPTION value=''>--NA--</OPTION>";
    else
    $select_options = "";

    $pattern = "/'0?'></";
    $replacement = "''>".$app_strings['LBL_NONE']."<";
    if (!is_array($selected_key)) $selected_key = array($selected_key);

    foreach ($key_list as $option_key=>$option_value) {
        $selected_string = '';
        if (($option_key != '' && $selected_key == $option_key) || ($selected_key == '' && $option_key == '') || (in_array($option_key, $selected_key)))
        {
            $selected_string = 'selected ';
        }

        $html_value = $option_key;

        $select_options .= "\n<OPTION ".$selected_string."value='$html_value'>$label_list[$option_key]</OPTION>";
    }
    $select_options = preg_replace($pattern, $replacement, $select_options);
    $log->debug("Exiting get_select_options_with_id_separate_key method ...");
    return $select_options;

}

function get_select_options_with_value_separate_key(&$label_list, &$key_list, $selected_key, $advsearch='false')
{
	global $log;
    $log->debug("Entering get_select_options_with_id_separate_key(".$label_list.",".$key_list.",".$selected_key.",".$advsearch.") method ...");
    global $app_strings;
    if($advsearch=='true')
    $select_options = "\n<OPTION value=''>--NA--</OPTION>";
    else
    $select_options = "";

    $pattern = "/'0?'></";
    $replacement = "''>".$app_strings['LBL_NONE']."<";
    if (!is_array($selected_key)) $selected_key = array($selected_key);

    foreach ($key_list as $option_key=>$option_value) {
        $selected_string = '';
        if (($option_key != '' && $selected_key == $option_key) || ($selected_key == '' && $option_key == '') || (in_array($option_key, $selected_key)))
        {
            $selected_string = 'selected ';
        }

        $html_value = $option_key;

        $select_options .= "\n<OPTION ".$selected_string."value='$label_list[$option_key]'>$label_list[$option_key]</OPTION>";
    }
    $select_options = preg_replace($pattern, $replacement, $select_options);
    $log->debug("Exiting get_select_options_with_id_separate_key method ...");
    return $select_options;

}

/**
 * Converts localized date format string to jscalendar format
 * Example: $array = array_csort($array,'town','age',SORT_DESC,'name');
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 */
function parse_calendardate($local_format) {
    global $log;
    $log->debug("Entering parse_calendardate(".$local_format.") method ...");
    global $current_user;

    if($current_user->date_format == 'dd-mm-yyyy')
    {
        $dt_popup_fmt = "%d-%m-%Y";
    }
    elseif($current_user->date_format == 'mm-dd-yyyy')
    {
        $dt_popup_fmt = "%m-%d-%Y";
    }
    elseif($current_user->date_format == 'yyyy-mm-dd')
    {
        $dt_popup_fmt = "%Y-%m-%d";
    }
    //ds@30
    elseif($current_user->date_format == 'mm.dd.yyyy')
    {
        $dt_popup_fmt = "%m.%d.%Y";
    }
    elseif($current_user->date_format == 'dd.mm.yyyy')
    {
        $dt_popup_fmt = "%d.%m.%y";
    }
    elseif($current_user->date_format == 'yyyy.mm.dd')
    {
        $dt_popup_fmt = "%Y.%m.%d";
    }
    elseif($current_user->date_format == 'mm/dd/yyyy')
    {
        $dt_popup_fmt = "%m/%d/%Y";
    }
    elseif($current_user->date_format == 'dd/mm/yyyy')
    {
        $dt_popup_fmt = "%d/%m/%y";
    }
    elseif($current_user->date_format == 'yyyy/mm/dd')
    {
        $dt_popup_fmt = "%Y/%m/%d";
    }
    //ds@30e
    $log->debug("Exiting parse_calendardate method ...");
    return $dt_popup_fmt;
}

/**
 * Decodes the given set of special character
 * input values $string - string to be converted, $encode - flag to decode
 * returns the decoded value in string fromat
 */

function from_html($string, $encode=true){
	global $log;
	//$log->debug("Entering from_html(".$string.",".$encode.") method ...");
        global $toHtml;
        //if($encode && is_string($string))$string = html_entity_decode($string, ENT_QUOTES);
	if(is_string($string)){
		if(preg_match('/(script).*(\/script)/i',$string))
			$string=preg_replace(array('/</', '/>/', '/"/'), array('&lt;', '&gt;', '&quot;'), $string);
		//$string = str_replace(array_values($toHtml), array_keys($toHtml), $string);
	}
	//$log->debug("Exiting from_html method ...");
        return $string;
}

function fck_from_html($string)
{
	if(is_string($string)){
		if(preg_match('/(script).*(\/script)/i',$string))
			$string=str_replace('script', '', $string);
	}
	return $string;
}

/**
 *	Function used to decodes the given single quote and double quote only. This function used for popup selection
 *	@param string $string - string to be converted, $encode - flag to decode
 *	@return string $string - the decoded value in string fromat where as only single and double quotes will be decoded
 */

function popup_from_html($string, $encode=true)
{
	global $log;
	$log->debug("Entering popup_from_html(".$string.",".$encode.") method ...");

	$popup_toHtml = array(
        			'"' => '&quot;',
			        "'" =>  '&#039;',
			     );

        //if($encode && is_string($string))$string = html_entity_decode($string, ENT_QUOTES);
        if($encode && is_string($string))
	{
                $string = addslashes(str_replace(array_values($popup_toHtml), array_keys($popup_toHtml), $string));
        }

	$log->debug("Exiting popup_from_html method ...");
        return $string;
}


/** To get the Currency of the specified user
  * @param $id -- The user Id:: Type integer
  * @returns  vtiger_currencyid :: Type integer
 */
function fetchCurrency($id)
{
	global $log;
	$log->debug("Entering fetchCurrency(".$id.") method ...");

	// Lookup the information in cache
	$currencyinfo = VTCacheUtils::lookupUserCurrenyId($id);

	if($currencyinfo === false) {
        global $adb, $table_prefix;
        $sql = "select currency_id from ".$table_prefix."_users where id=?";
        $result = $adb->pquery($sql, array($id));
        $currencyid=  $adb->query_result($result,0,"currency_id");

        VTCacheUtils::updateUserCurrencyId($id, $currencyid);

        // Re-look at the cache for consistency
        $currencyinfo = VTCacheUtils::lookupUserCurrenyId($id);
	}

	$currencyid = $currencyinfo['currencyid'];
	$log->debug("Exiting fetchCurrency method ...");
	return $currencyid;
}

/** Function to get the Currency name from the vtiger_currency_info
  * @param $currencyid -- vtiger_currencyid:: Type integer
  * @returns $currencyname -- Currency Name:: Type varchar
  *
 */
function getCurrencyName($currencyid, $show_symbol=true)
{
	global $log;
	$log->debug("Entering getCurrencyName(".$currencyid.") method ...");

	// Look at cache first
	$currencyinfo = VTCacheUtils::lookupCurrencyInfo($currencyid);

	if($currencyinfo === false) {
    	global $adb, $table_prefix;
    	$sql1 = "select * from ".$table_prefix."_currency_info where id= ?";
    	$result = $adb->pquery($sql1, array($currencyid));

    	$resultinfo = $adb->fetch_array($result);

    	// Update cache
    	VTCacheUtils::updateCurrencyInfo($currencyid,
    		$resultinfo['currency_name'], $resultinfo['currency_code'],
    		$resultinfo['currency_symbol'], $resultinfo['conversion_rate']
    	);

    	// Re-look at the cache now
    	$currencyinfo = VTCacheUtils::lookupCurrencyInfo($currencyid);
	}

	$currencyname = $currencyinfo['name'];
	$curr_symbol  = $currencyinfo['symbol'];

	$log->debug("Exiting getCurrencyName method ...");
	if($show_symbol) return getTranslatedCurrencyString($currencyname).' : '.$curr_symbol;
	else return $currencyname;
	// NOTE: Without symbol the value could be used for filtering/lookup hence avoiding the translation
}


/**
 * Function to fetch the list of vtiger_groups from group vtiger_table
 * Takes no value as input
 * returns the query result set object
 */

function get_group_options()
{
	global $log;
	$log->debug("Entering get_group_options() method ...");
	global $adb,$noof_group_rows, $table_prefix;
	$sql = "select groupname,groupid from ".$table_prefix."_groups";
	$result = $adb->pquery($sql, array());
	$noof_group_rows=$adb->num_rows($result);
	$log->debug("Exiting get_group_options method ...");
	return $result;
}

/**
 * Function to get the tabid
 * Takes the input as $module - module name
 * returns the tabid, integer type
 */

function getTabid($module)
{
	global $log;
	$log->debug("Entering getTabid(".$module.") method ...");

	// Lookup information in cache first
	$tabid = VTCacheUtils::lookupTabid($module);
	if($tabid === false) {

		if(file_exists('tabdata.php') && (filesize('tabdata.php') != 0)) {
			include('tabdata.php');
			$tabid= $tab_info_array[$module];

			// Update information to cache for re-use
			VTCacheUtils::updateTabidInfo($tabid, $module);

		} else {
	        $log->info("module  is ".$module);
    	    global $adb, $table_prefix;
			$sql = "select tabid from ".$table_prefix."_tab where name=?";
			$result = $adb->pquery($sql, array($module));
			$tabid=  $adb->query_result($result,0,"tabid");

			// Update information to cache for re-use
			VTCacheUtils::updateTabidInfo($tabid, $module);
		}
	}

	$log->debug("Exiting getTabid method ...");
	return $tabid;

}

/**
 * Function to get the fieldid
 *
 * @param Integer $tabid
 * @param Boolean $onlyactive
 */
function getFieldid($tabid, $fieldname, $onlyactive = true) {
	global $adb;

	// Look up information at cache first
	$fieldinfo = VTCacheUtils::lookupFieldInfo($tabid, $fieldname);
	if ($fieldinfo === false) {
		getColumnFields(getTabModuleName($tabid));
		$fieldinfo = VTCacheUtils::lookupFieldInfo($tabid, $fieldname);
	}

	// Get the field id based on required criteria
	$fieldid = false;

	if ($fieldinfo) {
		$fieldid = $fieldinfo['fieldid'];
		if ($onlyactive && !in_array($fieldinfo['presence'], array('0', '2'))) {
			$fieldid = false;
		}
	}
	return $fieldid;
}

/**
 * Function to get the CustomViewName
 * Takes the input as $cvid - customviewid
 * returns the cvname string fromat
 */

function getCVname($cvid)
{
        global $log;
        $log->debug("Entering getCVname method ...");

        global $adb, $table_prefix;
        $sql = "select viewname from ".$table_prefix."_customview where cvid=?";
        $result = $adb->pquery($sql, array($cvid));
        $cvname =  $adb->query_result($result,0,"viewname");

        $log->debug("Exiting getCVname method ...");
        return $cvname;

}



/**
 * Function to get the ownedby value for the specified module
 * Takes the input as $module - module name
 * returns the tabid, integer type
 */

function getTabOwnedBy($module)
{
	global $log;
	$log->debug("Entering getTabid(".$module.") method ...");

	$tabid=getTabid($module);

	if (file_exists('tabdata.php') && (filesize('tabdata.php') != 0))
	{
		include('tabdata.php');
		$tab_ownedby= $tab_ownedby_array[$tabid];
	}
	else
	{

        	$log->info("module  is ".$module);
        	global $adb, $table_prefix;
		$sql = "select ownedby from ".$table_prefix."_tab where name=?";
		$result = $adb->pquery($sql, array($module));
		$tab_ownedby=  $adb->query_result($result,0,"ownedby");
	}
	$log->debug("Exiting getTabid method ...");
	return $tab_ownedby;

}




/**
 * Function to get the tabid
 * Takes the input as $module - module name
 * returns the tabid, integer type
 */

function getSalesEntityType($crmid,$check_calendar_type=false)
{
	global $log, $adb, $table_prefix;
	$log->debug("Entering getSalesEntityType(".$crmid.") method ...");
	$log->info("in getSalesEntityType ".$crmid);
	//crmv@64475
	static $cache = array(); 
	if (!isset($cache[$crmid])) {
		$sql = "select setype from {$table_prefix}_crmentity where crmid=?";
		$result = $adb->pquery($sql, array($crmid));
		$parent_module = $cache[$crmid] = $adb->query_result($result,0,"setype");
	} else {
		$parent_module = $cache[$crmid];
	}
	//crmv@64475e
	if ($check_calendar_type && $parent_module == 'Calendar') {
		$activitytype = getActivityType($crmid);
		($activitytype == 'Task') ? $parent_module = 'Calendar' : $parent_module = 'Events';
	}
	$log->debug("Exiting getSalesEntityType method ...");
	return $parent_module;
}

/**
 * Function to get the AccountName when a vtiger_account id is given
 * Takes the input as $acount_id - vtiger_account id
 * returns the vtiger_account name in string format.
 */

function getAccountName($account_id)
{
	global $log;
	$log->debug("Entering getAccountName(".$account_id.") method ...");
	$log->info("in getAccountName ".$account_id);

	global $adb, $table_prefix;
	if($account_id != ''){
		$sql = "select accountname from ".$table_prefix."_account where accountid=?";
        $result = $adb->pquery($sql, array($account_id));
		$accountname = $adb->query_result($result,0,"accountname");
	}
	$log->debug("Exiting getAccountName method ...");
	return $accountname;
}

/**
 * Function to get the ProductName when a product id is given
 * Takes the input as $product_id - product id
 * returns the product name in string format.
 */

function getProductName($product_id)
{
	global $log;
	$log->debug("Entering getProductName(".$product_id.") method ...");

	$log->info("in getproductname ".$product_id);

	global $adb, $table_prefix;
	$sql = "select productname from ".$table_prefix."_products where productid=?";
        $result = $adb->pquery($sql, array($product_id));
	$productname = $adb->query_result($result,0,"productname");
	$log->debug("Exiting getProductName method ...");
	return $productname;
}

/**
 * Function to get the Potentail Name when a vtiger_potential id is given
 * Takes the input as $potential_id - vtiger_potential id
 * returns the vtiger_potential name in string format.
 */

function getPotentialName($potential_id)
{
	global $log;
	$log->debug("Entering getPotentialName(".$potential_id.") method ...");
	$log->info("in getPotentialName ".$potential_id);

	global $adb, $table_prefix;
	$potentialname = '';
	if($potential_id != '')
	{
		$sql = "select potentialname from ".$table_prefix."_potential where potentialid=?";
        $result = $adb->pquery($sql, array($potential_id));
		$potentialname = $adb->query_result($result,0,"potentialname");
	}
	$log->debug("Exiting getPotentialName method ...");
	return $potentialname;
}

/**
 * Function to get the Contact Name when a contact id is given
 * Takes the input as $contact_id - contact id
 * returns the Contact Name in string format.
 */

function getContactName($contact_id)
{
    global $log;
    $log->debug("Entering getContactName(".$contact_id.") method ...");
    $log->info("in getContactName ".$contact_id);

    global $adb, $current_user, $table_prefix;
    $contact_name = '';
    if($contact_id != '')
    {
            $sql = "select firstname,lastname from ".$table_prefix."_contactdetails where contactid=?"; // crmv@39110
            $result = $adb->pquery($sql, array($contact_id));
            $firstname = $adb->query_result($result,0,"firstname");
            $lastname = $adb->query_result($result,0,"lastname");
            $contact_name = $lastname;
            // Asha: Check added for ticket 4788
            if (getFieldVisibilityPermission("Contacts", $current_user->id,'firstname') == '0') {
                $contact_name .= ' '.$firstname;
            }
    }
    $log->debug("Exiting getContactName method ...");
        return $contact_name;
}

/**
 * Function to get the Contact Name when a contact id is given
 * Takes the input as $contact_id - contact id
 * returns the Contact Name in string format.
 */

function getLeadName($lead_id)
{
    global $log;
    $log->debug("Entering getLeadName(".$lead_id.") method ...");
    $log->info("in getLeadName ".$lead_id);

        global $adb, $current_user, $table_prefix;
    $lead_name = '';
    if($lead_id != '')
    {
            $sql = "select firstname,lastname from ".$table_prefix."_leaddetails where leadid=?"; // crmv@39110
            $result = $adb->pquery($sql, array($lead_id));
            $firstname = $adb->query_result($result,0,"firstname");
            $lastname = $adb->query_result($result,0,"lastname");
            $lead_name = $lastname;
            // Asha: Check added for ticket 4788
            if (getFieldVisibilityPermission("Leads", $current_user->id,'firstname') == '0') {
                $lead_name .= ' '.$firstname;
            }
    }
    $log->debug("Exiting getLeadName method ...");
        return $lead_name;
}

/**
 * Function to get the Full Name of a Contact/Lead when a query result and the row count are given
 * Takes the input as $result - Query Result, $row_count - Count of the Row, $module - module name
 * returns the Contact Name in string format.
 */

function getFullNameFromQResult($result, $row_count, $module)
{
    global $log, $adb, $current_user;
    $log->info("In getFullNameFromQResult(". print_r($result, true) . " - " . $row_count . "-".$module.") method ...");

    $rowdata = $adb->query_result_rowdata($result,$row_count);

    $name = '';
    if($rowdata != '' && count($rowdata) > 0)
    {
            $firstname = $rowdata["firstname"];
            $lastname = $rowdata["lastname"];
            $name = $lastname;
            // Asha: Check added for ticket 4788
            if (getFieldVisibilityPermission($module, $current_user->id,'firstname') == '0') {
                $name .= ' '.$firstname;
            }
    }
    $nam = textlength_check($name);

    return $nam;
}

/**
 * Function to get the Campaign Name when a campaign id is given
 * Takes the input as $campaign_id - campaign id
 * returns the Campaign Name in string format.
 */

function getCampaignName($campaign_id)
{
    global $log;
    $log->debug("Entering getCampaignName(".$campaign_id.") method ...");
    $log->info("in getCampaignName ".$campaign_id);

    global $adb, $table_prefix;
    $sql = "select campaignname from ".$table_prefix."_campaign where campaignid=?"; // crmv@39110
    $result = $adb->pquery($sql, array($campaign_id));
    $campaign_name = $adb->query_result($result,0,"campaignname");
    $log->debug("Exiting getCampaignName method ...");
    return $campaign_name;
}


/**
 * Function to get the Vendor Name when a vtiger_vendor id is given
 * Takes the input as $vendor_id - vtiger_vendor id
 * returns the Vendor Name in string format.
 */

function getVendorName($vendor_id)
{
    global $log;
    $log->debug("Entering getVendorName(".$vendor_id.") method ...");
    $log->info("in getVendorName ".$vendor_id);
        global $adb, $table_prefix;
        $sql = "select vendorname from ".$table_prefix."_vendor where vendorid=?"; // crmv@39110
        $result = $adb->pquery($sql, array($vendor_id));
        $vendor_name = $adb->query_result($result,0,"vendorname");
    $log->debug("Exiting getVendorName method ...");
        return $vendor_name;
}

/**
 * Function to get the Quote Name when a vtiger_vendor id is given
 * Takes the input as $quote_id - quote id
 * returns the Quote Name in string format.
 */

function getQuoteName($quote_id)
{
    global $log;
    $log->debug("Entering getQuoteName(".$quote_id.") method ...");
    $log->info("in getQuoteName ".$quote_id);
        global $adb, $table_prefix;
    if($quote_id != NULL && $quote_id != '')
    {
            $sql = "select subject from ".$table_prefix."_quotes where quoteid=?"; // crmv@39110
            $result = $adb->pquery($sql, array($quote_id));
            $quote_name = $adb->query_result($result,0,"subject");
    }
    else
    {
        $log->debug("Quote Id is empty.");
        $quote_name = '';
    }
    $log->debug("Exiting getQuoteName method ...");
        return $quote_name;
}

/**
 * Function to get the PriceBook Name when a vtiger_pricebook id is given
 * Takes the input as $pricebook_id - vtiger_pricebook id
 * returns the PriceBook Name in string format.
 */

function getPriceBookName($pricebookid)
{
    global $log;
    $log->debug("Entering getPriceBookName(".$pricebookid.") method ...");
    $log->info("in getPriceBookName ".$pricebookid);
        global $adb, $table_prefix;
        $sql = "select bookname from ".$table_prefix."_pricebook where pricebookid=?"; // crmv@39110
        $result = $adb->pquery($sql, array($pricebookid));
        $pricebook_name = $adb->query_result($result,0,"bookname");
    $log->debug("Exiting getPriceBookName method ...");
        return $pricebook_name;
}

/** This Function returns the  Purchase Order Name.
  * The following is the input parameter for the function
  *  $po_id --> Purchase Order Id, Type:Integer
  */
function getPoName($po_id)
{
    global $log;
    $log->debug("Entering getPoName(".$po_id.") method ...");
        $log->info("in getPoName ".$po_id);
        global $adb, $table_prefix;
        $sql = "select subject from ".$table_prefix."_purchaseorder where purchaseorderid=?"; // crmv@39110
        $result = $adb->pquery($sql, array($po_id));
        $po_name = $adb->query_result($result,0,"subject");
    $log->debug("Exiting getPoName method ...");
        return $po_name;
}
/**
 * Function to get the Sales Order Name when a vtiger_salesorder id is given
 * Takes the input as $salesorder_id - vtiger_salesorder id
 * returns the Salesorder Name in string format.
 */

function getSoName($so_id)
{
    global $log;
    $log->debug("Entering getSoName(".$so_id.") method ...");
    $log->info("in getSoName ".$so_id);
    global $adb, $table_prefix;
        $sql = "select subject from ".$table_prefix."_salesorder where salesorderid=?"; // crmv@39110
        $result = $adb->pquery($sql, array($so_id));
        $so_name = $adb->query_result($result,0,"subject");
    $log->debug("Exiting getSoName method ...");
        return $so_name;
}

/**
 * Function to get the Group Information for a given groupid
 * Takes the input $id - group id and $module - module name
 * returns the group information in an array format.
 */

function getGroupName($groupid)
{
	global $adb, $log, $table_prefix;
	$log->debug("Entering getGroupName(".$groupid.") method ...");
	$group_info = Array();
    $log->info("in getGroupName, entityid is ".$groupid);
    //crmv@60390
    static $groupnames_cache = Array();    
    if($groupid != '')
	{
		if (isset($groupnames_cache[$groupid])){
			return $groupnames_cache[$groupid];
		}		
		$sql = "select groupname,groupid from ".$table_prefix."_groups where groupid = ?";
		$result = $adb->pquery($sql, array($groupid));
        $group_info[] = decode_html($adb->query_result($result,0,"groupname"));
        $group_info[] = $adb->query_result($result,0,"groupid");
        $groupnames_cache[$groupid] = $group_info;
	}
	$log->debug("Exiting getGroupName method ...");
	//crmv@60390
   	return $group_info;
}
/**
 * Get the username by giving the user id.   This method expects the user id
 */
/* crmv@47905bis crmv@60390 */
function getUserName($userid,$with_name=false,$usernames_cache_input = Array())
{
    global $log;
    $log->debug("Entering getUserName(".$userid.") method ...");
    $log->info("in getUserName ".$userid);
    $focusUsers = CRMEntity::getInstance('Users');	//crmv@104988
    static $usernames_cache = Array();
    static $usernames_cache_withname = Array();    
    if (!empty($usernames_cache_input)){
    	$usernames_cache[$userid] = $usernames_cache_input['withoutname'];
    	$usernames_cache_withname[$userid] = $usernames_cache_input['withname'];
    }
	$user_name = '';
    if (file_exists('user_privileges/user_privileges_'.$userid.'.php')) require('user_privileges/user_privileges_'.$userid.'.php');
	if (!empty($user_info)) {
		$user_name = $focusUsers->formatUserName($userid, $user_info, $with_name);	//crmv@104988
	} elseif($userid != '') {
	    global $adb, $table_prefix;
    	if ($with_name) {
        	if (isset($usernames_cache_withname[$userid])){
    			return $usernames_cache_withname[$userid];
    		}    		
 	        $sql = "select user_name,last_name,first_name from ".$table_prefix."_users where id=?";
	        $result = $adb->pquery($sql, array($userid));
	        //crmv@104988
	        $user_info = array(
	        	'user_name'=>$adb->query_result($result,0,"user_name"),
	        	'first_name'=>$adb->query_result($result,0,"first_name"),
	        	'last_name'=>$adb->query_result($result,0,"last_name"),
	        );
			$user_name = $focusUsers->formatUserName($userid, $user_info, $with_name);
			//crmv@104988e
	        $usernames_cache_withname[$userid] = $user_name;
    	} else {
        	if (isset($usernames_cache[$userid])){
    			return $usernames_cache[$userid];
    		}    		
	        $sql = "select user_name from ".$table_prefix."_users where id=?";
	        $result = $adb->pquery($sql, array($userid));
	        $user_name = $adb->query_result($result,0,"user_name");
	        $usernames_cache[$userid] = $user_name;
    	}
    }
    $log->debug("Exiting getUserName method ...");
    return $user_name;
}

/**
* Get the user full name by giving the user id.   This method expects the user id
* DG 30 Aug 2006
*/
//crmv@47905bis crmv@90385
function getUserFullName($userid,$result=null)
{
	global $log, $adb, $table_prefix;
	$log->debug("Entering getUserFullName(".$userid.") method ...");
	$log->info("in getUserFullName ".$userid);
	if (!empty($result)) {
		$first_name = $adb->query_result($result,0,"first_name");
		$last_name = $adb->query_result($result,0,"last_name");
	} else {
		if (file_exists('user_privileges/user_privileges_'.$userid.'.php')) require('user_privileges/user_privileges_'.$userid.'.php');
		if (!empty($user_info)) {
			$first_name = $user_info['first_name'];
			$last_name = $user_info['last_name'];
		} elseif($userid != '') {
			$sql = "select first_name, last_name from ".$table_prefix."_users where id=?";
			$result = $adb->pquery($sql, array($userid));
			$first_name = $adb->query_result($result,0,"first_name");
			$last_name = $adb->query_result($result,0,"last_name");
		}
	}
	$log->debug("Exiting getUserFullName method ...");
	return trim($first_name.' '.$last_name);
}
//crmv@47905bise crmv@90385e

/** Fucntion to get related To name with id */
function getParentName($parent_id)
{
	global $adb, $table_prefix;
	if ($parent_id == 0)
		return "";
	$sql="select setype from ".$table_prefix."_crmentity where crmid=?";
	$result=$adb->pquery($sql,array($parent_id));
	//For now i have conditions only for accounts and contacts, if needed can add more
	if($adb->query_result($result,'setype') == 'Accounts')
		$sql1="select accountname name from ".$table_prefix."_account where accountid=?";
	else if($adb->query_result($result,'setype') == 'Contacts')
		$sql1="select concat( firstname, ' ', lastname ) name from ".$table_prefix."_contactdetails where contactid=?";
	$result1=$adb->pquery($sql1,array($parent_id));
	$asd=$adb->query_result($result1,'name');
	return $asd;
}

/**
 * Creates and returns database query. To be used for search and other text links.   This method expects the module object.
 * param $focus - the module object contains the column vtiger_fields
 */

function getURLstring($focus)
{
	global $log;
	$log->debug("Entering getURLstring(focus) method ...");
	$qry = "";
	foreach($focus->column_fields as $fldname=>$val)
	{
		if(isset($_REQUEST[$fldname]) && $_REQUEST[$fldname] != '')
		{
			if($qry == '')
			$qry = "&".$fldname."=".vtlib_purify($_REQUEST[$fldname]);
			else
			$qry .="&".$fldname."=".vtlib_purify($_REQUEST[$fldname]);
		}
	}
	if(isset($_REQUEST['current_user_only']) && $_REQUEST['current_user_only'] !='')
	{
		$qry .="&current_user_only=".vtlib_purify($_REQUEST['current_user_only']);
	}
	if(isset($_REQUEST['advanced']) && $_REQUEST['advanced'] =='true')
	{
		$qry .="&advanced=true";
	}

	if($qry !='')
	{
		$qry .="&query=true";
	}
	$log->debug("Exiting getURLstring method ...");
	return $qry;

}

/** This function returns the date in user specified format.
  * param $cur_date_val - the default date format
 */

//crmv@25432
function getValidDisplayDate($cur_date_val) {
	return getDisplayDate($cur_date_val);
}
//crmv@25432e

// crmv@30014
// NOTE: works only with ModComments module installed
function getFriendlyDate($lasttime, $nowtime = null) {
	if (is_null($nowtime)) $nowtime = time();
	$difference = $nowtime - strtotime($lasttime);
	$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
	$lengths = array("60", "60", "24", "7", "4.35", "12", "10");
	for($j = 0; $difference >= $lengths[$j] && $j < count($lengths); $j++) {
		if ($lengths[$j] != 0) $difference /= $lengths[$j];
	}
	$difference = round($difference);
	if($difference != 1) {
		$periods[$j].= "s";
	}
	if ($difference == 0 &&  $periods[$j] == 'seconds') {
		$text = getTranslatedString('lbl_now','ModComments');
	} else {
		$period = $difference.' '.getTranslatedString('lbl_'.$periods[$j],'ModComments');
		$text = sprintf(getTranslatedString('LBL_AGO','ModComments'), $period);
	}
	return $text;
}
// crmv@30014


function getDisplayDate($cur_date_val)
{
    global $log;
    $log->debug("Entering getDisplayDate(".$cur_date_val.") method ...");
    global $current_user;
    $dat_fmt = $current_user->date_format;
    if($dat_fmt == '')
    {
        $dat_fmt = 'dd-mm-yyyy';
    }
        $date_value = explode(' ',$cur_date_val);
        list($y,$m,$d) = explode('-',$date_value[0]);
        if($dat_fmt == 'dd-mm-yyyy')
        {
            $display_date = $d.'-'.$m.'-'.$y;
        }
        elseif($dat_fmt == 'mm-dd-yyyy')
        {

            $display_date = $m.'-'.$d.'-'.$y;
        }
        elseif($dat_fmt == 'yyyy-mm-dd')
        {

            $display_date = $y.'-'.$m.'-'.$d;
        }
        //ds@30
        elseif($dat_fmt == 'mm.dd.yyyy')
        {

            $display_date = $m.'.'.$d.'.'.$y;
        }
        elseif($dat_fmt == 'dd.mm.yyyy')
        {

            $display_date = $d.'.'.$m.'.'.$y;
        }
        elseif($dat_fmt == 'yyyy.mm.dd')
        {

            $display_date = $y.'.'.$m.'.'.$d;
        }


        elseif($dat_fmt == 'mm/dd/yyyy')
        {

            $display_date = $m.'/'.$d.'/'.$y;
        }
        elseif($dat_fmt == 'dd/mm/yyyy')
        {

            $display_date = $d.'/'.$m.'/'.$y;
        }
        elseif($dat_fmt == 'yyyy/mm/dd')
        {

            $display_date = $y.'/'.$m.'/'.$d;
        }
        //ds@30e

        if($date_value[1] != '')
        {
            $display_date = $display_date.' '.$date_value[1];
        }
    $log->debug("Exiting getDisplayDate method ...");
    return $display_date;

}

/** This function returns the date in user specified format.
  * Takes no param, receives the date format from current user object
  */

function getNewDisplayDate()
{
    global $log;
    $log->debug("Entering getNewDisplayDate() method ...");
        $log->info("in getNewDisplayDate ");

    global $current_user;
    $dat_fmt = $current_user->date_format;
    if($dat_fmt == '')
        {
                $dat_fmt = 'dd-mm-yyyy';
        }
    $display_date='';
    if($dat_fmt == 'dd-mm-yyyy')
    {
        $display_date = date('d-m-Y');
    }
    elseif($dat_fmt == 'mm-dd-yyyy')
    {
        $display_date = date('m-d-Y');
    }
    elseif($dat_fmt == 'yyyy-mm-dd')
    {
        $display_date = date('Y-m-d');
    }
    //ds@30
    elseif($dat_fmt == 'mm.dd.yyyy')
    {
        $display_date = date('m.d.Y');
    }
    elseif($dat_fmt == 'yyyy.mm.dd')
    {
        $display_date = date('Y.m.d');
    }
    elseif($dat_fmt == 'mm.dd.yyyy')
    {
        $display_date = date('m.d.Y');
    }


    elseif($dat_fmt == 'mm/dd/yyyy')
    {
        $display_date = date('m/d/Y');
    }
    elseif($dat_fmt == 'yyyy/mm/dd')
    {
        $display_date = date('Y/m/d');
    }
    elseif($dat_fmt == 'mm/dd/yyyy')
    {
        $display_date = date('m/d/Y');
    }

    //ds@30e

    $log->debug("Exiting getNewDisplayDate method ...");
    return $display_date;
}

/** This function returns the default vtiger_currency information.
  * Takes no param, return type array.
    */

function getDisplayCurrency()
{
    global $log;
    global $adb, $table_prefix;
    $log->debug("Entering getDisplayCurrency() method ...");
        $curr_array = Array();
        $sql1 = "select * from ".$table_prefix."_currency_info where currency_status=? and deleted=0";
        $result = $adb->pquery($sql1, array('Active'));
        $num_rows=$adb->num_rows($result);
        for($i=0; $i<$num_rows;$i++)
        {
                $curr_id = $adb->query_result($result,$i,"id");
                $curr_name = $adb->query_result($result,$i,"currency_name");
                $curr_symbol = $adb->query_result($result,$i,"currency_symbol");
                $curr_array[$curr_id] = $curr_name.' : '.$curr_symbol;
        }
    $log->debug("Exiting getDisplayCurrency method ...");
        return $curr_array;
}

/** This function returns the amount converted to dollar.
  * param $amount - amount to be converted.
    * param $crate - conversion rate.
      */

function convertToDollar($amount,$crate){
    global $log;
    $log->debug("Entering convertToDollar(".$amount.",".$crate.") method ...");
    $log->debug("Exiting convertToDollar method ...");
    	if ($crate == 0) $crate = 1;
        return $amount / $crate;
}

/** This function returns the amount converted from dollar.
  * param $amount - amount to be converted.
    * param $crate - conversion rate.
      */
function convertFromDollar($amount,$crate){
	global $log;
	$log->debug("Entering convertFromDollar(".$amount.",".$crate.") method ...");
	$log->debug("Exiting convertFromDollar method ...");
	return round($amount * $crate, 2);
}

/** This function returns the amount converted from master currency.
  * param $amount - amount to be converted.
  * param $crate - conversion rate.
  */
function convertFromMasterCurrency($amount,$crate){
    global $log;
    $log->debug("Entering convertFromDollar(".$amount.",".$crate.") method ...");
    $log->debug("Exiting convertFromDollar method ...");
        return $amount * $crate;
}

/** This function returns the conversion rate and vtiger_currency symbol
  * in array format for a given id.
  * param $id - vtiger_currency id.
  */

function getCurrencySymbolandCRate($id)
{
	global $log;
	$log->debug("Entering getCurrencySymbolandCRate(".$id.") method ...");

	// To initialize the currency information in cache
	getCurrencyName($id);

	$currencyinfo = VTCacheUtils::lookupCurrencyInfo($id);

	$rate_symbol['rate']  = $currencyinfo['rate'];
	$rate_symbol['symbol']= $currencyinfo['symbol'];

	$log->debug("Exiting getCurrencySymbolandCRate method ...");
	return $rate_symbol;
}

/** This function returns the terms and condition from the database.
  * Takes no param and the return type is text.
  */

function getTermsandConditions()
{
    global $log;
    $log->debug("Entering getTermsandConditions() method ...");
        global $adb, $table_prefix;
        $sql1 = "select * from ".$table_prefix."_inventory_tandc";
        $result = $adb->pquery($sql1, array());
        $tandc = $adb->query_result($result,0,"tandc");
    $log->debug("Exiting getTermsandConditions method ...");
        return $tandc;
}

/**
 * Create select options in a dropdown list.  To be used inside
  *  a reminder select statement in a vtiger_activity form.
   * param $start - start value
   * param $end - end value
   * param $fldname - vtiger_field name
   * param $selvalue - selected value
   */

function getReminderSelectOption($start,$end,$fldname,$selvalue='')
{
    global $log;
    $log->debug("Entering getReminderSelectOption(".$start.",".$end.",".$fldname.",".$selvalue=''.") method ...");
    global $mod_strings;
    global $app_strings;

    $def_sel ="";
    $OPTION_FLD = "<SELECT name=".$fldname.">";
    for($i=$start;$i<=$end;$i++)
    {
        if($i==$selvalue)
        $def_sel = "SELECTED";
        $OPTION_FLD .= "<OPTION VALUE=".$i." ".$def_sel.">".$i."</OPTION>\n";
        $def_sel = "";
    }
    $OPTION_FLD .="</SELECT>";
    $log->debug("Exiting getReminderSelectOption method ...");
    return $OPTION_FLD;
}

/** This function returns the List price of a given product in a given price book.
  * param $productid - product id.
  * param $pbid - vtiger_pricebook id.
  */

function getListPrice($productid,$pbid)
{
    global $log;
    $log->debug("Entering getListPrice(".$productid.",".$pbid.") method ...");
        $log->info("in getListPrice productid ".$productid);

    global $adb, $table_prefix;
    $query = "select listprice from ".$table_prefix."_pricebookproductrel where pricebookid=? and productid=?";
    $result = $adb->pquery($query, array($pbid, $productid));
    $lp = $adb->query_result($result,0,'listprice');
    $log->debug("Exiting getListPrice method ...");
    return $lp;
}

/** This function returns a string with removed new line character, single quote, and back slash double quoute.
  * param $str - string to be converted.
  */

function br2nl($str) {
   global $log;
   $log->debug("Entering br2nl(".$str.") method ...");
   $str = preg_replace("/(\r\n)/", "\\r\\n", $str);
   $str = preg_replace("/'/", " ", $str);
   $str = preg_replace("/\"/", " ", $str);
   $log->debug("Exiting br2nl method ...");
   return $str;
}

/** This function returns a text, which escapes the html encode for link tag/ a href tag
*param $text - string/text
*/

function make_clickable($text)
{
   global $log;
   $log->debug("Entering make_clickable(".$text.") method ...");
   $text = preg_replace('#(script|about|applet|activex|chrome):#is', "\\1&#058;", $text);
   // pad it with a space so we can match things at the start of the 1st line.
   $ret = ' ' . $text;

   // matches an "xxxx://yyyy" URL at the start of a line, or after a space.
   // xxxx can only be alpha characters.
   // yyyy is anything up to the first space, newline, comma, double quote or <
   $ret = preg_replace("#(^|[\n ])([\w]+?://.*?[^ \"\n\r\t<]*)#is", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $ret);

   // matches a "www|ftp.xxxx.yyyy[/zzzz]" kinda lazy URL thing
   // Must contain at least 2 dots. xxxx contains either alphanum, or "-"
   // zzzz is optional.. will contain everything up to the first space, newline,
   // comma, double quote or <.
   $ret = preg_replace("#(^|[\n ])((www|ftp)\.[\w\-]+\.[\w\-.\~]+(?:/[^ \"\t\n\r<]*)?)#is", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $ret);

   // matches an email@domain type address at the start of a line, or after a space.
   // Note: Only the followed chars are valid; alphanums, "-", "_" and or ".".
   $ret = preg_replace("#(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $ret);

   // Remove our padding..
   $ret = substr($ret, 1);

   //remove comma, fullstop at the end of url
   $ret = preg_replace("#,\"|\.\"|\)\"|\)\.\"|\.\)\"#", "\"", $ret);

   $log->debug("Exiting make_clickable method ...");
   return($ret);
}

// crmv@104568
function getDefaultPanelId($module) {
	global $adb, $table_prefix;
	
	$panelid = false;
	$res = $adb->limitPquery(
		"SELECT panelid 
		FROM {$table_prefix}_panels 
		INNER JOIN {$table_prefix}_tab ON {$table_prefix}_tab.tabid = {$table_prefix}_panels.tabid 
		WHERE {$table_prefix}_tab.name = ? AND {$table_prefix}_panels.visible = 0
		ORDER BY {$table_prefix}_panels.sequence ASC",
		0,1,
		array($module)
	);
	
	if ($res && $adb->num_rows($res) > 0) {
		$panelid = (int)$adb->query_result_no_html($res,0, 'panelid');
	}
	
	return $panelid;
}

function getCurrentPanelId($module) {
	return getDefaultPanelId($module);
}

function getPanelsAndBlocks($module, $record='') {	//crmv@105937
	$pb = array();
	$modInst = Vtecrm_Module::getInstance($module);
	$panels = Vtecrm_Panel::getAllForModule($modInst);
	if (is_array($panels)) {
		foreach ($panels as $panInst) {
			$blockids = $relids = array();
			$blocks = Vtecrm_Block::getAllForTab($panInst);
			if (is_array($blocks)) {
				foreach ($blocks as $b) {
					$blockids[] = (int)$b->id;
				}
			}
			$rels = $panInst->getRelatedLists();
			if (is_array($rels)) {
				foreach ($rels as $rel) {
					$relids[] = (int)$rel['id'];
				}
			}
			$pb[$panInst->id] = array(
				'panelid' => (int)$panInst->id,
				'label' => getTranslatedString($panInst->label),
				'blockids' => $blockids,
				'relatedids' => $relids,
			);
		}
		//crmv@105937
		if ($module == 'Processes') {
			$dynaform_blocks = array();
			$elementid = '';
			$focus = CRMEntity::getInstance($module);
			$focus->retrieve_entity_info_no_html($record,$module);
			require_once('modules/Settings/ProcessMaker/ProcessDynaForm.php');
			$processDynaFormObj = ProcessDynaForm::getInstance();
			$blocks = $processDynaFormObj->getCurrentDynaForm($focus,$elementid);
			if (!empty($blocks)) {
				foreach($blocks as $blockid => $block) {
					if (!empty($block['fields'])) {
						$dynaform_blocks[] = 'DF'.$blockid;
					}
				}
				$pb[getCurrentPanelId($module)]['blockids'] = array_merge($pb[getCurrentPanelId($module)]['blockids'],$dynaform_blocks);
			}
		}
		//crmv@105937e
	}
	return $pb;
}


/**
 * This function returns the vtiger_blocks and its related information for given module.
 * Input Parameter are $module - module name, $disp_view = display view (edit,detail or create),$mode - edit, $col_fields - * column vtiger_fields/
 * This function returns an array
 */
function getBlocks($module,$disp_view,$mode,$col_fields='',$info_type='',&$blockVisibility='')	//crmv@99316
{
	global $log;
	$log->debug("Entering getBlocks(".$module.",".$disp_view.",".$mode.",".$col_fields.",".$info_type.") method ...");
	global $adb,$current_user, $table_prefix;
	global $mod_strings;
	
	$tabid = getTabid($module);
	
	$block_detail = Array();

	$query = 
		"SELECT blockid, blocklabel, show_title, display_status, panelid
		FROM ".$table_prefix."_blocks 
		WHERE tabid=? and $disp_view=0 and visible = 0
		ORDER BY sequence";
	$result = $adb->pquery($query, array($tabid));
	$noofrows = $adb->num_rows($result);

	$blockid_list = array();
	$blockdata = array();
	for($i=0; $i<$noofrows; $i++)
	{
		$blockid = $adb->query_result_no_html($result,$i,"blockid");
		$blockid_list[] = $blockid; 
		$blockdata[$blockid] = array(
			'label' => $adb->query_result($result,$i,"blocklabel"),
			'panelid' => $adb->query_result_no_html($result,$i,"panelid"),
		);

		$sLabelVal = getTranslatedString($blockdata[$blockid]['label'], $module);
		$aBlockStatus[$sLabelVal] = $adb->query_result($result,$i,"display_status");
	}
	//crmv@113775
	if (in_array($_REQUEST['file'],array('ProcessMaker/actions/UpdateForm','ProcessMaker/actions/CreateForm'))) {
		// show all fields
		$display_type_check = $table_prefix.'_field.displaytype in (1,2,3,4)';
	//crmv@113775e
	}elseif($mode == 'edit')
	{
		$display_type_check = $table_prefix.'_field.displaytype = 1';
	}elseif($mode == 'mass_edit')
	{
		$display_type_check = $table_prefix.'_field.displaytype = 1 AND '.$table_prefix.'_field.masseditable NOT IN (0,2)';
	}else
	{
		$display_type_check = $table_prefix.'_field.displaytype in (1,4)';
	}

	/*if($non_mass_edit_fields!='' && sizeof($non_mass_edit_fields)!=0){
		$mass_edit_query = "AND ".$table_prefix."_field.fieldname NOT IN (". generateQuestionMarks($non_mass_edit_fields) .")";
	}*/

	//retreive the vtiger_profileList from database
	require('user_privileges/requireUserPrivileges.php'); // crmv@39110
	//crmv@49510
	$profileList = getCurrentUserProfileList();
	$col = 'mandatory';
  	$adb->format_columns($col);
	if($disp_view == "detail_view")
	{
        //crmv@7216+7217
        if($is_admin==true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2]== 0 || $module == 'Users' || $module == "Emails" || $module == "Fax" || $module == "Sms")
        {
  		//crmv@7216e
 			$sql = "SELECT {$table_prefix}_field.fieldid, MIN({$table_prefix}_profile2field.$col) AS \"mandatory\"
 					FROM {$table_prefix}_field
 					LEFT JOIN {$table_prefix}_profile2field ON ({$table_prefix}_profile2field.fieldid = {$table_prefix}_field.fieldid AND {$table_prefix}_profile2field.profileid IN (".generateQuestionMarks($profileList)."))
 					WHERE {$table_prefix}_field.tabid=? AND {$table_prefix}_field.block IN (". generateQuestionMarks($blockid_list) .") AND {$table_prefix}_field.displaytype IN (1,2,4) and {$table_prefix}_field.presence in (0,2)
 					GROUP BY {$table_prefix}_field.fieldid";
  			$params = array($profileList, $tabid, $blockid_list);
		}
  		else
  		{
				
			//crmv@60969
 			$sql = "SELECT {$table_prefix}_field.fieldid, MIN({$table_prefix}_profile2field.$col) AS \"mandatory\"
 					FROM {$table_prefix}_field
 					INNER JOIN {$table_prefix}_def_org_field ON {$table_prefix}_def_org_field.fieldid={$table_prefix}_field.fieldid
 					INNER JOIN {$table_prefix}_profile2field ON ({$table_prefix}_profile2field.fieldid = {$table_prefix}_field.fieldid AND {$table_prefix}_profile2field.profileid IN (".generateQuestionMarks($profileList)."))
 					INNER JOIN {$table_prefix}_profile2tab ON  {$table_prefix}_profile2tab.tabid = {$table_prefix}_field.tabid AND {$table_prefix}_profile2tab.permissions = 0 AND {$table_prefix}_profile2field.profileid = {$table_prefix}_profile2tab.profileid  					
	 				WHERE {$table_prefix}_field.tabid=? AND {$table_prefix}_field.block IN (". generateQuestionMarks($blockid_list) .") AND {$table_prefix}_field.displaytype IN (1,2,4) and {$table_prefix}_field.presence in (0,2) AND {$table_prefix}_def_org_field.visible=0 AND {$table_prefix}_profile2field.visible = 0
	 				GROUP BY {$table_prefix}_field.fieldid";
 			$params = array($profileList, $tabid, $blockid_list);
 			//crmv@60969e
  		}
  		//crmv@56398
  		$sql = "SELECT {$table_prefix}_field.*, CASE WHEN (q.$col is not null) THEN q.$col ELSE 1 END AS \"mandatory\" 
			FROM ($sql) q 
			INNER JOIN {$table_prefix}_field ON {$table_prefix}_field.fieldid = q.fieldid 
			INNER JOIN {$table_prefix}_blocks ON {$table_prefix}_field.block = {$table_prefix}_blocks.blockid 
			ORDER BY {$table_prefix}_blocks.sequence,{$table_prefix}_field.sequence";
		$result = $adb->pquery($sql, $params);
		//crmv@56398e

		// Added to unset the previous record's related listview session values
		if(isset($_SESSION['rlvs']))
			unset($_SESSION['rlvs']);

		$getBlockInfo=getDetailBlockInformation($mode,$module,$result,$col_fields,$tabid,$blockdata,$aBlockStatus);	//crmv@96450
	}
	else
	{
		if ($info_type != '')
		{
			if($is_admin==true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2]== 0 || $module == 'Users' || $module == "Emails")
  			{
 				$sql = "SELECT {$table_prefix}_field.fieldid, MIN({$table_prefix}_profile2field.$col) AS \"mandatory\"
 						FROM {$table_prefix}_field
 						LEFT JOIN {$table_prefix}_profile2field ON ({$table_prefix}_profile2field.fieldid = {$table_prefix}_field.fieldid AND {$table_prefix}_profile2field.profileid IN (".generateQuestionMarks($profileList)."))
 						WHERE {$table_prefix}_field.tabid=? AND {$table_prefix}_field.block IN (". generateQuestionMarks($blockid_list) .") AND $display_type_check AND info_type = ? and {$table_prefix}_field.presence in (0,2)
 						GROUP BY {$table_prefix}_field.fieldid";
  				$params = array($profileList, $tabid, $blockid_list, $info_type);
			}
  			else
  			{
 				//crmv@60969
				$sql = "SELECT {$table_prefix}_field.fieldid, MIN({$table_prefix}_profile2field.$col) AS \"mandatory\"
 					FROM {$table_prefix}_field
 					INNER JOIN {$table_prefix}_def_org_field ON {$table_prefix}_def_org_field.fieldid={$table_prefix}_field.fieldid
 					INNER JOIN {$table_prefix}_profile2field ON ({$table_prefix}_profile2field.fieldid = {$table_prefix}_field.fieldid AND {$table_prefix}_profile2field.profileid IN (".generateQuestionMarks($profileList)."))
 					INNER JOIN {$table_prefix}_profile2tab ON  {$table_prefix}_profile2tab.tabid = {$table_prefix}_field.tabid AND {$table_prefix}_profile2tab.permissions = 0 AND {$table_prefix}_profile2field.profileid = {$table_prefix}_profile2tab.profileid  					
	 				WHERE {$table_prefix}_field.tabid=? AND {$table_prefix}_field.block IN (". generateQuestionMarks($blockid_list) .") AND $display_type_check AND info_type = ? and {$table_prefix}_field.presence in (0,2) AND {$table_prefix}_def_org_field.visible=0 AND {$table_prefix}_profile2field.visible = 0
	 				GROUP BY {$table_prefix}_field.fieldid";
				$params = array($profileList, $tabid, $blockid_list, $info_type);
				//crmv@60969e
  			}
		}
		else
		{
			if($is_admin==true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0 || $module == 'Users' || $module == "Emails")
  			{
 				$sql = "SELECT {$table_prefix}_field.fieldid, MIN({$table_prefix}_profile2field.$col) AS \"mandatory\"
 						FROM {$table_prefix}_field
 						LEFT JOIN {$table_prefix}_profile2field ON ({$table_prefix}_profile2field.fieldid = {$table_prefix}_field.fieldid AND {$table_prefix}_profile2field.profileid IN (".generateQuestionMarks($profileList)."))
 						WHERE {$table_prefix}_field.tabid=? AND {$table_prefix}_field.block IN (". generateQuestionMarks($blockid_list).") AND $display_type_check  and {$table_prefix}_field.presence in (0,2)
 						GROUP BY {$table_prefix}_field.fieldid";
				$params = array($profileList, $tabid, $blockid_list);
			}
  			else
  			{
 				//crmv@60969
 				$sql = "SELECT {$table_prefix}_field.fieldid, MIN({$table_prefix}_profile2field.$col) AS \"mandatory\"
 						FROM {$table_prefix}_field
 						INNER JOIN {$table_prefix}_def_org_field ON {$table_prefix}_def_org_field.fieldid={$table_prefix}_field.fieldid
 						INNER JOIN {$table_prefix}_profile2field ON ({$table_prefix}_profile2field.fieldid = {$table_prefix}_field.fieldid AND {$table_prefix}_profile2field.profileid IN (".generateQuestionMarks($profileList)."))
 						INNER JOIN {$table_prefix}_profile2tab ON  {$table_prefix}_profile2tab.tabid = {$table_prefix}_field.tabid AND {$table_prefix}_profile2tab.permissions = 0 AND {$table_prefix}_profile2field.profileid = {$table_prefix}_profile2tab.profileid  						
 						WHERE {$table_prefix}_field.tabid=? AND {$table_prefix}_field.block IN (".generateQuestionMarks($blockid_list).") AND $display_type_check AND {$table_prefix}_def_org_field.visible=0 and {$table_prefix}_field.presence in (0,2) AND {$table_prefix}_profile2field.visible = 0
 						GROUP BY {$table_prefix}_field.fieldid";
				$params = array($profileList, $tabid, $blockid_list);
				//crmv@60969 e
  			}
		}
		$sql = "SELECT {$table_prefix}_field.*, CASE WHEN (q.$col is not null) THEN q.$col ELSE 1 END AS \"mandatory\"
				FROM ($sql) q
				INNER JOIN {$table_prefix}_field ON {$table_prefix}_field.fieldid = q.fieldid
				ORDER BY block,sequence";
		$result = $adb->pquery($sql, $params);

		$getBlockInfo=getBlockInformation($module,$result,$col_fields,$tabid,$blockdata,$mode,$aBlockStatus,$blockVisibility);	//crmv@96450 crmv@99316
	}
	//crmv@49510e
	$log->debug("Exiting getBlocks method ...");
	if(count($getBlockInfo) > 0)
	{
		foreach($getBlockInfo as $label=>$contents)
		{
			if(empty($getBlockInfo[$label]))
			{
				unset($getBlockInfo[$label]);
			}
		}
	}
	//ds@8 project tool	//crmv@14132
	if(isModuleInstalled('Projects') && vtlib_isModuleActive('Projects') && $module == 'HelpDesk' && $disp_view != 'detail_view'){
		$tmp_row = count($getBlockInfo[$mod_strings['LBL_TICKET_INFORMATION']])-1;
		if (empty($getBlockInfo[$mod_strings['LBL_TICKET_INFORMATION']][$tmp_row][1])) {
			$project_row = $tmp_row;
			$project_column = 1;
		}
		else {
			$project_row = $tmp_row+1;
			$project_column = 0;
		}
		$getBlockInfo[$mod_strings['LBL_TICKET_INFORMATION']][$project_row][$project_column][0]=Array(25);
		$getBlockInfo[$mod_strings['LBL_TICKET_INFORMATION']][$project_row][$project_column][1]=Array(getTranslatedString('Projects','Projects'));
		$getBlockInfo[$mod_strings['LBL_TICKET_INFORMATION']][$project_row][$project_column][2]=Array('projects');
		if($disp_view == 'create_view'){
			$getBlockInfo[$mod_strings['LBL_TICKET_INFORMATION']][$project_row][$project_column][3]=Array();
		} else {
			global $projects_names;
			$getBlockInfo[$mod_strings['LBL_TICKET_INFORMATION']][$project_row][$project_column][3]=Array($projects_names);
		}
		$getBlockInfo[$mod_strings['LBL_TICKET_INFORMATION']][$project_row][$project_column][4]=Array(10);
	}
	//ds@8e project tool	//crmv@14132e
	$_SESSION['BLOCKINITIALSTATUS'] = $aBlockStatus;
	return $getBlockInfo;
}
// crmv@104568

/**
 * This function is used to get the display type.
 * Takes the input parameter as $mode - edit  (mostly)
 * This returns string type value
 */
function getView($mode)
{
    global $log;
    $log->debug("Entering getView(".$mode.") method ...");
        if($mode=="edit")
            $disp_view = "edit_view";
        else
            $disp_view = "create_view";
    $log->debug("Exiting getView method ...");
        return $disp_view;
}
/**
 * This function is used to get the blockid of the customblock for a given module.
 * Takes the input parameter as $tabid - module tabid and $label - custom label
 * This returns string type value
 */

function getBlockId($tabid,$label)
{
    global $log;
    $log->debug("Entering getBlockId(".$tabid.",".$label.") method ...");
        global $adb, $table_prefix;
        $blockid = '';
        $query = "select blockid from ".$table_prefix."_blocks where tabid=? and blocklabel = ?";
        $result = $adb->pquery($query, array($tabid, $label));
        $noofrows = $adb->num_rows($result);
        if($noofrows == 1)
        {
                $blockid = $adb->query_result($result,0,"blockid");
        }
    $log->debug("Exiting getBlockId method ...");
        return $blockid;
}

/**
 * This function is used to get the Parent and Child vtiger_tab relation array.
 * Takes no parameter and get the data from parent_tabdata.php and vtiger_tabdata.php
 * This returns array type value
 */
//crmv@9587
function getHeaderArray()
{
    global $log;
    $log->debug("Entering getHeaderArray() method ...");
    global $adb;
    global $current_user;
    require('user_privileges/requireUserPrivileges.php'); // crmv@39110
    include('parent_tabdata.php');
    include('tabdata.php');
	$id = get_hidden_parenttab_array();
    $noofrows = count($parent_tab_info_array);
    foreach($parent_tab_info_array as $parid=>$parval)
    {
        //ds@23
        if($id[$parid] == 1 && $is_admin != true)
            continue;
        //ds@23e

        $subtabs = Array();
        $tablist=$parent_child_tab_rel_array[$parid];
        $noofsubtabs = count($tablist);

        foreach($tablist as $childTabId)
        {
            $module = array_search($childTabId,$tab_info_array);

            if($is_admin)
            {
                $subtabs[] = $module;
            }
            elseif($profileGlobalPermission[2]==0 ||$profileGlobalPermission[1]==0 || $profileTabsPermission[$childTabId]==0)
            {
                $subtabs[] = $module;
            }
        }

        $parenttab = getParentTabName($parid);

        if($parenttab != 'Settings' ||($parenttab == 'Settings' && $is_admin))
        {
            if(!empty($subtabs))
                $relatedtabs[$parenttab] = $subtabs;
        }
    }
    $log->debug("Exiting getHeaderArray method ...");
    return $relatedtabs;
}
//crmv@9587 e
/**
 * This function is used to get the Parent Tab name for a given parent vtiger_tab id.
 * Takes the input parameter as $parenttabid - Parent vtiger_tab id
 * This returns value string type
 */

function getParentTabName($parenttabid)
{
    global $log;
    $log->debug("Entering getParentTabName(".$parenttabid.") method ...");
    global $adb, $table_prefix;
    if (file_exists('parent_tabdata.php') && (filesize('parent_tabdata.php') != 0))
    {
        include('parent_tabdata.php');
        $parent_tabname= $parent_tab_info_array[$parenttabid];
    }
    else
    {
        $sql = "select parenttab_label from ".$table_prefix."_parenttab where parenttabid=?";
        $result = $adb->pquery($sql, array($parenttabid));
        $parent_tabname=  $adb->query_result($result,0,"parenttab_label");
    }
    $log->debug("Exiting getParentTabName method ...");
    return $parent_tabname;
}

/**
 * This function is used to get the Parent Tab name for a given module.
 * Takes the input parameter as $module - module name
 * This returns value string type
 */


function getParentTabFromModule($module)
{
    global $log;
    $log->debug("Entering getParentTabFromModule(".$module.") method ...");
    global $adb, $table_prefix;
    if (file_exists('tabdata.php') && (filesize('tabdata.php') != 0) && file_exists('parent_tabdata.php') && (filesize('parent_tabdata.php') != 0))
    {
        include('tabdata.php');
        include('parent_tabdata.php');
        $tabid=$tab_info_array[$module];
        foreach($parent_child_tab_rel_array as $parid=>$childArr)
        {
            if(in_array($tabid,$childArr))
            {
                $parent_tabname= $parent_tab_info_array[$parid];
                break;
            }
        }
        $log->debug("Exiting getParentTabFromModule method ...");
        return $parent_tabname;
    }
    else
    {
        $sql = "select ".$table_prefix."_parenttab.* from ".$table_prefix."_parenttab inner join ".$table_prefix."_parenttabrel on ".$table_prefix."_parenttabrel.parenttabid=".$table_prefix."_parenttab.parenttabid inner join ".$table_prefix."_tab on ".$table_prefix."_tab.tabid=".$table_prefix."_parenttabrel.tabid where ".$table_prefix."_tab.name=?";
        $result = $adb->pquery($sql, array($module));
        $tab =  $adb->query_result($result,0,"parenttab_label");
        $log->debug("Exiting getParentTabFromModule method ...");
        return $tab;
    }
}

/**
 * This function is used to get the Parent Tab name for a given module.
 * Takes no parameter but gets the vtiger_parenttab value from form request
 * This returns value string type
 */
//crmv@16312
function getParentTab() {
    global $log, $default_charset;
    $log->debug("Entering getParentTab() method ...");
    if(!empty($_REQUEST['parenttab'])) {
		$log->debug("Exiting getParentTab method ...");
        if(checkParentTabExists($_REQUEST['parenttab'])) {
		return vtlib_purify($_REQUEST['parenttab']);
        } else {
            return getParentTabFromModule($_REQUEST['module']);
        }
    } else {
		$log->debug("Exiting getParentTab method ...");
		return getParentTabFromModule($_REQUEST['module']);
    }
}

function checkParentTabExists($parenttab) {
    global $adb, $table_prefix;

    if (file_exists('parent_tabdata.php') && (filesize('parent_tabdata.php') != 0)) {
        include('parent_tabdata.php');
        if(in_array($parenttab,$parent_tab_info_array))
            return true;
        else
            return false;
    } else {

        $result = "select 1 from ".$table_prefix."_parenttab where parenttab_label = ?";
        $noofrows = $adb->num_rows($result);
        if($noofrows > 0)
            return true;
        else
            return false;
    }

}

//crmv@16312 end
/**
 * This function is used to get the days in between the current time and the modified time of an entity .
 * Takes the input parameter as $id - crmid  it will calculate the number of days in between the
 * the current time and the modified time from the vtiger_crmentity vtiger_table and return the result as a string.
 * The return format is updated <No of Days> day ago <(date when updated)>
 */

function updateInfo($id)
{
    global $log;
    $log->debug("Entering updateInfo(".$id.") method ...");

    global $adb, $table_prefix;
    global $app_strings;
    $query='select modifiedtime from '.$table_prefix.'_crmentity where crmid = ?';
    $result = $adb->pquery($query, array($id));
    $modifiedtime = $adb->query_result($result,0,'modifiedtime');
    $values=explode(' ',$modifiedtime);
    $date_info=explode('-',$values[0]);
    $time_info=explode(':',$values[1]);
    $date = $date_info[2].' '.$app_strings[date("M", mktime(0, 0, 0, $date_info[1], $date_info[2],$date_info[0]))].' '.$date_info[0];
    $time_modified = mktime($time_info[0], $time_info[1], $time_info[2], $date_info[1], $date_info[2],$date_info[0]);
    $time_now = time();
    $days_diff = (int)(($time_now - $time_modified) / (60 * 60 * 24));
    if($days_diff == 0)
        $update_info = $app_strings['LBL_UPDATED_TODAY']." (".$date.")";
    elseif($days_diff == 1)
        $update_info = $app_strings['LBL_UPDATED']." ".$days_diff." ".$app_strings['LBL_DAY_AGO']." (".$date.")";
    else
        $update_info = $app_strings['LBL_UPDATED']." ".$days_diff." ".$app_strings['LBL_DAYS_AGO']." (".$date.")";

    $log->debug("Exiting updateInfo method ...");
    return $update_info;
}


/**
 * This function is used to get the Product Images for the given Product  .
 * It accepts the product id as argument and returns the Images with the script for
 * rotating the product Images
 */

function getProductImages($id)
{
	global $log;
	$log->debug("Entering getProductImages(".$id.") method ...");
	global $adb, $table_prefix;
	$image_lists=array();
	$script_images=array();
	$script = '<script>var ProductImages = new Array(';
   	$i=0;
	$query='select imagename from '.$table_prefix.'_products where productid=?';
	$result = $adb->pquery($query, array($id));
	$imagename=$adb->query_result($result,0,'imagename');
	$image_lists=explode('###',$imagename);
	for($i=0;$i<count($image_lists);$i++)
	{
		$script_images[] = '"'.$image_lists[$i].'"';
	}
	$script .=implode(',',$script_images).');</script>';
	if($imagename != '')
	{
		$log->debug("Exiting getProductImages method ...");
		return $script;
	}
}

/**
 * This function is used to save the Images .
 * It acceps the File lists,modulename,id and the mode as arguments
 * It returns the array details of the upload
 */

function SaveImage($file,$module,$id,$mode)
{
	global $log, $root_directory;
	$log->debug("Entering SaveImage(".$file.",".$module.",".$id.",".$mode.") method ...");
	global $adb;
	$uploaddir = $root_directory."test/".$module."/" ;//set this to which location you need to give the contact image
	$log->info("The Location to Save the Contact Image is ".$uploaddir);
	$file_path_name = $file['imagename']['name'];
	if (isset($_REQUEST['imagename_hidden'])) {
		$file_name = vtlib_purify($_REQUEST['imagename_hidden']);
	} else {
		//allowed filename like UTF-8 Character
		$file_name = ltrim(basename(" ".$file_path_name)); // basename($file_path_name);
	}
	$image_error="false";
	$saveimage="true";
	if($file_name!="")
	{

		$log->debug("Contact Image is given for uploading");
		$image_name_val=file_exist_fn($file_name,0);

		$encode_field_values="";
		$errormessage="";

		$move_upload_status=move_uploaded_file($file["imagename"]["tmp_name"],$uploaddir.$image_name_val);
		$image_error="false";

		//if there is an error in the uploading of image

		$filetype= $file['imagename']['type'];
		$filesize = $file['imagename']['size'];

		$filetype_array=explode("/",$filetype);

		$file_type_val_image=strtolower($filetype_array[0]);
		$file_type_val=strtolower($filetype_array[1]);
		$log->info("The File type of the Contact Image is :: ".$file_type_val);
		//checking the uploaded image is if an image type or not
		if(!$move_upload_status) //if any error during file uploading
		{
			$log->debug("Error is present in uploading Contact Image.");
			$errorCode =  $file['imagename']['error'];
			if($errorCode == 4)
			{
				$errorcode="no-image";
				$saveimage="false";
				$image_error="true";
			}
			else if($errorCode == 2)
			{
				$errormessage = 2;
				$saveimage="false";
				$image_error="true";
			}
			else if($errorCode == 3 )
			{
				$errormessage = 3;
				$saveimage="false";
				$image_error="true";
			}
		}
		else
		{
			$log->debug("Successfully uploaded the Contact Image.");
			if($filesize != 0)
			{
				if (($file_type_val == "jpeg" ) || ($file_type_val == "png") || ($file_type_val == "jpg" ) || ($file_type_val == "pjpeg" ) || ($file_type_val == "x-png") || ($file_type_val == "gif") ) //Checking whether the file is an image or not
				{
					$saveimage="true";
					$image_error="false";
				}
				else
				{
					$savelogo="false";
					$image_error="true";
					$errormessage = "image";
				}
			}
			else
			{
				$savelogo="false";
				$image_error="true";
				$errormessage = "invalid";
			}

		}
	}
	else //if image is not given
	{
		$log->debug("Contact Image is not given for uploading.");
		if($mode=="edit" && $image_error=="false" )
		{
			if($module='contact')
			$image_name_val=getContactImageName($id);
			elseif($module='user')
			$image_name_val=getUserImageName($id);
			$saveimage="true";
		}
		else
		{
			$image_name_val="";
		}
	}
	$return_value=array('imagename'=>$image_name_val,
	'imageerror'=>$image_error,
	'errormessage'=>$errormessage,
	'saveimage'=>$saveimage,
	'mode'=>$mode);
	$log->debug("Exiting SaveImage method ...");
	return $return_value;
}

 /**
 * This function is used to generate file name if more than one image with same name is added to a given Product.
 * Param $filename - product file name
 * Param $exist - number time the file name is repeated.
 */

function file_exist_fn($filename,$exist)
{
	global $log;
	$log->debug("Entering file_exist_fn(".$filename.",".$exist.") method ...");
	global $uploaddir;

	if(!isset($exist))
	{
		$exist=0;
	}
	$filename_path=$uploaddir.$filename;
	if (file_exists($filename_path)) //Checking if the file name already exists in the directory
	{
		if($exist!=0)
		{
			$previous=$exist-1;
			$next=$exist+1;
			$explode_name=explode("_",$filename);
			$implode_array=array();
			for($j=0;$j<count($explode_name); $j++)
			{
				if($j!=0)
				{
					$implode_array[]=$explode_name[$j];
				}
			}
			$implode_name=implode("_", $implode_array);
			$test_name=$implode_name;
		}
		else
		{
			$implode_name=$filename;
		}
		$exist++;
		$filename_val=$exist."_".$implode_name;
		$testfilename = file_exist_fn($filename_val,$exist);
		if($testfilename!="")
		{
			$log->debug("Exiting file_exist_fn method ...");
			return $testfilename;
		}
	}
	else
	{
		$log->debug("Exiting file_exist_fn method ...");
		return $filename;
	}
}

/**
 * This function is used get the User Count.
 * It returns the array which has the total vtiger_users ,admin vtiger_users,and the non admin vtiger_users
 */

function UserCount()
{
    global $log;
    $log->debug("Entering UserCount() method ...");
    global $adb, $table_prefix;
    $result=$adb->pquery("select id from ".$table_prefix."_users where deleted =0", array()); // crmv@39110
    $user_count=$adb->num_rows($result);
    $result=$adb->pquery("select id from ".$table_prefix."_users where deleted =0 AND is_admin != 'on'", array()); // crmv@39110
    $nonadmin_count = $adb->num_rows($result);
    $admin_count = $user_count-$nonadmin_count;
    $count=array('user'=>$user_count,'admin'=>$admin_count,'nonadmin'=>$nonadmin_count);
    $log->debug("Exiting UserCount method ...");
    return $count;
}

/**
 * This function is used to create folders recursively.
 * Param $dir - directory name
 * Param $mode - directory access mode
 * Param $recursive - create directory recursive, default true
 */

function mkdirs($dir, $mode = 0777, $recursive = true)
{
    global $log;
    $log->debug("Entering mkdirs(".$dir.",".$mode.",".$recursive.") method ...");
    if( is_null($dir) || $dir === "" ){
        $log->debug("Exiting mkdirs method ...");
        return FALSE;
    }
    if( is_dir($dir) || $dir === "/" ){
        $log->debug("Exiting mkdirs method ...");
        return TRUE;
    }
    if( mkdirs(dirname($dir), $mode, $recursive) ){
        $log->debug("Exiting mkdirs method ...");
        return mkdir($dir, $mode);
    }
    $log->debug("Exiting mkdirs method ...");
    return FALSE;
}

/**
 * This function is used to set the Object values from the REQUEST values.
 * @param  object reference $focus - reference of the object
 */
function setObjectValuesFromRequest($focus)
{
	global $log;
	$log->debug("Entering setObjectValuesFromRequest(focus) method ...");
	global $current_user;
	$currencyid=fetchCurrency($current_user->id);
	$rate_symbol = getCurrencySymbolandCRate($currencyid);
	$rate = $rate_symbol['rate'];
	if(isset($_REQUEST['record']))
	{
		$focus->id = $_REQUEST['record'];
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
			$focus->column_fields[$fieldname] = $value;
		}
	}
	$log->debug("Exiting setObjectValuesFromRequest method ...");
}

// crmv@64542 - added inventory and product modules
/**
 * Function to write the tabid and name to a flat file vtiger_tabdata.txt so that the data
 * is obtained from the file instead of repeated queries
 * returns null
 */
function create_tab_data_file() {
	global $log;
	global $adb, $table_prefix;
	
	$log->debug("Entering create_tab_data_file() method ...");
	$log->info("creating vte_tabdata file");
	
	// vtlib customization: Disabling the tab item based on presence
	$sql = "select * from ".$table_prefix."_tab where presence in (0,2)";

	$result = $adb->pquery($sql, array());
	$num_rows=$adb->num_rows($result);
	$result_array=Array();
	$seq_array=Array();
	$ownedby_array=Array();

	for ($i=0; $i<$num_rows; ++$i) 	{
		$tabid = $adb->query_result_no_html($result,$i,'tabid');
		$tabname = $adb->query_result($result,$i,'name');
		$presence = $adb->query_result_no_html($result,$i,'presence');
		$ownedby = $adb->query_result_no_html($result,$i,'ownedby');
		$result_array[$tabname]=$tabid;
		$seq_array[$tabid]=$presence;
		$ownedby_array[$tabid]=$ownedby;
	}

	//Constructing the actionname=>actionid array
	$actionid_array=Array();
	$sql1="select * from ".$table_prefix."_actionmapping";
	$result1=$adb->pquery($sql1, array());
	$num_seq1=$adb->num_rows($result1);
	for ($i=0; $i<$num_seq1; ++$i) {
		$actionname=$adb->query_result($result1,$i,'actionname');
		$actionid=$adb->query_result_no_html($result1,$i,'actionid');
		$actionid_array[$actionname]=$actionid;
	}

	//Constructing the actionid=>actionname array with securitycheck=0
	$actionname_array=Array();
	$sql2="select * from ".$table_prefix."_actionmapping where securitycheck=0";
	$result2=$adb->pquery($sql2, array());
	$num_seq2=$adb->num_rows($result2);
	for ($i=0; $i<$num_seq2; ++$i)	{
		$actionname=$adb->query_result($result2,$i,'actionname');
		$actionid=$adb->query_result_no_html($result2,$i,'actionid');
		$actionname_array[$actionid]=$actionname;
	}
	
	// retrieve inventory modules
	$inventory_modules = array();
	$res = $adb->query(
		"SELECT t.tabid, t.name 
		FROM {$table_prefix}_tab t
		INNER JOIN {$table_prefix}_tab_info ti ON ti.tabid = t.tabid AND ti.prefname = 'is_inventory'
		WHERE t.presence IN (0,2) AND ti.prefvalue = '1'"
	);
	if ($res && $adb->num_rows($res) > 0) {
		while ($row = $adb->FetchByAssoc($res, -1, false)) {
			$inventory_modules[] = $row['name'];
		}
	}
	
	// retrieve product modules
	$product_modules = array();
	$res = $adb->query(
		"SELECT t.tabid, t.name 
		FROM {$table_prefix}_tab t
		INNER JOIN {$table_prefix}_tab_info ti ON ti.tabid = t.tabid AND ti.prefname = 'is_product'
		WHERE t.presence IN (0,2) AND ti.prefvalue = '1'"
	);
	if ($res && $adb->num_rows($res) > 0) {
		while ($row = $adb->FetchByAssoc($res, -1, false)) {
			$product_modules[] = $row['name'];
		}
	}
	
	$filename = 'tabdata.php';

	if (file_exists($filename)) {

		if (is_writable($filename)) {

			if (!$handle = fopen($filename, 'w+')) {
				echo "Cannot open file ($filename)";
				exit;
			}
			
			require_once('modules/Users/CreateUserPrivilegeFile.php');
			
			$newbuf='';
			$newbuf .="<?php\n\n";
			$newbuf .="\n";
			$newbuf .= "//This file contains the commonly used variables \n";
			$newbuf .= "\n";
			$newbuf .= "\$tab_info_array=".constructArray($result_array).";\n";
			$newbuf .= "\n";
			$newbuf .= "\$tab_seq_array=".constructArray($seq_array).";\n";
			$newbuf .= "\n";
			$newbuf .= "\$tab_ownedby_array=".constructArray($ownedby_array).";\n";
			$newbuf .= "\n";
			$newbuf .= "\$action_id_array=".constructSingleStringKeyAndValueArray($actionid_array).";\n";
			$newbuf .= "\n";
			$newbuf .= "\$action_name_array=".constructSingleStringValueArray($actionname_array).";\n";
			$newbuf .= "\n";
			$newbuf .= "\$inventory_modules=".constructSingleStringValueArray($inventory_modules).";\n";
			$newbuf .= "\n";
			$newbuf .= "\$product_modules=".constructSingleStringValueArray($product_modules).";\n";
			// no need of closing tag, avoid them if not necessary
			fputs($handle, $newbuf);
			fclose($handle);

			// crmv@85638 - fix for PHP 5.5
			if (function_exists('opcache_invalidate')) {
				opcache_invalidate($filename, true);
			}
			// crmv@85638e

		} else {
			echo "The file $filename is not writable";
		}
	
	} else {
		echo "The file $filename does not exist";
		$log->debug("Exiting create_tab_data_file method ...");
	}

}
// crmv@64542e


 /**
 * Function to write the vtiger_parenttabid and name to a flat file parent_tabdata.txt so that the data
 * is obtained from the file instead of repeated queries
 * returns null
 */
function create_parenttab_data_file() {
	global $log, $adb, $table_prefix;

	$log->debug("Entering create_parenttab_data_file() method ...");
	$log->info("creating parent_tabdata file");
	
	$result_array=Array();
	$result = $adb->query("select parenttabid,parenttab_label from ".$table_prefix."_parenttab where visible=0 order by sequence");
	$num_rows=$adb->num_rows($result);
	for ($i=0;$i<$num_rows;++$i) {
		$parenttabid=$adb->query_result_no_html($result,$i,'parenttabid');
		$parenttab_label=$adb->query_result_no_html($result,$i,'parenttab_label');
		$result_array[$parenttabid]=$parenttab_label;
	}

	$filename = 'parent_tabdata.php';

	if (file_exists($filename)) {
		
		if (is_writable($filename)) {

			if (!$handle = fopen($filename, 'w+')) {
				echo "Cannot open file ($filename)";
				exit;
			}
			require_once('modules/Users/CreateUserPrivilegeFile.php');
			$newbuf='';
			$newbuf .="<?php\n\n";
			$newbuf .="\n";
			$newbuf .= "//This file contains the commonly used variables \n";
			$newbuf .= "\n";
			$newbuf .= "\$parent_tab_info_array=".constructSingleStringValueArray($result_array).";\n";
			$newbuf .="\n";

			$parChildTabRelArray=Array();

			foreach($result_array as $parid=>$parvalue) {
				$childArray=Array();
				//$sql = "select * from ".$table_prefix."_parenttabrel where parenttabid=? order by sequence";
				// vtlib customization: Disabling the tab item based on presence
				$sql = "select * from ".$table_prefix."_parenttabrel where parenttabid=?
					and tabid in (select tabid from ".$table_prefix."_tab where presence in (0,2)) order by sequence";
				// END
				$result = $adb->pquery($sql, array($parid));
				$num_rows=$adb->num_rows($result);
				$result_array=Array();
				for ($i=0; $i<$num_rows; ++$i) {
					$childArray[] = $adb->query_result_no_html($result,$i,'tabid');
				}
				$parChildTabRelArray[$parid]=$childArray;

			}
			$newbuf .= "\n";
			$newbuf .= "\$parent_child_tab_rel_array=".constructTwoDimensionalValueArray($parChildTabRelArray).";\n";
			$newbuf .="\n";
			$newbuf .="\n";
			$newbuf .="\n";
			$newbuf .= "?>";
			fputs($handle, $newbuf);
			fclose($handle);

			// crmv@85638 - fix for PHP 5.5
			if (function_exists('opcache_invalidate')) {
				opcache_invalidate($filename, true);
			}
			// crmv@85638e

		} else {
			echo "The file $filename is not writable";
		}

	} else {
		echo "The file $filename does not exist";
		$log->debug("Exiting create_parenttab_data_file method ...");
	}
}

/**
 * This function is used to get the all the modules that have Quick Create Feature.
 * Returns Tab Name and Tablabel.
 */

function getQuickCreateModules($qc_module='')	/* crmv@20702 crmv@31197 crmv@47905bis */
{
	global $log;
	$log->debug("Entering getQuickCreateModules() method ...");
	global $adb, $table_prefix;

	$cache = Cache::getInstance('getQuickCreateModules');
	$tmp = $cache->get();
	if ($tmp === false) {
		$tmp = array();
		$qc_query = "select ".$table_prefix."_tab.tablabel,".$table_prefix."_tab.name,".$table_prefix."_quickcreate.img from ".$table_prefix."_tab inner join ".$table_prefix."_quickcreate ON ".$table_prefix."_quickcreate.tabid = ".$table_prefix."_tab.tabid where ".$table_prefix."_tab.presence != 1";
		$qc_query .= " order by ".$table_prefix."_tab.tablabel";
		$result = $adb->query($qc_query);
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByAssoc($result)) {
				$tabname = $row['name'];
			 	$tablabel = getTranslatedString("SINGLE_$tabname", $tabname);
			 	$img = $row['img'];
			 	if (empty($img)) {
			 		$img = 'themes/images/qc_default.png';
			 	}
				$tmp[$tabname] = array($tablabel,$tabname,$img);
			}
		}
		$cache->set($tmp,3600);
	}
	
	if (!empty($qc_module)) {
		$tmp = array($tmp[$qc_module]);
	}
	
	$return_qcmodule = Array();
	foreach($tmp as $module => $info) {
		if(isPermitted($module,'EditView','') == 'yes') {
         	$return_qcmodule[] = $info;
		}
	}
	
	$log->debug("Exiting getQuickCreateModules method ...");
	return $return_qcmodule;
}

/**
 * This function is used to get the Quick create form vtiger_field parameters for a given module.
 * Param $module - module name
 * returns the value in array format
 */

function QuickCreate($module, $col_fields=array())	//crmv@26631
{
	global $log;
	$log->debug("Entering QuickCreate(".$module.") method ...");
	global $adb, $table_prefix;
	global $current_user;
	global $mod_strings;

	$tabid = getTabid($module);

	//Adding Security Check
	require('user_privileges/requireUserPrivileges.php'); // crmv@39110
	//crmv@49510	crmv@56945	crmv@101877
	/* crmv@54659 : delete "OR {$table_prefix}_profile2field.mandatory = 0" */
	$profileList = getCurrentUserProfileList();
	$mandColumn = 'mandatory';
	$adb->format_columns($mandColumn);
	if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0)
	{
		$quickcreate_query = "SELECT {$table_prefix}_field.fieldid, MIN({$table_prefix}_profile2field.$mandColumn) as \"mandatory\"
							FROM ".$table_prefix."_field
							INNER JOIN {$table_prefix}_profile2field ON ({$table_prefix}_profile2field.fieldid = {$table_prefix}_field.fieldid AND {$table_prefix}_profile2field.profileid IN (".generateQuestionMarks($profileList)."))
							WHERE {$table_prefix}_field.tabid = ? AND {$table_prefix}_field.presence in (0,2) AND {$table_prefix}_field.displaytype = 1
							GROUP BY {$table_prefix}_field.fieldid";
		$params = array($profileList, $tabid);
	}
	else
	{
 		$quickcreate_query = "SELECT {$table_prefix}_field.fieldid, MIN({$table_prefix}_profile2field.$mandColumn) as \"mandatory\"
 							FROM {$table_prefix}_field
 							INNER JOIN {$table_prefix}_def_org_field ON {$table_prefix}_def_org_field.fieldid={$table_prefix}_field.fieldid
 							INNER JOIN {$table_prefix}_profile2field ON ({$table_prefix}_profile2field.fieldid = {$table_prefix}_field.fieldid AND {$table_prefix}_profile2field.profileid IN (".generateQuestionMarks($profileList)."))
 							WHERE {$table_prefix}_field.tabid=? AND {$table_prefix}_def_org_field.visible=0 AND {$table_prefix}_field.presence in (0,2) and displaytype = 1 AND {$table_prefix}_profile2field.visible = 0
 							GROUP BY {$table_prefix}_field.fieldid";
 		$params = array($profileList, $tabid);
	}
	$quickcreate_query = "SELECT {$table_prefix}_field.*, CASE WHEN (q.$mandColumn is not null) THEN q.$mandColumn ELSE 1 END AS \"mandatory\"
						FROM ($quickcreate_query) q
						INNER JOIN {$table_prefix}_field ON {$table_prefix}_field.fieldid = q.fieldid
						INNER JOIN {$table_prefix}_blocks ON {$table_prefix}_blocks.blockid = {$table_prefix}_field.block
						ORDER BY {$table_prefix}_blocks.sequence, {$table_prefix}_field.sequence";
	//crmv@49510e	crmv@56945e	crmv@101877e
	$category = getParentTab();
	$result = $adb->pquery($quickcreate_query, $params);
	$noofrows = $adb->num_rows($result);
	$fieldName_array = Array();
	for($i=0; $i<$noofrows; $i++)
	{
		$fieldtablename = $adb->query_result($result,$i,'tablename');
		$uitype = $adb->query_result($result,$i,"uitype");
		$fieldname = $adb->query_result($result,$i,"fieldname");
		$fieldlabel = $adb->query_result($result,$i,"fieldlabel");
		$maxlength = $adb->query_result($result,$i,"maximumlength");
		$generatedtype = $adb->query_result($result,$i,"generatedtype");
		$readonly = $adb->query_result($result,$i,"readonly");
		$typeofdata = getFinalTypeOfData($adb->query_result($result,$i,"typeofdata"), $adb->query_result($result,$i,"mandatory"));	//crmv@49510
		$fieldid = $adb->query_result($result,$i,"fieldid");
		//crmv@101877
		$quickcreate = $adb->query_result($result,$i,"quickcreate");
		$tmp = explode('~',$typeofdata);
		$is_mandatory = ($tmp[1] == 'M');
		if (!(in_array($quickcreate,array(0,2)) || $is_mandatory)) continue;	// use quickcreate and mandatory fields
		//crmv@101877e

		if ($module == 'Charts' && in_array($fieldname, array('assigned_user_id', 'reportid'))) continue; // crmv@104740
		
		//to get validationdata
		$fldLabel_array = Array();
		$fldLabel_array[getTranslatedString($fieldlabel)] = $typeofdata;
		$fieldName_array[$fieldname] = $fldLabel_array;
		//crmv@sdk-18508
		$sdk_files = SDK::getViews($module,'');
		if (!empty($sdk_files)) {
			foreach($sdk_files as $sdk_file) {
				$success = false;
				$readonly_old = $readonly;
				include($sdk_file['src']);
				SDK::checkReadonly($readonly_old,$readonly,$sdk_file['mode']);
				if ($success && $sdk_file['on_success'] == 'stop') {
					break;
				}
			}
		}
		//crmv@sdk-18508 e
		$custfld = getOutputHtml($uitype, $fieldname, $fieldlabel, $maxlength, $col_fields,$generatedtype,$module,'',$readonly,$typeofdata);
		$custfld[] = $fieldid;
		$qcreate_arr[]=$custfld;
	}
	for ($i=0,$j=0;$i<count($qcreate_arr);$i=$i+2,$j++)
	{
		$key1=$qcreate_arr[$i];
		if(is_array($qcreate_arr[$i+1]))
		{
			$key2=$qcreate_arr[$i+1];
		}
		else
		{
			$key2 =array();
		}
		$return_data[$j]=array(0 => $key1,1 => $key2);
	}
	$form_data['form'] = $return_data;
	$form_data['data'] = $fieldName_array;
	$log->debug("Exiting QuickCreate method ...".print_r($form_data,true));
	return $form_data;
}

function getUserslist($setdefval=true,$ret_array=false)	//crmv@31171
{
	global $log,$current_user,$module,$adb,$assigned_user_id;
	$log->debug("Entering getUserslist() method ...");
	require('user_privileges/requireUserPrivileges.php'); // crmv@39110
	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');

	if($is_admin==false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[getTabid($module)] == 3 or $defaultOrgSharingPermission[getTabid($module)] == 0))
	{
		$users_combo = get_select_options_array(get_user_array(FALSE, "Active", $current_user->id,'private'), $current_user->id);
	}
	else
	{
		$users_combo = get_select_options_array(get_user_array(FALSE, "Active", $current_user->id),$current_user->id);
	}
	//crmv@31171
	if ($ret_array) {
		return $users_combo;
	}
	//crmv@31171e
	foreach($users_combo as $userid=>$value)
	{

		foreach($value as $username=>$selected)
		{
			if ($setdefval == false) {
				$change_owner .= "<option value=$userid>".$username."</option>";
			} else {
				$change_owner .= "<option value=$userid $selected>".$username."</option>";
			}
		}
	}

	$log->debug("Exiting getUserslist method ...");
	return $change_owner;
}

function getGroupslist($ret_array=false)	//crmv@31171
{
	global $log,$adb,$module,$current_user;
	$log->debug("Entering getGroupslist() method ...");
	require('user_privileges/requireUserPrivileges.php'); // crmv@39110
	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');

	//Commented to avoid security check for groups
	if($is_admin==false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[getTabid($module)] == 3 or $defaultOrgSharingPermission[getTabid($module)] == 0))
	{
		$result=get_current_user_access_groups($module);
	}
	else
	{
		$result = get_group_options();
	}

	if($result) $nameArray = $adb->fetch_array($result);
	if(!empty($nameArray))
	{
		if($is_admin==false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[getTabid($module)] == 3 or $defaultOrgSharingPermission[getTabid($module)] == 0))
		{
			$groups_combo = get_select_options_array(get_group_array(FALSE, "Active", $current_user->id,'private'), $current_user->id);
		}
		else
		{
			$groups_combo = get_select_options_array(get_group_array(FALSE, "Active", $current_user->id), $current_user->id);
		}
	}
	//crmv@31171
	if ($ret_array) {
		return $groups_combo;
	}
	//crmv@31171e
	if(count($groups_combo) > 0) {
		foreach($groups_combo as $groupid=>$value)
		{
			foreach($value as $groupname=>$selected)
			{
				$change_groups_owner .= "<option value=$groupid $selected >".$groupname."</option>";
			}
		}
	}
	$log->debug("Exiting getGroupslist method ...");
	return $change_groups_owner;
}

/**
  *	Function to Check for Security whether the Buttons are permitted in List/Edit/Detail View of all Modules
  *	@param string $module -- module name
  *	Returns an array with permission as Yes or No
**/
function Button_Check($module)
{
	global $log;
	$log->debug("Entering Button_Check(".$module.") method ...");
	$permit_arr = array ('EditView' => '',
							'index' => '',
                             'Import' => '',
                             'Export' => '',
        					//crmv@8719
        					  'Merge' => '',
							  'DuplicatesHandling' => '' );
        					//crmv@8719e

	foreach($permit_arr as $action => $perr){
		$tempPer=isPermitted($module,$action,'');
		$permit_arr[$action] = $tempPer;
	}
	$permit_arr["Calendar"] = isPermitted("Calendar","index",'');
	// crmv@105193
	// TODO: find a way to avoid using the request
	if ($_REQUEST['action'] == 'index' || $_REQUEST['action'] == 'HomeView') {
		$permit_arr["moduleSettings"] = 'yes';
	} else {
		$permit_arr["moduleSettings"] = isModuleSettingPermitted($module);
	}
	// crmv@105193e
	$log->debug("Exiting Button_Check method ...");
	  return $permit_arr;

}

/**
  *	Function to Check whether the User is allowed to delete a particular record from listview of each module using
  *	mass delete button.
  *	@param string $module -- module name
  *	@param array $ids_list -- Record id
  *	Returns the Record Names of each module that is not permitted to delete
**/
// crmv@39110
function getEntityName($module, $ids_list, $single_result = false) { // crmv@44187
    global $log, $adb, $table_prefix, $current_user;
    $log->debug("Entering getEntityName(".$module.") method ...");
	if (!in_array($module,array('','Fax'))) {	//crmv@55198
		if (count($ids_list) <= 0) return array();
		if ($module == 'Activity') $module = 'Calendar';

		$moduleInstance = Vtiger_Module::getInstance($module);	//crmv@26307
		$moduleFocus = CRMEntity::getInstance($module);
		if ($current_user && $current_user->id > 0) {
			// get profile (only the first one)
			require('user_privileges/requireUserPrivileges.php'); // crmv@39110
			$userProfile = $current_user_profiles[0];
			// override default field with the profile one
			$query = "SELECT COALESCE(p2en.fieldname, en.fieldname) as fieldname
				FROM {$table_prefix}_entityname en
				LEFT JOIN {$table_prefix}_profile2entityname p2en ON p2en.profileid = ? AND p2en.tabid = en.tabid
				WHERE en.modulename = ?";
			$params = array($userProfile, $module);
		} else {
			$query = "select fieldname from ".$table_prefix."_entityname where modulename = ?";
			$params = array($module);
		}
		$result = $adb->pquery($query, $params);
		$fieldsname = $adb->query_result($result,0,'fieldname');
		//crmv@24944
		if ( strpos($fieldsname,',') !== false) {
			$fieldlists = explode(',',$fieldsname);
			$fl = array();
			foreach ($fieldlists as $w => $c) {
				if (count($fl)) {
					$fl[] = "' '";
				}
				$wsfield = WebserviceField::fromQueryResult($adb,$adb->pquery('select * from '.$table_prefix.'_field where tabid = ? and fieldname = ?',array($moduleInstance->id,$c)),0);	//crmv@26307
				$fl[] = "COALESCE(".$wsfield->getColumnName().", '')";	//crmv@33359

			}
			$fieldsname = $adb->sql_concat($fl);
		} else {
			$wsfield = WebserviceField::fromQueryResult($adb,$adb->pquery('select * from '.$table_prefix.'_field where tabid = ? and fieldname = ?',array($moduleInstance->id,$fieldsname)),0);	//crmv@26307
			$fieldsname = $wsfield->getColumnName();
		}
		//crmv@24944e
		$tablename = $wsfield->getTableName(); // assume table and index are shared among all fields
		$entityidfield = $moduleFocus->tab_name_index[$tablename];
		$query1 = "select $fieldsname entityname,$entityidfield from $tablename where $entityidfield in (". generateQuestionMarks($ids_list) .")";
		$params1 = array($ids_list);
		$result = $adb->pquery($query1, $params1);
		$numrows = $adb->num_rows($result);
		$account_name = array();
		for ($i = 0; $i < $numrows; $i++) {
			$entity_id = $adb->query_result($result,$i,$entityidfield);
			// crmv@39106 - fix uitype picklist multilanguage
			$value = $adb->query_result($result,$i,'entityname');
			if ($wsfield && $wsfield->getFieldDataType() == 'picklistmultilanguage' && class_exists('PickListMulti')) {
				$value = PickListMulti::getTranslatedPicklist($value,$fieldsname);
			}
			if ($single_result) return $value; // crmv@44187
			$entity_info[$entity_id] = $value;
			// crmv@39106e
		}
		return $entity_info;
	}
    $log->debug("Exiting getEntityName method ...");
}
// crmv@39110e

/**Function to get all permitted modules for a user with their parent
*/
//crmv@9587
function getAllParenttabmoduleslist($layout)
{
	global $adb, $table_prefix;
	global $current_user;
	$resultant_array = Array();
	require('user_privileges/requireUserPrivileges.php'); // crmv@39110
	if ($layout == 'tabs') {
		//$query = 'select name,tablabel,parenttab_label,'.$table_prefix.'_tab.tabid from '.$table_prefix.'_parenttabrel inner join '.$table_prefix.'_tab on '.$table_prefix.'_parenttabrel.tabid = '.$table_prefix.'_tab.tabid inner join '.$table_prefix.'_parenttab on '.$table_prefix.'_parenttabrel.parenttabid = '.$table_prefix.'_parenttab.parenttabid and '.$table_prefix.'_tab.presence order by '.$table_prefix.'_parenttab.sequence, '.$table_prefix.'_parenttabrel.sequence';
		// vtlib customization: Disabling the tab item based on presence
		$query = 'select name,tablabel,parenttab_label,'.$table_prefix.'_tab.tabid from '.$table_prefix.'_parenttabrel inner join '.$table_prefix.'_tab on '.$table_prefix.'_parenttabrel.tabid = '.$table_prefix.'_tab.tabid inner join '.$table_prefix.'_parenttab on '.$table_prefix.'_parenttabrel.parenttabid = '.$table_prefix.'_parenttab.parenttabid and '.$table_prefix.'_tab.presence in (0,2) order by '.$table_prefix.'_parenttab.sequence, '.$table_prefix.'_parenttabrel.sequence';
		// END
		$result = $adb->pquery($query, array());
		for($i=0;$i<$adb->num_rows($result);$i++)
		{
			$parenttabname = $adb->query_result($result,$i,'parenttab_label');
			$modulename = $adb->query_result($result,$i,'name');
			$tablabel = $adb->query_result($result,$i,'tablabel');
			$tabid = $adb->query_result($result,$i,'tabid');
			if($is_admin){
				$resultant_array[$parenttabname][] = Array($modulename,$tablabel);
			}
			elseif($profileGlobalPermission[2]==0 || $profileGlobalPermission[1]==0 || $profileTabsPermission[$tabid]==0)		     {
				$resultant_array[$parenttabname][] = Array($modulename,$tablabel);
			}
		}
	}
	return $resultant_array;
}

// crmv@95157
/**
 * @deprecated
 * Please use the method FileStorage::decideFilePath()
 * This function is kept here for compatibility only
 */
function decideFilePath() {
	$FS = FileStorage::getInstance();
	return $FS->decideFilePath();
}
// crmv@95157e

/**
 *     This function is used to get the Path in where we store the vtiger_files based on the module.
 *    @param string $module   - module name
 *     return string $storage_path - path inwhere the file will be uploaded (also where it was stored) will be return based on the module
*/
function getModuleFileStoragePath($module)
{
    global $log;
    $log->debug("Entering into getModuleFileStoragePath($module) method ...");

    $storage_path = "test/";

    if($module == 'Products')
    {
        $storage_path .= 'product/';
    }
    if($module == 'Contacts')
    {
        $storage_path .= 'contact/';
    }

    $log->debug("Exiting from getModuleFileStoragePath($module) method. return storage_path = \"$storage_path\"");
    return $storage_path;
}

/**
 *     This function is used to check whether the attached file is a image file or not
 *    @param string $file_details  - vtiger_files array which contains all the uploaded file details
 *     return string $save_image - true or false. if the image can be uploaded then true will return otherwise false.
*/
function validateImageFile($file_details)
{
	global $adb, $log,$app_strings;
	$log->debug("Entering into validateImageFile($file_details) method.");

	$savefile = 'true';
	$file_type_details = explode("/",$file_details['type']);
	$filetype = $file_type_details['1'];

	if(!empty($filetype)) $filetype = strtolower($filetype);
	if (($filetype == "jpeg" ) || ($filetype == "png") || ($filetype == "jpg" ) || ($filetype == "pjpeg" ) || ($filetype == "x-png") || ($filetype == "gif") || ($filetype == 'bmp') )
	{
		$saveimage = 'true';
	}
	else
	{
		$saveimage = 'false';
		$_SESSION['image_type_error'] .= "<br> &nbsp;&nbsp;<b>".$file_details[name]."</b>".$app_strings['MSG_IS_NOT_UPLOADED'];
		$log->debug("Invalid Image type == $filetype");
	}

	$log->debug("Exiting from validateImageFile($file_details) method. return saveimage=$saveimage");
	return $saveimage;
}

/**
 *     This function is used to get the Email Template Details like subject and content for particular template.
 *    @param integer $templateid  - Template Id for an Email Template
 *     return array $returndata - Returns Subject, Body of Template of the the particular email template.
*/

function getTemplateDetails($templateid)
{
	global $adb,$log, $table_prefix;
	$log->debug("Entering into getTemplateDetails($templateid) method ...");
	$returndata =  Array();
	//crmv@80155
	$result = $adb->pquery("select * from ".$table_prefix."_emailtemplates where templateid=?", array($templateid)); // crmv@39110
	if ($result) while($row=$adb->fetchByASsoc($result)) $returndata = $row;
	//crmv@80155e
	$log->debug("Exiting from getTemplateDetails($templateid) method ...");
	return $returndata;
}
//crmv@15309
function construct_ws_id($id,$module){
  	global $adb, $table_prefix;
  	$sql =	"select id from ".$table_prefix."_ws_entity where name = ?";
  	$res = $adb->pquery($sql,Array($module));
  	if ($res && $adb->num_rows($res) > 0){
  		$ret = $adb->query_result($res,0,'id')."x".$id;
  	}
  	return $ret;
}

/**
 *     This function is used to merge the Template Details with the email description
 *  @param string $description  -body of the mail(ie template)
 *    @param integer $tid  - Id of the entity
 *  @param string $parent_type - module of the entity
 *     return string $description - Returns description, merged with the input template.
*/

function getMergedDescription($description,$id,$parent_type,$newsletterid='',$templateid='', &$replacements = null) //crmv@22700 crmv@38592
{
    global $adb,$log;
    $log->debug("Entering getMergedDescription ...");
	require_once("include/Webservices/Retrieve.php");
    require_once('modules/com_vtiger_workflow/VTEntityCache.inc');
	require_once('modules/com_vtiger_workflow/VTWorkflowUtils.php');
	require_once('modules/com_vtiger_workflow/VTEmailRecipientsTemplate.inc');
	$util = new VTWorkflowUtils();
	$admin = $util->adminUser();
	$wsid = construct_ws_id($id,$parent_type);
	if ($wsid){
		$entity = new VTWorkflowEntity($admin, $wsid);
		$entityCache = new VTEntityCache($admin);
		$ct = new VTSimpleTemplate2($description,$newsletterid,$templateid);	//crmv@22700
		$description = $ct->render($entityCache, $entity->getId());
		if (!is_null($replacements)) $replacements = $ct->getReplacements(); //crmv@38592
	}
	$util->revertUser(); //crmv@28425
    $log->debug("Exiting from getMergedDescription ...");
    return $description;
}
//crmv@15309 end
/**    Function used to retrieve a single field value from database
 *    @param string $tablename - tablename from which we will retrieve the field value
 *    @param string $fieldname - fieldname to which we want to get the value from database
 *    @param string $idname     - idname which is the name of the entity id in the table like, inoviceid, quoteid, etc.,
 *    @param int    $id     - entity id
 *    return string $fieldval  - field value of the needed fieldname from database will be returned
 */
function getSingleFieldValue($tablename, $fieldname, $idname, $id, $html=true)
{
    global $log, $adb;
    $log->debug("Entering into function getSingleFieldValue($tablename, $fieldname, $idname, $id)");

    //crmv@30007
	$res = $adb->pquery("select $fieldname from $tablename where $idname = ?", array($id));
	if ($res){
		if ($html) {
	    	$fieldval = $adb->query_result($res,0,$fieldname);
		} else {
    		$fieldval = $adb->query_result_no_html($res,0,$fieldname);
		}
    }
	//crmv@30007 e

    $log->debug("Exit from function getSingleFieldValue. return value ==> \"$fieldval\"");

    return $fieldval;
}

/**    Function used to retrieve the rate converted into dollar tobe saved into database
 *    The function accepts the price in the current currency
 *    return integer $conv_price  -
 */
 function getConvertedPrice($price)
 {
     global $current_user;
     $currencyid=fetchCurrency($current_user->id);
     $rate_symbol = getCurrencySymbolandCRate($currencyid);
     $conv_price = convertToDollar($price,$rate_symbol['rate']);
     return $conv_price;
 }


/**    Function used to get the converted amount from dollar which will be showed to the user
 *    @param float $price - amount in dollor which we want to convert to the user configured amount
 *    @return float $conv_price  - amount in user configured currency
 */
function getConvertedPriceFromDollar($price)
{
    global $current_user;
    $currencyid=fetchCurrency($current_user->id);
    $rate_symbol = getCurrencySymbolandCRate($currencyid);
    $conv_price = convertFromDollar($price,$rate_symbol['rate']);
    return $conv_price;
}


/**
 *  Function to get recurring info depending on the recurring type
 *  return  $recurObj       - Object of class RecurringType
 */

function getrecurringObjValue() {
	$recurring_data = array();
	if (isset($_REQUEST['recurringtype']) && $_REQUEST['recurringtype'] != null && $_REQUEST['recurringtype'] != '--None--') {
		if (!empty($_REQUEST['date_start'])) {
			$startDate = $_REQUEST['date_start'];
		}
		if (!empty($_REQUEST['calendar_repeat_limit_date'])) {
			$endDate = $_REQUEST['calendar_repeat_limit_date'];
		} elseif (isset($_REQUEST['due_date']) && $_REQUEST['due_date'] != null) {
			$endDate = $_REQUEST['due_date'];
		}
		if (!empty($_REQUEST['time_start'])) {
			$startTime = $_REQUEST['time_start'];
		}
		if (!empty($_REQUEST['time_end'])) {
			$endTime = $_REQUEST['time_end'];
		}

		$recurring_data['startdate'] = $startDate;
		$recurring_data['starttime'] = $startTime;
		$recurring_data['enddate'] = $endDate;
		$recurring_data['endtime'] = $endTime;

		$recurring_data['type'] = $_REQUEST['recurringtype'];
		if ($_REQUEST['recurringtype'] == 'Weekly') {
			if (isset($_REQUEST['sun_flag']) && $_REQUEST['sun_flag'] != null)
				$recurring_data['sun_flag'] = true;
			if (isset($_REQUEST['mon_flag']) && $_REQUEST['mon_flag'] != null)
				$recurring_data['mon_flag'] = true;
			if (isset($_REQUEST['tue_flag']) && $_REQUEST['tue_flag'] != null)
				$recurring_data['tue_flag'] = true;
			if (isset($_REQUEST['wed_flag']) && $_REQUEST['wed_flag'] != null)
				$recurring_data['wed_flag'] = true;
			if (isset($_REQUEST['thu_flag']) && $_REQUEST['thu_flag'] != null)
				$recurring_data['thu_flag'] = true;
			if (isset($_REQUEST['fri_flag']) && $_REQUEST['fri_flag'] != null)
				$recurring_data['fri_flag'] = true;
			if (isset($_REQUEST['sat_flag']) && $_REQUEST['sat_flag'] != null)
				$recurring_data['sat_flag'] = true;
		}
		elseif ($_REQUEST['recurringtype'] == 'Monthly') {
			if (isset($_REQUEST['repeatMonth']) && $_REQUEST['repeatMonth'] != null)
				$recurring_data['repeatmonth_type'] = $_REQUEST['repeatMonth'];
			if ($recurring_data['repeatmonth_type'] == 'date') {
				if (isset($_REQUEST['repeatMonth_date']) && $_REQUEST['repeatMonth_date'] != null)
					$recurring_data['repeatmonth_date'] = $_REQUEST['repeatMonth_date'];
				else
					$recurring_data['repeatmonth_date'] = 1;
			}
			elseif ($recurring_data['repeatmonth_type'] == 'day') {
				$recurring_data['repeatmonth_daytype'] = $_REQUEST['repeatMonth_daytype'];
				switch ($_REQUEST['repeatMonth_day']) {
					case 0 :
						$recurring_data['sun_flag'] = true;
						break;
					case 1 :
						$recurring_data['mon_flag'] = true;
						break;
					case 2 :
						$recurring_data['tue_flag'] = true;
						break;
					case 3 :
						$recurring_data['wed_flag'] = true;
						break;
					case 4 :
						$recurring_data['thu_flag'] = true;
						break;
					case 5 :
						$recurring_data['fri_flag'] = true;
						break;
					case 6 :
						$recurring_data['sat_flag'] = true;
						break;
				}
			}
		}
		if (isset($_REQUEST['repeat_frequency']) && $_REQUEST['repeat_frequency'] != null)
			$recurring_data['repeat_frequency'] = $_REQUEST['repeat_frequency'];

		$recurObj = RecurringType::fromUserRequest($recurring_data);
		return $recurObj;
	}
}

/**	Function used to get the translated string to the input string
 *	@param string $str - input string which we want to translate
 *	@return string $str - translated string, if the translated string is available then the translated string other wise original string will be returned
 */
function getTranslatedString($str,$module='')
{
	//crmv@sdk
	global $adb;
	if (empty($adb->database) || !isModuleInstalled('SDK') || !function_exists('return_module_language')) {
 		return $str;
 	}
 	//crmv@sdk e
	global $app_strings, $mod_strings, $log, $current_language;
	//crmv@28065
	if (empty($app_strings)) {
		$app_strings = return_application_language($current_language);
	}
	//crmv@28065e
	$temp_mod_strings = ($module != '' )?return_module_language($current_language,$module):$mod_strings;
	$trans_str = ($temp_mod_strings[$str] != '')?$temp_mod_strings[$str]:(($app_strings[$str] != '')?$app_strings[$str]:$str);
	$log->debug("function getTranslatedString($str) - translated to ($trans_str)");
	return $trans_str;
}

/**
 * Get translated currency name string.
 * @param String $str - input currency name
 * @return String $str - translated currency name
 */
function getTranslatedCurrencyString($str) {
	global $app_currency_strings;
	if(isset($app_currency_strings) && isset($app_currency_strings[$str])) {
		return $app_currency_strings[$str];
	}
	return $str;
}

/**	function used to get the list of importable fields
 *	@param string $module - module name
 *	@return array $fieldslist - array with list of fieldnames and the corresponding translated fieldlabels. The return array will be in the format of [fieldname]=>[fieldlabel] where as the fieldlabel will be translated
 */
function getImportFieldsList($module)
{
	global $adb, $log, $table_prefix;
	$log->debug("Entering into function getImportFieldsList($module)");

	$tabid = getTabid($module);

	//Here we can add special cases for module basis, ie., if we want the fields of display type 3, we can add
	$displaytype = " displaytype=1 and ".$table_prefix."_field.presence in (0,2) ";

	$fieldnames = "";
	//For module basis we can add the list of fields for Import mapping
	if($module == "Leads" || $module == "Contacts")
	{
		$fieldnames = " fieldname='salutationtype' ";
	}

	//Form the where condition based on tabid , displaytype and extra fields
	$where = " WHERE tabid=? and ( $displaytype ";
	$params = array($tabid);
	if($fieldnames != "")
	{
		$where .= " or $fieldnames ";
	}
	$where .= ")";

	//Get the list of fields and form as array with [fieldname] => [fieldlabel]
	$query = "SELECT fieldname, fieldlabel FROM ".$table_prefix."_field $where";
	$result = $adb->pquery($query, $params);
	for($i=0;$i<$adb->num_rows($result);$i++)
	{
		$fieldname = $adb->query_result($result,$i,'fieldname');
		$fieldlabel = $adb->query_result($result,$i,'fieldlabel');
		$fieldslist[$fieldname] = getTranslatedString($fieldlabel, $module);
	}
	$log->debug("Exit from function getImportFieldsList($module)");

	return $fieldslist;
}
/**     Function to get all the comments for a troubleticket
  *     @param int $ticketid -- troubleticket id
  *     return all the comments as a sequencial string which are related to this ticket
**/
function getTicketComments($ticketid)
{
        global $log;
        $log->debug("Entering getTicketComments(".$ticketid.") method ...");
        global $adb, $table_prefix;

        $commentlist = '';
        $sql = "select comments from ".$table_prefix."_ticketcomments where ticketid=?"; // crmv@39110
        $result = $adb->pquery($sql, array($ticketid));
        for($i=0;$i<$adb->num_rows($result);$i++)
        {
                $comment = $adb->query_result($result,$i,'comments');
                if($comment != '')
                {
                        $commentlist .= '<br><br>'.$comment;
                }
        }
        if($commentlist != '')
                $commentlist = '<br><br> The comments are : '.$commentlist;

        $log->debug("Exiting getTicketComments method ...");
        return $commentlist;
}

function getTicketDetails($id,$whole_date)
{
     global $adb,$mod_strings, $table_prefix;
     if($whole_date['mode'] == 'edit')
     {
        $reply = $mod_strings["replied"];
        $temp = "Re : ";
     }
     else
     {
        $reply = $mod_strings["created"];
        $temp = " ";
     }

     $desc = $mod_strings['Ticket ID'] .' : '.$id.'<br> Ticket Title : '. $temp .' '.$whole_date['sub'];
     $desc .= "<br><br>".$mod_strings['Hi']." ". $whole_date['parent_name'].",<br><br>".$mod_strings['LBL_PORTAL_BODY_MAILINFO']." ".$reply." ".$mod_strings['LBL_DETAIL']."<br>";
     $desc .= "<br>".$mod_strings['Status']." : ".$whole_date['status'];
     $desc .= "<br>".$mod_strings['Category']." : ".$whole_date['category'];
     $desc .= "<br>".$mod_strings['Severity']." : ".$whole_date['severity'];
     $desc .= "<br>".$mod_strings['Priority']." : ".$whole_date['priority'];
     $desc .= "<br><br>".$mod_strings['Description']." : <br>".$whole_date['description'];
     $desc .= "<br><br>".$mod_strings['Solution']." : <br>".$whole_date['solution'];
     $desc .= getTicketComments($id);

     $sql = "SELECT * FROM ".$table_prefix."_ticketcf WHERE ticketid = ?";
     $result = $adb->pquery($sql, array($id));
     $cffields = $adb->getFieldsArray($result);
     foreach ($cffields as $cfOneField)
     {
         if ($cfOneField != 'ticketid')
         {
             $cfData = $adb->query_result($result,0,$cfOneField);
             $sql = "SELECT fieldlabel FROM ".$table_prefix."_field WHERE columnname = ?";
             $cfLabel = $adb->query_result($adb->pquery($sql,array($cfOneField)),0,'fieldlabel');
             $desc .= '<br><br>'.$cfLabel.' : <br>'.$cfData;
         }
     }
     // end of contribution
     $desc .= '<br><br><br>';
     $desc .= '<br>'.$mod_strings["LBL_REGARDS"].',<br>'.$mod_strings["LBL_TEAM"].'.<br>';
     return $desc;

}

function getPortalInfo_Ticket($id,$title,$contactname,$portal_url)
{
    global $mod_strings;
    $bodydetails =$mod_strings['Dear']." ".$contactname.",<br><br>";
        $bodydetails .= $mod_strings['reply'].' <b>'.$title.'</b>'.$mod_strings['customer_portal'];
        $bodydetails .= $mod_strings["link"].'<br>';
        $bodydetails .= $portal_url;
        $bodydetails .= '<br><br>'.$mod_strings["Thanks"].'<br><br>'.$mod_strings["Support_team"];
    return $bodydetails;
}

/**
 * This function is used to get a random password.
 * @return a random password with alpha numeric chanreters of length 8
 */
function makeRandomPassword()
{
    global $log;
    $log->debug("Entering makeRandomPassword() method ...");
    $salt = "abcdefghijklmnopqrstuvwxyz0123456789";
    srand((double)microtime()*1000000);
    $i = 0;
    while ($i <= 7)
    {
        $num = rand() % 33;
        $tmp = substr($salt, $num, 1);
        $pass = $pass . $tmp;
        $i++;
    }
    $log->debug("Exiting makeRandomPassword method ...");
    return $pass;
}

//added to get mail info for portal user
//type argument included when when addin customizable tempalte for sending portal login details
function getmail_contents_portalUser($request_array,$password,$type='')
{
    global $mod_strings ,$adb, $table_prefix;

    $subject = $mod_strings['Customer Portal Login Details'];

    //here id is hardcoded with 5. it is for support start notification in vtiger_notifyscheduler
	//crmv@12035
    $query="select notificationbody from ".$table_prefix."_notifyscheduler where schedulednotificationid = ?";
    $res = $adb->pquery($query,Array(5));
    if ($res && $adb->num_rows($res) == 1){
    	$body = $adb->query_result($res,0,'notificationbody');
    	if (is_numeric($body)){
    		$query2 = "SELECT ".$table_prefix."_emailtemplates.subject,".$table_prefix."_emailtemplates.body FROM ".$table_prefix."_emailtemplates where ".$table_prefix."_emailtemplates.templateid = ?";
    		$result = $adb->pquery($query2,Array($body));
    		if ($result && $adb->num_rows($result) == 1){
    			$body = $adb->query_result_no_html($result,0,'body');
    			$subject = $adb->query_result($result,0,'subject');
    		}
    	}
    }
    //crmv@12035 end
    $contents = $body;
    $contents = str_replace('$contact_name$',$request_array['first_name']." ".$request_array['last_name'],$contents);
    $contents = str_replace('$login_name$',$request_array['email'],$contents);
    $contents = str_replace('$password$',$password,$contents);
    $contents = str_replace('$URL$',$request_array['portal_url'],$contents);
    $contents = str_replace('$support_team$',$mod_strings['Support Team'],$contents);
    $contents = str_replace('$logo$','<img src="cid:logo" />',$contents);

    if($type == "LoginDetails")
    {
        $temp=$contents;
        $value["subject"]=$subject;
        $value["body"]=$temp;
        return $value;
    }

    return $contents;

}
/**
 * Function to get the UItype for a field.
 * Takes the input as $module - module name,and columnname of the field
 * returns the uitype, integer type
 */

function getUItype($module,$columnname)
{
        global $log;
        $log->debug("Entering getUItype(".$module.") method ...");
    //To find tabid for this module
    $tabid=getTabid($module);
        global $adb, $table_prefix;
        $sql = "select uitype from ".$table_prefix."_field where tabid=? and columnname=?";
        $result = $adb->pquery($sql, array($tabid, $columnname));
        $uitype =  $adb->query_result($result,0,"uitype");
        $log->debug("Exiting getUItype method ...");
        return $uitype;

}
//crmv@7216
function is_emailId($entity_id)
{
    global $log,$adb, $table_prefix;
    $log->debug("Entering is_EmailId(".$module.",".$entity_id.") method");

    $module = getSalesEntityType($entity_id);
    if($module == 'Contacts')
    {
        $sql = "select email,yahooid from ".$table_prefix."_contactdetails inner join ".$table_prefix."_crmentity on ".$table_prefix."_crmentity.crmid = ".$table_prefix."_contactdetails.contactid where contactid = ?";
        $result = $adb->pquery($sql, array($entity_id));
        $email1 = $adb->query_result($result,0,"email");
        $email2 = $adb->query_result($result,0,"yahooid");
        if(($email1 != "" || $email2 != "") || ($email1 != "" && $email2 != ""))
        {
            $check_mailids = "true";
        }
        else
            $check_mailids = "false";
    }
    elseif($module == 'Leads')
    {
        $sql = "select email,yahooid from ".$table_prefix."_leaddetails inner join ".$table_prefix."_crmentity on ".$table_prefix."_crmentity.crmid = ".$table_prefix."_leaddetails.leadid where leadid = ?";
        $result = $adb->pquery($sql, array($entity_id));
        $email1 = $adb->query_result($result,0,"email");
        $email2 = $adb->query_result($result,0,"yahooid");
        if(($email1 != "" || $email2 != "") || ($email1 != "" && $email2 != ""))
        {
            $check_mailids = "true";
        }
        else
            $check_mailids = "false";
    }
    if($module == 'Accounts')
    {
        $sql = "select email1,email2 from ".$table_prefix."_account inner join ".$table_prefix."_crmentity on ".$table_prefix."_crmentity.crmid = ".$table_prefix."_account.accountid where accountid = ?";
        $result = $adb->pquery($sql, array($entity_id));
        $email1 = $adb->query_result($result,0,"email1");
        $email2 = $adb->query_result($result,0,"email2");
        if(($email1 != "" || $email2 != "") || ($email1 != "" && $email2 != ""))
        {
            $check_mailids = "true";
        }
        else
            $check_mailids = "false";
    }
    $log->debug("Exiting is_EmailId() method ...");
    return $check_mailids;
}


function is_faxId($entity_id)
{
    global $log,$adb, $table_prefix;
    $log->debug("Entering is_faxId(".$module.",".$entity_id.") method");

    $module = getSalesEntityType($entity_id);
    if($module == 'Contacts')
    {
        $sql = "select fax from ".$table_prefix."_contactdetails inner join ".$table_prefix."_crmentity on ".$table_prefix."_crmentity.crmid = ".$table_prefix."_contactdetails.contactid where contactid = ?";
        $result = $adb->pquery($sql, array($entity_id));
        $fax = $adb->query_result($result,0,"fax");
        if($fax != "")
        {
            $check_faxids = "true";
        }
        else
            $check_faxids = "false";
    }
    elseif($module == 'Leads')
    {
        $sql = "select fax from ".$table_prefix."_leadaddress inner join ".$table_prefix."_crmentity on ".$table_prefix."_crmentity.crmid = ".$table_prefix."_leadaddress.leadaddressid where leadaddressid = ?";
        $result = $adb->pquery($sql, array($entity_id));
        $fax = $adb->query_result($result,0,"fax");
        if($fax != "")
        {
            $check_faxids = "true";
        }
        else
            $check_faxids = "false";
    }
    elseif($module == 'Accounts')
    {
        $sql = "select fax from ".$table_prefix."_account inner join ".$table_prefix."_crmentity on ".$table_prefix."_crmentity.crmid = ".$table_prefix."_account.accountid where accountid = ?";
        $result = $adb->pquery($sql, array($entity_id));
        $fax = $adb->query_result($result,0,"fax");
        if($fax != "")
        {
            $check_faxids = "true";
        }
        else
            $check_faxids = "false";
    }
    $log->debug("Exiting is_faxId() method ...");
    return $check_faxids;
}
function is_smsId($entity_id)
{
    global $log,$adb, $table_prefix;
    $log->debug("Entering is_smsId(".$module.",".$entity_id.") method");

    $module = getSalesEntityType($entity_id);
    if($module == 'Contacts')
    {
        $sql = "select mobile from ".$table_prefix."_contactdetails inner join ".$table_prefix."_crmentity on ".$table_prefix."_crmentity.crmid = ".$table_prefix."_contactdetails.contactid where contactid = ?";
        $result = $adb->pquery($sql, array($entity_id));
        $sms = $adb->query_result($result,0,"sms");
        if($sms != "")
        {
            $check_smsids = "true";
        }
        else
            $check_smsids = "false";
    }
    elseif($module == 'Leads')
    {
        $sql = "select mobile from ".$table_prefix."_leadaddress inner join ".$table_prefix."_crmentity on ".$table_prefix."_crmentity.crmid = ".$table_prefix."_leadaddress.leadaddressid where leadaddressid = ?";
        $result = $adb->pquery($sql, array($entity_id));
        $sms = $adb->query_result($result,0,"sms");
        if($sms != "")
        {
            $check_smsids = "true";
        }
        else
            $check_smsids = "false";
    }
    $log->debug("Exiting is_smsId() method ...");
    return $check_smsids;
}
//crmv@7216e
//crmv@7216e

/**
 * This function is used to get cvid of default "all" view for any module.
 * @return a cvid of a module
 */
function getCvIdOfAll($module)
{
    global $adb,$log, $table_prefix;
    $log->debug("Entering getCvIdOfAll($module)");
    $qry_res = $adb->pquery("select cvid from ".$table_prefix."_customview where viewname='All' and entitytype=?", array($module));
    $cvid = $adb->query_result($qry_res,0,"cvid");
    $log->debug("Exiting getCvIdOfAll($module)");
    return $cvid;


}


// crmv@105600
/** gives the option  to display  the tagclouds or not for the current user
 ** @param $id -- user id:: Type integer
 ** @returns true or false in $tag_cloud_view
 ** Added to provide User based Tagcloud
 **/
function getTagCloudView($id=""){
	global $log;
	global $adb, $table_prefix;
	$log->debug("Entering in function getTagCloudView($id)");
	
	$cache = RCache::getInstance();
	$key = "tagcloud_$id";
	$result = $cache->get($key);
	if (!empty($result)) return $result;
	
	if($id == ''){
		$tag_cloud_status =1;
	}else{
		$query = "select visible from ".$table_prefix."_homestuff where userid=? and stufftype='Tag Cloud'";
		$tag_cloud_status = $adb->query_result($adb->pquery($query, array($id)),0,'visible');
	}

	if($tag_cloud_status == 0){
		$tag_cloud_view='true';
	}else{
		$tag_cloud_view='false';
	}
	$cache->set($key, $tag_cloud_view);

	$log->debug("Exiting from function getTagCloudView($id)");
	return $tag_cloud_view;
}
// crmv@105600e

/** Stores the option in database to display  the tagclouds or not for the current user
 ** @param $id -- user id:: Type integer
 ** Added to provide User based Tagcloud
 **/
function SaveTagCloudView($id="")
{
	global $log;
	global $adb, $table_prefix;
	$log->debug("Entering in function SaveTagCloudView($id)");
	$tag_cloud_status=$_REQUEST['tagcloudview'];

	if($tag_cloud_status == "true"){
		$tag_cloud_view = 0;
	}else{
		$tag_cloud_view = 1;
	}

	if($id == ''){
		$tag_cloud_view =1;
	}else{
		$query = "update ".$table_prefix."_homestuff set visible = ? where userid=? and stufftype='Tag Cloud'";
		$adb->pquery($query, array($tag_cloud_view,$id));
	}

	$log->debug("Exiting from function SaveTagCloudView($id)");
}

/**     function used to change the Type of Data for advanced filters in custom view and Reports
 **     @param string $table_name - tablename value from field table
 **     @param string $column_nametable_name - columnname value from field table
 **     @param string $type_of_data - current type of data of the field. It is to return the same TypeofData
 **            if the  field is not matched with the $new_field_details array.
 **     return string $type_of_data - If the string matched with the $new_field_details array then the Changed
 **           typeofdata will return, else the same typeofdata will return.
 **
 **     EXAMPLE: If you have a field entry like this:
 **
 **         fieldlabel         | typeofdata | tablename            | columnname       |
 **            -------------------+------------+----------------------+------------------+
 **        Potential Name     | I~O        | vtiger_quotes        | potentialid      |
 **
 **     Then put an entry in $new_field_details  like this:
 **
 **                "vtiger_quotes:potentialid"=>"V",
 **
 **    Now in customview and report's advance filter this field's criteria will be show like string.
 **
 **/
function ChangeTypeOfData_Filter($table_name,$column_name,$type_of_data)
{
    global $adb,$log, $table_prefix;
    //$log->debug("Entering function ChangeTypeOfData_Filter($table_name,$column_name,$type_of_data)");
    $field=$table_name.":".$column_name;
    //Add the field details in this array if you want to change the advance filter field details

    $new_field_details = Array(

        //Contacts Related Fields
        $table_prefix."_contactdetails:accountid"=>"V",
        $table_prefix."_contactsubdetails:birthday"=>"D",
        $table_prefix."_contactdetails:email"=>"V",
        $table_prefix."_contactdetails:yahooid"=>"V",

        //Potential Related Fields
        $table_prefix."_potential:campaignid"=>"V",

        //Account Related Fields
        $table_prefix."_account:parentid"=>"V",
        $table_prefix."_account:email1"=>"V",
        $table_prefix."_account:email2"=>"V",

        //Lead Related Fields
        $table_prefix."_leaddetails:email"=>"V",
        $table_prefix."_leaddetails:yahooid"=>"V",

        //Notes Related Fields
        $table_prefix."_senotesrel:crmid"=>"V",

        //Calendar Related Fields
        $table_prefix."_seactivityrel:crmid"=>"V",
        $table_prefix."_seactivityrel:contactid"=>"V",
        $table_prefix."_recurringevents:recurringtype"=>"V",

        //HelpDesk Related Fields
        $table_prefix."_troubletickets:parent_id"=>"V",
        $table_prefix."_troubletickets:product_id"=>"V",

        //Product Related Fields
        $table_prefix."_products:discontinued"=>"C",
        $table_prefix."_products:vendor_id"=>"V",
        $table_prefix."_products:handler"=>"V",
        $table_prefix."_products:productlineid"=>"V",	//crmv@54531

        //Faq Related Fields
        $table_prefix."_faq:product_id"=>"V",

        //Vendor Related Fields
        $table_prefix."_vendor:email"=>"V",

        //Quotes Related Fields
        $table_prefix."_quotes:potentialid"=>"V",
        $table_prefix."_quotes:inventorymanager"=>"V",
        $table_prefix."_quotes:accountid"=>"V",

        //Purchase Order Related Fields
        $table_prefix."_purchaseorder:vendorid"=>"V",
        $table_prefix."_purchaseorder:contactid"=>"V",

        //SalesOrder Related Fields
        $table_prefix."_salesorder:potentialid"=>"V",
        $table_prefix."_salesorder:quoteid"=>"V",
        $table_prefix."_salesorder:contactid"=>"V",
        $table_prefix."_salesorder:accountid"=>"V",

        //Invoice Related Fields
        $table_prefix."_invoice:salesorderid"=>"V",
        $table_prefix."_invoice:contactid"=>"V",
        $table_prefix."_invoice:accountid"=>"V",

        //Campaign Related Fields
        $table_prefix."_campaign:product_id"=>"V",

        //Related List Entries(For Report Module)
        $table_prefix."_activityproductrel:activityid"=>"V",
        $table_prefix."_activityproductrel:productid"=>"V",

        $table_prefix."_campaigncontrel:campaignid"=>"V",
        $table_prefix."_campaigncontrel:contactid"=>"V",

        $table_prefix."_campaignleadrel:campaignid"=>"V",
        $table_prefix."_campaignleadrel:leadid"=>"V",

        $table_prefix."_cntactivityrel:contactid"=>"V",
        $table_prefix."_cntactivityrel:activityid"=>"V",

        $table_prefix."_contpotentialrel:contactid"=>"V",
        $table_prefix."_contpotentialrel:potentialid"=>"V",

        $table_prefix."_crmentitynotesrel:crmid"=>"V",
        $table_prefix."_crmentitynotesrel:notesid"=>"V",

        $table_prefix."_leadacctrel:leadid"=>"V",
        $table_prefix."_leadacctrel:accountid"=>"V",

        $table_prefix."_leadcontrel:leadid"=>"V",
        $table_prefix."_leadcontrel:contactid"=>"V",

        $table_prefix."_leadpotrel:leadid"=>"V",
        $table_prefix."_leadpotrel:potentialid"=>"V",

        $table_prefix."_pricebookproductrel:pricebookid"=>"V",
        $table_prefix."_pricebookproductrel:productid"=>"V",

        $table_prefix."_seactivityrel:crmid"=>"V",
        $table_prefix."_seactivityrel:activityid"=>"V",

        $table_prefix."_senotesrel:crmid"=>"V",
        $table_prefix."_senotesrel:notesid"=>"V",

        $table_prefix."_seproductsrel:crmid"=>"V",
        $table_prefix."_seproductsrel:productid"=>"V",

        $table_prefix."_seticketsrel:crmid"=>"V",
        $table_prefix."_seticketsrel:ticketid"=>"V",

        $table_prefix."_vendorcontactrel:vendorid"=>"V",
        $table_prefix."_vendorcontactrel:contactid"=>"V",

    	$table_prefix."_pricebook:currency_id"=>"V",
    );

    //If the Fields details does not match with the array, then we return the same typeofdata
    if(isset($new_field_details[$field]))
    {
        $type_of_data = $new_field_details[$field];
    }
    //$log->debug("Exiting function with the typeofdata: $type_of_data ");
    return $type_of_data;
}


/** Returns the URL for Basic and Advance Search
 ** Added to fix the issue 4600
 **/
function getBasic_Advance_SearchURL()
{

	$url = '';
	if($_REQUEST['searchtype'] == 'BasicSearch')
	{
		$url .= (isset($_REQUEST['query']))?'&query='.vtlib_purify($_REQUEST['query']):'';
		$url .= (isset($_REQUEST['search_field']))?'&search_field='.vtlib_purify($_REQUEST['search_field']):'';
		$url .= (isset($_REQUEST['search_text']))?'&search_text='.to_html(vtlib_purify($_REQUEST['search_text'])):'';
		$url .= (isset($_REQUEST['searchtype']))?'&searchtype='.vtlib_purify($_REQUEST['searchtype']):'';
		$url .= (isset($_REQUEST['type']))?'&type='.vtlib_purify($_REQUEST['type']):'';
	}
	if ($_REQUEST['searchtype'] == 'advance')
	{
		$url .= (isset($_REQUEST['query']))?'&query='.vtlib_purify($_REQUEST['query']):'';
		$count=$_REQUEST['search_cnt'];
		for($i=0;$i<$count;$i++)
		{
			$url .= (isset($_REQUEST['Fields'.$i]))?'&Fields'.$i.'='.stripslashes(str_replace("'","",vtlib_purify($_REQUEST['Fields'.$i]))):'';
			$url .= (isset($_REQUEST['Condition'.$i]))?'&Condition'.$i.'='.vtlib_purify($_REQUEST['Condition'.$i]):'';
			$url .= (isset($_REQUEST['Srch_value'.$i]))?'&Srch_value'.$i.'='.to_html(vtlib_purify($_REQUEST['Srch_value'.$i])):'';
		}
		$url .= (isset($_REQUEST['searchtype']))?'&searchtype='.vtlib_purify($_REQUEST['searchtype']):'';
		$url .= (isset($_REQUEST['search_cnt']))?'&search_cnt='.vtlib_purify($_REQUEST['search_cnt']):'';
		$url .= (isset($_REQUEST['matchtype']))?'&matchtype='.vtlib_purify($_REQUEST['matchtype']):'';
	}
	return $url;

}

/** Clear the Smarty cache files(in Smarty/smarty_c)
 ** This function will called after migration.
 **/
function clear_smarty_cache($path=null) {

    global $root_directory;
    if($path == null) {
        $path=$root_directory.'Smarty/templates_c/';
    }
    $mydir = @opendir($path);
    while(false !== ($file = readdir($mydir))) {
        if($file != "." && $file != ".." && $file != ".svn") {
            //chmod($path.$file, 0777);
            if(is_dir($path.$file)) {
                chdir('.');
                clear_smarty_cache($path.$file.'/');
                //rmdir($path.$file) or DIE("couldn't delete $path$file<br />"); // No need to delete the directories.
            }
            else {
                // Delete only files ending with .tpl.php
                if(strripos($file, '.tpl.php') == (strlen($file)-strlen('.tpl.php'))) {
                    unlink($path.$file) or DIE("couldn't delete $path$file<br />");
                }
            }
        }
    }
    @closedir($mydir);
}

/** Get Smarty compiled file for the specified template filename.
 ** @param $template_file Template filename for which the compiled file has to be returned.
 ** @return Compiled file for the specified template file.
 **/
function get_smarty_compiled_file($template_file, $path=null) {

    global $root_directory;
    if($path == null) {
        $path=$root_directory.'Smarty/templates_c/';
    }
    $mydir = @opendir($path);
    $compiled_file = null;
    while(false !== ($file = readdir($mydir)) && $compiled_file == null) {
        if($file != "." && $file != ".." && $file != ".svn") {
            //chmod($path.$file, 0777);
            if(is_dir($path.$file)) {
                chdir('.');
                $compiled_file = get_smarty_compiled_file($template_file, $path.$file.'/');
                //rmdir($path.$file) or DIE("couldn't delete $path$file<br />"); // No need to delete the directories.
            }
            else {
                // Check if the file name matches the required template fiel name
                if(strripos($file, $template_file.'.php') == (strlen($file)-strlen($template_file.'.php'))) {
                    $compiled_file = $path.$file;
                }
            }
        }
    }
    @closedir($mydir);
    return $compiled_file;
}

/** Function to carry out all the necessary actions after migration */
function perform_post_migration_activities() {
    //After applying all the DB Changes,Here we clear the Smarty cache files
    clear_smarty_cache();
    //Writing tab data in flat file
    create_tab_data_file();
    create_parenttab_data_file();
}

//ds@26
function getDisplayDateToDB($cur_date_val)
{
	global $log;

		$date_value = explode(' ',$cur_date_val);
		list($y,$m,$d) = explode('-',$date_value[0]);

			$display_date = $d.'.'.$m.'.'.$y;

	return $display_date;

}
//ds@26e

//------------------crmvillage 504 release start----------------------
function crmv_getAccountBanking($account_id)
{
	global $log;
	$log->debug("Entering crmv_getAccountBanking(".$account_id.") method ...");
	$log->info("in crmv_getAccountBanking ".$account_id);

	global $adb, $table_prefix;
	if($account_id != '')
	{
		$sql = "select crmv_bankdetails from ".$table_prefix."_account where accountid=".$account_id;
		$result = $adb->query($sql);
		$accountname = $adb->query_result($result,0,"crmv_bankdetails");
	}


	$log->debug("Exiting crmv_getAccountBanking method ...");
	return $accountname;
}

function crmv_getAccountPiva($account_id)
{
	global $log,$app_strings;
	$log->debug("Entering crmv_getAccountPiva(".$account_id.") method ...");
	$log->info("in crmv_getAccountPiva ".$account_id);

	global $adb, $table_prefix;
	if($account_id != '')
	{
		$sql = "select crmv_vat_registration_number,crmv_social_security_number from ".$table_prefix."_account where accountid=".$account_id;
		$result = $adb->query($sql);
		$piva1 = $adb->query_result($result,0,"crmv_vat_registration_number");
		$piva2 = $adb->query_result($result,0,"crmv_social_security_number");
	}

	if( isset($piva1) && $piva1 != "") {
		$piva = $app_strings["LBL_PIVA"]." : ".$piva1;
	} else if(isset($piva2) && $piva2 != "") {
		$piva = $app_strings["LBL_CF"]." : ".$piva2;
	} else $piva = "";



	$log->debug("Exiting crmv_getAccountPiva method ...");
	return $piva;
}

function crmv_getDisplayDate($cur_date_val)
{
	global $log;
	$log->debug("Entering getDisplayDate(".$cur_date_val.") method ...");
	global $current_user;
	$dat_fmt = $current_user->date_format;
	if($dat_fmt == '')
	{
		$dat_fmt = 'dd-mm-yyyy';
	}

	$date_value = explode(' ',$cur_date_val);
	list($y,$m,$d) = explode('-',$date_value[0]);
	if($dat_fmt == 'dd-mm-yyyy')
	{
		$display_date = $d.'/'.$m.'/'.$y;
	}
	elseif($dat_fmt == 'mm-dd-yyyy')
	{

		$display_date = $d.'/'.$m.'/'.$y;
	}
	elseif($dat_fmt == 'yyyy-mm-dd')
	{

		$display_date = $d.'/'.$m.'/'.$y;
	}

	if($date_value[1] != '')
	{
		$display_date = $display_date.' '.$date_value[1];
	}
	$log->debug("Exiting getDisplayDate method ...");
	return $display_date;

}
//------------------crmvillage 504 release stop-----------------------
//crmv@15309
function filter_template_fields($arr,$remove){
	if (!$remove)
		return $arr;
	$type_to_remove = Array('autogenerated', 'reference', 'owner', 'password'); // crmv@109388
	$ret_arr = Array();
	foreach ($arr as $id=>$arr2){
		if (in_array($arr2[type][name],$type_to_remove))
			continue;
		$ret_arr[$id] = $arr2;
	}
	return $ret_arr;
}
//crmv@31358
function construct_template_fields($arr,$type,$module,$related_field=''){
	$alias = Array();
	switch($type){
		case 'master':
			$ret_arr['label'] = $arr['label'];
			$alias['master'] = $module;
			break;
		case 'related':
			// crmv@56235
			// these are fake modules and there is no translation, but they trigger
			// a reload of all language strings causing a big delay
			if ($module == 'Currency' || $module == 'DocumentFolders') {
				$modLabel = $module;
			} else {
				$modLabel = getTranslatedString($module,'APP_STRINGS');
				if (empty($modLabel) || $modLabel === $module) $modLabel = getTranslatedString($module,$module);
			}
			// crmv@56235e
			$ret_arr['label'] = $arr['label_reference'].": ($modLabel) ".$arr['label'];
			$alias['master'] = $arr['module_reference'];
			if ($related_field != ''){
				$alias['related'] = $module."#".$related_field;
			}
			else{
				$alias['related'] = $module;
			}
			break;
		case 'custom':
			$ret_arr['label'] = getTranslatedString($arr['label']);
			$alias['master'] = 'custom';
			break;
		default:
			break;
	}
	$alias['name'] = $arr['name'];
	$ret_arr['alias'] = "$".$alias['master']."|".$alias['related']."|".$alias['name']."$";
	return array_values($ret_arr);
}
//crmv@31358e
/** Function To create Email template variables dynamically -- Pavani */
function getEmailTemplateFields($remove=true,$module=''){ //crmv@24644
	global $adb,$current_user, $table_prefix;
	$fieldsCache = array(); // crmv@56235
	$sql = "select ".$table_prefix."_tab.name,".$table_prefix."_tab.tabid from ".$table_prefix."_tab
		   inner join ".$table_prefix."_relatedlists on ".$table_prefix."_relatedlists.tabid = ".$table_prefix."_tab.tabid
		   where related_tabid = ?";
	$params = array(getTabid('Messages')); // crmv@38592
	//crmv@24644
	if ($module != '') {
		$sql .= " and ".$table_prefix."_tab.name = ?";
		$params[] = $module;
	}
	//crmv@24644 e
	$res = $adb->pquery($sql,$params);
	if ($res && $adb->num_rows($res) > 0){
		while ($row = $adb->fetchByAssoc($res)){
			$fields = Array();
			$related_fields = Array();
			// crmv@56235
			if (!array_key_exists($row['name'], $fieldsCache)) {
				$handler = vtws_getModuleHandlerFromName($row['name'], $current_user);
				$fieldsCache[$row['name']] = $handler->getModuleFields();
			}
			$fields = $fieldsCache[$row['name']];
			// crmv@56235e
			//see the assigned user as module related
			foreach ($fields as $id=>$arr){
				if ($arr['type']['name'] == 'owner'){
					$arr['type']['name']='reference';
					$arr['type']['refersTo'][]='Users';
				}
				if ($arr['type']['name'] == 'reference' || $arr['type']['name'] == 'owner'){
					if ($arr['type']['name'] == 'owner')
					$arr['type']['refersTo'][]='Users';
					foreach ($arr['type']['refersTo'] as $related_module){
						// crmv@56235
						if (!array_key_exists($related_module, $fieldsCache)) {
							$handler_related = vtws_getModuleHandlerFromName($related_module, $current_user);
							$fieldsCache[$related_module] = $handler_related->getModuleFields();
						}
						$related_fields = $fieldsCache[$related_module];
						// crmv@56235e
						foreach ($related_fields as $id2=>$arr2){
							$related_fields[$id2]['label_reference'] = $arr['label'];
							$related_fields[$id2]['module_reference'] = $row['name'];
							$related_fields[$id2]['id_reference'] = $id;
							$related_fields[$id2]['name_reference'] = $arr2['name'];
						}
						//crmv@31358
						$fields_total[$row['name']]['related'][$arr['name']][$related_module] = filter_template_fields($related_fields,$remove);
						//crmv@31358e
					}
				}
			}
			$fields_total[$row['name']]['master'] = filter_template_fields($fields,$remove);
		}
	}
	getAdvancedTemplateEmailFields($fields_total); //crmv@22700
	//add users fields
	$module = 'Users';
	// crmv@56235
	if (!array_key_exists($module, $fieldsCache)) {
		$handler = vtws_getModuleHandlerFromName($module, $current_user);
		$fieldsCache[$module] = $handler->getModuleFields();
	}
	$fields = $fieldsCache[$module];
	// crmv@56235e
	if ($remove)
		$fields_total[$module]['master'] = filter_template_fields($fields,$remove);
	//add custom fields
	$fields_total['Custom']['custom'][] = Array(
		'label'=>getTranslatedString('Current Date'),
		'name'=>'date',
	);
	$fields_total['Custom']['custom'][] = Array(
		'label'=>getTranslatedString('Current Time'),
		'name'=>'time',
	);
	return $fields_total;
}
function getEmailTemplateVariables(){
	$fields = getEmailTemplateFields();
	foreach ($fields as $module=>$arr){
		if (is_array($arr['master'])){
			foreach ($arr['master'] as $id=>$arr_fields){
				$alloptions[$module][] = construct_template_fields($arr_fields,'master',$module);
			}
		}
		if (is_array($arr['related'])){
			//crmv@31358
			foreach ($arr['related'] as $related_field=>$arr2){
				foreach ($arr2 as $related_module=>$arr3){
					foreach ($arr3 as $id=>$arr_fields){
						$alloptions[$module][] = construct_template_fields($arr_fields,'related',$related_module,$related_field);
					}
				}
			}
			//crmv@31358e
		}
		if (is_array($arr['custom'])){
			foreach ($arr['custom'] as $id=>$arr_fields){
				$alloptions[$module][] = construct_template_fields($arr_fields,'custom',$module);
			}
		}
	}
	return $alloptions;
}
//crmv@15309 end
//crmv@22700
function getTemplateTypeValues($value) {
	$templatetypes = array('Email','Newsletter');
	$result = array();
	foreach($templatetypes as $type) {
		$selected = '';
		if ($type == $value) {
			$selected = 'selected';
		}
		$result[] = array('value'=>$type,'selected'=>$selected,'label'=>getTranslatedString($type));
	}
	return $result;
}
function getAdvancedTemplateEmailFields(&$fields) {
	if (isModuleInstalled('Newsletter')) {
		$fields['Newsletter'] = getNewsletterTemplateEmailFields();
	}
}
function getNewsletterTemplateEmailFields() {
	global $adb, $table_prefix;
	require_once('vtlib/Vtecrm/Module.php');
	$newsletterInstance = Vtiger_Module::getInstance('Newsletter');
	$info = getNewsletterTemplateEmailInfoFields();
	$fields = array();
	foreach($info as $label => $info) {
		$fields[] = array('label'=>getTranslatedString($label,'Newsletter'),'name'=>$info['field']);
	}
	//crmv@2285m
	if(isModuleInstalled('Fairs')) {
		$fields[] = array('label'=>getTranslatedString('LBL_UNSUBSCRIPTION_LINK_DATA_PROCESSING','Newsletter'),'name'=>'tracklink#unsubscription_data_processing');
		$fields[] = array('label'=>getTranslatedString('LBL_UNSUBSCRIPTION_LINK_FAIR_COMUNICATIONS','Newsletter'),'name'=>'tracklink#unsubscription_fair_comunications');
		$fields[] = array('label'=>getTranslatedString('LBL_UNSUBSCRIPTION_LINK_THIRD_PARTY_COMUNICATIONS','Newsletter'),'name'=>'tracklink#unsubscription_third_party_comunications');
		$fields[] = array('label'=>getTranslatedString('LBL_UNSUBSCRIPTION_LINK_FAIR','Newsletter'),'name'=>'tracklink#unsubscription_fair');
	} else {
		$fields[] = array('label'=>getTranslatedString('LBL_UNSUBSCRIPTION_LINK','Newsletter'),'name'=>'tracklink#unsubscription');
	}
	//crmv@2285me
	$fields[] = array('label'=>getTranslatedString('LBL_PREVIEW_LINK','Newsletter'),'name'=>'tracklink#preview'); // crmv@38592
	$result = $adb->query('SELECT fieldlabel,fieldname FROM '.$table_prefix.'_field WHERE tabid = '.$newsletterInstance->id.' AND presence IN (0,2) AND readonly <> 100 ORDER BY block,sequence');
	while($row=$adb->fetchByAssoc($result)) {
		$fields[] = array('label'=>getTranslatedString($row['fieldlabel'],'Newsletter').' (Newsletter)','name'=>$row['fieldname']);
	}
	return array('master'=>$fields);
}
function getNewsletterTemplateEmailInfoFields($field='') {
	$return = array();
	$return['Target Name'] = array('field'=>'target_name','realfield'=>array('Accounts'=>'accountname','Contacts'=>'lastname','Leads'=>'lastname'));
	$return['Target Number'] = array('field'=>'target_no','realfield'=>array('Accounts'=>'account_no','Contacts'=>'contact_no','Leads'=>'lead_no'));
	$return['Target Phone'] = array('field'=>'target_phone','realfield'=>array('Accounts'=>'phone','Contacts'=>'phone','Leads'=>'phone'));
	$return['Target Fax'] = array('field'=>'target_fax','realfield'=>array('Accounts'=>'fax','Contacts'=>'fax','Leads'=>'fax'));
	$return['Target Other Phone'] = array('field'=>'target_otherphone','realfield'=>array('Accounts'=>'otherphone','Contacts'=>'mobile','Leads'=>'mobile'));
	$return['Target Email'] = array('field'=>'target_email','realfield'=>array('Accounts'=>'email1','Contacts'=>'email','Leads'=>'email'));
	$return['Target Assigned To'] = array('field'=>'target_assigned_user_id','realfield'=>array('Accounts'=>'assigned_user_id','Contacts'=>'assigned_user_id','Leads'=>'assigned_user_id'));
	$return['Target Created Time'] = array('field'=>'target_createdtime','realfield'=>array('Accounts'=>'createdtime','Contacts'=>'createdtime','Leads'=>'createdtime'));
	$return['Target Modified Time'] = array('field'=>'target_modifiedtime','realfield'=>array('Accounts'=>'modifiedtime','Contacts'=>'modifiedtime','Leads'=>'modifiedtime'));
	$return['Target Street'] = array('field'=>'target_street','realfield'=>array('Accounts'=>'bill_street','Contacts'=>'mailingstreet','Leads'=>'lane'));
	$return['Target City'] = array('field'=>'target_city','realfield'=>array('Accounts'=>'bill_city','Contacts'=>'mailingcity','Leads'=>'city'));
	$return['Target State'] = array('field'=>'target_state','realfield'=>array('Accounts'=>'bill_state','Contacts'=>'mailingstate','Leads'=>'state'));
	$return['Target Code'] = array('field'=>'target_code','realfield'=>array('Accounts'=>'bill_code','Contacts'=>'mailingzip','Leads'=>'code'));
	$return['Target Country'] = array('field'=>'target_country','realfield'=>array('Accounts'=>'bill_country','Contacts'=>'mailingcountry','Leads'=>'country'));
	$return['Target Po Box'] = array('field'=>'target_pobox','realfield'=>array('Accounts'=>'bill_pobox','Contacts'=>'mailingpobox','Leads'=>'pobox'));
	$return['Target Description'] = array('field'=>'target_description','realfield'=>array('Accounts'=>'description','Contacts'=>'description','Leads'=>'description'));
	if ($field != '') {
		foreach($return as $label => $info) {
			if ($info['field'] == $field)	return $return[$label];
		}
	}
	return $return;
}
//crmv@22700e
//crmv@manuele
function getPickListValues($tablename,$roleid)
{
	global $adb, $table_prefix;
	$query = "select $tablename from ".$table_prefix."_$tablename inner join ".$table_prefix."_role2picklist on ".$table_prefix."_role2picklist.picklistvalueid = ".$table_prefix."_$tablename.picklist_valueid where roleid=? and picklistid in (select picklistid from ".$table_prefix."_picklist) order by sortid";
	$result = $adb->pquery($query, array($roleid));
	$fldVal = Array();
	while($row = $adb->fetch_array($result))
	{
		$fldVal []= $row[$tablename];
	}
	return $fldVal;
}
//crmv@manuele-e

//crmv@34862
/**
 * Convert relative path to real path (doesn't replace symbolic links)
 * @param $path relative path
 * @return false for invalid path or the real path.
 */
function realpath_nolinks($path) {
	global $root_directory;

	// Set the base directory to compare with
	$use_root_directory = $root_directory;
	if(empty($use_root_directory)) {
		$use_root_directory = realpath(dirname(__FILE__).'/../../.');
	}

	// check if path begins with "/" ie. is absolute
	// if it isnt concat with root dir
	$path = str_replace('\\\\', '\\', $path);
	$path = str_replace("\\", "/", $path);
	$absWinPath = strpos($path, ":") === 1;
	$absLinuxPath = strpos($path, "/") === 0;
	if (!$absLinuxPath && !$absWinPath) {
		$path = $use_root_directory . "/" . $path;
		$absLinuxPath = strpos($path, "/") === 0;
	}

	// canonicalize
	$path = explode('/', $path);
	$newpath = array();
	for ($i = 0; $i < sizeof($path); ++$i) {
		if ($path[$i] === '' || $path[$i] === '.') continue;
		if ($path[$i] === '..') {
			array_pop($newpath);
			continue;
		}
		array_push($newpath, $path[$i]);
	}
	$finalpath = ($absLinuxPath ? "/" : "") . implode('/', $newpath);
	// check then return valid path or filename
	return (file_exists($finalpath) ? $finalpath : false);
}

/** Function to check the file access is made within web root directory. */
function checkFileAccess($filepath) {
	global $root_directory;
	// Set the base directory to compare with
	$use_root_directory = $root_directory;
	if(empty($use_root_directory)) {
		$use_root_directory = realpath(dirname(__FILE__).'/../../.');
	}

	$realfilepath = realpath_nolinks($filepath);
	if (!$realfilepath) die($filepath);

	/** Replace all \\ with \ first */
	$realfilepath = str_replace('\\\\', '\\', $realfilepath);
	$rootdirpath  = str_replace('\\\\', '\\', $use_root_directory);

	/** Replace all \ with / now */
	$realfilepath = str_replace('\\', '/', $realfilepath);
	$rootdirpath  = str_replace('\\', '/', $rootdirpath);
	if(stripos($realfilepath, $rootdirpath) !== 0) {
		die($filepath);
	}
}
//crmv@34862e

// crmv@37463
/** Function to check the file deletion within the deletable (safe) directories*/
function checkFileAccessForDeletion($filepath) {
	global $root_directory;
	// Set the base directory to compare with
	$use_root_directory = $root_directory;
	if (empty($use_root_directory)) {
		$use_root_directory = realpath(dirname(__FILE__) . '/../../.');
	}

	$safeDirectories = array('storage', 'cache', 'test');

	$realfilepath = realpath_nolinks($filepath);

	/** Replace all \\ with \ first */
	$realfilepath = str_replace('\\\\', '\\', $realfilepath);
	$rootdirpath = str_replace('\\\\', '\\', $use_root_directory);

	/** Replace all \ with / now */
	$realfilepath = str_replace('\\', '/', $realfilepath);
	$rootdirpath = str_replace('\\', '/', $rootdirpath);

	$relativeFilePath = str_replace($rootdirpath, '', $realfilepath);
	$filePathParts = explode('/', $relativeFilePath);

	if (stripos($realfilepath, $rootdirpath) !== 0 || !in_array($filePathParts[0], $safeDirectories)) {
		die("Sorry! Attempt to access restricted file.");
	}
}
// crmv@37463


/** Function to get the ActivityType for the given entity id
 *  @param entityid : Type Integer
 *  return the activity type for the given id
 */
function getActivityType($id)
{
	static $cache = array(); 
	if (!isset($cache[$id])) {
		global $adb, $table_prefix;
		$res = $adb->pquery("select activitytype from {$table_prefix}_activity where activityid=?", array($id));
		$acti_type = $cache[$id] = $adb->query_result($res,0,"activitytype");
	} else {
		$acti_type = $cache[$id];
	}
	return $acti_type;
}

// crmv@101930
/** Function to get owner name either user or group */
function getOwnerName($id, $with_name=false, &$groupNameList=null)
{
	global $adb, $log;
	$log->debug("Entering getOwnerName(".$id.") method ...");

	$ownerList = getOwnerNameList(array($id), $with_name, $groupNameList);
	$groupNameList = $groupNameList[$id];
	return $ownerList[$id];
}

/**
 * Function to get the owner type: "Users" or "Groups"
 */
function getOwnerType($id) {
	global $adb, $table_prefix;

	$result = $adb->pquery("SELECT id FROM {$table_prefix}_users WHERE id = ?", array($id));
	if ($result && $adb->num_rows($result) > 0) {
		return 'Users';
	} else {
		$result = $adb->pquery("SELECT groupid FROM {$table_prefix}_groups WHERE groupid = ?", array($id));
		if ($result && $adb->num_rows($result) > 0) {
			return 'Groups';
		}
	}
	return null;
}

/** Function to get owner name either user or group
 * If owner i a group in groupInfo there are some informations
 */
function getOwnerNameList($idList, $with_name=false, &$groupNameList=null) {
	global $table_prefix;

	if(!is_array($idList) || count($idList) == 0) {
		return array();
	}
	
	$nameList = array();
	$db = PearDatabase::getInstance();
	$sql = "select id,user_name,first_name,last_name from ".$table_prefix."_users where id in (".generateQuestionMarks($idList).")";
	$result = $db->pquery($sql, $idList);
	$it = new SqlResultIterator($db, $result);
	foreach ($it as $row) {
		$user_name = $row->user_name;
		if ($with_name) {
			$user_name .= " (".trim($row->first_name." ".$row->last_name).")";
		}
		$nameList[$row->id] = $user_name;
	}
	$groupIdList = array_diff($idList, array_keys($nameList));
	if(count($groupIdList) > 0) {
		$sql = "select groupname,groupid from ".$table_prefix."_groups where groupid in (".
				generateQuestionMarks($groupIdList).")";
		$result = $db->pquery($sql, $groupIdList);
		$it = new SqlResultIterator($db, $result);
		foreach ($it as $row) {
			$nameList[$row->groupid] = $row->groupname;
			if (is_array($groupNameList)) {
				require_once('include/utils/GetGroupUsers.php');
				$groupUsers = new GetGroupUsers();
				$groupUsers->getAllUsersInGroup($row->groupid,true);
				$groupNameList[$row->groupid] = getOwnerNameList($groupUsers->group_users,$with_name);
			}
		}
	}
	return $nameList;
}
// crmv@101930e


/**
 * This function is used to get the blockid of the settings block for a given label.
 * @param $label - settings label
 * @return string type value
 */
function getSettingsBlockId($label) {
	global $log, $adb, $table_prefix;
	$log->debug("Entering getSettingsBlockId(".$label.") method ...");

	$blockid = '';
	$query = "select blockid from ".$table_prefix."_settings_blocks where label = ?";
	$result = $adb->pquery($query, array($label));
	$noofrows = $adb->num_rows($result);
	if($noofrows == 1) {
		$blockid = $adb->query_result($result,0,"blockid");
	}
	$log->debug("Exiting getSettingsBlockId method ...");
	return $blockid;
}

// Function to check if the logged in user is admin
// and if the module is an entity module
// and the module has a Settings.php file within it
function isModuleSettingPermitted($module){
	if (file_exists("modules/$module/Settings.php") &&
		isPermitted('Settings','index','') == 'yes') {

			return 'yes';
	}
	return 'no';
}

/**
 * this function returns the entity field name for a given module; for e.g. for Contacts module it return concat(lastname, ' ', firstname)
 * @param string $module - the module name
 * @return string $fieldsname - the entity field name for the module
 */
function getEntityField($module){
	global $adb, $table_prefix;
	$data = array();
	if(!empty($module)){
		 $query = "select fieldname,tablename,entityidfield from ".$table_prefix."_entityname where modulename = ?";
		 $result = $adb->pquery($query, array($module));
		 $fieldsname = $adb->query_result($result,0,'fieldname');
		 $tablename = $adb->query_result($result,0,'tablename');
		 $entityidfield = $adb->query_result($result,0,'entityidfield');
		 if(!(strpos($fieldsname,',') === false)){
			 $fieldsname = str_replace (",",",' ',",$fieldsname);
			 $fieldlists = explode(',',$fieldsname);
			 //crmv@22861
			 foreach ($fieldlists as $key=>$value){
		 		if ($value != "' '"){
		 			$fieldlists[$key] = $tablename.".".$value;
		 		}
			 }
			 //crmv@22861 end
			 $fieldsname = $adb->sql_concat($fieldlists);
		 }
	}
	$data = array("tablename"=>$tablename, "fieldname"=>$fieldsname);
	return $data;
}

// vtiger cache utility
require_once('include/utils/VTCacheUtils.php');

// vtlib customization: Extended vtiger CRM utlitiy functions
require_once('include/utils/VtlibUtils.php');
// END

function vt_suppressHTMLTags($string){
	return preg_replace(array('/</', '/>/', '/"/'), array('&lt;', '&gt;', '&quot;'), $string);
}

//crmv@21198
/**
 * this function returns the entity information for a given module; for e.g. for Contacts module
 * it returns the information of tablename, modulename, fieldsname and id gets from vtiger_entityname
 * @param string $module - the module name
 * @return array $data - the entity information for the module
 */
function getEntityFieldNames($module) {
	global $table_prefix;
	$adb = PearDatabase::getInstance();
	$data = array();
	if (!empty($module)) {
		$query = "select fieldname,modulename,tablename,entityidfield from ".$table_prefix."_entityname where modulename = ?";
		$result = $adb->pquery($query, array($module));
		$fieldsName = $adb->query_result($result, 0, 'fieldname');
		$tableName = $adb->query_result($result, 0, 'tablename');
		$entityIdField = $adb->query_result($result, 0, 'entityidfield');
		$moduleName = $adb->query_result($result, 0, 'modulename');
		if (!(strpos($fieldsName, ',') === false)) {
			$fieldsName = explode(',', $fieldsName);
		}
	}
	$data = array("tablename" => $tableName, "modulename" => $moduleName, "fieldname" => $fieldsName, "entityidfield" => $entityIdField);
	return $data;
}

function getSqlForNameInDisplayFormat($input, $module, $glue = ' ') {
	$adb = PearDatabase::getInstance();
	$entity_field_info = getEntityFieldNames($module);
	$tableName = $entity_field_info['tablename'];
	$fieldsName = $entity_field_info['fieldname'];
	if(is_array($fieldsName)) {
		foreach($fieldsName as $key => $value) {
			$formattedNameList[] = $input[$value];
		}
		$formattedNameListString = implode(",'" . $glue . "',", $formattedNameList);
	} else {
		$formattedNameListString = $input[$fieldsName];
	}
	$formattedNameList = explode(",",$formattedNameListString);
	$sqlString = $adb->sql_concat($formattedNameList);
	return $sqlString;
}
//crmv@21198e
?>