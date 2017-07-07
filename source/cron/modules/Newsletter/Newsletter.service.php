<?php
/* crmv@47611 */

require('config.inc.php');
require_once('include/utils/utils.php');
require_once('include/logging.php');

global $adb, $log, $current_user, $table_prefix;

$log =& LoggerManager::getLogger('Newsletter');
$log->debug("invoked Newsletter");

if (!$current_user) {
	$current_user = CRMEntity::getInstance('Users');
	$current_user->id = 1;
}

$focus = CRMEntity::getInstance('Newsletter');
//crmv@24947
$query = "SELECT tbl_s_newsletter_queue.* FROM tbl_s_newsletter_queue
		INNER JOIN ".$table_prefix."_crmentity ON ".$table_prefix."_crmentity.crmid = tbl_s_newsletter_queue.newsletterid
		WHERE ".$table_prefix."_crmentity.deleted = 0 AND tbl_s_newsletter_queue.status = 'Scheduled' AND tbl_s_newsletter_queue.attempts < $focus->max_attempts_permitted AND tbl_s_newsletter_queue.date_scheduled <= '".$adb->formatDate(date('Y-m-d H:i:s'), true)."'";
//crmv@24947e
$result = $adb->limitQuery($query,0,$focus->getNoEmailProcessedBySchedule());
if ($result && $adb->num_rows($result)>0) {
	while($row=$adb->fetchByAssoc($result)) {
		$focus = CRMEntity::getInstance('Newsletter');
		$focus->id = $row['newsletterid'];
		$focus->retrieve_entity_info($row['newsletterid'],'Newsletter');
		$mail_status = $focus->sendNewsletter($row['crmid']);
		if ($mail_status == 1) {
			$adb->pquery("update tbl_s_newsletter_queue set status = ? where newsletterid = ? and crmid = ?",array('Sent',$row['newsletterid'],$row['crmid']));
			$adb->pquery("update tbl_s_newsletter_queue set date_sent = ? where newsletterid = ? and crmid = ?",array($adb->formatDate(date('Y-m-d H:i:s'), true),$row['newsletterid'],$row['crmid']));
		//crmv@25872	crmv@34219	crmv@55961
		} elseif (in_array($mail_status,array('LBL_RECORD_DELETE','LBL_RECORD_NOT_FOUND','LBL_OWNER_MISSING','LBL_ERROR_MAIL_UNSUBSCRIBED'))) {
			$adb->pquery("update tbl_s_newsletter_queue set status = ? where newsletterid = ? and crmid = ?",array('Failed',$row['newsletterid'],$row['crmid']));
			// crmv@38592
			$mail_status_id = intval(array_search($mail_status, $focus->status_list));
			$adb->pquery('insert into tbl_s_newsletter_failed (newsletterid,crmid,statusid) values (?,?,?)',array($row['newsletterid'],$row['crmid'],$mail_status_id));
			// crmv@38592e
		//crmv@25872e	crmv@34219e	crmv@55961e
		}
		//crmv@83542
		$attempts = $row['attempts']+1;
		$adb->pquery("update tbl_s_newsletter_queue set attempts = ? where newsletterid = ? and crmid = ?",array($attempts,$row['newsletterid'],$row['crmid']));
		$adb->pquery("update tbl_s_newsletter_queue set last_attempt = ? where newsletterid = ? and crmid = ?",array($adb->formatDate(date('Y-m-d H:i:s'), true),$row['newsletterid'],$row['crmid']));
		if ($attempts >= $focus->max_attempts_permitted) {
			$adb->pquery("update tbl_s_newsletter_queue set status = ? where newsletterid = ? and crmid = ?",array('Failed',$row['newsletterid'],$row['crmid']));
			$mail_status_id = intval(array_search('LBL_ATTEMPTS_EXHAUSTED', $focus->status_list));
			$adb->pquery('insert into tbl_s_newsletter_failed (newsletterid,crmid,statusid) values (?,?,?)',array($row['newsletterid'],$row['crmid'],$mail_status_id));
		}
		//crmv@83542e
		sleep($focus->getIntervalBetweenEmailDelivery());
	}
	// crmv@47611 - removed sleep
}

$log->debug("end Newsletter procedure");
?>