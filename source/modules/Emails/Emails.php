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
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Emails/Emails.php,v 1.41 2005/04/28 08:11:21 rank Exp $
 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

include_once('config.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');
require_once('data/CRMEntity.php');
require_once('modules/Contacts/Contacts.php');
require_once('modules/Accounts/Accounts.php');
require_once('modules/Potentials/Potentials.php');
require_once('modules/Users/Users.php');

// Email is used to store customer information.
class Emails extends CRMEntity {
	var $log;
	var $db;
	var $table_name;
	var $table_index= 'activityid';
	// Stored vtiger_fields
  	// added to check email save from plugin or not
	var $plugin_save = false;

	var $rel_users_table ;
	var $rel_contacts_table;
	var $rel_serel_table;

	var $tab_name = Array();
	var $tab_name_index = Array();

	// This is the list of vtiger_fields that are in the lists.
	var $list_fields = Array(
		'LBL_FROM'=>array('emaildetails'=>'from_email'), //crmv@30521
		'Subject'=>Array('activity'=>'subject'),
		'Related to'=>Array('seactivityrel'=>'parent_id'),
		'Date Sent'=>Array('activity'=>'date_start'),
		'Assigned To'=>Array('crmentity','smownerid'),
		'Access Count'=>Array('email_track','access_count')
	);

	var $list_fields_name = Array(
		'LBL_FROM'=>'from_email', //crmv@30521
		'Subject'=>'subject',
		'Related to'=>'parent_id',
		'Date Sent'=>'date_start',
		'Assigned To'=>'assigned_user_id',
		'Access Count'=>'access_count'
	);

	var $list_link_field= 'subject';

	var $column_fields = Array();

	var $sortby_fields = Array('subject','date_start','saved_toid');

	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'date_start';
	var $default_sort_order = 'ASC';

	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('subject','assigned_user_id');

	//crmv@32079
	var $default_account = array(
		'smtp' => array(
			'Gmail' => array(
				'server'=>'ssl://smtp.gmail.com',
				'server_port'=>'465',
				'server_username'=>'username@gmail.com',
				'server_password'=>'required',
				'smtp_auth'=>'checked',
			),
			'Hotmail' => array(
				'server'=>'smtp.live.com',
				'server_port'=>'587',
				'server_username'=>'username@hotmail.com',
				'server_password'=>'required',
				'smtp_auth'=>'checked',
			),
			'Yahoo!' => array(
				'server'=>'smtp.mail.yahoo.com',
				'server_port'=>'25',
				'server_username'=>'username@yahoo.com',
				'server_password'=>'required',
				'smtp_auth'=>'checked',
				'note'=>'LBL_YAHOO_SMTP_INFO',
			),
			'Exchange' => array(
				'server'=>'mail.example.com',
				'server_port'=>'25',
				'server_username'=>'username@example.com',
				'server_password'=>'required',
				'smtp_auth'=>'checked',
			),
			'Other' => array(
				'server'=>'smtp.example.com',
				'server_port'=>'25',
				'server_username'=>'',
				'smtp_auth'=>'',
			),
		),
		'imap' => array(
			'Gmail' => array(
				'server'=>'imap.gmail.com',
				'server_port'=>'993',
				'ssl_tls'=>'ssl',
			),
			'Yahoo!' => array(
				'server'=>'imap-ssl.mail.yahoo.com',
				'server_port'=>'993',
				'ssl_tls'=>'ssl',
			),
			'Exchange' => array(
				'server'=>'mail.example.com',
				'server_port'=>'993',
				'ssl_tls'=>'tls',
				'domain'=>'example.com',
			),
			'Other' => array(
				'server'=>'imap.example.com',
				'server_port'=>'143',
				'ssl_tls'=>'',
			),
		),
	);
	//crmv@32079e
	
	//crmv@44037	crmv@49001
	var $signatureId;
	var $signatureStatus = false;
	//crmv@44037e	crmv@49001e
	//crmv@58893
	var $max_attachment_size = 25;  //max attachments size in mb for plupload
	var $max_message_size = 10240000;  //max data size in bytes of smtp, fallback to default if not retrieved from server
	//crmv@58893 e
	/** This function will set the columnfields for Email module
	*/
	function __construct() {
		global $table_prefix;
		parent::__construct(); // crmv@37004
		$this->table_name = $table_prefix."_activity";
		$this->rel_users_table = $table_prefix."_salesmanactivityrel";
		$this->rel_contacts_table = $table_prefix."_cntactivityrel";
		$this->rel_serel_table = $table_prefix."_seactivityrel";
		$this->tab_name = Array($table_prefix.'_crmentity',$table_prefix.'_activity',$table_prefix.'_emaildetails');
		$this->tab_name_index = Array($table_prefix.'_crmentity'=>'crmid',$table_prefix.'_activity'=>'activityid',
				$table_prefix.'_seactivityrel'=>'activityid',$table_prefix.'_cntactivityrel'=>'activityid',$table_prefix.'_email_track'=>'mailid',$table_prefix.'_emaildetails'=>'emailid');
		$this->log = LoggerManager::getLogger('email');
		$this->log->debug("Entering Emails() method ...");
		$this->log = LoggerManager::getLogger('email');
		$this->db = PearDatabase::getInstance();
		$this->column_fields = getColumnFields('Emails');
		$this->log->debug("Exiting Email method ...");
	}
	
	function save_module($module) {}

