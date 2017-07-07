<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@48159 */

global $current_user, $currentModule, $adb, $table_prefix;
$account = vtlib_purify($_REQUEST['account']);
$folder = $_REQUEST['folder'];

$focus = CRMEntity::getInstance($currentModule);
$focus->addToPropagationCron('empty', array('userid'=>$current_user->id,'account'=>$account,'folder'=>$folder), 5);

$adb->pquery("UPDATE {$table_prefix}_crmentity
			INNER JOIN {$table_prefix}_messages ON {$table_prefix}_crmentity.crmid = {$table_prefix}_messages.messagesid
			SET deleted = ?
			WHERE deleted = ? AND smownerid = ? AND account = ? AND folder = ?",
array(1,0,$current_user->id,$account,$folder));
$focus->reloadCacheFolderCount($current_user->id,$account,$folder);

exit;
?>