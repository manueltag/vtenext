<?php


// crmv@106069

require_once('include/utils/CronUtils.php');
$cj = CronJob::getByName('Cleaner'); // to update if existing
if (empty($cj)) {
	$CU = CronUtils::getInstance();
	$cj = new CronJob();
	$cj->name = 'Cleaner';
	$cj->active = 1;
	$cj->singleRun = false;
	$cj->maxAttempts = 0;	// disable attempts check
	$cj->timeout = 600;		// 10 minutes timeout
	$cj->repeat = 21600;	// repeat every 6 hours
	$cj->fileName = 'cron/Cleaner.service.php';
	$CU->insertCronJob($cj);
}


// crmv@109663 - add the real assigned_user_id field to Products and Services

// copy the owner colum to the smownerid
$setypeCond = '';
if (PerformancePrefs::getBoolean('CRMENTITY_PARTITIONED')) {
	$setypeCond = "AND {$table_prefix}_crmentity.setype = 'Products'";
}		
if ($adb->isMysql()) {
	$res = $adb->query(
		"UPDATE {$table_prefix}_crmentity c
		INNER JOIN {$table_prefix}_products p ON p.productid = c.crmid $setypeCond
		SET c.smownerid = COALESCE(p.handler, 1)"
	);
} else {
	$res = $adb->query("SELECT productid, handler FROM {$table_prefix}_products ORDER BY productid");
	if ($res && $adb->num_rows($res) > 0) {
		while ($row = $adb->fetchByAssoc($res, -1, false)) {
			$adb->pquery("UPDATE {$table_prefix}_crmentity SET smownerid = ? WHERE crmid = ? $setypeCond", array($row['handler'], $row['productid']));
		}
	}	
}

// alter the field
$prodMod = Vtecrm_Module::getInstance('Products');
$tabid = $prodMod->id ?: 14;
$adb->pquery(
	"UPDATE {$table_prefix}_field 
	SET columnname = ?, tablename = ?, fieldlabel = ?, uitype = ?
	WHERE tabid = ? AND columnname = ?", 
	array('smownerid', $table_prefix.'_crmentity', 'Assigned To', 53, $tabid, 'handler')
);

// change the tab owner
$adb->pquery("UPDATE {$table_prefix}_tab SET ownedby = ? WHERE tabid = ?", array(0, $tabid));

// add sharing lines
for ($i=0; $i<4; ++$i) {
	if ($adb->isMysql()) {
		$adb->pquery("INSERT IGNORE INTO {$table_prefix}_org_share_action2tab (share_action_id, tabid) VALUES (?,?)", array($i, $tabid));
	} else {
		$adb->pquery("DELETE FROM {$table_prefix}_org_share_action2tab WHERE share_action_i = ? AND tabid = ?", array($i, $tabid));
		$adb->pquery("INSERT INTO {$table_prefix}_org_share_action2tab (share_action_id, tabid) VALUES (?,?)", array($i, $tabid));
	}
}

$res = $adb->pquery("SELECT ruleid FROM {$table_prefix}_def_org_share WHERE tabid = ?", array($tabid));
if ($res && $adb->num_rows($res) == 0) {
	$ruleid = $adb->getUniqueID("{$table_prefix}_def_org_share");
	$adb->pquery("INSERT INTO {$table_prefix}_def_org_share (ruleid, tabid, permission, editstatus) VALUES (?,?,?,?)", array($ruleid, $tabid, 2, 0));
}


// idem for the services

if (isModuleInstalled('Services')) {
	$servMod = Vtecrm_Module::getInstance('Services');
	$tabid = $servMod->id;
	
	// copy the owner colum to the smownerid
	$setypeCond = '';
	if (PerformancePrefs::getBoolean('CRMENTITY_PARTITIONED')) {
		$setypeCond = "AND {$table_prefix}_crmentity.setype = 'Services'";
	}		
	if ($adb->isMysql()) {
		$res = $adb->query(
			"UPDATE {$table_prefix}_crmentity c
			INNER JOIN {$table_prefix}_service s ON s.serviceid = c.crmid $setypeCond
			SET c.smownerid = COALESCE(s.handler, 1)"
		);
	} else {
		$res = $adb->query("SELECT serviceid, handler FROM {$table_prefix}_service ORDER BY serviceid");
		if ($res && $adb->num_rows($res) > 0) {
			while ($row = $adb->fetchByAssoc($res, -1, false)) {
				$adb->pquery("UPDATE {$table_prefix}_crmentity SET smownerid = ? WHERE crmid = ? $setypeCond", array($row['handler'], $row['serviceid']));
			}
		}	
	}
	
	// alter the field
	$adb->pquery(
		"UPDATE {$table_prefix}_field 
		SET columnname = ?, tablename = ?, fieldlabel = ?, uitype = ?
		WHERE tabid = ? AND columnname = ?", 
		array('smownerid', $table_prefix.'_crmentity', 'Assigned To', 53, $tabid, 'handler')
	);
	
	// change the tab owner
	$adb->pquery("UPDATE {$table_prefix}_tab SET ownedby = ? WHERE tabid = ?", array(0, $tabid));

	// add sharing lines
	for ($i=0; $i<4; ++$i) {
		if ($adb->isMysql()) {
			$adb->pquery("INSERT IGNORE INTO {$table_prefix}_org_share_action2tab (share_action_id, tabid) VALUES (?,?)", array($i, $tabid));
		} else {
			$adb->pquery("DELETE FROM {$table_prefix}_org_share_action2tab WHERE share_action_i = ? AND tabid = ?", array($i, $tabid));
			$adb->pquery("INSERT INTO {$table_prefix}_org_share_action2tab (share_action_id, tabid) VALUES (?,?)", array($i, $tabid));
		}
	}
	
	$res = $adb->pquery("SELECT ruleid FROM {$table_prefix}_def_org_share WHERE tabid = ?", array($tabid));
	if ($res && $adb->num_rows($res) == 0) {
		$ruleid = $adb->getUniqueID("{$table_prefix}_def_org_share");
		$adb->pquery("INSERT INTO {$table_prefix}_def_org_share (ruleid, tabid, permission, editstatus) VALUES (?,?,?,?)", array($ruleid, $tabid, 2, 0));
	}
}

Vtecrm_Module::syncfile();
RecalculateSharingRules();
