<?php
/* crmv@47611 */

// Get the list of activity for which reminder needs to be sent

// crmv@37463
require_once('include/utils/utils.php');
require_once('include/logging.php');

global $adb, $table_prefix, $log;
global $site_URL; //crmv@62588

$log =& LoggerManager::getLogger('SendReminder');
$log->debug(" invoked SendReminder ");

// retrieve the translated strings.
$app_strings = return_application_language($current_language);

//modified query for recurring events -Jag
//crmv@10488	//crmv@40525
if (check_notification_scheduler(8)){
	$query="SELECT
	  ".$table_prefix."_crmentity.smownerid,
	  ".$table_prefix."_crmentity.crmid,
	  ".$table_prefix."_seactivityrel.crmid AS \"setype\",
	  ".$table_prefix."_activity.*,
	  ".$table_prefix."_activity_reminder.reminder_time,
	  ".$table_prefix."_activity_reminder.reminder_sent,
	  ".$table_prefix."_activity_reminder.recurringid,
	  ".$table_prefix."_recurringevents.recurringdate
	FROM ".$table_prefix."_activity
	  INNER JOIN ".$table_prefix."_crmentity
	    ON ".$table_prefix."_crmentity.crmid = ".$table_prefix."_activity.activityid
	  INNER JOIN ".$table_prefix."_activity_reminder
	    ON ".$table_prefix."_activity.activityid = ".$table_prefix."_activity_reminder.activity_id
	  LEFT OUTER JOIN ".$table_prefix."_recurringevents
	    ON ".$table_prefix."_activity.activityid = ".$table_prefix."_recurringevents.activityid
	  LEFT OUTER JOIN ".$table_prefix."_seactivityrel
	    ON ".$table_prefix."_seactivityrel.activityid = ".$table_prefix."_activity.activityid
	WHERE ".$table_prefix."_activity.date_start >= '".date('Y-m-d')."'
	    AND ".$table_prefix."_crmentity.crmid != 0
	    AND ".$table_prefix."_activity_reminder.reminder_sent = 0
	    and deleted = 0";
	$params = array();
	$focus = CRMEntity::getInstance('Activity');
	if (!empty($focus->sendreminder_states)) {
		$params[] = $focus->sendreminder_states;
		$query .= " AND ".$table_prefix."_activity.eventstatus IN (".generateQuestionMarks($focus->send_reminder_states).")";
	}
	$result = $adb->pquery($query,$params);
//crmv@10488e	//crmv@40525e
	if($adb->num_rows($result) >= 1)
	{
		while($result_set = $adb->fetch_array($result))
		{
			$date_start = $result_set['date_start'];
			$time_start = $result_set['time_start'];
			$reminder_time = $result_set['reminder_time'];
	        $curr_time = strtotime(date("Y-m-d H:i"))/60;
			$activity_id = $result_set['activityid'];
			$activitymode = ($result_set['activitytype'] == "Task")?"Task":"Events";
			$parent_type = $result_set['setype'];
			$activity_sub = $result_set['subject'];

			if ($parent_type!='') {
				$parent_content = getParentInfo($parent_type)."\n";
			} else {
				$parent_content = "";
			}
			//code included for recurring events by jaguar starts
			$recur_id = $result_set['recurringid'];
			$current_date=date('Y-m-d');
			if($recur_id == 0)
			{
				$date_start = $result_set['date_start'];
			}
			else
			{
				$date_start = $result_set['recurringdate'];
			}
			//code included for recurring events by jaguar ends

	        $activity_time = strtotime(date("$date_start $time_start"))/60;

			if (($activity_time - $curr_time) > 0 && ($activity_time - $curr_time) <= $reminder_time)	//crmv@28057
			{
				$log->debug(" InSide  REMINDER");

				// Retriving the Subject and message from reminder table
				$sql = "select active,notificationsubject,notificationbody from ".$table_prefix."_notifyscheduler where schedulednotificationid=8";
				$result_main = $adb->pquery($sql, array());

				$subject = $app_strings['Reminder'].$result_set['activitytype']." @ ".$result_set['date_start']." ".$result_set['time_start']."] ".$adb->query_result_no_html($result_main,0,'notificationsubject');

				//Set the mail body/contents here
				//crmv@10448
				$contents = nl2br($adb->query_result_no_html($result_main,0,'notificationbody') ."\n\n ".$app_strings['Subject']." : ".$activity_sub."\n ". $parent_content ." ".$app_strings['Date & Time']." : ".$date_start." ".$time_start."\n\n ".$app_strings['Visit_Link']." <a href='".$site_URL."/index.php?action=DetailView&module=Calendar&record=".$activity_id."&activity_mode=".$activitymode."'>".$app_strings['Click here']."</a>");
				$contents = nl2br(getTranslatedString($adb->query_result_no_html($result_main,0,'notificationbody')) ."\n\n ".$app_strings['Subject']." : ".$activity_sub."\n ". $parent_content ." ".$app_strings['Date & Time']." : ".$date_start." ".$time_start."\n\n ".$app_strings['Visit_Link']." <a href='".$site_URL."/index.php?action=DetailView&module=Calendar&record=".$activity_id."&activity_mode=".$activitymode."'>".$app_strings['Click here']."</a>"); //crmv@97594
				//crmv@10488 e

				//crmv@29617
				$modNotificationsFocus = CRMEntity::getInstance('ModNotifications');
				$modNotificationsFocus->saveFastNotification(
					array(
						'assigned_user_id' => $result_set['smownerid'],
						'related_to' => $activity_id,
						'mod_not_type' => 'Reminder calendar',
						'subject' => $subject,
						'description' => $contents,
						'from_email' => $REMINDER_EMAIL_ID,
						'from_email_name' => $REMINDER_NAME,
					)
				);
				//crmv@29617e

				$upd_query = "UPDATE ".$table_prefix."_activity_reminder SET reminder_sent=1 where activity_id=?";
				$upd_params = array($activity_id);

				if($recur_id!=0)
				{
					$upd_query.=" and recurringid =?";
					array_push($upd_params, $recur_id);
				}

				$adb->pquery($upd_query, $upd_params);
			}
		}
	}
}
/**
 This function is used to get the Parent type and its Name
 It takes the input integer - crmid and returns the parent type and its name as string.
*/
function getParentInfo($value)
{
	global $adb, $table_prefix;
 	$parent_module = getSalesEntityType($value);
	if($parent_module == "Leads")
	{
		$sql = "select * from ".$table_prefix."_leaddetails where leadid=?";
		$result = $adb->pquery($sql, array($value));
		$first_name = $adb->query_result($result,0,"firstname");
		$last_name = $adb->query_result($result,0,"lastname");

		$parent_name = $last_name.' '.$first_name;
	}
	elseif($parent_module == "Accounts")
	{
		$sql = "select * from  ".$table_prefix."_account where accountid=?";
		$result = $adb->pquery($sql, array($value));
		$account_name = $adb->query_result($result,0,"accountname");

		$parent_name =$account_name;
	}
	elseif($parent_module == "Potentials")
	{
		$sql = "select * from  ".$table_prefix."_potential where potentialid=?";
		$result = $adb->pquery($sql, array($value));
		$potentialname = $adb->query_result($result,0,"potentialname");

		$parent_name =$potentialname;
	}
	  return $parent_module ." : ".$parent_name;
}


?>