	// crmv@66378 - don't overwrite the exising column fields
	function save($module_name,$longdesc=false,$offline_update=false,$triggerEvent=true) {
		
	    if (!empty($this->column_fields['date_start'])) {
	    	$date = getValidDBInsertDateValue($this->column_fields['date_start']);
	    	if (!empty($this->column_fields['time_start'])) {
	    		$date .= ' '.$this->column_fields['time_start'];
	    	}
	    }
		
		$column_fields = array_merge($this->column_fields, array(
			'subject'=>$this->column_fields['subject'],
			'description'=>$this->column_fields['description'],
			'mfrom'=>$this->column_fields['from_email'],
			'mfrom_f'=>$this->column_fields['from_email'],
			'mto'=>$this->column_fields['saved_toid'],
			'mto_f'=>$this->column_fields['saved_toid'],
			'mcc'=>$this->column_fields['ccmail'],
			'mcc_f'=>$this->column_fields['ccmail'],
			'mbcc'=>$this->column_fields['bccmail'],
			'mbcc_f'=>$this->column_fields['bccmail'],
			'mdate'=>$date,
			'assigned_user_id'=>$this->column_fields['assigned_user_id'],
			'parent_id'=>$this->column_fields['parent_id'],
			'mtype' => 'Link',
		));
		
		$focus = CRMEntity::getInstance('Messages');
		$focus->saveCacheLink($column_fields);

		//$this->insertIntoAttachment($this->id,$module);
	}
	// crmv@66378e

	function insertIntoAttachment($id,$module)
	{
		global $log, $adb, $current_user,$table_prefix;
		$log->debug("Entering into insertIntoAttachment($id,$module) method.");

		$file_saved = false;

		//Send document attachment
		if(isset($_REQUEST['pdf_attachment']) && $_REQUEST['pdf_attachment'] !='')
		{
			$file_saved = pdfAttach($this,$module,$_REQUEST['pdf_attachment'],$id);
		}

		//This is to added to store the existing attachment id of the contact where we should delete this when we give new image
		foreach($_FILES as $fileindex => $files)
		{
			if($files['name'] != '' && $files['size'] > 0)
			{
				$files['original_name'] = vtlib_purify($_REQUEST[$fileindex.'_hidden']);
				$file_saved = $this->uploadAndSaveFile($id,$module,$files);
			}
		}
		//crmv@22123
		$targetDir = 'storage/uploads_emails_'.$current_user->id;
		for($count_att=0;;$count_att++) {
			if (empty($_REQUEST['uploader_'.$count_att.'_tmpname'])) break;
			$files['name'] = $_REQUEST['uploader_'.$count_att.'_name'];
			$files['tmp_name'] = $targetDir."/".$_REQUEST['uploader_'.$count_att.'_tmpname'];
			$file_saved = $this->uploadAndSaveFile($id,$module,$files,true);
			//crmv@31456
			if(is_file($files['tmp_name']) && !isset($_REQUEST['save_in_draft'])){
				unlink($files['tmp_name']);
			}
			//crmv@31456e
		}
		//crmv@22123e
		if($module == 'Emails' && isset($_REQUEST['att_id_list']) && $_REQUEST['att_id_list'] != '')
		{
			$att_lists = explode(";",$_REQUEST['att_id_list'],-1);
			$id_cnt = count($att_lists);
			if($id_cnt != 0)
			{
				for($i=0;$i<$id_cnt;$i++)
				{
					$sql_rel='insert into '.$table_prefix.'_seattachmentsrel values(?,?)';
					$adb->pquery($sql_rel, array($id, $att_lists[$i]));
				}
			}
		}
		$log->debug("Exiting from insertIntoAttachment($id,$module) method.");
	}

	/**
	* Used to releate email and contacts -- Outlook Plugin
	*/
	function set_emails_contact_invitee_relationship($email_id, $contact_id)
	{
		global $log;
		$log->debug("Entering set_emails_contact_invitee_relationship(".$email_id.",". $contact_id.") method ...");
		$query = "insert into $this->rel_contacts_table (contactid,activityid) values(?,?)";
		$this->db->pquery($query, array($contact_id, $email_id), true,"Error setting email to contact relationship: "."<BR>$query");
		$log->debug("Exiting set_emails_contact_invitee_relationship method ...");
	}

	/**
	* Used to releate email and salesentity -- Outlook Plugin
	*/
	function set_emails_se_invitee_relationship($email_id, $contact_id)
	{
		global $log;
		$log->debug("Entering set_emails_se_invitee_relationship(".$email_id.",". $contact_id.") method ...");
		$query = "insert into $this->rel_serel_table (crmid,activityid) values(?,?)";
		$this->db->pquery($query, array($contact_id, $email_id), true,"Error setting email to contact relationship: "."<BR>$query");
		$log->debug("Exiting set_emails_se_invitee_relationship method ...");
	}

	/**
	* Used to releate email and Users -- Outlook Plugin
	*/
	function set_emails_user_invitee_relationship($email_id, $user_id)
	{
		global $log;
		$log->debug("Entering set_emails_user_invitee_relationship(".$email_id.",". $user_id.") method ...");
		$query = "insert into $this->rel_users_table (smid,activityid) values (?,?)";
		$this->db->pquery($query, array($user_id, $email_id), true,"Error setting email to user relationship: "."<BR>$query");
		$log->debug("Exiting set_emails_user_invitee_relationship method ...");
	}

