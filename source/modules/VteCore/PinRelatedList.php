<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/

$mode = vtlib_purify($_REQUEST['mode']);
$module = vtlib_purify($_REQUEST['module']);
$relmodule = vtlib_purify($_REQUEST['relmodule']);
$related = vtlib_purify($_REQUEST['related']);
$related = explode('_',$related);
$label = $related[1];

global $adb, $table_prefix, $current_user;
$result = $adb->pquery("select relation_id from {$table_prefix}_relatedlists where tabid = ? and related_tabid = ?",array(getTabid($module),getTabid($relmodule)));
if ($result) {
	$relation_id = $adb->query_result($result,0,'relation_id');
	if ($mode == 'pin') {
		$adb->pquery("insert into {$table_prefix}_relatedlists_pin (userid,relation_id) values (?,?)",array($current_user->id,$relation_id));
	} elseif ($mode == 'unPin') {
		$adb->pquery("delete from {$table_prefix}_relatedlists_pin where userid = ? and relation_id = ?",array($current_user->id,$relation_id));
	}
}
exit;
?>