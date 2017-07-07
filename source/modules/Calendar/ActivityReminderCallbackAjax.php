<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/

global $app_strings,$table_prefix;
global $currentModule,$image_path,$theme,$adb, $current_user;

require_once('Smarty_setup.php');
require_once("data/Tracker.php");
require_once('modules/VteCore/layout_utils.php');	//crmv@30447
require_once('include/utils/utils.php');
require_once('modules/Calendar/Activity.php');

$cur_time = time();
$_SESSION['last_reminder_check_time'] = $cur_time;
$_SESSION['next_reminder_interval'] = 60;
if($_SESSION['next_reminder_time'] == 'None') {
	return;
} elseif(isset($_SESSION['next_reminder_interval']) && (($_SESSION['next_reminder_time'] -
		$_SESSION['next_reminder_interval']) > $cur_time)) {
	echo "<script type='text/javascript' id='_vtiger_activityreminder_callback_interval_'>".
		($_SESSION['next_reminder_interval'] * 1000)."</script>";
	return;
}

$log = LoggerManager::getLogger('Activity_Reminder');
$smarty = new vtigerCRM_Smarty;
if(isPermitted('Calendar','index') == 'yes'){
	$active = $adb->pquery("select reminder_interval from ".$table_prefix."_users where id=?",array($current_user->id));
	$active_res = $adb->query_result($active,0,'reminder_interval');
	if($active_res == 'None') {
		$_SESSION['next_reminder_time'] = 'None';
	}
	if($active_res!='None'){
		$interval=$adb->query_result($active,0,"reminder_interval");
		$intervalInMinutes = ConvertToMinutes($interval);
		// check for reminders every minute
		$time = time();
		$_SESSION['next_reminder_time'] = $time + ($intervalInMinutes * 60);
		$date = date('Y-m-d', strtotime("+$intervalInMinutes minutes", $time));
		$time = date('H:i',   strtotime("+$intervalInMinutes minutes", $time));
		// crmv@64325
		$setypeCond = '';
		if (PerformancePrefs::getBoolean('CRMENTITY_PARTITIONED')) {
			$setypeCond = "AND {$table_prefix}_crmentity.setype = 'Calendar'";
		}
		//crmv@19691
		$callback_query =
		"SELECT ".$table_prefix."_act_reminder_popup.* FROM ".$table_prefix."_act_reminder_popup
		INNER JOIN ".$table_prefix."_crmentity on ".$table_prefix."_act_reminder_popup.recordid = ".$table_prefix."_crmentity.crmid ".
		"WHERE ".$table_prefix."_act_reminder_popup.status = 0 and " .
		$table_prefix."_crmentity.smownerid = ".$current_user->id." and ".$table_prefix."_crmentity.deleted = 0 $setypeCond " .
		" and ((".$adb->database->SQLDate('Y-m-d',$table_prefix.'_act_reminder_popup.date_start')." < '" . $date . "')" .
		" OR ((".$adb->database->SQLDate('Y-m-d',$table_prefix.'_act_reminder_popup.date_start')." = '" . $date . "')" .
		" AND ".$table_prefix."_act_reminder_popup.time_start <= '" . $time . "'))";
		//crmv@19691e // crmv@64325e
		$result = $adb->query($callback_query);

		$cbrows = $adb->num_rows($result);
		if($cbrows > 0) {
			for($index = 0; $index < $cbrows; ++$index) {
				$reminderid = $adb->query_result($result, $index, "reminderid");
				$cbrecord = $adb->query_result($result, $index, "recordid");
				$cbmodule = $adb->query_result($result, $index, "semodule");

				$focus = CRMEntity::getInstance($cbmodule);
				if (!isRecordExists($cbrecord)) {
					$del_qry = "delete from ".$table_prefix."_act_reminder_popup where reminderid = ?";
					$adb->pquery($del_qry,Array($reminderid));
					continue;
				}
								
				if (in_array($cbmodule,array('Calendar','Events'))) {
					$focus->retrieve_entity_info($cbrecord,$cbmodule);
					$cbsubject = $focus->column_fields['subject'];
					$cbactivitytype   = $focus->column_fields['activitytype'];
					$cbdate   = $focus->column_fields["date_start"];
					$cbtime   = $focus->column_fields["time_start"];
					// crmv@98866
					$duedate = $focus->column_fields["due_date"];
					$duetime = $focus->column_fields["time_end"];
					$location = $focus->column_fields["location"];
					// crmv@98866 end
				} else {
					// For non-calendar records.
					$cbsubject      = array_values(getEntityName($cbmodule, $cbrecord));
					$cbsubject      = $cbsubject[0];
					$cbactivitytype = getTranslatedString($cbmodule, $cbmodule);
					$cbdate         = $adb->query_result($result, $index, 'date_start');
					$cbtime         = $adb->query_result($result, $index, 'time_start');
				}

				if($cbactivitytype=='Task')
					$cbstatus   = $focus->column_fields["taskstatus"];
				else
					$cbstatus   = $focus->column_fields["eventstatus"];
				// Appending recordid we can get unique callback dom id for that record.
				$popupid = "ActivityReminder_$cbrecord";
				if($cbdate <= date('Y-m-d')){
					if(substr($cbdate,0,10) == date('Y-m-d') && $cbtime > date('H:i')) $cbcolor = '';
					else $cbcolor= '#FF1515';
				}
				$smarty->assign("THEME", $theme);
				$smarty->assign("popupid", $popupid);
				$smarty->assign("APP", $app_strings);
				$smarty->assign("cbreminderid", $reminderid);
				$smarty->assign("cbdate", getDisplayDate($cbdate));
				$smarty->assign("cbtime", $cbtime);
				$smarty->assign("cbsubject", $cbsubject);
				$smarty->assign("cbmodule", $cbmodule);
				$smarty->assign("cbrecord", $cbrecord);
				$smarty->assign("cbstatus", $cbstatus);
				$smarty->assign("cbcolor", $cbcolor);
				$smarty->assign("cblinkdtl", $cblinkdtl);
				$smarty->assign("activitytype", $cbactivitytype);
				
				// crmv@98866
				
				// crmv@103354
				$allDate = $cbdate . ' ' . $cbtime;
				$adjustedDate = adjustTimezone($allDate, 0, null, true);
				$cbdate1 = substr($adjustedDate, 0, 10);
				if (strlen($adjustedDate) > 10) {
					$cbtime1 = substr($adjustedDate, strpos($adjustedDate, ' ') + 1, 5);
				}
				$allDate1 = $cbdate1 . ' ' . $cbtime1;
				$allDateObj = new DateTime($allDate1);
				$period = getFriendlyDate($allDateObj->format('Y-m-d H:i:s'));
				$smarty->assign("PERIOD", $period);
				// crmv@103354e
				
				$when_string = getTranslatedString('LBL_WHEN') . ':';
				$when_string .= ' ' . $allDateObj->format('d') . '-' . getTranslatedString($allDateObj->format('M')) . '-' . $allDateObj->format('y');
				$when_string .= ' ' . getTranslatedString('LBL_FROM_HOUR') . ' ' . $cbtime;
				if (!empty($duetime)) {
					$when_string .= ' ' . getTranslatedString('LBL_TO_HOUR') . ' ' . $duetime;
				}
				
				$location_string = '';
				if (!empty($location)) {
					$location_string .= getTranslatedString('LBL_APP_LOCATION') . ':';
					$location_string .= ' ' . $location;
				}
				
				$smarty->assign("WHEN_STRING", $when_string);
				$smarty->assign("LOCATION_STRING", $location_string);
				// crmv@98866 end
				
				$smarty->display("ActivityReminderCallback.tpl");

				$mark_reminder_as_read = "UPDATE ".$table_prefix."_act_reminder_popup set status = 1 where reminderid = ?";
				$adb->pquery($mark_reminder_as_read, array($reminderid));
			}
			
			// crmv@104512
			echo "<script type='text/javascript'>
					if (!window.oldWindowDocumentTitle) {
						window.oldWindowDocumentTitle = window.top.document.title.replace(' - ' + browser_title, '');
						updateBrowserTitle('".$app_strings['LBL_APPOINTMENT_REMINDER']."');
					}
				</script>";
			// crmv@104512e
		} else {
			//crmv@19691 crmv@64325
			$callback_query =
			"SELECT ".$table_prefix."_act_reminder_popup.* FROM ".$table_prefix."_act_reminder_popup inner join ".$table_prefix."_crmentity on ".$table_prefix."_act_reminder_popup.recordid = ".$table_prefix."_crmentity.crmid where " .
			" ".$table_prefix."_act_reminder_popup.status = 0 and " .
			" ".$table_prefix."_crmentity.smownerid = ".$current_user->id." and ".$table_prefix."_crmentity.deleted = 0 $setypeCond ".
			"AND ".$table_prefix."_act_reminder_popup.reminderid > 0 ORDER BY date_start DESC , ".
			"".$table_prefix."_act_reminder_popup.time_start DESC";
			//crmv@19691e crmv@64325e
			$result = $adb->limitQuery($callback_query,0,1);
			$it = new SqlResultIterator($adb, $result);
			$nextReminderTime = null;
			foreach ($it as $row) {
				$nextReminderTime = strtotime($row->date_start.' '.$row->time_start);
			}
			$_SESSION['next_reminder_time'] = $nextReminderTime - ($intervalInMinutes * 60);
		}
		echo "<script type='text/javascript' id='_vtiger_activityreminder_callback_interval_'>".
				($_SESSION['next_reminder_interval'] * 1000)."</script>";
	}
}

?>