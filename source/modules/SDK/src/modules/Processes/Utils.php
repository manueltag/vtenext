<?php
/* crmv@103534 crmv@105685 */
function advQueryProcesses($module = '') {
	global $current_user, $table_prefix;
	
	$user_role = $current_user->column_fields['roleid'];
	$user_role_info = getRoleInformation($user_role);
	$current_user_parent_role_seq = $user_role_info[$user_role][1];
	
	$inst = CRMEntity::getInstance($module);
	$query = $inst->getNonAdminAccessQuery($module, $current_user, $current_user_parent_role_seq, array());
	
	$filter = "or exists (select id from {$table_prefix}_running_processes_logs where running_process = {$table_prefix}_processes.running_process and userid in (".$query."))";
	return $filter;
}
function advPermProcesses($module, $actionname, $record_id='') {
	if (!empty($record_id)) {
		global $current_user, $adb, $table_prefix;
		if ($actionname == 'DetailViewAjax' && $_REQUEST['ajxaction'] == 'SHOWGRAPH') $actionname = 'DetailView';	//crmv@109685 the same permissions of the DetailView
		$user_role = $current_user->column_fields['roleid'];
		$user_role_info = getRoleInformation($user_role);
		$current_user_parent_role_seq = $user_role_info[$user_role][1];
		
		$inst = CRMEntity::getInstance($module);
		$query = $inst->getNonAdminAccessQuery($module, $current_user, $current_user_parent_role_seq, array());
		$query = "select id from {$table_prefix}_running_processes_logs where running_process = ? and userid in (".$query.")";
		$result = $adb->pquery($query, array(getSingleFieldValue($inst->table_name, 'running_process', $inst->table_index, $record_id)));
		if ($result && $adb->num_rows($result) > 0) {
			$processMakerUtils = ProcessMakerUtils::getInstance();
			if ($processMakerUtils->edit_permission_mode == 'all') {
				$action_permission = isPermitted($module,$actionname,'',false);
				if ($action_permission == 'yes') return 'yes';
			} elseif ($processMakerUtils->edit_permission_mode == 'assigned') {
				$actionid = getActionid($actionname);
				if (in_array($actionid,array(0,1,2)) && $current_user->id != getSingleFieldValue($inst->entity_table, 'smownerid', $inst->tab_name_index[$inst->entity_table], $record_id)) {
					return 'no';
				} else {
					$action_permission = isPermitted($module,$actionname,'',false);
					if ($action_permission == 'yes') return 'yes';
				}
			}
		}
	}
	return '';
}