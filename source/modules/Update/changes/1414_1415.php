<?php
global $adb, $table_prefix;

/* crmv@106298 */
// fix reports with wrong dates in stdfilters

global $current_user;

if (!$current_user) {
	$current_user = CRMEntity::getInstance('Users');
	$current_user->id = 1;
}

if (Vtiger_Utils::CheckTable("{$table_prefix}_reportconfig")) {
	$currentUserBak = $current_user;
	$query = "SELECT r.reportid, r.owner, u.date_format, rc.stdfilters FROM {$table_prefix}_report r 
		INNER JOIN {$table_prefix}_reportconfig rc ON r.reportid = rc.reportid
		LEFT JOIN {$table_prefix}_users u ON u.id = r.owner
		WHERE stdfilters IS NOT NULL";
	($adb->isMssql()) ? $query .= " and stdfilters NOT LIKE ''" : $query .= " and stdfilters != ''";
	$res = $adb->query($query);
	if ($res && $adb->num_rows($res) > 0) {
		while ($row = $adb->fetchByAssoc($res, -1, false)) {
			$reportid = $row['reportid'];
			$filter = Zend_Json::decode($row['stdfilters']);
			if (is_array($filter) && $filter[0]['value'] == 'custom') {
				$date1 = fixReportStdDate($filter[0]['startdate'], $row['date_format']);
				$date2 = fixReportStdDate($filter[0]['enddate'], $row['date_format']);
				// check if dates changed
				if ($date1 != $filter[0]['startdate'] || $date2 != $filter[0]['enddate']) {
					//update!!
					$filter[0]['startdate'] = $date1;
					$filter[0]['enddate'] = $date2;
					// save!
					$adb->pquery("UPDATE {$table_prefix}_reportconfig SET stdfilters = ? WHERE reportid = ?", array(Zend_Json::encode($filter), $reportid));
				}
				
			}
		}
	}
	$current_user = $currentUserBak;
}


function fixReportStdDate($date, $date_format = 'dd-mm-yyyy') {
	global $current_user;
	
	$current_user->date_format = $date_format;
	if (!empty($date)) {
		$date = substr(trim($date),0, 10); // date only, no time!
		if (!preg_match('/^[12][0-9]{3}-[0-3][0-9]-[0-3][0-9]$/', $date)) {
			// not in db format, need to sanitize
			$date = getValidDBInsertDateValue($date);
		}
	}
	return $date;
}