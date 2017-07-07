<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/

global $currentModule;
$record = vtlib_purify($_REQUEST['record']);
$contentid = vtlib_purify($_REQUEST['contentid']);
$mode = vtlib_purify($_REQUEST['mode']);	//crmv@80250

$focus = CRMEntity::getInstance($currentModule);

//crmv@46760	crmv@91321
global $adb, $table_prefix;
$sql = "select s.attachmentsid, n.notesid, a.contentname
from {$table_prefix}_messages_attach a 
inner join {$table_prefix}_seattachmentsrel s on s.crmid = a.document
inner join {$table_prefix}_notes n on n.notesid = a.document
inner join {$table_prefix}_crmentity e on e.crmid = n.notesid
where deleted = 0 and messagesid = ? and contentid = ? and coalesce(a.document,'') <> ''";
$params = Array($record,$contentid);
$res = $adb->pquery($sql,$params);
if ($res && $adb->num_rows($res)>0){
	$attachmentsid = $adb->query_result_no_html($res,0,'attachmentsid');
	$name = $adb->query_result_no_html($res,0,'contentname');
	if ($mode == 'inline' && $focus->isConvertableFormat($name) && extension_loaded('imagick')) {
		$dbQuery = "SELECT * FROM ".$table_prefix."_attachments WHERE attachmentsid = ?" ;
		$result = $adb->pquery($dbQuery, array($attachmentsid));
		$saved_filename = $adb->query_result_no_html($result, 0, "path").$attachmentsid."_".$adb->query_result_no_html($result, 0, "name");

		$image = new Imagick($saved_filename);
		$image->setImageFormat('png');
		$str = $image;
		$pathinfo = pathinfo($name);
		$name = $pathinfo['filename'].'.png';
		
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: X-Requested-With");
		header('Content-Type: image/png');
		header("Content-Disposition: attachment; filename=\"{$name}\"");
		echo $image;
		exit;
	}
	$_REQUEST['fileid'] = $attachmentsid;
	$_REQUEST['entityid'] = $adb->query_result_no_html($res,0,'notesid');
	include('modules/uploads/downloadfile.php');
	exit;
}
//crmv@46760e	crmv@91321e

$focus->retrieve_entity_info($record,$currentModule);
$uid = $focus->column_fields['xuid'];
$accountid = $focus->column_fields['account'];

$result = $adb->pquery("select userid from {$table_prefix}_messages_account where id = ?", array($accountid));
if ($result && $adb->num_rows($result) > 0) {
	$userid = $adb->query_result($result,0,'userid');

	$focus->setAccount($accountid);
	$focus->getZendMailStorageImap($userid);
	$focus->selectFolder($focus->column_fields['folder']);
	
	$messageId = $focus->getMailResource()->getNumberByUniqueId($uid);
	$message = $focus->getMailResource()->getMessage($messageId);
	$parts = $focus->getMessageContentParts($message,$id,true);	//crmv@59492
	if (!empty($parts['other'][$contentid])) {
		$content = $parts['other'][$contentid];
		$str = $content['content'];
		$str = $focus->decodeAttachment($str,$content['parameters']['encoding'],$content['parameters']['charset']);
		
		$parameters = $content['parameters'];
		$name = $content['name'];
		//crmv@53651
		if (in_array($name,array('','Unknown'))) {
			$r = $adb->pquery("select contentname from {$table_prefix}_messages_attach where messagesid = ? and contentid = ?", array($record,$contentid));
			if ($r && $adb->num_rows($r) > 0) {
				$tmp = $adb->query_result($r,0,'contentname');
				if (in_array($name,array('','Unknown'))) $name = $tmp;
			}
		}
		//crmv@53651e
		//crmv@80250
		if ($mode == 'inline' && !$focus->isSupportedInlineFormat($name)) {
			exit;
		}
		//crmv@80250e
		//crmv@91321
		elseif ($mode == 'inline' && $focus->isSupportedInlineFormat($name) && $focus->isConvertableFormat($name)) {
			if (extension_loaded('imagick')) {
				$image = new Imagick();
				$image->readimageblob($str);
				$image->setImageFormat('png');
				$parameters['contenttype'] = 'image/png';
				$str = $image;
			} else {
				exit;
			}
		}
		//crmv@91321e
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: X-Requested-With");
		header('Content-Type: '.$parameters['contenttype']);
		header("Content-Disposition: {$parameters['contentdisposition']}; filename=\"{$name}\"");
		echo $str;
	}
}
?>