	// Function to unlink an entity with given Id from another entity
	function unlinkRelationship($id, $return_module, $return_id) {
		global $log,$table_prefix;

		//crmv@26265
		$sql='DELETE FROM '.$table_prefix.'_seactivityrel WHERE activityid=? AND crmid=?';
		$this->db->pquery($sql, array($id, $return_id));
		//crmv@26265e

		$sql = 'DELETE FROM '.$table_prefix.'_crmentityrel WHERE (crmid=? AND relmodule=? AND relcrmid=?) OR (relcrmid=? AND module=? AND crmid=?)';
		$params = array($id, $return_module, $return_id, $id, $return_module, $return_id);
		$this->db->pquery($sql, $params);

		$this->db->pquery("UPDATE {$table_prefix}_crmentity SET modifiedtime = ? WHERE crmid IN (?,?)", array($this->db->formatDate(date('Y-m-d H:i:s'), true), $id, $return_id)); // crmv@49398 crmv@69690
	}

	//crmv@2963m
	function getMessageForwardHeader($message) {
		global $default_charset, $adb, $table_prefix;
		$editor_size = 76;

		$display = array(getTranslatedString('Subject','Messages') => strlen(getTranslatedString("Subject",'Messages')),
						getTranslatedString("From",'Messages') => strlen(getTranslatedString("From",'Messages')),
						getTranslatedString("Date",'Messages') => strlen(getTranslatedString("Date",'Messages')),
						getTranslatedString("To",'Messages') => strlen(getTranslatedString("To",'Messages')),
						getTranslatedString("Cc",'Messages') => strlen(getTranslatedString("Cc",'Messages'))
		);
		$maxsize = max($display);
		$indent = str_pad('',$maxsize+2);
		foreach($display as $key => $val) {
			$display[$key] = $key .': '. str_pad('', $maxsize - $val);
		}
		$from = htmlentities(htmlentities($message->column_fields['mfrom'], ENT_COMPAT, $default_charset), ENT_COMPAT, $default_charset);
		//crmv@97344
		$to = htmlentities(htmlentities($message->column_fields['mto_f'], ENT_COMPAT, $default_charset), ENT_COMPAT, $default_charset); 
		$cc = htmlentities(htmlentities($message->column_fields['mcc_f'], ENT_COMPAT, $default_charset), ENT_COMPAT, $default_charset);
		//crmv@97344e
		$subject = htmlentities(htmlentities($message->column_fields['subject'], ENT_COMPAT, $default_charset), ENT_COMPAT, $default_charset);
		$bodyTop =  str_pad(' '.getTranslatedString("Original Message",'Messages').' ', $editor_size-2, '-', STR_PAD_BOTH) ."<br />" .
					$display[getTranslatedString("Subject",'Messages')] . $subject . "<br />" .
					$display[getTranslatedString("From",'Messages')] . $from . "<br />" .
					$display[getTranslatedString("Date",'Messages')] . $message->getFullDate($message->column_fields['mdate']) . "<br />" .
					$display[getTranslatedString("To",'Messages')] . $to . "<br />";
		if (!empty($cc)) {
			$bodyTop .= $display[getTranslatedString("Cc",'Messages')] . $cc . "<br />";
		}
		$bodyTop .= str_pad('', $editor_size-2+9, '-')."<br /><br />";
		return $bodyTop;
	}
	function getMessageReplyHeader($message) {
		$orig_from = $message->column_fields['mfrom_n'];
		if (empty($orig_from)) {
			$orig_from = $message->column_fields['mfrom'];
		}
		$orig_date = $message->getFullDate($message->column_fields['mdate']);
		$full_reply_citation = sprintf(getTranslatedString("On %s, %s wrote:",'Messages'), $orig_date, $orig_from);
		return $full_reply_citation."\n";
	}
	//crmv@2963me
	//crmv@2051m
	function getFromEmailList($from_email,$account='') {
		global $adb, $current_user;
		$skip_select_option = false;	//crmv@60095
		$list = array();
		$focusMessages = CRMEntity::getInstance('Messages');
		$user_accounts = $focusMessages->getUserAccounts();
		$main_account = $focusMessages->getMainUserAccount();
		if (!empty($user_accounts)) {
			$commonDomains = array(
				'Gmail' => 'gmail.com',
			);
			foreach($user_accounts as $a) {
				$email = $a['email']; //crmv@50745
				//crmv@46012
				$domain = $a['domain'] ?: $commonDomains[$a['account']];
				if (strpos($email,'@') === false && !empty($domain)) $email = $email.'@'.$domain;
				//crmv@46012e
				if (!array_key_exists($email,$list)) {
					$list[$email] = array('email'=>$email,'name'=>$a['description'],'account'=>$a['id'],'selected'=>'');
				}
			}
		}
		//crmv@53659
		if (empty($list) || (!empty($list) && !empty($current_user->column_fields['email1']) && !array_key_exists($current_user->column_fields['email1'],$list))) {
			$list[$current_user->column_fields['email1']] = array('email'=>$current_user->column_fields['email1'],'name'=>trim(getUserFullName($current_user->id)),'account'=>$main_account['id'],'selected'=>'');
		}
		//crmv@53659e
		//crmv@80029
		$pop3_accounts = $focusMessages->getPop3();
		if (!empty($pop3_accounts)) {
			foreach($pop3_accounts as $a) {
				if ($a['active'] == 1) {
					$list[$a['username']] = array('email'=>$a['username'],'name'=>$a['username'],'account'=>$a['accountid'],'selected'=>'');
				}
			}
		}
		//crmv@80029e
		//crmv@2043m	
		if ($_REQUEST['reply_mail_user'] == 'mailconverter' && isset($_REQUEST['reply_mail_converter']) && $_REQUEST['reply_mail_converter'] != '') {
			$HelpDeskFocus = CRMEntity::getInstance('HelpDesk');
			$HelpDeskFocus->retrieve_entity_info_no_html($_REQUEST['reply_mail_converter_record'], 'HelpDesk');
			if ($HelpDeskFocus->column_fields['helpdesk_from'] != '') {
				if (!array_key_exists($HelpDeskFocus->column_fields['helpdesk_from'], $list)) {
					//crmv@60095
					$list[$HelpDeskFocus->column_fields['helpdesk_from']] = array('email'=>$HelpDeskFocus->column_fields['helpdesk_from'],'name'=>$HelpDeskFocus->column_fields['helpdesk_from_name'],'account'=>$main_account['id'],'selected'=>'selected');
					$skip_select_option = true;
					//crmv@60095e
				}
			}
		}
		//crmv@2043me
		if (!$skip_select_option) {	//crmv@60095
			foreach ($list as $i => $info) {
				$selected = '';
				if (isset($info['account']) && $account !== '' && $account == $info['account']) {
					$selected = 'selected';
				} elseif ($from_email == $info['email']) {
					$selected = 'selected';
				}
				if ($selected != '') {
					$list[$i]['selected'] = $selected;
					break;
				}
			}
			//crmv@83942
			if ($selected == '') {
				foreach ($list as $i => $info) {
					if ($info['account'] == $main_account['id']) {
						$list[$i]['selected'] = 'selected';
						break;
					}
				}
			}
			//crmv@83942e
		}
		return $list;
	}
	function getFromEmailName($from_email) {
		$list = $this->getFromEmailList($from_email);
		foreach($list as $info) {
			if ($info['email'] == $from_email) {
				return $info['name'];
			}
		}
		return $from_email;
	}
	function getFromEmailAccount($from_email) {
		$list = $this->getFromEmailList($from_email);
		foreach($list as $info) {
			if ($info['email'] == $from_email) {
				return $info['account'];
			}
		}
	}
	//crmv@2051me
	//crmv@48501
	function add2SendingQueue($userid, $method, $request) {
		global $adb, $table_prefix;
		if ($method == 'draft') {
			$adb->pquery("DELETE FROM {$table_prefix}_emails_send_queue WHERE method = ? AND request LIKE ?",array('draft','%"draft_id":"'.$request['draft_id'].'"%'));
		}
		$id = $adb->getUniqueID($table_prefix."_emails_send_queue");
		$sql = "insert into {$table_prefix}_emails_send_queue (id,userid,method,request,date) values (?,?,?,".$adb->getEmptyClob(true).",?)";
		$params = array($id, $userid, $method, date('Y-m-d H:i:s'));
		$adb->pquery($sql, $params);
		$adb->updateClob($table_prefix.'_emails_send_queue','request',"id=$id",Zend_Json::encode($request));
	}
	function processSendingQueue($user_start='',$user_end='') {	//crmv@71322
		global $adb, $table_prefix, $current_user;
		
		//crmv@55094
		require_once("modules/Emails/class.phpmailer.php");
		$mail = new PHPMailer();
		//crmv@55094e
		
		$original_request = $_REQUEST;	//crmv@53929

		// clean queue
		$adb->pquery("delete from {$table_prefix}_emails_send_queue where status = ?",array(1));
		
		// process queue
		$sql = "select * from {$table_prefix}_emails_send_queue where status = ?";
		//crmv@71322
		if ($user_start != '') {
			$sql .= " and userid >= $user_start";
			if ($user_end != '') {
				$sql .= " and userid <= $user_end";
			}
		}
		//crmv@71322e
		$sql .= " order by userid, id";
		$result = $adb->limitpQuery($sql,0,5,array(0));
		if ($result && $adb->num_rows($result) > 0) {
			$userid = '';
			while($row=$adb->fetchByAssoc($result,-1,false)) {
				if ($mail->SMTPDebug) echo "sending message {$row['id']}...\n";	//crmv@55094
				$adb->pquery("update {$table_prefix}_emails_send_queue set status = ?, date = ? where id = ?",array(2,date('Y-m-d H:i:s'),$row['id']));
				if ($userid != $row['userid']) {
					$userid = $row['userid'];
					$current_user = CRMEntity::getInstance('Users');
					$current_user->retrieve_entity_info($userid, 'Users');
				}
				$_REQUEST = array_merge($original_request,Zend_Json::decode($row['request']));	//crmv@53929
				$_REQUEST['sending_queue_currentid'] = $row['id'];
				$skip_exit = true;
				$error_message = '';
				include("modules/Emails/Save.php");
				if (empty($error_message)) {
					$adb->pquery("update {$table_prefix}_emails_send_queue set status = ? where id = ?",array(1,$row['id']));
				} else {
					$adb->pquery("update {$table_prefix}_emails_send_queue set error = ? where id = ?",array($error_message,$row['id']));
				}
				if ($mail->SMTPDebug) echo "\nmessage {$row['id']} sent!\n\n";	//crmv@55094
			}
		}
	}
	//crmv@48501e
	//crmv@44037	crmv@49001
	function setSignatureId() {
		$this->signatureId = rand(10000,99999);
	}
	function addSignature() {
		if ($this->signatureStatus) return;
		if (empty($this->signatureId)) $this->setSignatureId();
		$this->column_fields['description'] .= '<p></p><div id="signature'.$this->signatureId.'"></div>';
		$this->signatureStatus = true;
	}
	//crmv@44037e	crmv@49001e
	//crmv@51130	crmv@55094
	function checkBeforeSending($request,&$error) {
		
		require_once("modules/Emails/class.phpmailer.php");
		require_once("modules/Emails/class.smtp.php");
		
		global $adb, $table_prefix, $current_user;
		
		$subject = $request['subject'];
		$description = $request['description'];
		$from_email = $request['from_email'];
		$cc = explode(',',$request['ccmail']);
		$cc = array_map('trim', $cc);
		$cc = array_filter($cc);
		$cc = implode(',',$cc);
		$bcc = explode(',',$request['bccmail']);
		$bcc = array_map('trim', $bcc);
		$bcc = array_filter($bcc);
		$bcc = implode(',',$bcc);
		
		$to_mail = $_REQUEST['to_mail'];
		$parentid = $_REQUEST['parent_id'];
		$myids = explode("|",$parentid);
		if (!empty($myids)) {
			$myids = array_filter($myids);
		}
		$to = array();
		if(isset($to_mail) && $to_mail != '') {
			$to = explode(',',$to_mail);
			$to = array_map('trim', $to);
			$to = array_filter($to);
		}
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
				// vtlib customization: Enabling mail send from other modules
				$myfocus = CRMEntity::getInstance($pmodule);
				$myfocus->retrieve_entity_info($mycrmid, $pmodule);
				// END
				$fldname = $adb->query_result($fresult,0,'fieldname');
				$emailadd = br2nl($myfocus->column_fields[$fldname]);
			}
			if($emailadd != '') {
				$to[] = $emailadd;
			}
		}

		$mail = new PHPMailer();
		$request_backup = $_REQUEST;
		$_REQUEST = $request;
		if ($mail->SMTPDebug) ob_start();
		setMailerProperties($mail,$subject,$description,$from_email,$current_user->column_fields['user_name'],$to,'all');
		$_REQUEST = $request_backup;
		setCCAddress($mail,'cc',$cc);
		setCCAddress($mail,'bcc',$bcc);
		
		// crmv@114260
		if ($from_email) {
			$account = $this->getFromEmailAccount($from_email);
			if ($account > 0) {
				// check if I can use the account smtp
				$msgFocus = CRMEntity::getInstance('Messages');
				if ($msgFocus->hasSmtpAccount($account)) {
					$smtpinfo = $msgFocus->getSmtpConfig($account);
					if ($smtpinfo) {
						$mail->SMTPAuth = ($smtpinfo['smtp_auth'] == "true");
						$mail->Host = $smtpinfo['smtp_server'];
						$mail->Username = $smtpinfo['smtp_username'];
						$mail->Password = $smtpinfo['smtp_password'];
						if ($smtpinfo['smtp_port']) {
							$mail->Port = $smtpinfo['smtp_port'];
						}
					}
				}
			}
		}
		// crmv@114260e
		
		// code of function SmtpSend in class.phpmailer.php
		$return = true;
		if(!$mail->SmtpConnect()) {
            $return = false;
		} else {
	        $smtp_from = ($mail->Sender == "") ? $mail->From : $mail->Sender;
	        if(!$mail->smtp->Mail($smtp_from)) {
	            $error = $mail->Lang("from_failed") . $smtp_from;
	            $return = false;
	        } else {
		        // Attempt to send attach all recipients
		        for($i = 0; $i < count($mail->to); $i++)
		        {
		            if(!$mail->smtp->Recipient($mail->to[$i][0]))
		                $bad_rcpt[] = $mail->to[$i][0];
		        }
		        for($i = 0; $i < count($mail->cc); $i++)
		        {
		            if(!$mail->smtp->Recipient($mail->cc[$i][0]))
		                $bad_rcpt[] = $mail->cc[$i][0];
		        }
		        for($i = 0; $i < count($mail->bcc); $i++)
		        {
		            if(!$mail->smtp->Recipient($mail->bcc[$i][0]))
		                $bad_rcpt[] = $mail->bcc[$i][0];
		        }
		        if(count($bad_rcpt) > 0) // Create error message
				{
		            for($i = 0; $i < count($bad_rcpt); $i++)
		            {
		                if($i != 0) { $error .= ", "; }
		                $error .= $bad_rcpt[$i];
		            }
		            $error = $mail->Lang("recipients_failed") . $error;
		            $return = false;
				} else {
			        //crmv@58893	crmv@65328
			        if(!empty($mail->AltBody))
			            $mail->ContentType = "multipart/alternative";
			
			        $mail->error_count = 0; // reset errors
			        $mail->SetMessageType();
			        $mail->smtp->Hello();
			    	if (preg_match("/250-SIZE(.*)/",$mail->smtp->getHelloOutput(),$match)){
			    		$max_size = trim($match[1]);
			    	} else { //cannot specify size, use default one
			    		$max_size = $this->max_message_size;
			    	}
			    	try {
			    		$bodystring = $mail->CreateBody();
			    	} catch(Exception $e){
			    		 $error = $e->getMessage() . $error;
			    		 $return = false; 
			    	}
			    	if ($return !== false) {
			    		$message_size = mb_strlen($bodystring);
			    		
			    		// calculate size of forwarded attachments
			    		$message_mode = vtlib_purify($_REQUEST['message_mode']);
						$messageid = vtlib_purify($_REQUEST['message']);
			    		if ($message_mode == 'forward' && !empty($messageid)) {
			    			$focusMessages = CRMEntity::getInstance('Messages');
							$focusMessages->id = $messageid;		
							$result = $focusMessages->retrieve_entity_info($messageid,'Messages',false);
							if (empty($result)) {	// no errors
								$sz = $focusMessages->getAttachmentsSize($messageId);
								$message_size += $sz;
							}
			    		}
			    		
				    	if ($message_size > $max_size && !empty($max_size)){ //crmv@73375
				    		$size_mb = round($max_size/1024/1024,2)." MB";
				    		$error = sprintf(getTranslatedString('LBL_MESSAGE_TOO_BIG','Emails').$error,$size_mb);
				    		$return = false;
				    	}
				    	unset($bodystring);
			    	}
			    	//crmv@58893e	crmv@65328e
		        }
			}
		}
		if ($mail->SMTPDebug) {
			$smtplog = ob_get_contents();
			$smtplog = "Subject: {$subject}\nFrom: {$from_email}\nTo: ".implode(', ',$to)."\nDate: ".date('Y-m-d H:i:s')."\n\n".$smtplog;
			ob_end_clean();
			file_put_contents('logs/checkBeforeSending.log',$smtplog."\n\n--------------------------------------\n\n",FILE_APPEND);
		}
		return $return;
	}
	//crmv@51130e	crmv@55094e
	
	//crmv@114260
	public function checkSmtpServer($accountid = 0) {
		global $adb, $table_prefix;
		
		// check account server
		if ($accountid > 0) {
			$msg = CRMEntity::getInstance('Messages');
			$hasit = $msg->hasSmtpAccount($accountid);
			if ($hasit) return true;
		}
		
		// check global smtp server
		$res = $adb->pquery("SELECT id FROM {$table_prefix}_systems WHERE server_type = ?", array('email'));
		return ($res && $adb->num_rows($res) > 0);
	}
	//crmv@114260e
	
	//crmv@80029
	/*
	 * extends the class and use this method in order to change the object $mail
	 * ex. you can change the server smtp or the sender, the recipients, ecc.
	 */
	//function overwriteMailConfiguration(&$mail) {}
	//crmv@80029e
}
/** Function to get the emailids for the given ids form the request parameters
 *  It returns an array which contains the mailids and the parentidlists
*/

