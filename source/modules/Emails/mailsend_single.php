<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@55137 */
$success = false;
$append_status = false;	//crmv@86304
$to = array();
if(isset($to_mail) && $to_mail != '') {
	$to = explode(',',$to_mail);
	$to = array_map('trim', $to);
	$to = array_filter($to);
}
if (!isset($nlparam)) $nlparam = ''; // crmv@114260

foreach($myids as $myid) {
	$emailadd = '';
	$realid = explode("@",$myid);
	$nemail = count($realid);
	$mycrmid = $realid[0];

	// support to old mode
	if($realid[1] == -1) {
		$emailadd = $adb->query_result($adb->pquery("select email1 from ".$table_prefix."_users where id=?", array($mycrmid)),0,'email1');
		$to[] = $emailadd;
		continue;
	}
	
	$pmodule = getSalesEntityType($mycrmid);
	if ($pmodule == '') {
		$res = $adb->query('SELECT * FROM '.$table_prefix.'_users WHERE id = '.$mycrmid);
		if ($res && $adb->num_rows($res)>0) {
			$pmodule = 'Users';
		}
	}
	for ($j=1;$j<$nemail;$j++) {
		$temp = $realid[$j];
		if (strpos($temp,'-') === 0) {
			$pmodule = 'Users';
			$temp = substr($temp,1);
		}
		$myquery = 'select fieldname from '.$table_prefix.'_field where fieldid = ? and '.$table_prefix.'_field.presence in (0,2)';
		$fresult = $adb->pquery($myquery, array($temp));
		$fldname = $adb->query_result_no_html($fresult,0,'fieldname');
		// vtlib customization: Enabling mail send from other modules
		$myfocus = CRMEntity::getInstance($pmodule);
		// crmv@77583 - if record is deleted, try to retrieve the address anyway
		$retrieve_err = $myfocus->retrieve_entity_info($mycrmid, $pmodule, false);
		if ($retrieve_err == 'LBL_RECORD_DELETE') {
			$modTabid = getTabid($pmodule);
			$sqlTab = "SELECT tablename, columnname FROM {$table_prefix}_field WHERE tabid = ? AND fieldname = ?";
			$resTab = $adb->pquery($sqlTab,array($modTabid,$fldname));
			if($resTab && $adb->num_rows($resTab) > 0) {
				$tableField = $adb->query_result_no_html($resTab,0,'tablename');
				$columnname = $adb->query_result_no_html($resTab,0,'columnname');
				$tableindex = $myfocus->tab_name_index[$tableField];
				if (!empty($tableindex) && !empty($columnname)) {
					$resMail = $adb->pquery("SELECT {$columnname} FROM {$tableField} WHERE {$tableindex} = ?", array($mycrmid));
					if($resMail && $adb->num_rows($resMail) > 0) {
						$emailtosend = $adb->query_result($resMail,0,$columnname);
						$emailadd = br2nl($emailtosend);
					}
				}
			}
		} else {
			$emailadd = br2nl($myfocus->column_fields[$fldname]);
		}
		// crmv@77583e
		// END
	}
	if($emailadd != '') {
		$to[] = $emailadd;
	}
}
$subject_send = $subject;
$description_send = $description;
//Email Tracking disabilitato per il send_mode single
$pos = strpos($description_send, '$logo$');
if ($pos !== false) {
	$description_send = str_replace('$logo$','<img src="cid:logo" />', $description_send);
	$logo = 1;
}
if ($message_mode == 'forward') {	// || $message_mode == 'draft' (crmv@48501*1)
	$attach_messageid = $messageid;
} else {
	$attach_messageid = '';
}


$attach_mode = 'all';
if (!empty($_REQUEST['attachments_mode'])) $attach_mode = $_REQUEST['attachments_mode'];
$send_mail_status = send_mail('Emails',$to,$from_name,$from_address,$subject_send,$description_send,$cc,$bcc,$attach_mode,$attach_messageid,$logo,$nlparam,$mail_tmp,$messageid,$message_mode); // crmv@114260

if($send_mail_status == 1) {
	if (!empty($_REQUEST['sending_queue_currentid'])) $adb->pquery("update {$table_prefix}_emails_send_queue set s_send = ? where id = ?",array(1,$_REQUEST['sending_queue_currentid']));	//crmv@48501
	//crmv@2043m
	if(isset($_REQUEST['reply_mail_converter']) && $_REQUEST['reply_mail_converter'] != '') {
		global $current_user;
		$HelpDeskFocus = CRMEntity::getInstance('HelpDesk');
		$HelpDeskFocus->retrieve_entity_info_no_html($_REQUEST['reply_mail_converter_record'], 'HelpDesk');
		$HelpDeskFocus->id = $_REQUEST['reply_mail_converter_record'];
		$HelpDeskFocus->mode = 'edit';
		if ($HelpDeskFocus->waitForResponseStatus != '') {
			$HelpDeskFocus->column_fields['ticketstatus'] = $HelpDeskFocus->waitForResponseStatus;
		}
		$HelpDeskFocus->column_fields['comments'] = strip_tags($description);
		$HelpDeskFocus->save('HelpDesk');
	}
	//crmv@2043me
	//crmv@86304
	$append_status = append_mail($mail_tmp,$account,$parentid,$to,$from_name,$from_address,$subject,$description,$cc,$bcc,$send_mode);
	if (!$append_status) {
		global $currentModule;
		$currentModule = 'Messages';
		$focusMessages = CRMentity::getInstance($currentModule);
		$focusMessages->internalAppendMessage($mail_tmp,$account,$parentid,$to,$from_name,$from_address,$subject,$description,$cc,$bcc,$send_mode);
		$currentModule = 'Emails';
	}
	//crmv@86304e
	$success = true;
} else {
	$error_message = $send_mail_status;
}
if ($success) {
	if (!empty($_REQUEST['sending_queue_currentid'])) $adb->pquery("update {$table_prefix}_emails_send_queue set s_append = ? where id = ?",array(1,$_REQUEST['sending_queue_currentid']));	//crmv@48501

	cleanPuploadAttachments($_REQUEST['uploaddir']);
	
	if (!empty($_REQUEST['sending_queue_currentid'])) $adb->pquery("update {$table_prefix}_emails_send_queue set s_clean_pupload_attach = ? where id = ?",array(1,$_REQUEST['sending_queue_currentid']));	//crmv@48501

	/*
	 * TODO: eliminare cartelle tmp di allegati di bozze
	 *
	 * quando viene salvata una bozza con allegati non posso svuotare la cartella perch� se poi clicco Salva non troverebbe pi� l'allegato da inviare
	 * per� nel momento in cui chiudo la finestra di composizione e si � salvata la bozza la cartella con i file diventa inutile perch� se riapro la bozza faccio riferimento agli allegati che sono nel server
	 * quindi si potrebbe fare uno script che cancella le cartelle pi� vecchie di X giorni
	 */
	if (!empty($messageid) && !empty($message_mode)) {
		$javascript_code .= setflag_mail($messageid, $message_mode);
	}
} else {
	$skip_delete_drafts = true;
}
?>