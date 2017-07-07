<?php
require_once('../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
global $adb;
global $table_prefix;
$crmid = $_REQUEST["c"];
$newsletterid = $_REQUEST["n"];
if ($crmid && $newsletterid) {
	$result = $adb->pquery('select first_view,num_views from tbl_s_newsletter_queue where newsletterid = ? and crmid = ?',array($newsletterid,$crmid));
	if ($result && $adb->num_rows($result)>0) {
		$first_view = $adb->query_result($result,0,'first_view');
		$num_views = $adb->query_result($result,0,'num_views');
		if (strtotime($first_view) == '' || $first_view == '0000-00-00 00:00:00') { //crmv@59488 - refer to http://stackoverflow.com/questions/17805751/strtotime0000-00-00-0000-return-negative-value
			$adb->pquery('update tbl_s_newsletter_queue set first_view = ? where newsletterid = ? and crmid = ?',array($adb->formatDate(date('Y-m-d H:i:s'),true),$newsletterid,$crmid));
		}
		$adb->pquery('update tbl_s_newsletter_queue set last_view = ? where newsletterid = ? and crmid = ?',array($adb->formatDate(date('Y-m-d H:i:s'),true),$newsletterid,$crmid));
		$adb->pquery('update tbl_s_newsletter_queue set num_views = ? where newsletterid = ? and crmid = ?',array(($num_views+1),$newsletterid,$crmid));
	}
	//aggiorno anche il conteggio delle aperture che si vede nelle related Emails dei moduli Account,Contacts e Leads
	$result = $adb->pquery('SELECT sact1.activityid
							FROM '.$table_prefix.'_seactivityrel sact1
							INNER JOIN '.$table_prefix.'_seactivityrel sact2 ON sact1.activityid = sact2.activityid
							WHERE sact1.crmid = ? AND sact2.crmid = ?',array($crmid,$newsletterid));
	if ($result && $adb->num_rows($result)>0) {
		$activityid = $adb->query_result($result,0,'activityid');
		header("Location: $site_URL/modules/Emails/TrackAccess.php?record=$crmid&mailid=$activityid&app_key=$application_unique_key");
	}
}
header("Content-Type: image/png");
print base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAABGdBTUEAALGPC/xhBQAAAAZQTFRF////AAAAVcLTfgAAAAF0Uk5TAEDm2GYAAAABYktHRACIBR1IAAAACXBIWXMAAAsSAAALEgHS3X78AAAAB3RJTUUH0gQCEx05cqKA8gAAAApJREFUeJxjYAAAAAIAAUivpHEAAAAASUVORK5CYII=');
?>