<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/

function vtws_getRelationsExtra($module, $recordid){
	// getting relations for module or single id
	return getRelationsExtra($module, $recordid);
}

function getRelationsExtra($module, $recordid) {
	global $adb, $table_prefix,$enterprise_current_version;
	$relations = array();	// result array
	$records = array();	// result array
	
	$qparams = array();
	$q = "	SELECT crmid as crmid, setype as module
			FROM {$table_prefix}_crmentity 
			WHERE deleted = 0 AND setype = ?
			";
	$qparams[] = $module; 
	if($recordid != null && trim($recordid) != '' && $recordid > 0)	{
		$q .= " AND crmid = ?";
		$qparams[] = $recordid;
	}	
	// query setted: running query
	$res = $adb->pquery($q, $qparams);
	$cnt = 0;
	if ($res) {
		if(substr(trim($enterprise_current_version), 0, 3) >= 4.5) { // versions 4.5/5+: using getRelatedIdsExtra (RelationManager)
			while ($row = $adb->FetchByAssoc($res, -1, false)) {
				PopulateRelations_5Extra($relations, $row);
			}
		}
		else {	// other non-specified versions
			$relations = Array();
		}
		
	}
	return $relations;
}

function PopulateRelations_5Extra(&$relations, $row) {
	require_once('include/utils/RelationManager.php');
	// searching relations for each crmid in recordset (bulk)
	$rm = RelationManager::getInstance();
	$excludedMods = array(); //array('ModComments');
	$relIds = $rm->getRelatedIdsExtra($row['module'], $row['crmid'], array(), $excludedMods, false, true);
	foreach ($relIds as $mod=>$ids) {
		foreach ($ids as $rid) {
				$relations[] = array(	'crmid' => $row['crmid'], 
										'module' => $row['module'], 
										'relcrmid' => $rid, 
										'relmodule' => $mod,
			);
		}
	}
}
?>