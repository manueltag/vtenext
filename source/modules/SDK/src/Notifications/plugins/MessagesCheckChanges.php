<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@2963m */
global $current_user;
$focus = CRMEntity::getInstance('Messages');

// crmv@64325
$setypeCond = '';
if (PerformancePrefs::getBoolean('CRMENTITY_PARTITIONED')) {
	$setypeCond = "AND {$table_prefix}_crmentity.setype = 'Messages'";
}
$query = "SELECT count(*) AS count FROM {$focus->table_name}
		INNER JOIN {$table_prefix}_crmentity ON {$table_prefix}_crmentity.crmid = {$focus->table_name}.messagesid
		WHERE deleted = 0 $setypeCond AND smownerid = ? AND {$focus->table_name}.seen = ? AND {$focus->table_name}.mtype = ?";
// crmv@64325e
$params = array($current_user->id,0,'Webmail');

$specialFolders = $focus->getAllSpecialFolders('INBOX');
if (empty($specialFolders)) {
	echo 0;
} else {
	$tmp = array();
	foreach($specialFolders as $account => $folders) {
		$tmp[] = "({$table_prefix}_messages.account = '{$account}' AND {$table_prefix}_messages.folder = '{$folders['INBOX']}')";
	}
	$query .= " AND (".implode(' OR ',$tmp).")";
	$result = $adb->pquery($query,$params);
	if ($result && $adb->num_rows($result) > 0) {
		$count = $adb->query_result($result,0,'count');
	}
	if ($count > 0) {
		echo $count;
	} else {
		echo 0;
	}
}
?>