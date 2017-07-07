<?php


// crmv@113953
// fix customviews
$sql="SELECT modhomeid, tabid FROM {$table_prefix}_modulehome WHERE cvid = 0";
$res=$adb->query($sql);
if($res && $adb->num_rows($res)>0){
	while($row = $adb->fetchByAssoc($res, -1, false)){
		$tabname = getTabName($row['tabid']);
		if ($tabname) {
			$cvres = $adb->pquery("SELECT cvid FROM {$table_prefix}_customview WHERE entitytype = ? AND viewname = ?",array($tabname,'All'));
			$all_cvid = $adb->query_result_no_html($cvres,0,'cvid');
			$adb->pquery("UPDATE {$table_prefix}_modulehome SET cvid = ? WHERE modhomeid = ?",array($all_cvid,$row['modhomeid']));
		}
	}
}


/* crmv@102955 */

// remove all the multiple notifications

$notUsers = array();
$res = $adb->query("SELECT DISTINCT smownerid FROM {$table_prefix}_modnotifications INNER JOIN {$table_prefix}_crmentity ON crmid = modnotificationsid WHERE deleted = 0 AND setype = 'ModNotifications' AND related_to IS NOT NULL and related_to > 0");
if ($res && $adb->num_rows($res) > 0) {
	while ($row = $adb->FetchByAssoc($res, -1, false)) {
		$notUsers[] = intval($row['smownerid']);
	}
}

$setypeCond = '';
if (PerformancePrefs::getBoolean('CRMENTITY_PARTITIONED')) {
	$setypeCond = "AND {$table_prefix}_crmentity.setype = 'ModNotifications'";
}
$query = 
	"SELECT related_to, MAX(modnotificationsid) AS modnotificationsid
	FROM {$table_prefix}_modnotifications
	INNER JOIN ".$table_prefix."_crmentity ON ".$table_prefix."_crmentity.crmid = ".$table_prefix."_modnotifications.modnotificationsid
	WHERE ".$table_prefix."_crmentity.deleted = 0 $setypeCond AND ".$table_prefix."_crmentity.smownerid = ?
	GROUP BY related_to";
		
foreach ($notUsers as $userid) {
	// for each user, remove all but the last notification per record
	
	$res = $adb->pquery($query, array($userid));
	if ($res && $adb->num_rows($res) > 0) {
		$now = date('Y-m-d H:i:s');
		while ($row = $adb->FetchByAssoc($res, -1, false)) {
			$relid = intval($row['related_to']);
			$notid = intval($row['modnotificationsid']);
			if ($notid > 0 && $relid > 0) {
				// raw update, it's faster!
				if ($adb->isMysql()) {
					$res2 = $adb->pquery(
						"UPDATE {$table_prefix}_modnotifications
						INNER JOIN ".$table_prefix."_crmentity ON ".$table_prefix."_crmentity.crmid = ".$table_prefix."_modnotifications.modnotificationsid $setypeCond
						SET modifiedtime = ?, deleted = 1
						WHERE smownerid = ? AND related_to = ? AND modnotificationsid != ?",
						array($now, $userid, $relid, $notid)
					);
				} elseif ($adb->isMssql()) {
					$res2 = $adb->pquery(
						"UPDATE {$table_prefix}_modnotifications
						SET modifiedtime = ?, deleted = 1
						INNER JOIN ".$table_prefix."_crmentity ON ".$table_prefix."_crmentity.crmid = ".$table_prefix."_modnotifications.modnotificationsid $setypeCond
						WHERE smownerid = ? AND related_to = ? AND modnotificationsid != ?",
						array($now, $userid, $relid, $notid)
					);
				} else {
					$res2 = $adb->pquery(
						"SELECT modnotificationsid
						FROM {$table_prefix}_modnotifications
						INNER JOIN ".$table_prefix."_crmentity ON ".$table_prefix."_crmentity.crmid = ".$table_prefix."_modnotifications.modnotificationsid $setypeCond
						WHERE smownerid = ? AND related_to = ? AND modnotificationsid != ?",
						array($userid, $relid, $notid)
					);
					if ($res2) {
						while ($row2 = $adb->FetchByAssoc($res2, -1, false)) {
							$crmid = $row2['modnotificationsid'];
							$adb->pquery("UPDATE ".$table_prefix."_crmentity SET modifiedtime = ?, deleted = 1 WHERE crmid = ? $setypeCond", array($now, $crmid));
						}
					}
				}
			}
		}
	}	
}

// now remove the old table
$tmpTable = $table_prefix.'_modnot_tmp_list';
if (Vtiger_Utils::CheckTable($tmpTable)) {
	$sqlarray = $adb->datadict->DropTableSQL($tmpTable);
	$adb->datadict->ExecuteSQLArray($sqlarray);
}