function get_to_emailids($module)
{
	global $adb,$table_prefix;
	if(isset($_REQUEST["field_lists"]) && $_REQUEST["field_lists"] != "")
	{
		$field_lists = $_REQUEST["field_lists"];
		if (is_string($field_lists)) $field_lists = explode(":", $field_lists);
		$query = 'select columnname,fieldid from '.$table_prefix.'_field where fieldid in ('. generateQuestionMarks($field_lists) .') and '.$table_prefix.'_field.presence in (0,2)';
		$result = $adb->pquery($query, array($field_lists));
		$columns = Array();
		$idlists = '';
		$mailids = '';
		while($row = $adb->fetch_array($result))
    	{
			$columns[]=$row['columnname'];
			$fieldid[]=$row['fieldid'];
		}
		$columnlists = implode(',',$columns);
		//crmv@27096	//crmv@27917
		$idarray = getListViewCheck($module);
		if (empty($idarray)) {
			$idstring = $_REQUEST['idlist'];
		} else {
			$idstring = implode(':',$idarray);
		}
		//crmv@27096e	//crmv@27917e
		$single_record = false;
		if(!strpos($idstring,':'))
		{
			$single_record = true;
		}
		$crmids = str_replace(':',',',$idstring);
		$crmids = explode(",", $crmids);
		switch($module)
		{
			case 'Leads':
				$query = 'select crmid,'.$adb->sql_concat(Array('firstname',"' '",'lastname')).' as entityname,'.$columnlists.' from '.$table_prefix.'_leaddetails inner join '.$table_prefix.'_crmentity on '.$table_prefix.'_crmentity.crmid='.$table_prefix.'_leaddetails.leadid left join '.$table_prefix.'_leadscf on '.$table_prefix.'_leadscf.leadid = '.$table_prefix.'_leaddetails.leadid where '.$table_prefix.'_crmentity.deleted=0 and ((ltrim('.$table_prefix.'_leaddetails.email) is not null) or (ltrim('.$table_prefix.'_leaddetails.yahooid) is not null)) and '.$table_prefix.'_crmentity.crmid in ('. generateQuestionMarks($crmids) .')';
				break;
			case 'Contacts':
				//email opt out funtionality works only when we do mass mailing.
				if(!$single_record)
				$concat_qry = '(((ltrim('.$table_prefix.'_contactdetails.email) is not null)  or (ltrim('.$table_prefix.'_contactdetails.yahooid) is not null)) and ('.$table_prefix.'_contactdetails.emailoptout != 1)) and ';
				else
				$concat_qry = '((ltrim('.$table_prefix.'_contactdetails.email) is not null)  or (ltrim('.$table_prefix.'_contactdetails.yahooid) is not null)) and ';
				$query = 'select crmid,'.$adb->sql_concat(Array('firstname',"' '",'lastname')).' as entityname,'.$columnlists.' from '.$table_prefix.'_contactdetails inner join '.$table_prefix.'_crmentity on '.$table_prefix.'_crmentity.crmid='.$table_prefix.'_contactdetails.contactid left join '.$table_prefix.'_contactscf on '.$table_prefix.'_contactscf.contactid = '.$table_prefix.'_contactdetails.contactid where '.$table_prefix.'_crmentity.deleted=0 and '.$concat_qry.'  '.$table_prefix.'_crmentity.crmid in ('. generateQuestionMarks($crmids) .')';
				break;
			case 'Accounts':
				//added to work out email opt out functionality.
				if(!$single_record)
					$concat_qry = '(((ltrim('.$table_prefix.'_account.email1) is not null) or (ltrim('.$table_prefix.'_account.email2) is not null)) and ('.$table_prefix.'_account.emailoptout != 1)) and ';
				else
					$concat_qry = '((ltrim('.$table_prefix.'_account.email1) is not null) or (ltrim('.$table_prefix.'_account.email2) is not null)) and ';
				$query = 'select crmid,accountname as entityname,'.$columnlists.' from '.$table_prefix.'_account inner join '.$table_prefix.'_crmentity on '.$table_prefix.'_crmentity.crmid='.$table_prefix.'_account.accountid left join '.$table_prefix.'_accountscf on '.$table_prefix.'_accountscf.accountid = '.$table_prefix.'_account.accountid where '.$table_prefix.'_crmentity.deleted=0 and '.$concat_qry.' '.$table_prefix.'_crmentity.crmid in ('. generateQuestionMarks($crmids) .')';
				break;
			case 'Vendors':
				$query = 'select crmid,vendorname as entityname,'.$columnlists.' from '.$table_prefix.'_vendor inner join '.$table_prefix.'_crmentity on '.$table_prefix.'_crmentity.crmid='.$table_prefix.'_vendor.vendorid left join '.$table_prefix.'_vendorcf on '.$table_prefix.'_vendorcf.vendorid = '.$table_prefix.'_vendor.vendorid where '.$table_prefix.'_crmentity.deleted=0 and '.$table_prefix.'_crmentity.crmid in ('. generateQuestionMarks($crmids) .')';
				break;
			//crmv@48167
			default:
				$focus = CRMEntity::getInstance($module);
				$query = "select crmid, $columnlists from {$focus->table_name} 
							inner join {$table_prefix}_crmentity on {$table_prefix}_crmentity.crmid = {$focus->table_name}.{$focus->tab_name_index[$focus->table_name]} 
							left join {$focus->customFieldTable[0]} on {$focus->customFieldTable[0]}.{$focus->customFieldTable[1]} = {$focus->table_name}.{$focus->tab_name_index[$focus->table_name]} 
							where deleted = 0 and {$focus->table_name}.{$focus->tab_name_index[$focus->table_name]} in (".generateQuestionMarks($crmids).")";
				break;
			//crmv@48167e
		}
		$result = $adb->pquery($query, array($crmids));
		while($row = $adb->fetch_array($result))
		{
			$name = $row['entityname'];
			//crmv@48167
			if (empty($name)) {
				$tmp = getEntityName($module, $row['crmid']);
				$name = $tmp[$row['crmid']];
			}
			//crmv@48167e
			for($i=0;$i<count($columns);$i++)
			{
				if($row[$columns[$i]] != NULL && $row[$columns[$i]] !='')
				{
					$idlists .= $row['crmid'].'@'.$fieldid[$i].'|';
					$mailids .= $name.'<'.$row[$columns[$i]].'>,';
				}
			}
		}
		$return_data = Array('idlists'=>$idlists,'mailds'=>$mailids);
	} else {
		$return_data = Array('idlists'=>"",'mailds'=>"");
	}
	return $return_data;

}

