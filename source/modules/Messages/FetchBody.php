<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/

/* crmv@59094 */

global $currentModule;

$focus = CRMEntity::getInstance('Messages');
$focus->id = vtlib_purify($_REQUEST['record']);
$focus->retrieve_entity_info(vtlib_purify($_REQUEST['record']), $currentModule);

$focus->setAccount($focus->column_fields['account']);
$focus->getZendMailStorageImap($focus->column_fields['assigned_user_id']);
$focus->selectFolder($focus->column_fields['folder']);

$messageId = $focus->getMailResource()->getNumberByUniqueId($focus->column_fields['xuid']);
$message = $focus->getMailResource()->getMessage($messageId);

$data = $focus->getMessageContentParts($message,$messageId,true);
if (!empty($data['text/plain'])) $data['text/plain'] = implode("\n\n",$data['text/plain']);
if (!empty($data['text/html'])) $data['text/html'] = implode('<br><br>',$data['text/html']);
$body = '';
if (isset($data['text/html'])) {
	$body = $data['text/html'];
	$body = str_replace('&lt;','&amp;lt;',$body);
	$body = str_replace('&gt;','&amp;gt;',$body);
} elseif (isset($data['text/plain'])) {
	$body = nl2br(htmlentities($data['text/plain'], ENT_COMPAT, $default_charset));
}
$focus->column_fields['cleaned_body'] = '';	// in order to recalculate it
$focus->column_fields['description'] = $body;
$focus->column_fields['other'] = $data['other'];
$focus->mode = 'edit';
$focus->save('Messages');

echo 'SUCCESS::';
if (!empty($data['other'])) {
	echo 'ATTACHMENTS';
}
exit();
?>