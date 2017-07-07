<?php
/* crmv@33448 crmv@55708 crmv@62394 */
global $adb, $table_prefix, $current_user, $theme;
global $app_strings;
require_once('Smarty_setup.php');
require_once('modules/SDK/src/CalendarTracking/CalendarTrackingUtils.php');

if ($_REQUEST['mode'] == 'save_state') {
	$record = intval($_REQUEST['record']);
	switch ($_REQUEST['type']) {
		case 'start': 
			if (getActiveTracked() === false) {
				activateTrack($record);
			}
			break;
		case 'pause': 
			pauseTrack($record);
			break;
		case 'stop': 
			stopTrack($record);
			break;
	}
}

$list = array();
$result = $adb->pquery(
	"SELECT ct.id, ct.record
	FROM {$table_prefix}_cal_tracker ct
	INNER JOIN {$table_prefix}_crmentity c ON c.crmid = ct.record 
	WHERE c.deleted = 0 AND ct.userid = ?",array($current_user->id)
);
if ($result && $adb->num_rows($result) > 0) {
	$id = $adb->query_result_no_html($result,0,'id');
	$record = $adb->query_result_no_html($result,0,'record');
	
	$entity_type = getSalesEntityType($record);
	
	if (Vtlib_isModuleActive($entity_type)) {
	
		$entitd_name = array_values(getEntityName($entity_type,$record));
		// Module Sequence Numbering
		$mod_seq_field = getModuleSequenceField($entity_type);
		if ($mod_seq_field != null) {
			$focus = CRMEntity::getInstance($entity_type);
			$focus->retrieve_entity_info($record,$entity_type);
			$mod_seq_id = $focus->column_fields[$mod_seq_field['name']];
		} else {
			$mod_seq_id = $record;
		}
		$list[$record] = array(
			'number'=>$mod_seq_id,
			'name'=>$entitd_name[0],
			'module'=>$entity_type,
			'entity_type'=>getSingleModuleName($entity_type,$record),
			'enable'=>true
		);
	}
}

// crmv@63349 - removed temporary table
$result = $adb->pquery(
	"SELECT ctl.record
	FROM {$table_prefix}_cal_tracker_log ctl
	INNER JOIN {$table_prefix}_crmentity c ON c.crmid = ctl.record
	INNER JOIN (
		SELECT MAX(id) AS id 
		FROM {$table_prefix}_cal_tracker_log 
		WHERE userid = ? 
		GROUP BY record
	) subctl ON subctl.id = ctl.id
	WHERE c.deleted = 0 AND ctl.status = ?",
	array($current_user->id, 'Paused')
);
// crmv@63349e
if ($result && $adb->num_rows($result) > 0) {
	while($row = $adb->fetchByAssoc($result, -1, false)) {
		$record = $row['record'];
		$entity_type = getSalesEntityType($record);
		
		if (!Vtlib_isModuleActive($entity_type)) continue;
		
		$entitd_name = array_values(getEntityName($entity_type,$record));
		// Module Sequence Numbering
		$mod_seq_field = getModuleSequenceField($entity_type);
		if ($mod_seq_field != null) {
			$focus = CRMEntity::getInstance($entity_type);
			$focus->retrieve_entity_info($record,$entity_type);
			$mod_seq_id = $focus->column_fields[$mod_seq_field['name']];
		} else {
			$mod_seq_id = $record;
		}
		$list[$record] = array(
			'number'=>$mod_seq_id,
			'name'=>$entitd_name[0],
			'module'=>$entity_type,
			'entity_type'=>getSingleModuleName($entity_type,$record),
			'enable'=>false
		);
	}
}

$active_tracked = getActiveTracked();

// ----- display section ------

$smarty = new vtigerCRM_Smarty;
$smarty->assign('THEME',$theme);
$smarty->assign('APP',$app_strings);

$smarty->assign('ID',$record);
$smarty->assign('RECORD',$record);
$smarty->assign('ACTIVE_TRACKED',$active_tracked);
$smarty->assign('TRACKLIST',$list);
$smarty->assign('TICKETS_AVAILABLE', Vtlib_isModuleActive('HelpDesk'));

$smarty->display('modules/SDK/src/CalendarTracking/TrackingList.tpl');