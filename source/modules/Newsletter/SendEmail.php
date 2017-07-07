<?php
require_once('modules/Emails/mail.php');
global $adb,$current_user,$currentModule,$mod_strings;
$record = $_REQUEST['record'];

$focus = CRMEntity::getInstance($currentModule);
$focus->id = $record;
$focus->retrieve_entity_info($record,$currentModule);

if (in_array($focus->column_fields['templateemailid'],array('',0))) {
	die($mod_strings['LBL_TEMPLATE_EMPTY']);
}
if ($_REQUEST['mode'] == 'test') {	//send test email
	$to_address = getUserEmailId('id',$current_user->id);
	$mail_status = $focus->sendNewsletter('','test',$to_address);
	if ($mail_status == 1) {
		die($mod_strings['LBL_TEST_MAIL_SENT']);
	} else {
		die(getTranslatedString('LBL_NOTIFICATION_ERROR','Calendar'));
	}
} else {	//schedule email
	
	try { set_time_limit(600); } catch(Exception $e) { }	// 10 minutes
	
	// populate queue
	$target_list = $focus->getTargetList();
	if (empty($target_list)) {
		die($mod_strings['LBL_TARGET_LIST_EMPTY']);
	}
	$date_scheduled = $focus->column_fields['date_scheduled'].' '.$focus->column_fields['time_scheduled'];
	// crmv@38592
	$newQueue = false;
	foreach($target_list as $crmid) {
		$result = $adb->pquery('select * from tbl_s_newsletter_queue where newsletterid = ? and crmid = ?',array($focus->id,$crmid));
		if ($result && $adb->num_rows($result) > 0) {
			//do nothing
		} else {
			$res = $adb->pquery('insert into tbl_s_newsletter_queue (newsletterid,crmid,status,attempts,date_scheduled,num_views) values (?,?,?,?,?,?)',array($focus->id,$crmid,'Scheduled',0,$adb->formatDate($date_scheduled,true),0));
			$newQueue = true;
		}
	}

	// save template used
	if ($newQueue) $focus->saveTemplateEmail();
	// crmv@38592e

	$focus->mode = 'edit';
	$focus->column_fields['scheduled'] = 1;
	$focus->save($currentModule);

	die($mod_strings['LBL_MAIL_SCHEDULED']);
}
?>