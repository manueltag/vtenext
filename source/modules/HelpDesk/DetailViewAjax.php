<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/
// crmv@67410
global $adb, $table_prefix;
global $currentModule, $current_user;

$modObj = CRMEntity::getInstance($currentModule);

$ajaxaction = $_REQUEST["ajxaction"];
if($ajaxaction == "DETAILVIEW")
{
	$crmid = $_REQUEST["recordid"];
	$tablename = $_REQUEST["tableName"];
	$fieldname = $_REQUEST["fldName"];
	$fieldvalue = utf8RawUrlDecode($_REQUEST["fieldValue"]);

	if($crmid != ""){

		$permEdit = isPermitted($currentModule, 'DetailViewAjax', $crmid);
		$permField = getFieldVisibilityPermission($currentModule, $current_user->id, $fieldname);

		if ($permEdit != 'yes' || $permField != 0) {
			echo ":#:FAILURE";
			return;
		}

		$modObj->retrieve_entity_info($crmid,$currentModule);
		
		//Added to avoid the comment save, when we edit other fields through ajax edit
		if($fieldname != 'comments')
			$modObj->column_fields['comments'] = '';

		$modObj->column_fields[$fieldname] = $fieldvalue;
		$modObj->id = $crmid;
		$modObj->mode = "edit";
		$modObj->save($currentModule);
		
		global $mod_strings;
		//crmv@87556
		if ($fieldname == 'comments' && !empty($modObj->column_fields['mailscanner_action'])) {
			$mail_status = $modObj->sendMailScannerReply();
		//crmv@87556e
		} elseif($fieldname == "solution" || $fieldname == "comments" || $fieldname =="assigned_user_id" ||($fieldname == "ticketstatus" && $fieldvalue == $mod_strings['Closed'])) {
			require_once('modules/Emails/mail.php');
			$user_emailid = getUserEmailId('id',$modObj->column_fields['assigned_user_id']);
			
			$subject = $modObj->column_fields['ticket_no'] . ' [ '.$mod_strings['LBL_TICKET_ID'].' : '.$modObj->id.' ] Re : '.$modObj->column_fields['ticket_title'];
			$parent_id = $modObj->column_fields['parent_id'];
			//crmv@26552  crmv@26821 crmv@93276
			if(!empty($parent_id) && $parent_id!=0){
				$parent_module = getSalesEntityType($parent_id);
				$isactive = 0;
				$emailoptout = 1;
				if($parent_module == 'Contacts') {
					$result = $adb->pquery("select firstname,lastname,email,emailoptout from ".$table_prefix."_contactdetails where contactid=?", array($parent_id));
					$emailoptout = $adb->query_result_no_html($result,0,'emailoptout');
					$contactname = $adb->query_result($result,0,'firstname').' '.$adb->query_result($result,0,'lastname');
					$parentname = $contactname;
					$contact_mailid = $adb->query_result($result,0,'email');
					if($contact_mailid != '' && $emailoptout == 0) {
						$sql = "select isactive from ".$table_prefix."_portalinfo where user_name=?";
						$isactive = $adb->query_result_no_html($adb->pquery($sql, array($contact_mailid)),0,'isactive');
					}
				}
				if($parent_module == 'Accounts') {
					$result = $adb->pquery("select accountname,emailoptout from ".$table_prefix."_account where accountid=?", array($parent_id));
					$emailoptout = $adb->query_result_no_html($result,0,'emailoptout');
					$parentname = $adb->query_result($result,0,'accountname');
					if($emailoptout == 0){
						$result_cnt = $adb->pquery("select contactid from ".$table_prefix."_contactdetails where accountid=?", array($parent_id));
						while($row_cnt = $adb->fetchByAssoc($result_cnt)){
							$result1 = $adb->pquery("select emailoptout,email from ".$table_prefix."_contactdetails where contactid=?", array($row_cnt['contactid']));
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
			
				$url = "<a href='".$PORTAL_URL."/index.php?module=HelpDesk&action=index&ticketid=".$modObj->id."&fun=detail'>".$mod_strings['LBL_TICKET_DETAILS']."</a>";
				$email_body_portal = $subject.'<br><br>'.getPortalInfo_Ticket($modObj->id,$modObj->column_fields['ticket_title'],$parentname,$url,"edit");

				if($emailoptout == 0 && $isactive == 1 && !empty($parent_id) && $fieldname != "assigned_user_id") {
					//send mail to parent
					$parent_email = getParentMailId($parent_module,$parent_id);
					$mail_status = send_mail('HelpDesk',$parent_email,$HELPDESK_SUPPORT_NAME,$HELPDESK_SUPPORT_EMAIL_ID,$subject,$email_body_portal);
				}
			}
			//crmv@26552e crmv@26821e crmv@93276e
		}
		if($modObj->id != ""){
			if($fieldname == "comments"){
				$comments = $modObj->getCommentInformation($modObj->id);
				echo ":#:SUCCESS".$comments;
			}else{
				echo ":#:SUCCESS";
			}
		}else{
			echo ":#:FAILURE";
		}   
	}else{
		echo ":#:FAILURE";
	}
} elseif($ajaxaction == "LOADRELATEDLIST" || $ajaxaction == "DISABLEMODULE"){
	require_once 'include/ListView/RelatedListViewContents.php';
}
?>