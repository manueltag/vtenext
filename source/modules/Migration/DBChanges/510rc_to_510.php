<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/

//5.1.0 RC to 5.1.0 database changes
global $table_prefix;
//we have to use the current object (stored in PatchApply.php) to execute the queries
$adb = $_SESSION['adodb_current_object'];
$conn = $_SESSION['adodb_current_object'];

$migrationlog->debug("\n\nDB Changes from 5.1.0 RC to 5.1.0 -------- Starts \n\n");

ExecuteQuery("DELETE ".$table_prefix."_cvcolumnlist FROM ".$table_prefix."_cvcolumnlist INNER JOIN ".$table_prefix."_customview WHERE ".$table_prefix."_cvcolumnlist.columnname LIKE '%".$table_prefix."_notes:filename%' AND ".$table_prefix."_customview.cvid = ".$table_prefix."_cvcolumnlist.cvid AND ".$table_prefix."_customview.entitytype='HelpDesk'");
ExecuteQuery("DELETE ".$table_prefix."_cvcolumnlist FROM ".$table_prefix."_cvcolumnlist INNER JOIN ".$table_prefix."_customview WHERE (".$table_prefix."_cvcolumnlist.columnname LIKE '%parent_id%' OR ".$table_prefix."_cvcolumnlist.columnname LIKE '%".$table_prefix."_contactdetails%') AND ".$table_prefix."_customview.cvid = ".$table_prefix."_cvcolumnlist.cvid AND ".$table_prefix."_customview.entitytype='Documents'");

ExecuteQuery("DELETE ".$table_prefix."_cvadvfilter FROM ".$table_prefix."_cvadvfilter INNER JOIN ".$table_prefix."_customview WHERE ".$table_prefix."_cvadvfilter.columnname LIKE '%".$table_prefix."_notes:filename%' AND ".$table_prefix."_customview.cvid = ".$table_prefix."_cvadvfilter.cvid AND ".$table_prefix."_customview.entitytype='HelpDesk'");
ExecuteQuery("DELETE ".$table_prefix."_cvadvfilter FROM ".$table_prefix."_cvadvfilter INNER JOIN ".$table_prefix."_customview WHERE (".$table_prefix."_cvadvfilter.columnname LIKE '%parent_id%' OR ".$table_prefix."_cvadvfilter.columnname LIKE '%".$table_prefix."_contactdetails%') AND ".$table_prefix."_customview.cvid = ".$table_prefix."_cvadvfilter.cvid AND ".$table_prefix."_customview.entitytype='Documents'");

// Fixed issue with Calendar duration calculation
ExecuteQuery("ALTER TABLE ".$table_prefix."_activity MODIFY duration_hours VARCHAR(200)");

$result = $adb->query("SELECT activityid,date_start,due_date, time_start,time_end FROM ".$table_prefix."_activity WHERE activitytype NOT IN ('Task','Emails')");
$noofrows = $adb->num_rows($result);
for($index=0;$index<$noofrows;$index++){
 	$activityid = $adb->query_result($result,$index,'activityid');
	$date_start = $adb->query_result($result,$index,'date_start');
	$time_start = $adb->query_result($result,$index,'time_start');
	$due_date = $adb->query_result($result,$index,'due_date');
	$time_end = $adb->query_result($result,$index,'time_end');
	
	$start_date = explode("-",$date_start);
	$end_date = explode("-",$due_date);
	$start_time = explode(":",$time_start);
	$end_time = explode(":",$time_end);
	
	$start = mktime(intval($start_time[0]),intval($start_time[1]),0,intval($start_date[1]),intval($start_date[2]),intval($start_date[0]));
	$end = mktime(intval($end_time[0]),intval($end_time[1]),0,intval($end_date[1]),intval($end_date[2]),intval($end_date[0]));
	
	$duration_in_minutes = floor(($end-$start)/(60));//get the difference between start time and end time in minutes
	$hours = floor($duration_in_minutes/60);
	$minutes = $duration_in_minutes%60;
	$adb->pquery("UPDATE ".$table_prefix."_activity SET duration_hours=?, duration_minutes=? WHERE activityid=?",array($hours, $minutes,$activityid));
}

$migrationlog->debug("\n\nDB Changes from 5.1.0 RC to 5.1.0 -------- Ends \n\n");

?>