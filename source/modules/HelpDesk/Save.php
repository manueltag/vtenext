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
 * $Header: /advent/projects/wesat/vtiger_crm/vtigercrm/modules/HelpDesk/Save.php,v 1.8 2005/04/25 05:21:46 Mickie Exp $
 * Description:  Saves an Account record and then redirects the browser to the 
 * defined return URL.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('modules/HelpDesk/HelpDesk.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');
global $table_prefix, $currentModule;
$focus = CRMEntity::getInstance('HelpDesk');

//added to fix 4600
$search=vtlib_purify($_REQUEST['search_url']);
if(isset($_REQUEST['dup_check']) && $_REQUEST['dup_check'] != ''){
	
	check_duplicate(vtlib_purify($_REQUEST['module']),
	vtlib_purify($_REQUEST['colnames']),vtlib_purify($_REQUEST['fieldnames']),
	vtlib_purify($_REQUEST['fieldvalues']));
	die;
}

setObjectValuesFromRequest($focus);
global $adb,$mod_strings;
//Added to update the ticket history
//Before save we have to construct the update log. 
$mode = $_REQUEST['mode'];
if($mode == 'edit')
{
	$usr_qry = $adb->pquery("select * from ".$table_prefix."_crmentity where crmid=?", array($focus->id));
	$old_user_id = $adb->query_result($usr_qry,0,"smownerid");
}

//crmv@17952
if($_REQUEST['assigntype'] == 'U')  {
	$focus->column_fields['assigned_user_id'] = $_REQUEST['assigned_user_id'];
} elseif($_REQUEST['assigntype'] == 'T') {
	$focus->column_fields['assigned_user_id'] = $_REQUEST['assigned_group_id'];
}
//crmv@17952e

$focus->save("HelpDesk");

//ds@8 project tool
$internals = "";
$externals = "";
if(isset($_REQUEST["projects_ids"]) && $_REQUEST["projects_ids"]!=""){
  $Projects_Ids = explode(";",$_REQUEST["projects_ids"]);
  if($mode == 'edit'){
    $adb->query("delete from ".$table_prefix."_projects_tickets where ticket_id='".$focus->id."'");
  }

  foreach($Projects_Ids as $project_id)
  {
    if($project_id != "")
    {
    
      $adb->query("insert into ".$table_prefix."_projects_tickets values('$project_id', '".$focus->id."')");
       
      $sql = "SELECT deleted FROM ".$table_prefix."_crmentity WHERE crmid= ?";
      $res = $adb->limitpQuery($sql,0,1,Array($project_id));    
      if ($res && $adb->query_result($res,0,'deleted') != "1")
      {
        $sqlP = "SELECT ".$table_prefix."_projects.project_name AS intern, ".$table_prefix."_projectscf.external_project_number AS extern 
                FROM ".$table_prefix."_projects 
                LEFT JOIN ".$table_prefix."_projectscf 
                   ON ".$table_prefix."_projects.projectid = ".$table_prefix."_projectscf.projectid 
                WHERE ".$table_prefix."_projects.projectid = $project_id ";
        $resultP = $adb->query($sqlP);    
        
        $intern = $adb->query_result($resultP,0,"intern");   
        if ($internals !="") $internals .= ", ";
        $internals .= $intern;
        
        $extern = $adb->query_result($resultP,0,"extern");
        if ($externals !="") $externals .= ", ";
        $externals .= $extern;
      }
      
    }
  }
}
$sqlR = "UPDATE ".$table_prefix."_troubletickets SET internal_project_number = '$internals', external_project_number = '$externals' WHERE ticketid = '".$focus->id."'";
$adb->query($sqlR);
//ds@8

