<?php 
/*+*************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@57183 crmv@112297 crmv@114144 */
require_once('modules/Settings/ProcessMaker/ProcessMakerUtils.php');

class Conditionals extends CRMEntity{
	
	var $permissions = array();
	
	function __construct()	{
		global $current_language;
		$this->log = LoggerManager::getLogger('Conditionals');
	}
	
	function existsConditionalPermissions($module, $focus) {
		global $adb;

		// check Process Conditionals
		$PMUtils = ProcessMakerUtils::getInstance();
		if ($PMUtils->todoFunctions) {
			$processConditionals = $PMUtils->getAllConditionals($focus->id);
			if (!empty($processConditionals)) return true;
		}

		// check standard rules
		$result = $this->getAllRules($module, getTabid($module), $focus->column_fields);
		return ($result && $adb->num_rows($result) > 0);
	}
	
	function getAllRules($module, $tabid, $column_fields) {
		global $adb, $table_prefix, $current_user;
		static $res = false;
		if (!$res) {
			//costruisco le condizioni in base a ruolo, ruolo e subordinati,gruppi.
			//ruolo:
			$conditions[] = "roles::".$current_user->roleid;
			//ruoli e subordinati:
			$subordinates=getRoleAndSubordinatesInformation($current_user->roleid);
			$parent_role=$subordinates[$current_user->roleid][1];
			if (!is_array($parent_role)){
				$parent_role = explode('::',$parent_role);
				foreach ($parent_role as $parent_role_value){
					$conditions[] = "rs::".$parent_role_value;
				}
			}
			//gruppi:
			require('user_privileges/requireUserPrivileges.php'); // crmv@39110
			if (is_array($current_user_groups)){
				foreach ($current_user_groups as $current_user_groups_value){
					$conditions[] = "groups::".$current_user_groups_value;
				}
			}
			//tutti:
			$conditions[] = 'ALL';
			$sql = "SELECT tbl_s_conditionals_rules.ruleid,
				tbl_s_conditionals_rules.chk_fieldname,
				tbl_s_conditionals_rules.chk_criteria_id,
				tbl_s_conditionals_rules.chk_field_value
				FROM tbl_s_conditionals 
				LEFT JOIN tbl_s_conditionals_rules ON tbl_s_conditionals.ruleid = tbl_s_conditionals_rules.ruleid 
				LEFT JOIN ".$table_prefix."_field ON ".$table_prefix."_field.fieldid = tbl_s_conditionals.fieldid
				WHERE tbl_s_conditionals.active = 1 
				and ".$table_prefix."_field.tabid = ?
				and ".$table_prefix."_field.fieldname in (".generateQuestionMarks($column_fields).")
				and tbl_s_conditionals.role_grp_check in (".generateQuestionMarks($conditions).")
				group by tbl_s_conditionals_rules.ruleid,
				tbl_s_conditionals_rules.chk_fieldname,
				tbl_s_conditionals_rules.chk_criteria_id,
				tbl_s_conditionals_rules.chk_field_value order by tbl_s_conditionals_rules.ruleid";
			$params[] = $tabid;
			$params[] = array_keys($column_fields);
			$params[] = $conditions;
			$res = $adb->pquery($sql,$params);
		}
		$res->MoveFirst();
		return $res;
	}
	function Initialize($module='',$tabid='',&$column_fields=''){	//crmv@112297
		global $adb, $table_prefix;
		
		if ($module == '' && $tabid == '' && $column_fields == '') return;
		
		$rule_check = false;
		$rule_success = true;
		$rules = array();
		
		// priority to Process Conditionals
		$PMUtils = ProcessMakerUtils::getInstance();
		$processConditionals = $PMUtils->getAllConditionals($column_fields['record_id']);
		if (!empty($processConditionals)) {
			$this->permissions = $PMUtils->getConditionalPermissions($processConditionals,$column_fields);
		} else {
			// get standard rules
			$conditional_permissions = array();
			$res = $this->getAllRules($module, $tabid, $column_fields);
			if ($res && $adb->num_rows($res)>0){
				//per ogni regola controllo se le condizioni sono TUTTE soddisfatte
				while ($row = $adb->fetchByAssoc($res,-1,false)){
					if ($rule_check && $rule_check != $row['ruleid']){
						if ($rule_success){
							$rules[] = $rule_check;
						}
						$rule_success = true;
					}
					$rule_check = $row['ruleid'];
					$moduleFieldValue = getTranslatedString($column_fields[$row['chk_fieldname']],$module);
					$chk_field_value = getTranslatedString($row['chk_field_value'],$module);
					if (!$this->check_rule($row['chk_criteria_id'],$moduleFieldValue,$chk_field_value)){
						$rule_success = false;
					}
				}
				if ($rule_success){
					$rules[] = $rule_check;
				}
			}
			if (!empty($rules)){
				$sql_permissions = "select tbl_s_conditionals.fieldid, {$table_prefix}_field.fieldname, min(read_perm) as read_perm, min(write_perm) as write_perm, min(mandatory) as mandatory 
						from tbl_s_conditionals
						inner join {$table_prefix}_field on tbl_s_conditionals.fieldid = {$table_prefix}_field.fieldid
						where ruleid in (".generateQuestionMarks($rules).") group by fieldid";
				$res_permissions = $adb->pquery($sql_permissions,$rules);
				if ($res_permissions && $adb->num_rows($res_permissions)>0){
					$i = 0;
					while ($row_permissions = $adb->fetchByAssoc($res_permissions,-1,false)){
						$this->setFieldConditionalPermissions($row_permissions, $i, $row_permissions['fieldname'], $conditional_permissions);
						$this->permissions[$row_permissions['fieldid']] = Array(
							'f2fp_visible'=>$row_permissions['read_perm'],
							'f2fp_editable'=>$row_permissions['write_perm'],
							'f2fp_mandatory'=>$row_permissions['mandatory'],
						);
					}
				}
			}
			// set in request cache
			$cache = RCache::getInstance();
			$cache->set('conditional_permissions', $conditional_permissions);
		}
	}
	function setFieldConditionalPermissions($perm, $i, $fieldname, &$permissions) {
		//if ($perm['FpovManaged'] == 1) {
			if ($i == 0) {
				$permissions[$fieldname]['readonly'] = 1;
				$permissions[$fieldname]['mandatory'] = false;
			}
			if ($perm['read_perm'] == 1) {
				if ($perm['write_perm'] == 1) {
					$readonly = 1;
					if ($perm['mandatory'] == 1) {
						$permissions[$fieldname]['mandatory'] = true;
					}
				} else {
					$readonly = 99;
				}
			} else {
				$readonly = 100;
			}
			//crmv@103826
			// the first conditional overwrite the standard permissions
			// or if there are more conditionals verified set the most restrictive rule
			if ($i == 0 || $readonly > $permissions[$fieldname]['readonly']) {
				$permissions[$fieldname]['readonly'] = $readonly;
			}
			// TODO if ($perm['FpovValueActive'] == 1) $permissions[$fieldname]['value'] = $perm['FpovValueStr'];
			//crmv@103826e
		//}
	}
	function check_rule($criteriaID,$moduleFieldValue,$criteriaFieldValue){
		$criteriaPassed = false;
		switch ($criteriaID){
			case 0:
				// <=
				$criteriaPassed = ($moduleFieldValue <= $criteriaFieldValue);
				break;
			case 1:
				// <
				$criteriaPassed = ($moduleFieldValue < $criteriaFieldValue);
				break;
			case 2:
				// >=
				$criteriaPassed = ($moduleFieldValue >= $criteriaFieldValue);
				break;
			case 3:
				// >
				$criteriaPassed = ($moduleFieldValue > $criteriaFieldValue);
				break;
			case 4:
				// ==
				$criteriaPassed = ($moduleFieldValue == $criteriaFieldValue);
				break;
			case 5:
				// !=
				$criteriaPassed = ($moduleFieldValue != $criteriaFieldValue);
				break;
			case 6:
				// includes
				$criteriaPassed = (stristr($moduleFieldValue, $criteriaFieldValue) !== false);
				break;
		}
		return $criteriaPassed;
	}	
 	/**
	* Invoked when special actions are performed on the module.
	* @param String Module name
	* @param String Event Type
	*/
	function vtlib_handler($moduleName, $eventType) {
 					
		require_once('include/utils/utils.php');			
		global $adb,$mod_strings,$table_prefix;
 		
 		if($eventType == 'module.postinstall') {			
			// Mark the module as Standard module
			$adb->pquery('UPDATE '.$table_prefix.'_tab SET customized=0 WHERE name=?', array($moduleName));
			
			
			$blockid = getSettingsBlockId('LBL_STUDIO');
			$fieldid = $adb->getUniqueID($table_prefix.'_settings_field');
			
			// changed, to put it after the workflows
			$sequence = 20;
			$seq_res = $adb->pquery("SELECT sequence FROM {$table_prefix}_settings_field WHERE blockid = ? AND name = ?", array($blockid, 'LBL_LIST_WORKFLOWS'));
			if ($adb->num_rows($seq_res) > 0) {
				$cur_seq = intval($adb->query_result_no_html($seq_res, 0, 'sequence'));
				// shift all the following ones
				$adb->pquery("UPDATE {$table_prefix}_settings_field SET sequence = sequence + 1 WHERE blockid = ? AND sequence > ?", array($blockid, $cur_seq));
				$sequence = $cur_seq+1;
			}

			$adb->pquery('INSERT INTO '.$table_prefix.'_settings_field(fieldid, blockid, name, iconpath, description, linkto, sequence) 
				VALUES (?,?,?,?,?,?,?)', array($fieldid, $blockid, 'LBL_COND_MANAGER', 'workflow.gif', 'LBL_COND_MANAGER_DESCRIPTION', 'index.php?module=Conditionals&action=index&parenttab=Settings', $sequence));
					
			
		} else if($eventType == 'module.disabled') {
		// TODO Handle actions when this module is disabled.
		} else if($eventType == 'module.enabled') {
		// TODO Handle actions when this module is enabled.
		} else if($eventType == 'module.preuninstall') {
		// TODO Handle actions when this module is about to be deleted.
		} else if($eventType == 'module.preupdate') {
		// TODO Handle actions before this module is updated.
		} else if($eventType == 'module.postupdate') {
		// TODO Handle actions after this module is updated.
		}
 	}
 	function getTransitionConditionalWorkflowModulesList() {
 		global $adb;
 		foreach (com_vtGetModules($adb) as $key=>$value){
 			$modules_list[] = Array($key,$key);
 		}
 		return $modules_list;
 	}
 	function we_checkUserRoleGrp($userobj,$roleGrpCheck) {
 		if($roleGrpCheck == 'ALL') return true;
 		$conditions = split("::",$roleGrpCheck);
 		switch($conditions[0]) {
 			case 'roles':
 				return ($userobj->roleid == $conditions[1]);
 				break;
 			case 'rs':
 				//crmv@18354
 				$subordinates=getRoleAndSubordinatesInformation($userobj->roleid);
 				$parent_role=$subordinates[$userobj->roleid][1];
 				$parent_rol_arr=explode('::',$parent_role);
 				if(in_array($conditions[1],$parent_rol_arr)) return true;
 				//crmv@18354e
 				break;
 			case 'groups':
 				require('user_privileges/user_privileges_'.$userobj->id.'.php');
 					
 				if(sizeof($current_user_groups) > 0)
 				{
 					foreach ($current_user_groups as $grpid)
 					{
 						if($grpid == $conditions[1]) return true;
 					}
 				}
 				return false;
 				break;
 			default:
 				//@todo - gestione errori
 				return true;
 		}
 		return true;
 	}

 	function we_checkCriteria($criteriaID,$moduleFieldValue,$criteriaFieldValue,$roleGrpCheck="ALL") {
 		global $current_user;
 		$criteriaPassed = false;

 		switch ($criteriaID)
 		{
 			case 0:
 				// <=
 				$criteriaPassed = ($moduleFieldValue <= $criteriaFieldValue) && $this->we_checkUserRoleGrp($current_user,$roleGrpCheck);
 				break;
 			case 1:
 				// <
 				$criteriaPassed = ($moduleFieldValue < $criteriaFieldValue) && $this->we_checkUserRoleGrp($current_user,$roleGrpCheck);
 				break;
 			case 2:
 				// >=
 				$criteriaPassed = ($moduleFieldValue >= $criteriaFieldValue) && $this->we_checkUserRoleGrp($current_user,$roleGrpCheck);
 				break;
 			case 3:
 				// >
 				$criteriaPassed = ($moduleFieldValue > $criteriaFieldValue) && $this->we_checkUserRoleGrp($current_user,$roleGrpCheck);
 				break;
 			case 4:
 				// ==
 				$criteriaPassed = ($moduleFieldValue == $criteriaFieldValue) && $this->we_checkUserRoleGrp($current_user,$roleGrpCheck);
 				break;
 			case 5:
 				// !=
 				$criteriaPassed = ($moduleFieldValue != $criteriaFieldValue) && $this->we_checkUserRoleGrp($current_user,$roleGrpCheck);
 				break;
 			case 6:
 				// includes
 				$criteriaPassed = (stristr($moduleFieldValue, $criteriaFieldValue) !== false) && $this->we_checkUserRoleGrp($current_user,$roleGrpCheck);
 				break;
 		}
 		return $criteriaPassed;
 	}

 	function _wui_check_rules($result,$fieldid,$module,$column_fields) {
 		global $adb;

 		if($result && $adb->num_rows($result)>0) {
 			$num_rows = $adb->num_rows($result);
 			for ($k = 0; $k < $num_rows; $k++) {
 				$chk_fieldname = $adb->query_result($result, $k, 'chk_fieldname');
 				$chk_criteria_id = $adb->query_result($result, $k, 'chk_criteria_id');
 				$chk_field_value = $adb->query_result($result, $k, 'chk_field_value');
 				$chk_role_grp = $adb->query_result($result, $k, 'role_grp_check');
 				if(array_key_exists($chk_fieldname,$column_fields)) {
 					$moduleFieldValue = $column_fields["$chk_fieldname"];
 					//crmv@9960
 					$moduleFieldValue = getTranslatedString($moduleFieldValue);
 					$chk_field_value = getTranslatedString($chk_field_value);
 					//crmv@9960e
 					if($this->we_checkCriteria($chk_criteria_id,$moduleFieldValue,$chk_field_value,$chk_role_grp)) {
 						//					if ($fieldid == '804')
 						//						echo "CONTINUO IL CICLO: $chk_fieldname $chk_criteria_id $chk_field_value<br />";
 						continue;
 					}
 					else {
 						//					if ($fieldid == '804')
 						//						echo "ESCO: $chk_fieldname $chk_criteria_id $chk_field_value<br />";
 						return null;
 					}
 				}
 			}
 			$read_perm  = $adb->query_result($result, 0, 'read_perm');
 			$write_perm = $adb->query_result($result, 0, 'write_perm');
 			$mandatory_perm = $adb->query_result($result, 0, 'mandatory');
 			if($write_perm == 1) $read_perm = 1;

 			//		if ($fieldid == '825')
 			//			print_r(Array('f2fp_visible'=>$read_perm,'f2fp_editable'=>$write_perm,'f2fp_mandatory'=>$mandatory_perm));

 			return Array('f2fp_visible'=>$read_perm,'f2fp_editable'=>$write_perm,'f2fp_mandatory'=>$mandatory_perm);
 		}
 		return null; // no rules defined - calles need to check null value
 	}

 	function wui_get_FieldPermissionsOnFieldValue($fieldid,$module,$column_fields) {
 		global $adb,$current_user,$table_prefix;

 		require('user_privileges/requireUserPrivileges.php'); // crmv@39110
 		require('user_privileges/sharing_privileges_'.$current_user->id.'.php');

 		$q = "SELECT DISTINCT ruleid FROM tbl_s_conditionals
			INNER JOIN ".$table_prefix."_field ON ".$table_prefix."_field.fieldid = tbl_s_conditionals.fieldid
			INNER JOIN ".$table_prefix."_tab ON ".$table_prefix."_field.tabid = ".$table_prefix."_tab.tabid
			WHERE ".$table_prefix."_tab.name = '$module'";
 		$res = $adb->query($q);
 		$rules_returned = array();
 		if ($res && $adb->num_rows($res) > 0)
 		while($row=$adb->fetchByAssoc($res,-1,false)){
 			$fix_query = "SELECT chk_fieldname,chk_criteria_id,chk_field_value,read_perm,write_perm,mandatory,role_grp_check
	    				FROM tbl_s_conditionals
	    				LEFT JOIN tbl_s_conditionals_rules ON tbl_s_conditionals.ruleid = tbl_s_conditionals_rules.ruleid
	    				WHERE tbl_s_conditionals.ruleid = ".$row['ruleid'];

 			// check rules fro roleandsubordinates
 			$parnet_role_array = explode("::",$current_user_parent_role_seq);
 			for($r=0;$r<count($parnet_role_array);$r++) {
 				$query = $fix_query." and active = 1 and fieldid = $fieldid and role_grp_check = 'rs::".$parnet_role_array[$r]."' order by sequence asc";
 				$result = $adb->query($query);
 				$rules = $this->_wui_check_rules($result,$fieldid,$module,$column_fields);
 				if($rules != null) break;
 			}
 			if($rules != null) {
 				if ($rules['f2fp_visible'] == 1)
 				$rules_returned['f2fp_visible'] = 1;
 				elseif ($rules_returned['f2fp_visible'] != 1)
 				$rules_returned['f2fp_visible'] = 0;

 				if ($rules['f2fp_editable'] == 1)
 				$rules_returned['f2fp_editable'] = 1;
 				elseif ($rules_returned['f2fp_editable'] != 1)
 				$rules_returned['f2fp_editable'] = 0;

 				if ($rules['f2fp_mandatory'] == 1)
 				$rules_returned['f2fp_mandatory'] = 1;
 				elseif ($rules_returned['f2fp_mandatory'] != 1)
 				$rules_returned['f2fp_mandatory'] = 0;
 			}

 			// no rules then check for role
 			$query = $fix_query." and active = 1 and fieldid = $fieldid and role_grp_check = 'roles::".$current_user->roleid."' order by sequence asc";
 			$result = $adb->query($query);
 			$rules = $this->_wui_check_rules($result,$fieldid,$module,$column_fields);
 			if($rules != null) {
 				if ($rules['f2fp_visible'] == 1)
 				$rules_returned['f2fp_visible'] = 1;
 				elseif ($rules_returned['f2fp_visible'] != 1)
 				$rules_returned['f2fp_visible'] = 0;

 				if ($rules['f2fp_editable'] == 1)
 				$rules_returned['f2fp_editable'] = 1;
 				elseif ($rules_returned['f2fp_editable'] != 1)
 				$rules_returned['f2fp_editable'] = 0;

 				if ($rules['f2fp_mandatory'] == 1)
 				$rules_returned['f2fp_mandatory'] = 1;
 				elseif ($rules_returned['f2fp_mandatory'] != 1)
 				$rules_returned['f2fp_mandatory'] = 0;
 			}
 			// no rules then check for groups
 			$user_groups = new GetUserGroups();
 			$user_groups->getAllUserGroups($current_user->id);
 			for($g=0;$g<count($user_groups->user_groups);$g++) {
 				$query = $fix_query." and active = 1 and fieldid = $fieldid and role_grp_check = 'groups::". $user_groups->user_groups[$g]."' order by sequence asc";
 				$result = $adb->query($query);
 				$rules = $this->_wui_check_rules($result,$fieldid,$module,$column_fields);
 				if($rules != null) break;
 			}
 			if($rules != null) {
 				if ($rules['f2fp_visible'] == 1)
 				$rules_returned['f2fp_visible'] = 1;
 				elseif ($rules_returned['f2fp_visible'] != 1)
 				$rules_returned['f2fp_visible'] = 0;

 				if ($rules['f2fp_editable'] == 1)
 				$rules_returned['f2fp_editable'] = 1;
 				elseif ($rules_returned['f2fp_editable'] != 1)
 				$rules_returned['f2fp_editable'] = 0;

 				if ($rules['f2fp_mandatory'] == 1)
 				$rules_returned['f2fp_mandatory'] = 1;
 				elseif ($rules_returned['f2fp_mandatory'] != 1)
 				$rules_returned['f2fp_mandatory'] = 0;
 			}
 			// no rules -> check rules for all ------------------------------------------------------------------------------------------
 			if($rules == null) {
 				$query = $fix_query." and active = 1 and fieldid = $fieldid and role_grp_check = 'ALL' order by sequence asc";
 				$result = $adb->query($query);
 				$rules = $this->_wui_check_rules($result,$fieldid,$module,$column_fields);
 			}
 			if($rules != null) {
 				if ($rules['f2fp_visible'] == 1)
 				$rules_returned['f2fp_visible'] = 1;
 				elseif ($rules_returned['f2fp_visible'] != 1)
 				$rules_returned['f2fp_visible'] = 0;

 				if ($rules['f2fp_editable'] == 1)
 				$rules_returned['f2fp_editable'] = 1;
 				elseif ($rules_returned['f2fp_editable'] != 1)
 				$rules_returned['f2fp_editable'] = 0;

 				if ($rules['f2fp_mandatory'] == 1)
 				$rules_returned['f2fp_mandatory'] = 1;
 				elseif ($rules_returned['f2fp_mandatory'] != 1)
 				$rules_returned['f2fp_mandatory'] = 0;
 			}
 		}
 		return $rules_returned;
 	}

 	//------------------------------------------------------------------
 	function wui_getFpofvListViewHeader() {
 		global $currentModule;
 		$header = Array("","LBL_FPOFV_RULE_NAME","LBL_MODULE","","","","","","","","FpofvChkRoleGroup","LBL_ACTION");
 		for($i=0;$i<count($header);$i++) {
 			$header[$i] = getTranslatedString($header[$i],$currentModule);
 		}
 		return $header;
 	}

 	//------------------------------------------------------------------
	function wui_getFpofvListViewEntries($fields_columnnames) {
 		global $adb,$mod_strings,$app_strings,$table_prefix;

 		$roleDetails=getAllRoleDetails();
 		unset($roleDetails['H1']);
 		$grpDetails=getAllGroupName();
 		
 		
 		// crmv@77249
 		$wherecond = "";
 		if (!empty($_REQUEST['formodule'])) {
			$tabid = intval(getTabid($_REQUEST['formodule']));
			$wherecond = " WHERE ".$table_prefix."_tab.tabid= '$tabid'";
 		}
 		
 		$query = "select
			    distinct
				ruleid, 
				name,
				description,
				role_grp_check 
				from tbl_s_conditionals 
				inner join ".$table_prefix."_field on ".$table_prefix."_field.fieldid = tbl_s_conditionals.fieldid
				inner join ".$table_prefix."_tab on ".$table_prefix."_field.tabid = ".$table_prefix."_tab.tabid
				$wherecond
				group by ruleid, name, description, role_grp_check 
				order by description";
		// crmv@77249e

 		$result = $adb->query($query);
 		$ret_val = Array();
 		if($result && $adb->num_rows($result)>0) {
 			$num_rows = $adb->num_rows($result);
 			for ($k = 0; $k < $num_rows; $k++) {
 					
 				$ret_val[$k][1] = $adb->query_result($result, $k, 'description');
 				$ret_val[$k][2] = $app_strings[$adb->query_result($result, $k, 'name')];
 					
 				$role_grp_check = $adb->query_result($result, $k, 'role_grp_check');
 				if($role_grp_check == "ALL")
 				$role_grp_string = $mod_strings['NO_CONDITIONS'];
 				$rolefound = false;
 				foreach($roleDetails as $roleid=>$rolename)
 				{
 					if('roles::'.$roleid == $role_grp_check) {
 						$role_grp_string = $mod_strings['LBL_ROLES']."::".$rolename[0];
 						$rolefound = true;
 						break;
 					}
 				}
 				if(!$rolefound)
 				foreach($roleDetails as $roleid=>$rolename)
 				{
 					if('rs::'.$roleid == $role_grp_check) {
 						$role_grp_string = $mod_strings['LBL_ROLES_SUBORDINATES']."::".$rolename[0];
 						$rolefound = true;
 						break;
 					}
 				}
 				if(!$rolefound)
 				foreach($grpDetails as $groupid=>$groupname)
 				{
 					if('groups::'.$groupid == $role_grp_check) {
 						$role_grp_string = $mod_strings['LBL_GROUP']."::".$groupname;
 						$rolefound = true;
 						break;
 					}
 				}

 				$ret_val[$k][12] = $role_grp_string;
 					
 				$ruleid = $adb->query_result($result, $k, 'ruleid');
 				
 				// crmv@77249
				if ($_REQUEST['included'] == true) {
					$params = array(
						'included' => 'true',
						'skip_vte_header' => 'true',
						'skip_footer' => 'true',
						'formodule' => $_REQUEST['formodule']
					);
					$otherParams = "&".http_build_query($params);
				}
				// crmv@77249e
 				
 				$edit_lnk = "<a href='index.php?module=Conditionals&action=EditView&ruleid=$ruleid&parenttab=Settings$otherParams'>".$app_strings['LNK_EDIT']."</a>";
 				$del_lnk = "<a href='index.php?module=Conditionals&action=Delete&ruleid=$ruleid&parenttab=Settings$otherParams'>".$app_strings['LNK_DELETE']."</a>";
 				$ret_val[$k][13] = $edit_lnk."&nbsp;|&nbsp;".$del_lnk;
 			}
 		}
 		return $ret_val;
 	}

 	function getRulesInfo($ruleid) {
 		global $adb,$table_prefix;
 		$info = array();

 		$res = $adb->query("SELECT
						tbl_s_conditionals.*,
						name as tablabel
						FROM tbl_s_conditionals 
						INNER JOIN ".$table_prefix."_field ON ".$table_prefix."_field.fieldid = tbl_s_conditionals.fieldid
						INNER JOIN ".$table_prefix."_tab ON ".$table_prefix."_field.tabid = ".$table_prefix."_tab.tabid
 		where ruleid = $ruleid");
 		$info = $adb->fetchByAssoc($res,-1,false);

 		$res = $adb->query("select chk_fieldname,chk_criteria_id,chk_field_value
 		from tbl_s_conditionals_rules
 		where ruleid = $ruleid");
 		while($row=$adb->fetchByAssoc($res,-1,false)) {
 			$info['rules'][] = $row;
 		}
 		return $info;
 	}

 	function wui_getFpofvData($ruleid='',$module) {
 		global $adb,$mod_strings,$table_prefix;

 		if ($ruleid == '') $ruleid = 0;
 		$tabid = getTabid($module);
 		$query = "select
				tbl2.fieldid, 
				tbl2.fieldname, 
				tbl2.name as module, 
				tbl2.fieldlabel, 
				tbl2.uitype,
				 ".$adb->database->IfNull('read_perm',0)." as read_perm, 
				 ".$adb->database->IfNull('write_perm',0)." as write_perm, 
				 ".$adb->database->IfNull('mandatory',0)." as mandatory, 
				tbl2.sequence,  
				1 as active, 
				 ".$adb->database->IfNull('managed',0)." as managed,
				".$table_prefix."_blocks.blocklabel as blocklabel
				
				 from (
					select 
					tbl_s_conditionals.* , 
					".$table_prefix."_tab.name, 
					".$table_prefix."_field.fieldlabel, 
					".$table_prefix."_field.fieldname, 
					1 as managed 
					from tbl_s_conditionals
						inner join ".$table_prefix."_field on tbl_s_conditionals.fieldid = ".$table_prefix."_field.fieldid
						inner join ".$table_prefix."_tab on ".$table_prefix."_field .tabid = ".$table_prefix."_tab.tabid 
 		where
 		ruleid = $ruleid
 		) tbl1
 		right outer join (
 		select ".$table_prefix."_field.*,  ".$table_prefix."_tab.name from ".$table_prefix."_field inner join ".$table_prefix."_tab on ".$table_prefix."_field .tabid = ".$table_prefix."_tab.tabid where ".$table_prefix."_field.tabid = $tabid
 		) tbl2 on tbl1.fieldid = tbl2.fieldid
 		inner join ".$table_prefix."_blocks on tbl2.block = ".$table_prefix."_blocks.blockid
				order by ".$table_prefix."_blocks.sequence, tbl2.sequence";

 		$result = $adb->query($query);
 		$ret_val = Array();
 		if($result) {
 			for($i=0;$i<$adb->num_rows($result);$i++) {
 				//crmv@115268
 				$uitype = $adb->query_result($result,$i,'uitype');
				$HideFpovValue = false;
				$HideFpovManaged = false;
 				$HideFpovReadPermission = false;
 				$HideFpovWritePermission = false;
				$HideFpovMandatoryPermission = false;
 				if ($uitype == 220) {
 					$HideFpovValue = true;
					$HideFpovMandatoryPermission = true;
 				}
 				$ret_val[] = array(
 					'FpofvFieldid' => $adb->query_result($result, $i, 'fieldid'),
 					'ModuleField' => $adb->query_result($result, $i, 'chk_fieldname'),
 					'Module' => $adb->query_result($result, $i, 'module'),
 					'FpovReadPermission' => $adb->query_result($result, $i, 'read_perm'),
 					'FpovWritePermission' => $adb->query_result($result, $i, 'write_perm'),
 					'FpovManaged' => $adb->query_result($result, $i, 'managed'),
 					'FpovMandatoryPermission' => $adb->query_result($result, $i, 'mandatory'),
 					'FpofvSequence' => $adb->query_result($result, $i, 'sequence'),
 					'FpofvActive' => $adb->query_result($result, $i, 'active'),
 					'FpofvBlockLabel' => $adb->query_result($result, $i, 'blocklabel'),
 					'FpofvChkFieldLabel' => $adb->query_result($result, $i, 'fieldlabel'),
 					'FpofvChkFieldName' => $adb->query_result($result, $i, 'fieldname'),
 					'uitype' => $uitype, //crmv@112297 in future add here the columns of the table here
					'HideFpovValue'=>$HideFpovValue,
					'HideFpovManaged'=>$HideFpovManaged,
					'HideFpovReadPermission'=>$HideFpovReadPermission,
					'HideFpovWritePermission'=>$HideFpovWritePermission,
					'HideFpovMandatoryPermission'=>$HideFpovMandatoryPermission,
 				);
 				//crmv@115268e
 			}
 			return $ret_val;
 		}
 		return null;
 	}

 	function wui_getCriteriaLabel($criteriaID) {
 		global $mod_strings;
 		switch ($criteriaID)
 		{
 			case 0:
 				return $mod_strings['LBL_CRITERIA_VALUE_LESS_EQUAL'];
 				// <=
 				break;
 			case 1:
 				// <
 				return $mod_strings['LBL_CRITERIA_VALUE_LESS_THAN'];
 				break;
 			case 2:
 				// >=
 				return $mod_strings['LBL_CRITERIA_VALUE_MORE_EQUAL'];
 				break;
 			case 3:
 				// >
 				return $mod_strings['LBL_CRITERIA_VALUE_MORE_THAN'];
 				break;
 			case 4:
 				// ==
 				return $mod_strings['LBL_CRITERIA_VALUE_EQUAL'];
 				break;
 			case 5:
 				// !=
 				return $mod_strings['LBL_CRITERIA_VALUE_NOT_EQUAL'];
 				break;
 			case 6:
 				// includes
 				return $mod_strings['LBL_CRITERIA_VALUE_INCLUDES'];
 				break;
 		}
 		return $criteriaID;
 	}

 	//------------------------------------------------------------------------------------------------
	//crmv@101719
	function getStatusBlockRules($module='',$column_fields=''){
		global $current_language, $adb, $current_user, $table_prefix;

		if ($module == '' && $column_fields == '' ) return;
		
		$rules=array();
		$tabid=getTabid($module);

		//costruisco le condizioni in base a ruolo, ruolo e subordinati,gruppi.
		//ruolo:
		$conditions[] = "roles::".$current_user->roleid;
		//ruoli e subordinati:
		$subordinates=getRoleAndSubordinatesInformation($current_user->roleid);
		$parent_role=$subordinates[$current_user->roleid][1];
		if (!is_array($parent_role)){
			$parent_role = explode('::',$parent_role);
			foreach ($parent_role as $parent_role_value){
				$conditions[] = "rs::".$parent_role_value;
			}
		}
		//gruppi:
		require('user_privileges/requireUserPrivileges.php'); // crmv@39110
		if (is_array($current_user_groups)){
			foreach ($current_user_groups as $current_user_groups_value){
				$conditions[] = "groups::".$current_user_groups_value;
			}
		}
		//tutti:
		$conditions[] = 'ALL';
		$sql = "SELECT tbl_s_conditionals_rules.ruleid,
			tbl_s_conditionals_rules.chk_fieldname,
			tbl_s_conditionals_rules.chk_criteria_id,
			tbl_s_conditionals_rules.chk_field_value
			FROM tbl_s_conditionals 
			LEFT JOIN tbl_s_conditionals_rules ON tbl_s_conditionals.ruleid = tbl_s_conditionals_rules.ruleid 
			left join ".$table_prefix."_field ON (".$table_prefix."_field.fieldid = tbl_s_conditionals.fieldid OR tbl_s_conditionals_rules.chk_fieldname = ".$table_prefix."_field.fieldname)
			WHERE tbl_s_conditionals.active = 1 
			and ".$table_prefix."_field.tabid = ?
			and ".$table_prefix."_field.fieldname in (".generateQuestionMarks($column_fields).")
			and tbl_s_conditionals.role_grp_check in (".generateQuestionMarks($conditions).")
			group by tbl_s_conditionals_rules.ruleid,
			tbl_s_conditionals_rules.chk_fieldname,
			tbl_s_conditionals_rules.chk_criteria_id,
			tbl_s_conditionals_rules.chk_field_value order by tbl_s_conditionals_rules.ruleid";
		$params[] = $tabid;
		$params[] = array_keys($column_fields);
		$params[] = $conditions;
		$res = $adb->pquery($sql,$params);
		$rule_check = false;
		$rule_success = true;
		if ($res && $adb->num_rows($res)>0){
			//per ogni regola controllo se le condizioni sono TUTTE soddisfatte
			while ($row = $adb->fetchByAssoc($res,-1,false)){
				if ($rule_check && $rule_check != $row['ruleid']){
					if ($rule_success){
						$rules[] = $rule_check;
					}
					$rule_success = true;
				}
				$rule_check = $row['ruleid'];
				$moduleFieldValue = getTranslatedString($column_fields[$row['chk_fieldname']],$module);
				$chk_field_value = getTranslatedString($row['chk_field_value'],$module);
				if (!$this->check_rule($row['chk_criteria_id'],$moduleFieldValue,$chk_field_value)){
					$rule_success = false;
				}
			}
			if ($rule_success){
				$rules[] = $rule_check;
			}
		}
		
		return $rules;
	}
	//crmv@101719e
	
 	function wui_sql_restric_status_on_mandatory_fields($vtigerobj,$module,$fieldname,$status,$rule2check=array()) { //crmv@101719
 		global $adb,$table_prefix;
 		$ret_val[0] = false;
 		$tabid = getTabid($module);
 		$status = getTranslatedString($status,$module);	//crmv@9960		//crmv@17935
		$params = array();
 		$query = "SELECT ".$table_prefix."_field.fieldname AS module_fieldname,
			  ".$table_prefix."_field.fieldlabel     AS module_fieldlabel,
			  ".$table_prefix."_field.uitype         AS field_uitype,
			  ".$table_prefix."_field.typeofdata     AS field_typeofdata,
			  tbl_s_conditionals_rules.*
			FROM tbl_s_conditionals
			INNER JOIN ".$table_prefix."_field ON ".$table_prefix."_field.fieldid = tbl_s_conditionals.fieldid
			INNER JOIN tbl_s_conditionals_rules on tbl_s_conditionals_rules.ruleid = tbl_s_conditionals.ruleid
			WHERE chk_fieldname = '".$fieldname."' and chk_field_value = '".$status."' and mandatory = 1";
		
		//crmv@101719
		if(!empty($rule2check)){
			$query .= " and tbl_s_conditionals.ruleid in (".generateQuestionMarks($rule2check).")";
			array_push($params,$rule2check);
		}
		//crmv@101719e
 		//@todo - vincolare la query al profilo
 		$index = 1;
 		$result = $adb->pquery($query,$params); //crmv@101719
 		if($result && $adb->num_rows($result)>0) {
 			$num_rows = $adb->num_rows($result);
 			for ($k = 0; $k < $num_rows; $k++) {
 				$module_fieldname = $adb->query_result($result, $k, 'module_fieldname');
 				$module_fieldlabel = $adb->query_result($result, $k, 'module_fieldlabel');
 				$chk_fieldname = $adb->query_result($result, $k, 'chk_fieldname');
 				$chk_criteria_id = $adb->query_result($result, $k, 'chk_criteria_id');
 				$chk_field_value = $adb->query_result($result, $k, 'chk_field_value');
 				$chk_role_grp = $adb->query_result($result, $k, 'role_grp_check');
 				$field_uitype = $adb->query_result($result, $k, 'field_uitype');
 				$field_typeofdata = $adb->query_result($result, $k, 'field_typeofdata');
 				if(array_key_exists($chk_fieldname,$vtigerobj->column_fields)) {
 					$moduleFieldValue = $vtigerobj->column_fields["$chk_fieldname"];
 					//crmv@9960		//crmv@17935
 					$moduleFieldValue = getTranslatedString($moduleFieldValue,$module);
 					$chk_field_value = getTranslatedString($chk_field_value,$module);
 					//crmv@9960e	//crmv@17935e
 					if($this->we_checkCriteria($chk_criteria_id,$moduleFieldValue,$chk_field_value,$chk_role_grp)) {
 						if ($this->check_value_field($vtigerobj->column_fields[$module_fieldname],$field_typeofdata,$field_uitype)) {}	//crmv@17935
 						else {
 							$ret_val[0] = true;
 							$ret_val[$index] = Array($module_fieldname,$module_fieldlabel);
 							$index++;
 						}
 					}
 				}
 			}
 		}
 		return $ret_val;
 	}

 	function check_value_field($value,$typeofdata,$uitype){
 		$type_arr = split("~",$typeofdata);
 		$typeofdata = $type_arr[0];
 		if (in_array($uitype,Array(10,53))){
 			if ($value == '0'){
 				$value = '';
 			}
 		}
 		//crmv@17935
 		if (in_array($typeofdata,Array('N','I')))
 		if (ceil($value) == 0) return false;
 		if (in_array($uitype,Array(15,16,111)))
 		if (in_array(trim($value),array('--Nessuno--','--None--','--nd--'))) return false;
 		if ($value == '')
 		return false;
 		//crmv@17935e
 		return true;
 	}

 	//performance_conditiona_listview - i
 	function wui_get_FieldPermissionsOnFieldValueFields($module,$column_fields,$conditional_fieldid) {
 		$rules = Array();
 		foreach($conditional_fieldid as $fieldid) {
 			$rules[$fieldid] = $this->wui_get_FieldPermissionsOnFieldValue($fieldid,$module,$column_fields);
 		}
 		return $rules;
 	}

 	function getConditionalFields($module) {
 		global $adb,$table_prefix;
 		//crmv@18039
 		$query = "SELECT
			  ".$table_prefix."_field.tablename,
			  ".$table_prefix."_field.columnname,
			  ".$table_prefix."_field.fieldname,
			  ".$table_prefix."_field.fieldlabel
			  FROM ".$table_prefix."_field 
			  INNER JOIN tbl_s_conditionals_rules ON ".$table_prefix."_field.fieldname = tbl_s_conditionals_rules.chk_fieldname
			  INNER JOIN ".$table_prefix."_tab
			    ON ".$table_prefix."_field.tabid = ".$table_prefix."_tab.tabid
			WHERE ".$table_prefix."_tab.name = '$module'";
 		$result = $adb->query($query);
 		$ret_arr = false;
 		//crmv@18039 end
 		while($row=$adb->fetchByAssoc($result,-1,false)){
 			$ret_arr[] = $row;
 		}
 		return $ret_arr;
 	}
 	//performance_conditiona_listview - e
}
