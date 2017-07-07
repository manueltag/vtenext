<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/

/* crmv@38592 crmv@55961 */

require_once('../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
global $adb;

$focus = CRMEntity::getInstance('Newsletter');

$id = $_REQUEST['id'];
$track = base64_decode($id);
@list($msgtype,$linkid,$newsletterid,$crmid) = explode('|',$track);
// sanitization
$msgtype = substr($msgtype, 0, 1);
$linkid = intval($linkid);
$newsletterid = intval($newsletterid);
$crmid = intval($crmid);

// early check
if (!in_array($msgtype, array('T', 'H')) || $newsletterid <= 0) {
	FileNotFound();
	exit;
}

// get links
$result = $adb->pquery('select * from tbl_s_newsletter_links where linkid = ? and newsletterid = ?',array($linkid,$newsletterid));
$linkdata = $adb->FetchByAssoc($result, -1, false);
$linkurlid = $linkdata['linkid'];

// no link found
if (empty($linkurlid)) {
	$focus->id = $newsletterid;
	$focus->retrieve_entity_info($newsletterid,'Newsletter', false);
	if ($focus->column_fields["record_id"] != $newsletterid) {
		FileNotFound();
	} else {
		$_REQUEST['record'] = $newsletterid;
		$_REQUEST['crmid'] = $crmid;
		$_REQUEST['appkey'] = $application_unique_key;
		include('modules/Newsletter/ShowPreview.php');
	}
	exit;
}

if ($linkdata['forward'] == $focus->url_unsubscription_file) {
	header("Location: ".$linkdata['forward']."?id=$id");
} else {
	$focus->trackLink($linkid,$newsletterid,$crmid,$linkurlid,$msgtype);
	header("Location: ".$linkdata['forward']);
}
exit;

function FileNotFound() {
	header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
	echo '<html><head><title>404 Not Found</title></head><body><h1>Not Found</h1>The requested document was not found on this server</body></html>';
	exit;
}
?>