//Added to retrieve the existing attachment of the ticket and save it for the new duplicated ticket
if($_FILES['filename']['name'] == '' && $_REQUEST['mode'] != 'edit' && $_REQUEST['old_id'] != '')
{
        $sql = "select ".$table_prefix."_attachments.* from ".$table_prefix."_attachments inner join ".$table_prefix."_seattachmentsrel on ".$table_prefix."_seattachmentsrel.attachmentsid=".$table_prefix."_attachments.attachmentsid where ".$table_prefix."_seattachmentsrel.crmid= ?";
        $result = $adb->pquery($sql, array($_REQUEST['old_id']));
        if($adb->num_rows($result) != 0)
	{
                $attachmentid = $adb->query_result($result,0,'attachmentsid');
		$filename = decode_html($adb->query_result($result,0,'name'));
		$filetype = $adb->query_result($result,0,'type');
		$filepath = $adb->query_result($result,0,'path');

		$new_attachmentid = $adb->getUniqueID($table_prefix."_crmentity");
		$date_var = date('Y-m-d H:i:s'); //crmv@69690

		$upload_filepath = decideFilePath();

		//Read the old file contents and write it as a new file with new attachment id
		$handle = @fopen($upload_filepath.$new_attachmentid."_".$filename,'w');
		fputs($handle, file_get_contents($filepath.$attachmentid."_".$filename));
		fclose($handle);	

		$adb->pquery("update ".$table_prefix."_troubletickets set filename=? where ticketid=?", array($filename, $focus->id));	
		$adb->pquery("insert into ".$table_prefix."_crmentity (crmid,setype,createdtime) values(?,?,?)", array($new_attachmentid, 'HelpDesk Attachment', $date_var));
		$adb->pquery("insert into ".$table_prefix."_attachments (attachmentsid,name,description,type,path) values(?,?,?,?,?)", array($new_attachmentid, $filename, '', $filetype, $upload_filepath));

		$adb->pquery("insert into ".$table_prefix."_seattachmentsrel values(?,?)", array($focus->id, $new_attachmentid));
	}
}


$return_id = $focus->id;

if(isset($_REQUEST['parenttab']) && $_REQUEST['parenttab'] != "") $parenttab = $_REQUEST['parenttab'];
if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] != "") $return_module = $_REQUEST['return_module'];
else $return_module = "HelpDesk";
if(isset($_REQUEST['return_action']) && $_REQUEST['return_action'] != "") $return_action = $_REQUEST['return_action'];
else $return_action = "DetailView";
if(isset($_REQUEST['return_id']) && $_REQUEST['return_id'] != "") $return_id = $_REQUEST['return_id'];

if($_REQUEST['mode'] == 'edit')
	$reply = 'Re : ';
else
	$reply = '';

$subject = '[ '.$mod_strings['LBL_TICKET_ID'].' : '.$focus->id.' ] '.$reply.$_REQUEST['ticket_title'];
$bodysubject = $mod_strings['LBL_TICKET_ID'].' : '.$focus->id.'<br> '.$mod_strings['LBL_SUBJECT'].$_REQUEST['ticket_title'];

