<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/

/* crmv@55961 */

require_once('../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
require_once('Smarty_setup.php');
global $adb, $default_language, $default_charset, $HELPDESK_SUPPORT_EMAIL_ID;
$current_language = $default_language;

header('Content-Type: text/html; charset='. $default_charset);
$smarty = new vtigerCRM_Smarty;
$smarty->assign('PATH','../../');
$smarty->assign('CURRENT_LANGUAGE',$current_language);

$id = vtlib_purify($_REQUEST['id']);
$unsub = vtlib_purify($_REQUEST['unsub']);

if (!empty($unsub) && in_array($unsub,array('all','campaign'))) {
	
	$track = base64_decode($id);
	@list($msgtype,$linkid,$newsletterid,$crmid) = explode('|',$track);
	// sanitization
	$msgtype = substr($msgtype, 0, 1);
	$linkid = intval($linkid);
	$newsletterid = intval($newsletterid);
	$crmid = intval($crmid);
	
	$result = $adb->pquery('select * from tbl_s_newsletter_links where linkid = ? and newsletterid = ?',array($linkid,$newsletterid));
	$linkdata = $adb->FetchByAssoc($result, -1, false);
	$linkurlid = $linkdata['linkid'];
	
	$focus = CRMEntity::getInstance('Newsletter');
	$focus->id = $newsletterid;
	$focus->retrieve_entity_info($newsletterid,'Newsletter');
	
	$focus->trackLink($linkid,$newsletterid,$crmid,$linkurlid,$msgtype);
	
	$result = $focus->unsubscribe($crmid,$unsub);
	
	if ($unsub == 'campaign') {
		if ($result == 1) {
			$description = getTranslatedString('LBL_SUCCESS_UNSUBSCRIPTION','Newsletter');
		} elseif ($result == 2) {
			$description = getTranslatedString('LBL_ALREADY_UNSUBSCRIPTION','Newsletter');
		} elseif ($result == 3) {
			$description = sprintf(getTranslatedString('LBL_UNSUCCESS_UNSUBSCRIPTION','Newsletter'),$HELPDESK_SUPPORT_EMAIL_ID);
		}
	} elseif ($unsub == 'all') {
		if ($result == 1) {
			$description = getTranslatedString('LBL_SUCCESS_GENERAL_UNSUBSCRIPTION','Newsletter');
		} elseif ($result == 2) {
			$description = getTranslatedString('LBL_ALREADY_GENERAL_UNSUBSCRIPTION','Newsletter');
		} elseif ($result == 3) {
			$description = sprintf(getTranslatedString('LBL_UNSUCCESS_UNSUBSCRIPTION','Newsletter'),$HELPDESK_SUPPORT_EMAIL_ID);
		}
	}
	
	$description = '
	<table border="0" cellpadding="20" cellspacing="0" width="100%" align="center" class="small">
	<tr><td colspan="2">'.$description.'</td></tr>
	</table>
	';
} else {
	$description = '
	<table border="0" cellpadding="10" cellspacing="0" width="100%" align="center" class="small">
	<tr><td></td></tr>
	<tr><td>'.getTranslatedString('LBL_NEWSLETTER_UNSUBSCRIPTION','Newsletter').'</td></tr>
	<tr><td align="center"><input class="crmbutton small edit" onclick="location.href=\'Unsubscription.php?unsub=campaign&id='.$id.'\'" type="button" value="'.getTranslatedString('LBL_NEWSLETTER_UNSUBSCRIPTION_BUTTON','Newsletter').'"></td></tr>
	<tr><td></td></tr>
	<tr><td>'.getTranslatedString('LBL_GENERAL_UNSUBSCRIPTION','Newsletter').'</td></tr>
	<tr><td align="center"><input class="crmbutton small edit" onclick="location.href=\'Unsubscription.php?unsub=all&id='.$id.'\'" type="button" value="'.getTranslatedString('LBL_GENERAL_UNSUBSCRIPTION_BUTTON','Newsletter').'"></td></tr>
	<tr><td></td></tr>
	</table>';
}

$smarty->assign('BODY',$description);
$smarty->display('NoLoginMsg.tpl');
?>