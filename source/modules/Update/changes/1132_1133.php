<?php
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter' => 'Targets'));

global $adb, $table_prefix;
$focus = CRMEntity::getInstance('Newsletter');

$mail_status_id = 6;
$adb->query("insert into tbl_s_newsletter_status values($mail_status_id,'LBL_ATTEMPTS_EXHAUSTED')");
SDK::setLanguageEntries('Newsletter', 'LBL_ATTEMPTS_EXHAUSTED', array(
	'it_it'=>'Numero di tentativi di invio esaurito',
	'en_us'=>'Attempts exhausted',
	'de_de'=>'Versuche erschöpft',
	'nl_nl'=>'pogingen uitgeput',
	'pt_br'=>'tentativas esgotado',
));

// move messages in status failed
$result = $adb->pquery("SELECT * FROM tbl_s_newsletter_queue WHERE attempts >= ? AND status = ?", array($focus->max_attempts_permitted,'Scheduled'));
if ($result && $adb->num_rows($result) > 0) {
	while($row=$adb->fetchByAssoc($result)) {
		$adb->pquery("update tbl_s_newsletter_queue set status = ? where newsletterid = ? and crmid = ?",array('Failed',$row['newsletterid'],$row['crmid']));
		$adb->pquery('insert into tbl_s_newsletter_failed (newsletterid,crmid,statusid) values (?,?,?)',array($row['newsletterid'],$row['crmid'],$mail_status_id));
	}
}

// set readonly 99 the flag scheduled
$moduleInstance = Vtiger_Module::getInstance('Newsletter');
$adb->pquery("update {$table_prefix}_field set readonly = ? where tabid = ? and fieldname = ?", array(99,$moduleInstance->id,'scheduled'));
?>