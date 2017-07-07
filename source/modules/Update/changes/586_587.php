<?php
global $adb, $table_prefix;

// aggiorno uitype per i campi durata del calendario
$adb->query("update {$table_prefix}_field set uitype = 7, typeofdata = 'I~O' where tabid = 9 and fieldname = 'duration_hours'");
$adb->query("update {$table_prefix}_field set uitype = 7, typeofdata = 'I~M' where tabid = 16 and fieldname = 'duration_hours'");
$adb->query("update {$table_prefix}_field set uitype = 7, typeofdata = 'I~O' where tabid in (9,16) and fieldname = 'duration_minutes'");

// ricalcolo le durate degli eventi
$res = $adb->query("select activityid, date_start, due_date, time_start, time_end from {$table_prefix}_activity inner join {$table_prefix}_crmentity on crmid = activityid where deleted = 0 and date_start is not null and date_start != '' and date_start != '0000-00-00' and due_date is not null and due_date != '' and due_date != '0000-00-00'");
if ($res) {
	while ($row = $adb->fetchByAssoc($res, -1, false)) {

		$start_hour = empty($row['time_start']) ? '00:00:00' : $row['time_start'];
		$end_hour = empty($row['time_end']) ? '00:00:00' : $row['time_end'];

		$ts_start = strtotime($row['date_start']." ".$start_hour);
		$ts_end = strtotime($row['due_date']." ".$end_hour);

		$dh = (int)(abs($ts_end-$ts_start)/(3600));
		$dm = (int)((abs($ts_end-$ts_start)/60)%60);
		$adb->pquery("update {$table_prefix}_activity set duration_hours = ?, duration_minutes = ? where activityid = ?", array($dh, $dm, $row['activityid']));
	}
}
?>