//added for attach the generated pdf with email
function pdfAttach($obj,$module,$file_name,$id)
{
	global $log;
	$log->debug("Entering into pdfAttach() method.");

	global $adb, $current_user,$table_prefix;
	global $upload_badext;
	$date_var = date('Y-m-d H:i:s');

	$ownerid = $obj->column_fields['assigned_user_id'];
	if(!isset($ownerid) || $ownerid=='')
		$ownerid = $current_user->id;

	$current_id = $adb->getUniqueID($table_prefix."_crmentity");

	$upload_file_path = decideFilePath();

	//crmv@31456
	if (isset($_REQUEST['draft_id']) && !in_array($_REQUEST['draft_id'],array('','undefined'))) {
		$res = $adb->pquery("SELECT
							  {$table_prefix}_attachments.attachmentsid
							FROM {$table_prefix}_attachments
							  INNER JOIN {$table_prefix}_seattachmentsrel
							    ON {$table_prefix}_attachments.attachmentsid = {$table_prefix}_seattachmentsrel.attachmentsid
							  INNER JOIN {$table_prefix}_crmentity
							    ON {$table_prefix}_crmentity.crmid = {$table_prefix}_attachments.attachmentsid
							WHERE {$table_prefix}_crmentity.deleted = 0
							    AND {$table_prefix}_seattachmentsrel.crmid = ? AND {$table_prefix}_attachments.name = ?",array($_REQUEST['draft_id'],$file_name));
		if ($res && $adb->num_rows($res) > 0) {
			$query = "insert into {$table_prefix}_seattachmentsrel values(?,?)";
			$adb->pquery($query, array($id, $adb->query_result($res,0,'attachmentsid')));
		}
		return true;
	}
	//crmv@31456

	//Copy the file from temporary directory into storage directory for upload
	$source_file_path = "storage/".$file_name;
	if (!is_file($source_file_path)) {
		return false;
	}
	$status = copy($source_file_path, $upload_file_path.$current_id."_".$file_name);
	//Check wheather the copy process is completed successfully or not. if failed no need to put entry in attachment table
	if($status)
	{
		$query1 = "insert into ".$table_prefix."_crmentity (crmid,smcreatorid,smownerid,setype,description,createdtime,modifiedtime) values(?,?,?,?,?,?,?)";
		$params1 = array($current_id, $current_user->id, $ownerid, $module." Attachment", $obj->column_fields['description'], $adb->formatDate($date_var, true), $adb->formatDate($date_var, true));
		$adb->pquery($query1, $params1);

		$query2="insert into ".$table_prefix."_attachments(attachmentsid, name, description, type, path) values(?,?,?,?,?)";
		$params2 = array($current_id, $file_name, $obj->column_fields['description'], 'pdf', $upload_file_path);
		$adb->pquery($query2, $params2);

		$query3='insert into '.$table_prefix.'_seattachmentsrel values(?,?)';
		$adb->pquery($query3, array($id, $current_id));

		// Delete the file that was copied
		checkFileAccessForDeletion($source_file_path); // crmv@37463
		unlink($source_file_path);

		return true;
	}
	else
	{
		$log->debug("pdf not attached");
		return false;
	}
}
//this function check email fields profile permission as well as field access permission
function emails_checkFieldVisiblityPermission($fieldname) {
	global $current_user;
	$ret = getFieldVisibilityPermission('Emails',$current_user->id,$fieldname);
	return $ret;
}

//crmv@25356
function setAddressInfo($idlist, $to_email_array=Array(), $cleanAdv=false) {
	$tmp = explode('|',$idlist);
	$autosuggest = '';
	array_walk($to_email_array,'addressClean');
	if ($cleanAdv) {
		array_walk($to_email_array,'addressCleanAdv');
	}
	$to_email_array = array_filter($to_email_array);
	$to_email_array_tmp = $to_email_array;
	if (!empty($tmp)) {
		foreach($tmp as $k => $t) {
			if ($t == '') {
				continue;
			}
			$id = explode('@',$t);
			$crmid = $id[0];
			$fieldid = $id[1];
			//crmv@2043m
			if ($crmid == '' || $fieldid == '') {
				continue;
			}
			//crmv@2043me
			//crmv@30434 - support to old mode
			if ($fieldid == -1){
				$mod = 'Users';
				$name = array($crmid => getUserFullName($crmid));
				$em = getUserEmail($crmid);
			}
			//crmv@30434e
			elseif (strpos($fieldid,'-') === 0) {
				$mod = 'Users';
				$fieldid = substr($fieldid,1);
				$name = array($crmid => getUserFullName($crmid));
				$em = getEmailFromIdlist($mod,$crmid,$fieldid);
			} else {
				$mod = getSalesEntityType($crmid);
				$name = getEntityName($mod,array($crmid));
				$em = getEmailFromIdlist($mod,$crmid,$fieldid);
			}
			if (in_array($em,$to_email_array)) {
				unset($to_email_array[array_search($em,$to_email_array)]);
			}

			$autosuggest .= '<span id="to_'.$t.'" class="addrBubble">'.$name[$crmid]
			.'<div id="to_'.$t.'_parent_id" style="display:none;">'.$t.'</div>'
			.'<div id="to_'.$t.'_parent_name" style="display:none;">'.$name[$crmid].'</div>'
			.'<div id="to_'.$t.'_hidden_toid" style="display:none;">'.$em.'</div>'
			.'<div id="to_'.$t.'_remove" class="ImgBubbleDelete" onClick="removeAddress(\'to\',\''.$t.'\');"><i class="vteicon small">clear</i></div>'
			.'</span>';
		}
	}
	return array('autosuggest'=>$autosuggest,'to_mail'=>implode(', ',array_diff($to_email_array_tmp,$to_email_array)),'other_to_mail'=>implode(', ',$to_email_array));
}
function addressClean(&$to_email_array) {
	$to_email_array = trim($to_email_array);
}
function addressCleanAdv(&$to_email_array) {
	$separatorl = strpos($to_email_array,'<');
	$separatorr = strpos($to_email_array,'>');
	if ($separatorl !== false && $separatorr !== false) {
		$to_email_array = substr($to_email_array,$separatorl+1,($separatorr-$separatorl-1));
	}
}
function getEmailFromIdlist($module,$crmid,$fieldid) {
	global $adb,$table_prefix;
	if ($fieldid != '') {
		$email = '';
		$result = $adb->pquery('select columnname, tablename from '.$table_prefix.'_field where fieldid = ?',array($fieldid));
		$columnname = $adb->query_result($result,0,'columnname');
		$tablename = $adb->query_result($result,0,'tablename');
		$moduleInstance = CRMEntity::getInstance($module);
		$result = $adb->pquery('select '.$columnname.' from '.$tablename.' where '.$moduleInstance->tab_name_index[$tablename].' = ?',array($crmid));
		if ($result && $adb->num_rows($result)>0) {
			$email = $adb->query_result($result,0,$columnname);
		}
		return $email;
	}
}
//crmv@25356e
//crmv@2043m
function getIdListReplyMailConverter($record, $email_list) {
	global $adb,$table_prefix;
	$module = getSalesEntityType($record);
	$focus = CRMEntity::getInstance($module);
	$query = "SELECT fieldid,tablename,columnname FROM ".$table_prefix."_field WHERE tabid=? and uitype=13";
	$result = $adb->pquery($query, array(getTabid($module)));
	if ($result && $adb->num_rows($result) > 0) {
		while($row=$adb->fetchByAssoc($result)) {
			foreach($email_list as $email) {
				$query1 = 'select '.$row['columnname'].' from '.$row['tablename'].' where '.$focus->tab_name_index[$row['tablename']].' = ? and '.$row['columnname'].' = ?';
				$result1 = $adb->pquery($query1,array($record, $email));
				if ($result1 && $adb->num_rows($result1) > 0) {
					return "$record@".$row['fieldid'].'|';
				}
			}
		}
	}
	return '';
}
function getFieldList($module) {
	global $adb,$table_prefix;
	$ids = array();
	$query = "SELECT fieldid FROM ".$table_prefix."_field WHERE tabid=? and uitype=13 and presence IN (0,2)";
	$result = $adb->pquery($query, array(getTabid($module)));
	if ($result && $adb->num_rows($result) > 0) {
		while($row=$adb->fetchByAssoc($result)) {
			$ids[] = $row['fieldid'];
		}
	}
	return $ids;
}
//crmv@2043me
?>