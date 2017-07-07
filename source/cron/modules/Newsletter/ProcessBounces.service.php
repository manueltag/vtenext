<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@64516 */

include('modules/Campaigns/ProcessBounces.config.php');

if (!(date('D') == 'Sun' && date('G') >= 21)) return;	// run only on Sunday at 21

if (!function_exists('imap_open')) {
	echo "IMAP is not included in your PHP installation, cannot continue\nCheck out http://www.php.net/manual/en/ref.imap.php\n";
	return;
}
if (!$bounce_mailbox && (!$bounce_mailbox_host || !$bounce_mailbox_user || !$bounce_mailbox_password)) {
	echo "Bounce mechanism not properly configured\n";
	return;
}

switch ($bounce_protocol) {
	case "pop":
		$download_report = processPop($bounce_mailbox_host,$bounce_mailbox_port,$bounce_mailbox_folder,$bounce_mailbox_user,$bounce_mailbox_password);
		break;
	case "mbox":
		$download_report = processMbox($bounce_mailbox);
		break;
	default:
		echo "bounce_protocol not supported\n";
		return;
}

$result = $adb->query('SELECT tbl_s_newsletter_bounce_rel.crmid,COUNT(*) AS bounce_count FROM tbl_s_newsletter_bounce
						INNER JOIN tbl_s_newsletter_bounce_rel ON tbl_s_newsletter_bounce.id = tbl_s_newsletter_bounce_rel.bounce
						GROUP BY tbl_s_newsletter_bounce_rel.crmid');
if ($result && $adb->num_rows($result)>0) {
	$usercnt = 0;
	$unsubscribed = "";
	while ($row=$adb->fetchByAssoc($result)) {
		$crmid = $row['crmid'];
		$cnt = $row['bounce_count'];
		if (empty($crmid)) continue;
		if ($cnt >= $bounce_unsubscribe_threshold) {
			
			//seleziono la mail relativa al crmid
			$focus_newsletter = CRMEntity::getInstance('Newsletter');
			$module = getSalesEntityType($crmid);
			if (empty($module)) continue;
			$focus = CRMEntity::getInstance($module);
			$error = $focus->retrieve_entity_info($crmid,$module,false);
			$email = $focus->column_fields[$focus_newsletter->email_fields[$module]['fieldname']];
			if (!empty($error) || empty($email)) continue;
			
			//aggiungo la mail nella tabella dei disiscritti
			//mi basta inserirla per una sola newsletter per toglierla da tutte le prossime email spedite dalla stessa campagna
			//in futuro si può creare un livello più alto di disiscrizione da tutte le campagne (es. un campo nel contatto/lead/azienda) e a quel punto basterebbe fare l'update lì
			$result1 = $adb->query('SELECT newsletterid FROM tbl_s_newsletter_bounce_rel WHERE crmid = '.$crmid);
			$newsletterid = $adb->query_result($result1,0,'newsletterid');
			$result2 = $adb->pquery('select * from tbl_s_newsletter_unsub where newsletterid = ? and email = ?',array($newsletterid,$email));
			if ($result2 && $adb->num_rows($result2)>0) {
				//do nothing
			} else {
				$adb->pquery('insert into tbl_s_newsletter_unsub (newsletterid,email,statusid) values (?,?,?)',array($newsletterid,$email,1));
			}
	
			$unsubscribed .= "$email [$crmid] ($cnt)\n";
		}
		$usercnt++;
		flush();
	}
} else {
	echo "Nothing to do\n";
}

$report = '';

if ($download_report) {
	$report .= "Report:\n$download_report";
}
if ($unsubscribed) {
	$report .= "\nBelow are the $usercnt users who have been marked unconfirmed. The number in [] is their crmid, the number in () is the number of consecutive bounces\n";
	$report .= $unsubscribed;
}
if (VERBOSE) echo $report;

function processPop($server,$port='',$folder='',$user,$password) {
	if (!$port) {
		$port = '110/pop3/notls';
	}
	if (!$folder) {
		$folder = 'INBOX';
	}
	set_time_limit(6000);

	if (!TEST) {
		$link=imap_open("{".$server.":".$port."}$folder",$user,$password,CL_EXPUNGE);
	} else {
		$link=imap_open("{".$server.":".$port."}$folder",$user,$password);
	}
	if (!$link) {
		echo "Cannot create POP3 connection to $server: ".imap_last_error()."\n";
		return;
	}
	return processMessages($link,100000);
}
function processMbox($file) {
	set_time_limit(6000);

	if (!TEST) {
		$link=imap_open($file,"","",CL_EXPUNGE);
	} else {
		$link=imap_open($file,"","");
	}
	if (!$link) {
		echo "Cannot open mailbox file ".imap_last_error()."\n";
		return;
	}
	return processMessages($link,100000);
}
function processMessages($link,$max = 3000) {
	#error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	global $bounce_mailbox_purge_unprocessed,$bounce_mailbox_purge;
	$num = imap_num_msg($link);
	if (VERBOSE) echo "Bounces to fetch from the mailbox\nPlease do not interrupt this process\n";
	$report = $num . " "."bounces to process\n";
	if ($num > $max) {
		if (VERBOSE) echo "Processing first $max bounces\n";
		$report .= $num . " "."processing first"." $max "."bounces\n";
		$num = $max;
	}
	if (TEST) {
		echo "Running in test mode, not deleting messages from mailbox\n";
	} else {
		if (VERBOSE) echo "Processed messages:\n";
	}
	$nberror = 0;
	#  for ($x=1;$x<150;$x++) {
	for($x=1; $x <= $num; $x++) {
		set_time_limit(60);
		$header = imap_fetchheader($link,$x);
		if ($x % 25 == 0)
			#    output( $x . " ". nl2br($header));
			if (VERBOSE) echo "$x done\n";
		flush();
		$processed = processBounce($link,$x,$header);
		if ($processed) {
			if (!TEST && $bounce_mailbox_purge) {
				if (VERBOSE)
					echo "Deleting message $x\n";
				imap_delete($link,$x);
			}
		} else {
			if (!TEST && $bounce_mailbox_purge_unprocessed) {
				if (VERBOSE)
					echo "Deleting message $x\n";
				imap_delete($link,$x);
			}
		}
		flush();
	}
	flush();
	if (VERBOSE) echo "Closing mailbox, and purging messages\n\n";
	set_time_limit(60 * $num);
	imap_close($link);
	#  print '<script language="Javascript" type="text/javascript"> finish(); </script>';
	if ($num)
		return $report;
}
function processBounce($link,$num,$header) {
	global $adb,$tables;

	$headerinfo = imap_headerinfo($link,$num);
	$body= imap_body($link,$num);

	$newsletterid = 0;$crmid_tmp = 0;
	preg_match ("/X-MessageId: (.*)/i",$body,$match);
	if (is_array($match) && isset($match[1]))
		$newsletterid= trim($match[1]);
	if (!$newsletterid) {
		# older versions use X-Message
		preg_match ("/X-Message: (.*)/i",$body,$match);
		if (is_array($match) && isset($match[1]))
			$newsletterid= trim($match[1]);
	}

	preg_match ("/X-ListMember: (.*)/i",$body,$match);
	if (is_array($match) && isset($match[1]))
		$crmid_tmp = trim($match[1]);
	if (!$crmid_tmp) {
		# older version use X-User
		preg_match ("/X-User: (.*)/i",$body,$match);
		if (is_array($match) && isset($match[1]))
			$crmid_tmp = trim($match[1]);
	}
	
	//leggo sempre e solo il parametro X-ListMember
	if (VERBOSE)
		echo "UID $crmid_tmp MSGID $newsletterid\n";
	$crmid = $crmid_tmp;
	
	$bounceid = $adb->getUniqueID('tbl_s_newsletter_bounce');
	$adb->pquery('insert into tbl_s_newsletter_bounce (id,date,header,data) values(?,?,?,?)',array($bounceid,$adb->formatDate(date("Y-m-d H:i:s",@strtotime($headerinfo->date)),true),addslashes($header),addslashes($body)));
	if ($newsletterid == "systemmessage" && $crmid) {
		$adb->query('update tbl_s_newsletter_bounce set status = "bounced system message", comment = "'.$crmid.' marked unconfirmed" where id = '.$bounceid);
	} elseif ($newsletterid && $crmid) {
		$adb->query('update tbl_s_newsletter_bounce set status = "bounced list message '.$newsletterid.'", comment = "'.$crmid.' bouncecount increased" where id = '.$bounceid);
		$rel_id = $adb->getUniqueID('tbl_s_newsletter_bounce_rel');
		$adb->pquery('insert into tbl_s_newsletter_bounce_rel (id,crmid,newsletterid,bounce,time) values(?,?,?,?,?)',array($rel_id,$crmid,$newsletterid,$bounceid,$adb->formatDate(date("Y-m-d H:i:s",@strtotime($headerinfo->date)),true)));
	} elseif ($crmid) {
		$adb->query('update tbl_s_newsletter_bounce set status = "bounced unidentified message", comment = "'.$crmid.' bouncecount increased" where id = '.$bounceid);
	} elseif ($newsletterid === 'systemmessage') {
		$adb->query('update tbl_s_newsletter_bounce set status = "bounced system message", comment = "unknown user" where id = '.$bounceid);
	} elseif ($newsletterid) {
		$adb->query('update tbl_s_newsletter_bounce set status = "bounced list message '.$newsletterid.'", comment = "unknown user" where id = '.$bounceid);
	} else {
		$adb->query('update tbl_s_newsletter_bounce set status = "unidentified bounce", comment = "not processed" where id = '.$bounceid);
		return false;
	}
	return true;
}
?>