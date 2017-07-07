<?php
/* crmv@33448 crmv@55708 crmv@62394 */

// TODO: move all the other functions here
class CalendarTracking {

	public static $enabled = true;
	public static $enabled_in_detailview = array('Accounts', 'Contacts', 'HelpDesk', 'ProjectTask');
	public static $enabled_in_turbolift = array('Messages', 'Emails');
	
	public static function isEnabledForModule($module) {
		return (self::$enabled && (in_array($module, self::$enabled_in_detailview) || in_array($module, self::$enabled_in_turbolift)));
	}
	
	public static function isEnabledForDetailview($module) {
		return (self::$enabled && in_array($module, self::$enabled_in_detailview));
	}
	
	public static function isEnabledForTurbolift($module) {
		return (self::$enabled && in_array($module, self::$enabled_in_turbolift));
	}
	
	public static function getTrackerData($module, $recordid) {
		$return = array();
		
		$enable_buttons = true;
		$active_tracked = getActiveTracked();
		if ($active_tracked !== false && $recordid != $active_tracked) {
			$enable_buttons = false;
		}
		$return['current_tracked'] = $active_tracked;
		$return['enable_buttons'] = $enable_buttons;

		$buttons = array();
		if ($enable_buttons) {

			if ($recordid > 0 && $recordid == $active_tracked) {
				$buttons['start'] = false;
				$buttons['pause'] = true;
				$buttons['stop'] = true;
			} else {
				$buttons['start'] = true;
				$buttons['pause'] = false;
				$buttons['stop'] = false;
			}
			
			$return['buttons'] = $buttons;

			$labels = array(
				'start' => getTranslatedString('LBL_TRACK_START_FOR', 'APP_STRING').getTranslatedString('SINGLE_'.$module),
				'pause' => getTranslatedString('LBL_TRACK_PAUSE_FOR', 'APP_STRING').getTranslatedString('SINGLE_'.$module),
				'stop' => getTranslatedString('LBL_TRACK_STOP_FOR', 'APP_STRING').getTranslatedString('SINGLE_'.$module),
			);
			$return['buttons_labels'] = $labels;
			
		} else {
			$active_tracked_module = getSalesEntityType($active_tracked);
			$active_tracked_name = array_values(getEntityName($active_tracked_module, $active_tracked));
			$active_tracked_entity_type = getSingleModuleName($active_tracked_module, $active_tracked);
			
			$return['current_tracked_name'] = $active_tracked_name[0];
			$return['current_tracked_module'] = $active_tracked_module;
			$return['current_tracked_entity_type'] = $active_tracked_entity_type;
		}
		
		//crmv@65492 - 18
		$module_heldesk_active = Vtlib_isModuleActive('HelpDesk');
		$tickets_available_permission = false;
		if($module_heldesk_active){
			//check also for user profile permissions
			if(isPermitted('HelpDesk','EditView','') == 'yes'){
				$tickets_available_permission = true;
			}
		}
		$return['tickets_available'] = $tickets_available_permission;
		//crmv@65492e - 18

		$return['already_tracking_by_other'] = getOtherUsersTracking($recordid);

		return $return;
	}
	
	// create the tracking event and inject the id in the request, so it's linked to the outgoing email
	//crmv@69922
	public static function trackSendEmail($startTs, $stopTs = null, $otherFields=array()) {
		
		$now = $stopTs ?: time();
		$timediff = $now - $startTs;
		$subject = trim(vtlib_purify($_REQUEST['subject']));
		
		if ($timediff <= 0 || $timediff > 3600*24) return false;
		if (empty($subject)) $subject = "Tracking Email";
		
		$fields = array(
			'subject' => $subject,
			'description' => 'Email tracking',
			'date_start' => date('Y-m-d', $startTs),
			'time_start' => date('H:i', $startTs),
			'due_date' => date('Y-m-d', $now),
			'time_end' => date('H:i', $now),
		);
		$fields = array_merge($fields, $otherFields);
		$calid = self::createCalTrackForCreate($fields);
		
		if ($calid > 0) {
			// now inject the id
			if (substr($_REQUEST['relation'], -1, 1) != '|') $_REQUEST['relation'] .= '|';
			$_REQUEST['relation'] .= $calid;
		}

		return $calid;
	}

	public static function createCalTrackForCreate($fields) {
		global $current_user;
		
		$focus = CRMEntity::getInstance('Events');
		
		$focus->mode = '';

		// set the fields
		$focus->column_fields = array_merge($focus->column_fields, $fields);
		
		// sanitize
		$focus->column_fields['date_start'] = substr($focus->column_fields['date_start'],0,10);
		$focus->column_fields['time_start'] = substr($focus->column_fields['time_start'],0,5);
		$focus->column_fields['due_date'] = substr($focus->column_fields['due_date'],0,10);
		$focus->column_fields['time_end'] = substr($focus->column_fields['time_end'],0,5);
		
		// forced values
		$focus->column_fields['activitytype'] = 'Tracked';
		$focus->column_fields['eventstatus'] = 'Held';
		$focus->column_fields['taskpriority'] = 'Low';
		$focus->column_fields['visibility'] = 'Standard';
		$focus->column_fields['assigned_user_id'] = $current_user->id;
		
		//save
		$focus->save('Events');
		
		return $focus->id;
	}
	//crmv@69922e
	
}


