<?php
/*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ********************************************************************************/

/* crmv@2963m crmv@95157 */

require_once('modules/Users/Users.php');
require_once('modules/Documents/storage/StorageBackendUtils.php');

/**
 * Mail Scanner Action
 */
class Vtiger_MailScannerAction {
	// actionid for this instance
	var $actionid  = false;
	// scanner to which this action is associated
	var $scannerid = false;
	// type of mailscanner action
	var $actiontype= false;
	// text representation of action
	var $actiontext= false;
	// target module for action
	var $module    = false;
	// lookup information while taking action
	var $lookup    = false;

	// Storage folder to use
	var $STORAGE_FOLDER = 'storage/mailscanner/';

	/** DEBUG functionality */
	var $debug     = false;
	function log($message) {
		global $log;
		if($log && $this->debug) { $log->debug($message); }
		else if($this->debug) echo "$message\n";
	}

	/**
	 * Constructor.
	 */
	function __construct($foractionid) {
		$this->initialize($foractionid);
	}

	/**
	 * Initialize this instance.
	 */
	function initialize($foractionid) {
		global $adb,$table_prefix;
		$result = $adb->pquery("SELECT * FROM ".$table_prefix."_mailscanner_actions WHERE actionid=? ORDER BY sequence", Array($foractionid));

		if($adb->num_rows($result)) {
			$this->actionid   = $adb->query_result($result, 0, 'actionid');
			$this->scannerid  = $adb->query_result($result, 0, 'scannerid');
			$this->actiontype = $adb->query_result($result, 0, 'actiontype');
			$this->module     = $adb->query_result($result, 0, 'module');
			$this->lookup     = $adb->query_result($result, 0, 'lookup');
			$this->actiontext = "$this->actiontype,$this->module,$this->lookup";
		}
	}

