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
require_once('Smarty_setup.php');
require_once('include/utils/utils.php');
$smarty = new VTECRM_Smarty();

global $client;

$customerid = $_SESSION['customer_id'];
$sessionid = $_SESSION['customer_sessionid'];

// crmv@5946
// $paramspick = Array(Array('id'=>"$customerid"));
// $result_tickets = $client->call('picklist_tickets', $paramspick, $Server_Path, $Server_Path);

// $smarty->assign('POTENTIAL_USER',$result_tickets);
// crmv@5946e

$params = Array(Array('id'=>"$customerid", 'sessionid'=>"$sessionid", 'language'=>getPortalCurrentLanguage()));	//crmv@55264
$result = $client->call('get_combo_values', $params, $Server_Path, $Server_Path);

$_SESSION['combolist'] = $result;
$combolist = $_SESSION['combolist'];
for($i=0;$i<count($result);$i++)
{
	if($result[$i]['productid'] != '')
	{
		$productslist[0] = $result[$i]['productid'];
	}
	if($result[$i]['productname'] != '')
	{
		$productslist[1] = $result[$i]['productname'];
	}
	if($result[$i]['ticketpriorities'] != '')
	{
// 		$ticketpriorities_keys =  $result[$i]['ticketpriorities_keys'];
// 		$ticketpriorities = $result[$i]['ticketpriorities'];
		
// 		$ticketpriorities = array_combine($ticketpriorities_keys, array_values($ticketpriorities));
		$ticketpriorities = $result[$i]['ticketpriorities'];
	}
	if($result[$i]['ticketseverities'] != '')
	{
// 		$ticketseverities_keys =  $result[$i]['ticketseverities_keys'];
// 		$ticketseverities = $result[$i]['ticketseverities'];
		
// 		$ticketseverities = array_combine($ticketseverities_keys, array_values($ticketseverities));
		
		$ticketseverities = $result[$i]['ticketseverities'];
		
	}
	if($result[$i]['ticketcategories'] != '')
	{
// 		$ticketcategories_keys =  $result[$i]['ticketcategories_keys'];
// 		$ticketcategories = $result[$i]['ticketcategories'];
		
// 		$ticketcategories = array_combine($ticketcategories_keys, array_values($ticketcategories));
		
		$ticketcategories = $result[$i]['ticketcategories'];
	}
	if($result[$i]['servicename'] != ''){
		$servicename = $result[$i]['servicename'];
	}
	if($result[$i]['serviceid'] != ''){
		$serviceid= $result[$i]['serviceid'];
	}
}

if($productslist[0] != '#MODULE INACTIVE#'){
	$noofrows = count($productslist[0]);
	
	for($i=0;$i<$noofrows;$i++)
	{
		if($i > 0)
			$productarray .= ',';
		$productarray .= "'".$productslist[1][$i]."'";
	}
}
if($servicename == '#MODULE INACTIVE#' || $serviceid == '#MODULE INACTIVE#'){
	unset($servicename); 
	unset($serviceid);
}

$smarty->assign('PRODUCTARRAY',$productarray);
$smarty->assign('PRIORITY',$ticketpriorities);
$smarty->assign('SEVERITY',$ticketseverities);
$smarty->assign('CATEGORY',$ticketcategories);
$smarty->assign('PROJECTID',$_REQUEST['projectid']);

$filevalidation_script = <<<JSFILEVALIDATION
<script type="text/javascript">

function getFileNameOnly(filename) {
	var onlyfilename = filename;
  	// Normalize the path (to make sure we use the same path separator)
 	var filename_normalized = filename.replace(/\\\\/g, '/');
  	if(filename_normalized.lastIndexOf("/") != -1) {
    	onlyfilename = filename_normalized.substring(filename_normalized.lastIndexOf("/") + 1);
  	}
  	return onlyfilename;
}
/* Function to validate the filename */
function validateFilename(form_ele) {
if (form_ele.value == '') return true;
	var value = getFileNameOnly(form_ele.value);
	// Color highlighting logic
	var err_bg_color = "#FFAA22";
	if (typeof(form_ele.bgcolor) == "undefined") {
		form_ele.bgcolor = form_ele.style.backgroundColor;
	}
	// Validation starts here
	var valid = true;
	/* Filename length is constrained to 255 at database level */
	if (value.length > 255) {
		alert(alert_arr.LBL_FILENAME_LENGTH_EXCEED_ERR);
		valid = false;
	}
	if (!valid) {
		form_ele.style.backgroundColor = err_bg_color;
		return false;
	}
	form_ele.style.backgroundColor = form_ele.bgcolor;
	form_ele.form[form_ele.name + '_hidden'].value = value;
	return true;
}
</script>
JSFILEVALIDATION;

echo $filevalidation_script;

$smarty->display('NewTicket.tpl');
?>