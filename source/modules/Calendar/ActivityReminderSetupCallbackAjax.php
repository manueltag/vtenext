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

global $app_strings;
global $currentModule,$image_path,$theme,$adb, $current_user,$table_prefix;

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

require_once("data/Tracker.php");
require_once('modules/VteCore/layout_utils.php');	//crmv@30447
require_once('include/utils/utils.php');

$log = LoggerManager::getLogger('Activity_Reminder');

$cbaction = $_REQUEST['cbaction'];
$cbmodule = $_REQUEST['cbmodule'];
$cbrecord = $_REQUEST['cbrecord'];
$cbparams = $_REQUEST['cbparams']; // crmv@98866

// crmv@103354
$interval = getSingleFieldValue($table_prefix."_users", 'reminder_interval', 'id', $current_user->id);
$intervalInSeconds = $interval != 'None' ? intval(ConvertToMinutes($interval)*60) : null;
// crmv@103354e

if($cbaction == 'POSTPONE') {
	if(!empty($cbmodule) && !empty($cbrecord)) { // crmv@98866
		$reminderid = $_REQUEST['cbreminderid'];
		if(!empty($reminderid) ) {
			// crmv@103354
			unset($_SESSION['next_reminder_time']);
			if ($intervalInSeconds) {
				$_SESSION['next_reminder_time'] = time() + $intervalInSeconds;
			}
			// crmv@103354e
			$reminder_query = "UPDATE ".$table_prefix."_act_reminder_popup set status = 0 WHERE reminderid = ? AND semodule = ? AND recordid = ?";
			$adb->pquery($reminder_query, array($reminderid, $cbmodule, $cbrecord));
			echo ":#:SUCCESS";
		} else {
			echo ":#:FAILURE";			
		}		
	// crmv@98866
	} else if (!empty($cbparams)) {
		$cbparams = json_decode($cbparams, true);
		if (is_array($cbparams) && !empty($cbparams)) {
			// crmv@103354
			unset($_SESSION['next_reminder_time']);
			if ($intervalInSeconds) {
				$_SESSION['next_reminder_time'] = time() + $intervalInSeconds;
			}
			// crmv@103354e
			foreach ($cbparams as $cbparam) {
				$module = $cbparam['module'];
				$record = $cbparam['record'];
				$reminderid = $cbparam['reminderid'];
				if(!empty($reminderid)) {
					$reminder_query = "UPDATE ".$table_prefix."_act_reminder_popup set status = 0 WHERE reminderid = ? AND semodule = ? AND recordid = ?";
					$adb->pquery($reminder_query, array($reminderid, $module, $record));
				} else {
					echo ":#:FAILURE";
					exit();
				}
			}
			echo ":#:SUCCESS";
		}
	}
	// crmv@98866 end
}
elseif($cbaction == 'CLOSE') {
	if(!empty($cbmodule) && !empty($cbrecord)) { // crmv@98866
		$reminderid = $_REQUEST['cbreminderid'];
		if(!empty($reminderid) ) {
			$reminder_query = "UPDATE ".$table_prefix."_act_reminder_popup set status = 1 WHERE reminderid = ? AND semodule = ? AND recordid = ?";
			$adb->pquery($reminder_query, array($reminderid, $cbmodule, $cbrecord));
			echo ":#:SUCCESS";
		} else {
			echo ":#:FAILURE";			
		}		
	// crmv@98866
	} else if (!empty($cbparams)) {
		$cbparams = json_decode($cbparams, true);
		if (is_array($cbparams) && !empty($cbparams)) {
			foreach ($cbparams as $cbparam) {
				$module = $cbparam['module'];
				$record = $cbparam['record'];
				$reminderid = $cbparam['reminderid'];
				if(!empty($reminderid)) {
					$reminder_query = "UPDATE ".$table_prefix."_act_reminder_popup set status = 1 WHERE reminderid = ? AND semodule = ? AND recordid = ?";
					$adb->pquery($reminder_query, array($reminderid, $module, $record));
				} else {
					echo ":#:FAILURE";
					exit();
				}
			}
			echo ":#:SUCCESS";
		}
	}
	// crmv@98866 end
}

?>