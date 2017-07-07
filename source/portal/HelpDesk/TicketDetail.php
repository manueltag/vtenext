<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
*
 ********************************************************************************/
$smarty = new VTECRM_Smarty();

global $result;
global $client;
global $Server_Path;
$customerid = $_SESSION['customer_id'];
$sessionid = $_SESSION['customer_sessionid'];
if($ticketid != '')
{	
	$params = array('id' => "$ticketid", 'block'=>"$block",'contactid'=>$customerid,'sessionid'=>"$sessionid",'language'=>getPortalCurrentLanguage());	//crmv@slowear
	$result = $client->call('get_details', $params, $Server_Path, $Server_Path);
	// Check for Authorization
	if (count($result) == 1 && $result[0] == "#NOT AUTHORIZED#") {
		$smarty->display('NotAuthorized.tpl');
		include("footer.html");
		die();
	}
	$ticketinfo = $result[0][$block];
	$params = Array(Array('id'=>"$customerid", 'sessionid'=>"$sessionid", 'ticketid' => "$ticketid"));
	$commentresult = $client->call('get_ticket_comments', $params, $Server_Path, $Server_Path);
	$ticketscount = count($result);
	$commentscount = count($commentresult);
	$params = Array(Array('id'=>"$customerid", 'sessionid'=>"$sessionid", 'ticketid' => "$ticketid"));
		
	//Get the creator of this ticket
	$creator = $client->call('get_ticket_creator', $params, $Server_Path, $Server_Path);

	$ticket_status = '';
 
	foreach($ticketinfo as $key=>$value) {
		$fieldlabel = $value['fieldlabel'];
		$fieldvalue = $value['fieldvalue'];
		if ($fieldlabel == getTranslatedString('Status')) {
			$ticket_status = $fieldvalue;
			$ticketinfo[$key]['fieldvalue'] = getTranslatedString($ticketinfo[$key]['fieldvalue']);
			break;
		}
	}
	$smarty->assign('TICKETID',$ticketid); // crmv@81291
	//If the ticket is created by this customer and status is not Closed then allow him to Close this ticket otherwise not
	if ($ticket_status != getTranslatedString('Closed') && $ticket_status != '') {
		$smarty->assign('TICKETID',$ticketid);
	} else {
		$ticket_close_link = '';
	}
	
	/*crmv@57342*/
	include('HelpDesk/config.php');
	foreach ($ticketinfo as $i) {
		if (in_array($i['fieldname'],$permittedFields)) {
			$info[]=$i;
		}
	}

	$smarty->assign('FIELDLIST', getblock_fieldlist($info));
	/*crmv@57342e*/
	
	$comments = array();
	if($commentscount >= 1 && is_array($commentresult))
	{
		//Form the comments in between tr tags
		for($j=0;$j<$commentscount;$j++)
		{
			$comments[$commentscount-$j] = array(
			'comment'=>$commentresult[$j]['comments'],
			'owner'=>$commentresult[$j]['owner'],
			'createdtime'=>$commentresult[$j]['createdtime'],
			'ownertype'=>$commentresult[$j]['ownertype'],
			);
			$smarty->assign('BADGE',$commentscount);
		}
		
		$smarty->assign('COMMENTS',$comments);
	}

	$smarty->assign('TICKETSTATUS',getTranslatedString($ticket_status));

	$files_array = getTicketAttachmentsList($ticketid);

	$smarty->assign('FILES',$files_array);
	
	//To display the file upload error
	$smarty->assign('UPLOADSTATUS',$upload_status);
}
else
	echo getTranslatedString('LBL_NONE_SUBMITTED');


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

$smarty->display('TicketDetail.tpl');
?>