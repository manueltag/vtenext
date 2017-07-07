<?php

// crmv@68357: main rewrite, move methods in external class

define('_BENNU_VERSION', '0.1');

require_once('include/utils/utils.php');
require_once('modules/Calendar/CalendarCommon.php');
require_once('modules/Calendar/iCal/includeAll.php');

global $current_user, $currentModule;
global $adb, $table_prefix; //crmv@62716

// crmv@27651
$filename = vtlib_purify($_REQUEST['filename']);

// generate the query 
$customView = new CustomView($currentModule);
$viewid = $customView->getViewId($currentModule);

$queryGenerator = new QueryGenerator($currentModule, $current_user);
if ($viewid != "0") {
	$queryGenerator->initForCustomViewById($viewid);
} else {
	$queryGenerator->initForDefaultCustomView();
}

$ical_query = $queryGenerator->getQuery();
//crmv@29407
if(isset($_SESSION['export_where'])){
	$where =$_SESSION['export_where'];
	$where = ltrim($where,' and');	//crmv@21448
	$ical_query.=" and ".$where;
}
//crmv@29407e
//$where = $queryGenerator->getConditionalWhere();

/*
// OLD QUERY, NO PERMISSIONS CHECKS!
$ical_query = "select vtiger_activity.*,vtiger_crmentity.description,vtiger_activity_reminder.reminder_time from vtiger_activity inner join vtiger_crmentity on vtiger_activity.activityid = vtiger_crmentity.crmid " .
	" LEFT JOIN vtiger_activity_reminder ON vtiger_activity_reminder.activity_id=vtiger_activity.activityid AND vtiger_activity_reminder.recurringid=0" .
	" where vtiger_crmentity.deleted = 0 and vtiger_crmentity.smownerid = " . $current_user->id . 
	" and vtiger_activity.activitytype NOT IN ('Emails')";
*/

// aggiungo join con reminder
if (!preg_match('/join\s+'.$table_prefix.'_activity_reminder/i', $ical_query)) { //crmv@62716
	$ical_query = preg_replace('/inner join '.$table_prefix.'_crmentity/i', 'LEFT JOIN '.$table_prefix.'_activity_reminder ON '.$table_prefix.'_activity_reminder.activity_id = '.$table_prefix.'_activity.activityid AND '.$table_prefix.'_activity_reminder.recurringid=0 INNER JOIN '.$table_prefix.'_crmentity', $ical_query); //crmv@62716
}

// change columns (note, the ?: non-greedy search)
$ical_query = preg_replace('/^(select) .*? (from)/i', '\1 '.$table_prefix.'_activity.*, '.$table_prefix.'_crmentity.description, smownerid, createdtime, modifiedtime, '.$table_prefix.'_activity_reminder.reminder_time \2', $ical_query); //crmv@62716 crmv@68357

// init helper class
$config = array( "unique_id" => "VTECRM");
$vcalendar = new VTEvcalendar( $config );

$myical = $vcalendar->generateFromSql($ical_query);

// Send the right content type and filename
header('Content-type: text/calendar');
header("Content-Disposition: attachment; filename={$filename}.ics");

// Print the actual calendar
echo $myical->serialize();
