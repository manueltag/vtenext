<?php
include_once('../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user;

die('Remove this line prior to execution. Restore it afterwards.');

require_once('modules/Emails/class.phpmailer.php');

set_time_limit(0);
//ini_set('memory_limit','256M'); // for diff algorithm

echo "<br><b>Migrating emails...</b><br>\n"; flush();


function formatImapDate($timestamp) {
	$months = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
	$m = date('m', $timestamp);
	return date('d-', $timestamp).$months[$m-1].date('-Y', $timestamp);
}


function UpdateSearchEmail(&$msgFocus, &$row) {
	global $current_user;

	$msgFocus->getZendMailStorageImap();
	$protocol = $msgFocus->getZendMailProtocolImap();

	if (!$protocol) return null;

	$date = DateTime::createFromFormat('Y-m-d H:i:s', $row['createdtime']);
	$date0 = formatImapDate($date->getTimestamp());
	$searchArr = array(
		'SUBJECT "'.$row['subject'].'"',
		'FROM "'.$row['from_email'].'"',
		'ON "'.$date0.'"', // WRONG!!
		//'BODY "'.substr($row['description'], 0, 100).'"',
	);
	try {
		$res = $protocol->search($searchArr);
	} catch (Exception $e) {
		echo "Search Error\n";
		return null;
	}

	// get the message
	// fetch all, the iterate for exact match
	$MR = $msgFocus->getMailResource();
	if (is_array($res) && count($res) > 0) {
		$found = false;
		foreach ($res as $mid) {
			echo $mid;
			// ok, only
			$mid = $res[0];
			try {
				$message = $MR->getMessage($mid);
			} catch (Exception $e) {
				$message = null;
			}
			if ($message) {
				$header = $msgFocus->getMessageHeader($message);
				//print_r($header);

				$dateCheck = false;
				// check only hour & minute
				if (preg_match('/\d{2}:\d{2}:\d{2}/', $header['Date'], $matches)) {
					$headerDate = DateTime::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d ').$matches[0])->getTimestamp();
					$searchDate = $date->getTimestamp();
					$ddiff = abs($headerDate-$searchDate);
					$dateCheck = ($ddiff < 3600); // 1hour interval
				}
				if ($header['Subject'] == $row['subject'] && $dateCheck) {
					$found = true;
					$header['X-Uid'] = $mid;
					break;
				}
			}
		}
		if ($found) {
			return $header;
		}
	} else {
		//echo "No messages found\n";
	}
	return null;

}


$mailer = new PHPMailer();

	$t0 = time();
	// MIGRAZIONE EMAIL
	// email inviate da crm (non newsletter) -> messaggi
	$current_user = CRMEntity::getInstance('Users');
	$res = $adb->query(
		"select
		e.*, sq.imap_id,
		c.smownerid, c.smcreatorid, c.createdtime, c.description,
		a.subject, a.date_start, a.time_start
		from {$table_prefix}_emaildetails e
		inner join {$table_prefix}_crmentity c on c.crmid = e.emailid
		inner join {$table_prefix}_activity a on a.activityid = e.emailid
		left join crmv_squirrelmailrel sq on sq.mail_id = e.emailid
		left join {$table_prefix}_newsletter n on e.idlists like ".$adb->sql_concat(array('cast(n.newsletterid as char(20))', "'@|%'"))."
		where c.deleted = 0 and c.setype = 'Emails' and e.email_flag = 'SENT' and n.newsletterid is null"
	);
	$saved = 0;
	$xuidtimer = time();
	if ($res && $adb->num_rows($res) > 0) {
		while ($row = $adb->FetchByAssoc($res, -1, false)) {

			// impersonate the email owner
			$current_user->retrieveCurrentUserInfoFromFile($row['smownerid']);
			$current_user->id = $row['smownerid'];

			$msg = CRMEntity::getInstance('Messages');
			$account = $msg->getMainUserAccount();
			$msg->setAccount($account['id']);
			//$specialFolders = $msg->getSpecialFolders();

			// try to get the username of the email
			$res2 = $adb->pquery("select user_name, first_name, last_name from {$table_prefix}_users where id = ? and email1 = ?", array($row['smcreatorid'], $row['from_email']));
			if ($res2 && $adb->num_rows($res2) == 1) {
				$r = $adb->FetchByAssoc($res2, -1, false);
				if (empty($r['first_name']) && empty($r['last_name'])) {
					$from_email = $r['user_name'];
				} else {
					$from_email = $r['first_name']." ".$r['last_name'];
				}
			} else {
				$from_email = '';
			}


			// check if newsletter - not needed, check is in query
			/*$isNewsletter = false;
			// set/update relations
			if (!empty($row['idlists'])) {
				$ids = array_filter(explode('|', $row['idlists']));
				foreach ($ids as $relid) {
					list($elid, $fieldid) = explode('@', $relid, 2);
					$mod = getSalesEntityType($elid);
					if ($mod == 'Newsletter') $isNewsletter = true;
				}
			}
			if ($isNewsletter) continue;*/

			// NO SERVER SEARCH

			// try to get sent email
			/*if ($row['imap_id'] > 0) {
			 $msg->getZendMailStorageImap();
			$msg->selectFolder($specialFolders['Sent']);
			try {
			$message = $msg->getMailResource()->getMessage($row['imap_id']);
			// TODO: non le trova -> dovrei prendere il messageid
			} catch (Exception $e) {
			// go on
			}
			}

			$foundMsg = UpdateSearchEmail($msg, $row);
			*/

			$to_email = Zend_Json::decode($row['to_email']);
			if (is_array($to_email)) $to_email = implode(', ', array_map('trim', array_filter($to_email)));

			$cc_email = Zend_Json::decode($row['cc_email']);
			if (is_array($cc_email)) $cc_email = implode(', ', array_map('trim', array_filter($cc_email)));

			$bcc_email = Zend_Json::decode($row['bcc_email']);
			if (is_array($bcc_email)) $bcc_email = implode(', ', array_map('trim', array_filter($bcc_email)));

			if ($foundMsg) {
				$xuid = $foundMsg['X-Uid'];
				$messageid = $foundMsg['Messageid'];
			} else {
				$xuid = '';//++$xuidtimer; - removed
				$uniq_id = md5(implode('', $row));
				$messageid = sprintf('<%s@%s>', $uniq_id, $mailer->ServerHostname()); // in this way, we won't duplicate messages
			}

			$description = $row['description'];
			if (substr($description, 0, 5) == '<pre>') {
				$description = str_replace(array('<pre>', '</pre>'), '', $description);
				$description = str_replace(array("\r", "\n"), array('', '<br>'), $description);
			}

			// save the message
			$msg->column_fields['subject'] = html_entity_decode($row['subject'],ENT_QUOTES, 'UTF-8');
			$msg->column_fields['assigned_user_id'] = $row['smownerid'];
			$msg->column_fields['description'] = $description;
			$msg->column_fields['mdate'] = $row['date_start'].' 00:00:00';
			$msg->column_fields['mfrom'] = $row['from_email'];
			$msg->column_fields['mfrom_n'] = $from_email;
			$msg->column_fields['mfrom_f'] = (empty($from_email) ? $row['from_email'] : $from_email." <{$row['from_email']}>");
			$msg->column_fields['mto'] = $to_email;
			$msg->column_fields['mto_n'] = '';
			$msg->column_fields['mto_f'] = $to_email;
			$msg->column_fields['mcc'] = $cc_email;
			$msg->column_fields['mcc_n'] = '';
			$msg->column_fields['mcc_f'] = $cc_email;
			$msg->column_fields['mbcc'] = $bcc_email;
			$msg->column_fields['mbcc_n'] = '';
			$msg->column_fields['mbcc_f'] = $bcc_email;
			$msg->column_fields['mreplyto'] = '';
			$msg->column_fields['mreplyto_n'] = '';
			$msg->column_fields['mreplyto_f'] = '';
			$msg->column_fields['in_reply_to'] = ($foundMsg ? $foundMsg['In-Reply-To'] : '');
			$msg->column_fields['mreferences'] = ($foundMsg ? $foundMsg['References'] : '');
			$msg->column_fields['thread_index'] = ($foundMsg ? $foundMsg['Thread-Index'] : '');
			$msg->column_fields['messageid'] = $messageid;
			//$msg->column_fields['messagehash'] = ''; // calculated
			$msg->column_fields['xmailer'] = 'VTECRM-WEBMAIL';
			$msg->column_fields['xuid'] = $xuid; // so we update existing email
			//$msg->column_fields['folder'] = $specialFolders['Sent'];
			$msg->column_fields['mtype'] = ($foundMsg ? 'Webmail' : 'Link');
			$msg->column_fields['mvisibility'] = 'Public';
			$msg->column_fields['seen'] = '1';
			$msg->column_fields['answered'] = '0';
			$msg->column_fields['forwarded'] = '0';
			$msg->column_fields['flagged'] = '0';
			$msg->mode = '';

			// check existence
			$res2 = $adb->pquery("select messagesid from {$msg->table_name} inner join {$table_prefix}_crmentity on {$table_prefix}_crmentity.crmid = {$msg->table_name}.{$msg->table_index} where {$table_prefix}_crmentity.deleted = 0 and mtype = ? and messageid = ? and smownerid = ?",
			array($msg->column_fields['mtype'],$messageid,$current_user->id)
			);
			if ($res2 && $adb->num_rows($res2) > 0) {
				$existingCrmid = $adb->query_result_no_html($res2, 0, 'messagesid');
				if ($existingCrmid > 0) {
					$msg->mode = 'edit';
					$msg->id = $existingCrmid;
				}
			}

			// save/update
			try {
				$msg->save('Messages', false, false, false);
				$mesid = $msg->id;
			} catch (Exception $e) {
				echo "Error while saving";
				continue;
			}

			if (!$mesid) {
				echo "Unable to save Message";
				continue;
			}

			// update times
			$adb->pquery("update {$table_prefix}_crmentity set smcreatorid = ?, createdtime = ?, modifiedtime = ? where crmid = ?", array($row['smcreatorid'], $row['createdtime'], $row['modifiedtime'], $mesid));


			// links array
			$ids = array();

			// search links (in seactivityrel)
			$rr = $adb->pquery("select crmid from {$table_prefix}_seactivityrel sr where activityid = ?", array($row['emailid']));
			if ($rr) {
				while ($row2 = $adb->FetchByAssoc($rr, -1, false)) {
					$ids[] = $row2['crmid'];
				}
			}

			// search links (in idlists)
			if (!empty($row['idlists'])) {
				$ids2 = array_filter(explode('|', $row['idlists']));
				foreach ($ids2 as $relid) {
					list($elid, $fieldid) = explode('@', $relid, 2);
					$ids[] = $elid;
				}
			}

			if (!empty($ids)) {
				foreach ($ids as $linkid) {
					$mod = getSalesEntityType($linkid);
					if ($mod) {
						$msg->save_related_module_small($messageid, $mod, $linkid);
					}
				}
			}

			// add documents relations
			$res2 = $adb->pquery(
				"SELECT att.*
				FROM {$table_prefix}_attachments att
				INNER JOIN {$table_prefix}_crmentity c on c.crmid = att.attachmentsid
				INNER JOIN {$table_prefix}_seattachmentsrel arel on arel.attachmentsid = att.attachmentsid
				WHERE c.deleted = 0 AND arel.crmid = ?", array($row['emailid'])
			);
			if ($res2 && $adb->num_rows($res2) > 0) {
				//echo "Found ".$adb->num_rows($res2)." attachments<br>\n";
				
				// create the folder for the attachments
				$folderInfo = getEntityFoldersByName('Imported Attachments', 'Documents');
				if (empty($folderInfo)) {
					$folderid = addEntityFolder('Documents', 'Imported Attachments');
				} else {
					$folderid = $folderInfo[0]['folderid'];
				}
				if (empty($folderid)) {
					echo "Unable to create folder for imported attachments<br>\n";
					continue;
				}
				$doc = CRMEntity::getInstance('Documents');
				$contentid = 0;
				while ($attrow = $adb->FetchByAssoc($res2, -1, false)) {
					$attid = $attrow['attachmentsid'];
					// now create the document linked to this attachment
					
					$path = $attrow['path'].$attid.'_'.$attrow['name'];
					if (!is_readable($path)) {
						echo "File $path is not readable, please check the file name.<br>\n";
					}
					
					$doc->column_fields['notes_title'] = $attrow['name'];
					$doc->column_fields['folderid'] = $folderid;
					$doc->column_fields['filename'] = $attrow['name'];
					$doc->column_fields['assigned_user_id'] = $row['smownerid'];
					$doc->column_fields['filetype'] = $attrow['type'];
					$doc->column_fields['filesize'] = filesize($path);
					$doc->column_fields['filelocationtype'] = 'I';
					$doc->column_fields['fileversion'] = '';
					$doc->column_fields['filestatus'] = '1';
					$doc->column_fields['filedownloadcount'] = '0';
					$doc->mode = '';
					
					// check existence
					$res3 = $adb->pquery("select notesid 
						from {$table_prefix}_notes 
						inner join {$table_prefix}_crmentity on {$table_prefix}_crmentity.crmid = {$table_prefix}_notes.notesid 
						where {$table_prefix}_crmentity.deleted = 0 and title = ? and folderid = ? and filesize = ? and smownerid = ?",
						array($doc->column_fields['notes_title'], $doc->column_fields['folderid'], $doc->column_fields['filesize'], $doc->column_fields['assigned_user_id'])
					);
					if ($res3 && $adb->num_rows($res3) > 0) {
						$existingDocid = $adb->query_result_no_html($res3, 0, 'notesid');
						if ($existingDocid > 0) {
							$doc->mode = 'edit';
							$doc->id = $existingDocid;
						}
					}
					
					try {
						$doc->save('Documents', false, false, false);
						$documentid = $doc->id;
					} catch (Exception $e) {
						echo "Error while saving document<br>\n";
						continue;
					}
				
					// now link the attachment to the document
					$res3 = $adb->pquery("delete from {$table_prefix}_seattachmentsrel where attachmentsid = ? and crmid = ?", array($attid, $documentid));
					$res3 = $adb->pquery("insert into {$table_prefix}_seattachmentsrel (attachmentsid, crmid) values (?,?)", array($attid, $documentid));
					
					if ($res3) {
						// and link the message
						$msg->save_related_module_small($messageid, 'Documents', $documentid);
					} else {
						echo "Error while saving attachment relation<br>\n";
					}
						
					// add it also as an attachment to the email
					$res4 = $adb->pquery("select * from {$table_prefix}_messages_attach where messagesid = ? and contentid = ? and document = ?", array($mesid, $contentid, $documentid));
					if ($res4 && $adb->num_rows($res4) == 0) {
						// insert
						$res4 = $adb->pquery("insert into {$table_prefix}_messages_attach (messagesid, contentid, contentname, contenttype, contentdisposition, contentencoding, document) values (?,?,?,?,?,?,?)", 
							array($mesid, $contentid, $doc->column_fields['notes_title'], $doc->column_fields['filetype'], 'attachment', 'base64', $documentid)
						);
					} else {
						// update
						$res4 = $adb->pquery("update {$table_prefix}_messages_attach set contentname=?, contenttype=?, contentdisposition=?, contentencoding=? where messagesid = ? and contentid = ? and document = ?", 
							array($doc->column_fields['notes_title'], $doc->column_fields['filetype'], 'attachment', 'base64', $mesid, $contentid, $documentid)
						);
					}
					
					$contentid++;
				}
				
			}

			++$saved;
		}
	}
	$t1 = time();
	//echo ($t1-$t0)/$saved;
	// circa 1.3s / email (con ricerca server)

	$t0 = time();
	// email collegate, ma non inviate da crm
	$res = $adb->query(
		"select
		e.*, sq.imap_id,
		c.smownerid, c.smcreatorid, c.createdtime, c.description,
		a.subject, a.date_start, a.time_start
		from {$table_prefix}_emaildetails e
		inner join {$table_prefix}_crmentity c on c.crmid = e.emailid
		inner join {$table_prefix}_activity a on a.activityid = e.emailid
		left join crmv_squirrelmailrel sq on sq.mail_id = e.emailid
		where c.deleted = 0 and c.setype = 'Emails' and e.email_flag not in ('SENT', 'DRAFT') and e.idlists is not null and e.idlists != ''
		-- order by c.createdtime desc
		-- limit 5
		"
	);
	$saved = 0;
	$xuidtimer = time();
	if ($res && $adb->num_rows($res) > 0) {
		while ($row = $adb->FetchByAssoc($res, -1, false)) {

			// impersonate the email owner
			if (!is_readable('user_privileges/user_privileges_'.$row['smownerid'].'.php')) {
				echo "User is missing ({$row['smownerid']})\n";
				continue;
			}
			$current_user->retrieveCurrentUserInfoFromFile($row['smownerid']);


			$msg = CRMEntity::getInstance('Messages');

						// try to get the username of the email
			$res2 = $adb->pquery("select user_name, first_name, last_name from {$table_prefix}_users where id = ? and email1 = ?", array($row['smcreatorid'], $row['from_email']));
			if ($res2 && $adb->num_rows($res2) == 1) {
				$r = $adb->FetchByAssoc($res2, -1, false);
				if (empty($r['first_name']) && empty($r['last_name'])) {
					$from_email = $r['user_name'];
				} else {
					$from_email = $r['first_name']." ".$r['last_name'];
				}
			} else {
				$from_email = '';
			}

		$account = $msg->getMainUserAccount();
		if ($account['id'] > 0) $msg->setAccount($account['id']);

		// no server search!!

		// try to get sent email
		/*if ($row['imap_id'] > 0) {
		$msg->getZendMailStorageImap();
		$msg->selectFolder($specialFolders['Sent']);
		try {
		$message = $msg->getMailResource()->getMessage($row['imap_id']);
		// TODO: non le trova -> dovrei prendere il messageid
		} catch (Exception $e) {
		// go on
		}
		}*/

		//$foundMsg = UpdateSearchEmail($msg, $row);


		$to_email = Zend_Json::decode($row['to_email']);
		if (is_array($to_email)) $to_email = implode(', ', array_map('trim', array_filter($to_email)));

		$cc_email = Zend_Json::decode($row['cc_email']);
		if (is_array($cc_email)) $cc_email = implode(', ', array_map('trim', array_filter($cc_email)));

		$bcc_email = Zend_Json::decode($row['bcc_email']);
		if (is_array($bcc_email)) $bcc_email = implode(', ', array_map('trim', array_filter($bcc_email)));

		if ($foundMsg) {
			$xuid = $foundMsg['X-Uid'];
			$messageid = $foundMsg['Messageid'];
		} else {
			$xuid = ''; //++$xuidtimer;
			$uniq_id = md5(implode('', $row));
			$messageid = sprintf('<%s@%s>', $uniq_id, $mailer->ServerHostname()); // in this way, we won't have duplicate messages
		}


		// save the message
		$msg->column_fields['subject'] = $row['subject'];
		$msg->column_fields['assigned_user_id'] = $row['smownerid'];
		$msg->column_fields['description'] = $row['description'];
		$msg->column_fields['mdate'] = $row['date_start'].' 00:00:00';
		$msg->column_fields['mfrom'] = $row['from_email'];
		$msg->column_fields['mfrom_n'] = $from_email;
		$msg->column_fields['mfrom_f'] = (empty($from_email) ? $row['from_email'] : $from_email." <{$row['from_email']}>");
		$msg->column_fields['mto'] = $to_email;
		$msg->column_fields['mto_n'] = '';
		$msg->column_fields['mto_f'] = $to_email;
		$msg->column_fields['mcc'] = $cc_email;
		$msg->column_fields['mcc_n'] = '';
		$msg->column_fields['mcc_f'] = $cc_email;
		$msg->column_fields['mbcc'] = $bcc_email;
		$msg->column_fields['mbcc_n'] = '';
		$msg->column_fields['mbcc_f'] = $bcc_email;
		$msg->column_fields['mreplyto'] = '';
		$msg->column_fields['mreplyto_n'] = '';
		$msg->column_fields['mreplyto_f'] = '';
		$msg->column_fields['in_reply_to'] = ($foundMsg ? $foundMsg['In-Reply-To'] : '');
		$msg->column_fields['mreferences'] = ($foundMsg ? $foundMsg['References'] : '');
		$msg->column_fields['thread_index'] = ($foundMsg ? $foundMsg['Thread-Index'] : '');
		$msg->column_fields['messageid'] = $messageid;
		//$msg->column_fields['messagehash'] = ''; // calculated
		$msg->column_fields['xmailer'] = 'VTECRM-WEBMAIL';
		$msg->column_fields['xuid'] = $xuid; // so we update existing email
		//$msg->column_fields['folder'] = $specialFolders['INBOX'];
		$msg->column_fields['mtype'] = ($foundMsg ? 'Webmail' : 'Link');
		$msg->column_fields['mvisibility'] = 'Public';
		$msg->column_fields['seen'] = '1';
		$msg->column_fields['answered'] = '0';
		$msg->column_fields['forwarded'] = '0';
		$msg->column_fields['flagged'] = '0';
		$msg->mode = '';

		// check existence
		$res2 = $adb->pquery("select messagesid from {$msg->table_name} inner join {$table_prefix}_crmentity on {$table_prefix}_crmentity.crmid = {$msg->table_name}.{$msg->table_index} where {$table_prefix}_crmentity.deleted = 0 and mtype = ? and messageid = ? and smownerid = ?",
		array($msg->column_fields['mtype'],$messageid,$current_user->id)
		);
		if ($res2 && $adb->num_rows($res2) > 0) {
			$existingCrmid = $adb->query_result_no_html($res2, 0, 'messagesid');
			if ($existingCrmid > 0) {
				$msg->mode = 'edit';
				$msg->id = $existingCrmid;
			}
		}

		// save/update
		try {
			$msg->save('Messages', false, false, false);
			$mesid = $msg->id;
		} catch (Exception $e) {
			echo "Error while saving";
			continue;
		}

		if (!$mesid) {
			echo "Unable to save Message";
			continue;
		}

		// update times
		$adb->pquery("update {$table_prefix}_crmentity set smcreatorid = ?, createdtime = ?, modifiedtime = ? where crmid = ?", array($row['smcreatorid'], $row['createdtime'], $row['modifiedtime'], $mesid));

		// set/update relations
		if (!empty($row['idlists'])) {
			$ids = array_filter(explode('|', $row['idlists']));
			foreach ($ids as $relid) {
				list($elid, $fieldid) = explode('@', $relid, 2);
				$mod = getSalesEntityType($elid);
				if ($mod) {
					$msg->save_related_module_small($messageid, $mod, $elid);
				}
			}
		}

		++$saved;
		}
	}

	$t1 = time();
	//echo ($t1-$t0)/$saved;
	// circa 0.2s / email (no server search)

// rimozione related emails
$adb->pquery("delete from {$table_prefix}_relatedlists where name = ? and related_tabid = ?", array('get_emails', getTabid('Emails')));


echo "<br><b>Done.</b><br>\n";
echo "<b>After checking the migration correctness, to free up space you may now execute the script modules/Update/changes/722_723clean.php</b><br>\n";
flush();
?>