function getActiveTracked() {
	global $adb, $table_prefix, $current_user;
	$result = $adb->pquery("select record from {$table_prefix}_cal_tracker inner join {$table_prefix}_crmentity on {$table_prefix}_crmentity.crmid = {$table_prefix}_cal_tracker.record where deleted = 0 and userid = ?",array($current_user->id));
	if ($result && $adb->num_rows($result) > 0) {
		return $adb->query_result($result,0,'record');
	}
	return false;
}
function deleteTracking($record) {
	global $adb, $table_prefix, $current_user;
	$result = $adb->pquery("delete from {$table_prefix}_cal_tracker_log where userid = ? and record = ?",array($current_user->id,$record));
}
function getTrackedStatus($record) {
	global $adb, $table_prefix, $current_user;
	$q = "SELECT status FROM {$table_prefix}_cal_tracker_log WHERE userid = ? AND record = ? ORDER BY id DESC";
	$result = $adb->limitpQuery($q,0,1,array($current_user->id,$record));
	$status = '';
	if ($result && $adb->num_rows($result)) {
		$status = $adb->query_result($result,0,'status');
	}
	return $status;
}
function activateTrack($record) {
	global $adb, $table_prefix, $current_user;
	$id = $adb->getUniqueID($table_prefix."_cal_tracker_log");
	$adb->pquery("insert into {$table_prefix}_cal_tracker (userid,record,id) values (?,?,?)",array($current_user->id,$record,$id));
	$adb->pquery("insert into {$table_prefix}_cal_tracker_log (id,userid,record,status,date) values (?,?,?,?,?)",array($id,$current_user->id,$record,'Started',date('Y-m-d H:i:s')));
}
function pauseTrack($module,$record,$description,$other_args=array()) { //crmv@69922
	global $adb, $table_prefix, $current_user;
	$id = $adb->getUniqueID($table_prefix."_cal_tracker_log");
	$adb->pquery("delete from {$table_prefix}_cal_tracker where userid = ? and record = ?",array($current_user->id,$record));
	$date = date('Y-m-d H:i:s');
	$adb->pquery("insert into {$table_prefix}_cal_tracker_log (id,userid,record,status,date) values (?,?,?,?,?)",array($id,$current_user->id,$record,'Paused',$date));
	$activityid = createCalTrack($id,$module,$record,$date,$description,$other_args); //crmv@69922
	return $activityid;
}
function stopTrack($module,$record,$description,$other_args=array()) { //crmv@69922
	global $adb, $table_prefix, $current_user;
	$id = $adb->getUniqueID($table_prefix."_cal_tracker_log");
	$adb->pquery("delete from {$table_prefix}_cal_tracker where userid = ? and record = ?",array($current_user->id,$record));
	$date = date('Y-m-d H:i:s');
	$adb->pquery("insert into {$table_prefix}_cal_tracker_log (id,userid,record,status,date) values (?,?,?,?,?)",array($id,$current_user->id,$record,'Stopped',$date));
	$activityid = createCalTrack($id,$module,$record,$date,$description,$other_args); //crmv@69922
	return $activityid;
}
function createCalTrack($id,$module,$record,$due_date,$description,$other_args=array()) { //crmv@69922
	global $adb, $table_prefix, $current_user;
	
	$q = "SELECT * FROM {$table_prefix}_cal_tracker_log WHERE userid = ? AND record = ? AND id < ? AND status = ? ORDER BY id DESC";
	$result = $adb->limitpQuery($q,0,1,array($current_user->id,$record,$id,'Started'));
	if ($result && $adb->num_rows($result)) {
		$date_start = $adb->query_result($result,0,'date');
	} else {
		return false;
	}
	
	$parent_module = getSalesEntityType($record);
	$subject = array_values(getEntityName($parent_module, $record));
	
	$focus = CRMEntity::getInstance('Events');
	$focus->mode = '';
	$focus->column_fields['subject'] = $subject[0];
	$focus->column_fields['activitytype'] = 'Tracked';
	$focus->column_fields['date_start'] = substr($date_start,0,10);
	$focus->column_fields['time_start'] = substr($date_start,11,5);
	$focus->column_fields['due_date'] = substr($due_date,0,10);
	$focus->column_fields['time_end'] = substr($due_date,11,5);
	$focus->column_fields['eventstatus'] = 'Held';
	$focus->column_fields['priority'] = 'Basso';
	$focus->column_fields['visibility'] = 'Standard';
	if ($parent_module == 'Contacts') {
		$focusContacts = CRMEntity::getInstance($parent_module);
		$focusContacts->retrieve_entity_info($record, $parent_module);
		$focus->column_fields['parent_id'] = $focusContacts->column_fields['account_id'];
		$focus->column_fields['contact_id'] = $record;
	} elseif ($parent_module == 'Messages') {
		// done later
	} else {
		$focus->column_fields['parent_id'] = $record;
	}
	$focus->column_fields['assigned_user_id'] = $current_user->id;
	$focus->column_fields['description'] = $description;

	$focus->column_fields = array_merge($focus->column_fields, $other_args); //crmv@69922

	$focus->save('Events');
	
	if ($parent_module == 'Messages' && $focus->id > 0) {
		$messFocus = CRMEntity::getInstance('Messages');
		$messFocus->save_related_module('Messages', $record, 'Calendar', $focus->id);
	}
	
	return $focus->id;
}
function getOtherUsersTracking($record) {
	global $adb, $table_prefix, $current_user;
	$result = $adb->pquery("select userid from {$table_prefix}_cal_tracker inner join {$table_prefix}_crmentity on {$table_prefix}_crmentity.crmid = {$table_prefix}_cal_tracker.record where deleted = 0 and record = ?",array($record));
	if ($result && $adb->num_rows($result) > 0) {
		$users = array();
		while($row=$adb->fetchByAssoc($result)) {
			if ($current_user->id != $row['userid']) {
				$users[] = $row['userid'];
			}
		}
		return $users;
	}
	return false;
	
}
?>