	/**
	 * Create/Update the information of Action into database.
	 */
	function update($ruleid, $actiontext) {
		global $adb,$table_prefix;

		$inputparts = explode(',', $actiontext);
		$this->actiontype = $inputparts[0]; // LINK, CREATE
		$this->module     = $inputparts[1]; // Module name
		$this->lookup     = $inputparts[2]; // FROM, TO

		$this->actiontext = $actiontext;

		if($this->actionid) {
			$adb->pquery("UPDATE ".$table_prefix."_mailscanner_actions SET scannerid=?, actiontype=?, module=?, lookup=? WHERE actionid=?",
				Array($this->scannerid, $this->actiontype, $this->module, $this->lookup, $this->actionid));
		} else {
			$this->sequence = $this->__nextsequence();
			//crmv@16212
			$this->actionid = $adb->getUniqueID($table_prefix.'_mailscanner_actions');
			$adb->pquery("INSERT INTO ".$table_prefix."_mailscanner_actions(actionid,scannerid, actiontype, module, lookup, sequence) VALUES(?,?,?,?,?,?)",
				Array($this->actionid,$this->scannerid, $this->actiontype, $this->module, $this->lookup, $this->sequence));
			//crmv@16212 end
		}
		$checkmapping = $adb->pquery("SELECT COUNT(*) AS ruleaction_count FROM ".$table_prefix."_mailscanner_ruleactions
			WHERE ruleid=? AND actionid=?", Array($ruleid, $this->actionid));
		if($adb->num_rows($checkmapping) && !$adb->query_result($checkmapping, 0, 'ruleaction_count')) {
			$adb->pquery("INSERT INTO ".$table_prefix."_mailscanner_ruleactions(ruleid, actionid) VALUES(?,?)",
				Array($ruleid, $this->actionid));
		}
	}

	/**
	 * Delete the actions from tables.
	 */
	function delete() {
		global $adb,$table_prefix;
		if($this->actionid) {
			$adb->pquery("DELETE FROM ".$table_prefix."_mailscanner_actions WHERE actionid=?", Array($this->actionid));
			$adb->pquery("DELETE FROM ".$table_prefix."_mailscanner_ruleactions WHERE actionid=?", Array($this->actionid));
		}
	}

	/**
	 * Get next sequence of Action to use.
	 */
	function __nextsequence() {
		global $adb,$table_prefix;
		$seqres = $adb->pquery("SELECT max(sequence) AS max_sequence FROM ".$table_prefix."_mailscanner_actions", Array());
		$maxsequence = 0;
		if($adb->num_rows($seqres)) {
			$maxsequence = $adb->query_result($seqres, 0, 'max_sequence');
		}
		++$maxsequence;
		return $maxsequence;
	}

	/**
	 * Apply the action on the mail record.
	 */
	function apply($mailscanner, $mailrecord, $mailscannerrule, $matchresult) {
		$returnid = false;
		if($this->actiontype == 'CREATE') {
			if($this->module == 'HelpDesk') {
				$returnid = $this->__CreateTicket($mailscanner, $mailrecord);
			}
		} else if($this->actiontype == 'LINK') {
			$returnid = $this->__LinkToRecord($mailscanner, $mailrecord);
		} else if($this->actiontype == 'UPDATE') {
			if($this->module == 'HelpDesk') {
				$returnid = $this->__UpdateTicket($mailscanner, $mailrecord, $mailscannerrule->hasRegexMatch($matchresult), $mailscannerrule->compare_parentid, $mailscannerrule->match_field);	//crmv@78745 crmv@81643
			}
		//crmv@27618
		} else if($this->actiontype == 'DO_NOTHING') {
			$returnid = $this->actiontype;
		//crmv@27618e
		}
		return $returnid;
	}

	/**
	 * Update ticket action.
	 */
	function __UpdateTicket($mailscanner, $mailrecord, $regexMatchInfo, $compare_parentid, $match_field) {	//crmv@78745 crmv@81643
		global $adb,$table_prefix;
		$returnid = false;

		$usesubject = false;
		if($this->lookup == 'SUBJECT') {
			// If regex match was performed on subject use the matched group
			// to lookup the ticket record
			if($regexMatchInfo) $usesubject = $regexMatchInfo['matches'];
			else $usesubject = $mailrecord->_subject;

			// Get the ticket record that was created by SENDER earlier
			$fromemail = $mailrecord->_from[0];

			$linkfocus = $mailscanner->GetTicketRecord($usesubject, $fromemail, $compare_parentid, $match_field);	//crmv@78745 crmv@81643
			$relatedid = $linkfocus->column_fields[parent_id];

			// If matching ticket is found, update comment, attach email
			if($linkfocus) {
				$timestamp = $adb->formatDate(date('Y-m-d H:i:s'), true);
				$comid = $adb->getUniqueID($table_prefix.'_ticketcomments');
				$adb->pquery("INSERT INTO ".$table_prefix."_ticketcomments(commentid,ticketid, comments, ownerid, ownertype, createdtime) VALUES(?,?,?,?,?,?)",	//crmv@fix
					Array($comid,$linkfocus->id, html_entity_decode($mailrecord->getBodyText(),ENT_COMPAT,'UTF-8'), $relatedid, 'customer', $timestamp));
				//crmv@2043m
				if ($linkfocus->answeredByCustomerStatus != '') {
					$ticket_status = $linkfocus->answeredByCustomerStatus;
				} else {
					$ticket_status = 'Open';
				}
				// Set the ticket status to Open if its Closed
				$adb->pquery("UPDATE ".$table_prefix."_troubletickets set status=? WHERE ticketid=?", Array($ticket_status, $linkfocus->id));
				$adb->pquery("UPDATE ".$table_prefix."_crmentity set modifiedtime=? WHERE crmid=?", Array($timestamp, $linkfocus->id));	//crmv@81643
				/*
				if ($linkfocus->answeredByCustomerStatus != '') {
					$linkfocus->retrieve_entity_info($linkfocus->id, 'HelpDesk');
					$linkfocus->mode = 'edit';
					$linkfocus->column_fields['ticketstatus'] = $linkfocus->answeredByCustomerStatus;
					$linkfocus->save('HelpDesk');
				}
				*/
				//crmv@2043me
				$returnid = $this->__CreateNewEmail($mailrecord, $this->module, $linkfocus);

			} else {
				// TODO If matching ticket was not found, create ticket?
				// $returnid = $this->__CreateTicket($mailscanner, $mailrecord);
			}
		}
		return $returnid;
	}

	/**
	 * Create ticket action.
	 */
	function __CreateTicket($mailscanner, $mailrecord) {
		// Prepare data to create trouble ticket
		$usetitle = $mailrecord->_subject;
		$description = $mailrecord->getBodyText();
		//crmv@2043m
		$matches = preg_match('/<body[^>]*>(.*)/ims',$description,$tmp);
		if ($matches) {
			$description = $tmp[1];
		}
		if (strpos($description,'</body>') !== false) {
			$description = substr($description,0,strpos($description,'</body>'));
		}
		//crmv@2043me

		// There will be only on FROM address to email, so pick the first one
		$fromemail = $mailrecord->_from[0];
		$linktoid = $mailscanner->LookupContact($fromemail);
		if(!$linktoid) $linktoid = $mailscanner->LookupAccount($fromemail);
		
		//crmv@49609 : moved upwards
		global $current_user;
		if(!$current_user) $current_user = new Users();
		$current_user->id = 1;
		//crmv@49609e
		
		//crmv@2043m
		global $adb,$table_prefix;
		require_once('include/Webservices/WebserviceField.php');
		$fieldInstance = WebserviceField::fromQueryResult($adb,$adb->query("SELECT * FROM ".$table_prefix."_field WHERE tabid = 13 AND fieldname = 'parent_id'"),0);
		$referenceList = $fieldInstance->getReferenceList();
		if (in_array('Leads',$referenceList)) {
			if(!$linktoid) $linktoid = $mailscanner->LookupLead($fromemail);
			if(!$linktoid) $linktoid = $mailscanner->CreateLead($fromemail);
		}
		//crmv@2043me

		/** Now Create Ticket **/
		
		// Create trouble ticket record
		$ticket = CRMEntity::getInstance('HelpDesk');
		$ticket->column_fields['ticket_title'] = $usetitle;
		$ticket->column_fields['description'] = $description;
		$ticket->column_fields['ticketstatus'] = 'Open';
		$ticket->column_fields['assigned_user_id'] = $current_user->id;
		if($linktoid) $ticket->column_fields['parent_id'] = $linktoid;
		//crmv@2043m
		if (isset($ticket->column_fields['email_from'])) {
			//crmv@OPER6053
			if (is_array($mailrecord->_from)) {
				$email_from = implode(',',$mailrecord->_from);
			} else {
				$email_from = $mailrecord->_from;
			}
			$ticket->column_fields['email_from'] = $email_from;
			//crmv@OPER6053e
		}
		if (isset($ticket->column_fields['email_to'])) {
			if (is_array($mailrecord->_to)) {
				$email_to = implode(',',$mailrecord->_to);
			} else {
				$email_to = $mailrecord->_to;
			}
			$ticket->column_fields['email_to'] = $email_to;
		}
		if (isset($ticket->column_fields['email_cc'])) {
			if (is_array($mailrecord->_cc)) {
				$email_cc = implode(',',$mailrecord->_cc);
			} else {
				$email_cc = $mailrecord->_cc;
			}
			$ticket->column_fields['email_cc'] = $email_cc;
		}
		if (isset($ticket->column_fields['email_bcc'])) {
			if (is_array($mailrecord->_bcc)) {
				$email_bcc = implode(',',$mailrecord->_bcc);
			} else {
				$email_bcc = $mailrecord->_bcc;
			}
			$ticket->column_fields['email_bcc'] = $email_bcc;
		}
		//crmv@2043me
		//crmv@27618
		if (isset($ticket->column_fields['mailscanner_action']) && $this->actionid !== false) {
			$ticket->column_fields['mailscanner_action'] = $this->actionid;
			//crmv@OPER6053
			$result = $adb->pquery("SELECT username FROM {$table_prefix}_mailscanner WHERE scannerid = ?", array($this->scannerid));
			if ($result && $adb->num_rows($result) > 0) {
				$ticket->column_fields['helpdesk_from'] = $adb->query_result($result,0,'username');
			}
			//crmv@OPER6053e
		}
		//crmv@27618e
		$ticket->save('HelpDesk');
		//crmv@2043m
		if (isset($ticket->column_fields['email_date'])) {
			$adb->pquery('update '.$table_prefix.'_troubletickets set email_date = ? where ticketid = ?',array(date('Y-m-d H:i:s', $mailrecord->_date), $ticket->id));
		}
		//crmv@2043me

		// Associate any attachement of the email to ticket
		$this->__SaveAttachements($mailrecord, 'HelpDesk', $ticket, $ticket);	//crmv@27657

		//crmv@2043m
		$mailrecord->_subject .= ' - Ticket Id: '.$ticket->id;
		$this->__CreateNewEmail($mailrecord, $this->module, $ticket);
		//crmv@2043me

		return $ticket->id;
	}

	/**
	 * Add email to CRM record like Contacts/Accounts
	 */
	function __LinkToRecord($mailscanner, $mailrecord) {
		$linkfocus = false;

		$useemail  = false;
		if($this->lookup == 'FROM') $useemail = $mailrecord->_from;
		else if($this->lookup == 'TO') $useemail = $mailrecord->_to;

		if($this->module == 'Contacts') {
			foreach($useemail as $email) {
				$linkfocus = $mailscanner->GetContactRecord($email);
				if($linkfocus) break;
			}
		} else if($this->module == 'Accounts') {
			foreach($useemail as $email) {
				$linkfocus = $mailscanner->GetAccountRecord($email);
				if($linkfocus) break;
			}
		//crmv@2043m
		} else if($this->module == 'Leads') {
			foreach($useemail as $email) {
				$linkfocus = $mailscanner->GetLeadRecord($email);
				if($linkfocus) break;
			}
		//crmv@2043me
		//crmv@27657
		} else if($this->module == 'Vendors') {
			foreach($useemail as $email) {
				$linkfocus = $mailscanner->GetVendorRecord($email);
				if($linkfocus) break;
			}
		//crmv@27657e
		}

		$returnid = false;
		if($linkfocus) {
			$returnid = $this->__CreateNewEmail($mailrecord, $this->module, $linkfocus);
		}
		return $returnid;
	}

	/**
	 * Create new Email record (and link to given record) including attachements
	 */
	function __CreateNewEmail($mailrecord, $module, $linkfocus) {
		global $current_user, $adb,$table_prefix;
		if(!$current_user) $current_user = new Users();
		$current_user->id = 1;

		//crmv@2043m
		$fieldid = '-1';
		$result = $adb->pquery('SELECT fieldid FROM '.$table_prefix.'_field WHERE tabid = ? AND (fieldname = ? OR fieldname = ? OR fieldname = ?)',array(getTabid($module),'email','email1','email2'));
		if ($result && $adb->num_rows($result) > 0) {
			$fieldid = $adb->query_result($result,0,'fieldid');
		} else {
			if ($module == 'HelpDesk') {
				$fieldid = '';
			}
		}
		//crmv@2043me

		$from = $mailrecord->_from[0];
		$to = $mailrecord->_to[0];
		$cc = (!empty($mailrecord->_cc))? implode(',', $mailrecord->_cc) : '';
		$bcc = (!empty($mailrecord->_bcc))? implode(',', $mailrecord->_bcc) : '';

		$column_fields = array(
			'subject'=>$mailrecord->_subject,
			'description'=>$mailrecord->getBodyHTML(),
			'mfrom'=>$from,
			'mto'=>$to,
			'mcc'=>$cc,
			'mbcc'=>$bcc,
			'mdate'=>date('Y-m-d H:i:s', $mailrecord->_date),
			'assigned_user_id'=>$linkfocus->column_fields['assigned_user_id'],
			'parent_id'=>"$linkfocus->id@$fieldid|",
			//crmv@56233
			'folder'=>$mailrecord->_folder,
			'xuid'=>$mailrecord->_xuid,
			'messageid'=>$mailrecord->_uniqueid,
			//crmv@56233e
			'mtype'=>'Link',
			'mvisibility'=>'Public',
		);
		$focus = CRMentity::getInstance('Messages');
		$messagesid = $focus->saveCacheLink($column_fields);
		$focus->retrieve_entity_info_no_html($messagesid,'Messages');
		$focus->id = $messagesid;
		$this->__SaveAttachements($mailrecord, 'Messages', $focus, $linkfocus);
		return $messagesid;
	}

	/**
	 * Save attachments from the email and add it to the module record.
	 */
	function __SaveAttachements($mailrecord, $basemodule, $basefocus, $modulefocus) {	//crmv@27657
		global $adb,$table_prefix;

		// If there is no attachments return
		if(!$mailrecord->_attachments) return;
		
		$SBU = StorageBackendUtils::getInstance();

		$userid = $basefocus->column_fields['assigned_user_id'];
		$attach_contentid = -1;

		foreach($mailrecord->_attachments as $filename=>$filecontent) {

			// Create document record
			//crmv@86304
			$resFolder = $adb->pquery("select folderid from {$table_prefix}_crmentityfolder where foldername = ?", array('Message attachments'));
			($resFolder && $adb->num_rows($resFolder) > 0) ? $folderid = $adb->query_result($resFolder,0,'folderid') : $folderid = 1;
			//crmv@86304e
			
			$document = CRMEntity::getInstance('Documents');
			$document->column_fields['notes_title']      = $filename;
			$document->column_fields['filename']         = $filename;
			$document->column_fields['filestatus']       = 1;
			$document->column_fields['filelocationtype'] = 'B';
			$document->column_fields['backend_name'] = $SBU->defaultBackend;
			$document->column_fields['folderid']         = $folderid;	//crmv@86304
			$document->column_fields['assigned_user_id'] = $userid;
			$document->column_fields['filesize'] = 0;	//crmv@18341
			
			$fieldname = $document->getFile_FieldName();
			
			// create a temporary file
			$fh = tmpfile();
			$r = fwrite($fh, $filecontent);
			if ($r === false) {
				// TODO: log something
				@fclose($fh);
				continue;
			}
			$finfo = stream_get_meta_data($fh);
			
			// populate a fake files request
			$_FILES = array();
			$_FILES[$fieldname] = array(
				'name' => $filename,
				'size' => strlen($filecontent),
				'type' => MailAttachmentMIME::detect($filename),
				'tmp_name' => realpath($finfo['uri']),
			);
			$_POST['copy_not_move'] = true;
			$document->alternativeFileStorage = $this->STORAGE_FOLDER;
			
			// save the record
			$document->save('Documents');
			
			$documentid = $document->id;
			
			$FS = FileStorage::getInstance();
			$attachid = $FS->getLastInsertedId();
			
			// close and delete the temporary file
			fclose($fh);

			//crmv@27657

			//Link file attached to email
			if ($attachid > 0) {
				$adb->pquery("INSERT INTO ".$table_prefix."_seattachmentsrel(crmid, attachmentsid) VALUES(?,?)",Array($basefocus->id, $attachid));
			}

			if ($basemodule == 'Messages') {
				//crmv@90941
				$extension = substr(strrchr($filename,'.'), 1);
				(strtolower($extension) == 'eml') ? $contenttype = 'message/rfc822' : $contenttype = '';
				
				//$basefocus->save_related_module_small($basefocus->column_fields['messageid'], 'Documents', $documentid);
				$adb->pquery("insert into {$basefocus->table_name}_attach ({$basefocus->table_index},contentid,contentname,contenttype,contentdisposition,document) values (?,?,?,?,?,?)",
					array($basefocus->id,$attach_contentid,$filename,$contenttype,'attachment',$documentid));
					
				if (strtolower($extension) == 'eml') {
					global $currentModule; $currentModule_tmp = $currentModule; $currentModule = 'Messages';
					$messagesid = 0;
					$error = '';
					$success = $basefocus->parseEML($attach_contentid, $messagesid, $error, $filecontent);
					$currentModule = $currentModule_tmp;
				}
				//crmv@90941e
					
				$attach_contentid--;
			} elseif ($basemodule != 'Emails') {
				// Link document to base record
				$adb->pquery("INSERT INTO ".$table_prefix."_senotesrel (crmid, notesid) VALUES (?,?)",Array($modulefocus->id, $documentid)); // crmv@38798
			}
			//crmv@27657e

		}
		return $documentid;
	}

}
?>