//crmv@93276
$emailoptout = 1;
$isactive = 0;
//To get the emailoptout value and then decide whether send mail about the tickets or not
if($focus->column_fields['parent_id'] != '') {
	// crmv@26821
	$parent_module = getSalesEntityType($focus->column_fields['parent_id']);
	$parent_id = $focus->column_fields['parent_id'];
	if($parent_module == 'Contacts')
	{
		$result = $adb->pquery("select firstname,lastname,email,emailoptout from ".$table_prefix."_contactdetails where contactid=?", array($focus->column_fields['parent_id']));
		$emailoptout = $adb->query_result_no_html($result,0,'emailoptout');
		$contactname = $adb->query_result($result,0,'firstname').' '.$adb->query_result($result,0,'lastname');
		$parentname = $contactname;
		$contact_mailid = $adb->query_result($result,0,'email');
		//Get the status of the vtiger_portal user. if the customer is active then send the vtiger_portal link in the mail
		if($contact_mailid != '' && $emailoptout == 0)
		{
			$sql = "select isactive from ".$table_prefix."_portalinfo where user_name=?";
			$isactive = $adb->query_result_no_html($adb->pquery($sql, array($contact_mailid)),0,'isactive');
		}
	}
	if($parent_module == 'Accounts')
	{
		$result = $adb->pquery("select accountname,emailoptout from ".$table_prefix."_account where accountid=?", array($focus->column_fields['parent_id']));
		$emailoptout = $adb->query_result_no_html($result,0,'emailoptout');
		$parentname = $adb->query_result($result,0,'accountname');
		if($emailoptout == 0){
			$result_cnt = $adb->pquery("select contactid from ".$table_prefix."_contactdetails where accountid=?", array($focus->column_fields['parent_id']));
			while($row_cnt = $adb->fetchByAssoc($result_cnt)){
				$result1 = $adb->pquery("select email,emailoptout from ".$table_prefix."_contactdetails where contactid=?", array($row_cnt['contactid']));
				$emailoptout_cnt = $adb->query_result_no_html($result1,0,'emailoptout');
				$contact_mailid_cnt = $adb->query_result($result1,0,'email');
				if($contact_mailid_cnt != '' && $emailoptout_cnt == 0) {
					$sql = "select isactive from ".$table_prefix."_portalinfo where user_name=?";
					$isactive = $adb->query_result_no_html($adb->pquery($sql, array($contact_mailid_cnt)),0,'isactive');
					if($isactive) break;
				}
			}
		}
	}
	//TODO: Handle Leads ?
	
	$url = "<a href='".$PORTAL_URL."/index.php?module=HelpDesk&action=index&ticketid=".$focus->id."&fun=detail'>".$mod_strings['LBL_TICKET_DETAILS']."</a>"; //crmv@82517
	$email_body_portal = $bodysubject.'<br><br>'.getPortalInfo_Ticket($focus->id,$_REQUEST['ticket_title'],$parentname,$url,$_REQUEST['mode']);

	require_once('modules/Emails/mail.php');

	//added condition to check the emailoptout(this is for contacts and vtiger_accounts.)
	//crmv@87556
	if ($focus->column_fields['comments'] != '' && !empty($focus->column_fields['mailscanner_action'])) {
		$mail_status = $focus->sendMailScannerReply();
	//crmv@87556e
	} elseif(!empty($parent_id) && $emailoptout == 0 && $isactive) {
		//send mail to parent
		$parent_email = getParentMailId($parent_module,$parent_id);
		if ($_REQUEST['mode'] != 'edit' ||
			$focus->column_fields['ticketstatus'] == $mod_strings["Closed"] || 
			$focus->column_fields['comments'] != '' || 
			$_REQUEST['helpdesk_solution'] != $_REQUEST['solution'])
		{	
			$mail_status = send_mail('HelpDesk',$parent_email,$HELPDESK_SUPPORT_NAME,$HELPDESK_SUPPORT_EMAIL_ID,$subject,$email_body_portal);
		}
		$mail_status_str .= $parent_email."=".$mail_status."&&&";
		
	} else {
		$adb->println("'".$parentname."' doesn't want to receive emails about the ticket details as emailoptout is selected or portal not active");
	}
	// crmv@26821e
}

$_REQUEST['return_id'] = $return_id;

if($_REQUEST['return_module'] == 'Products' && $_REQUEST['product_id'] != '' &&  $focus->id != '') {
	$return_id = $_REQUEST['product_id'];
}

//crmv@93276e

if ($mail_status != '' && $mail_status != 1) { // crmv@104782
	$mail_error_status = getMailErrorString($mail_status_str);
}

//code added for returning back to the current view after edit from list view
if($_REQUEST['return_viewname'] == '') $return_viewname='0';
if($_REQUEST['return_viewname'] != '')$return_viewname=$_REQUEST['return_viewname'];

//crmv@54375
if($_REQUEST['return2detail'] == 'yes') {
	$return_module = $currentModule;
	$return_action = 'DetailView';
	$return_id = $focus->id;
}
//crmv@54375e

$url = "index.php?action=$return_action&module=$return_module&parenttab=$parenttab&record=$return_id&$mail_error_status&viewname=$return_viewname&start=".$_REQUEST['pagenumber'].$search;

$from_module = vtlib_purify($_REQUEST['module']);
if (!empty($from_module)) $url .= "&from_module=$from_module";

header("Location: $url");
?>