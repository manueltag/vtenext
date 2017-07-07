<?php
/* crmv@101683 */
if (!function_exists('getCustomUserList')) {
	function getCustomUserList($module, $fieldname) {
		global $adb, $table_prefix, $showfullusername;
		$user_array = array();
		$fieldinfo = $adb->pquery("select info from {$table_prefix}_field
			inner join {$table_prefix}_fieldinfo on {$table_prefix}_field.fieldid = {$table_prefix}_fieldinfo.fieldid
			where tabid = ? and fieldname = ?", array(getTabid($module),$fieldname));
		if ($fieldinfo && $adb->num_rows($fieldinfo) > 0) {
			$info = Zend_Json::decode($adb->query_result_no_html($fieldinfo,0,'info'));
			$info = $info['users'];	//crmv@106857
			if (!empty($info)) {
				foreach($info as $id) {
					$user_array[$id] = getUserName($id,$showfullusername);
				}
			}
		}
		return $user_array;
	}
}

global $sdk_mode, $current_user, $showfullusername;
switch($sdk_mode) {
	case 'detail':
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$user_id = $col_fields[$fieldname];
		$user_name = getUserName($user_id);
		if(is_admin($current_user))
		{
			$label_fld[] = '<a href="index.php?module=Users&action=DetailView&record='.$user_id.'">'.$user_name.'</a>';
		}
		else
		{
			$label_fld[] = $user_name;
		}
		if (isset($dynaform_info['users'])) {
			$users_arr = array();
			$dynaform_info_users = explode(',',$dynaform_info['users']);
			foreach($dynaform_info_users as $id) {
				$users_arr[$id] = getUserName($id,$showfullusername);
			}
		} else {
			$users_arr = getCustomUserList($module,$fieldname);
		}
		$users_combo = get_select_options_array($users_arr, $user_id);
		$label_fld["options"] = $users_combo;
		break;
	case 'edit':
		$editview_label[] = getTranslatedString($fieldlabel, $module_name);
		($mode == '' && empty($value)) ? $assigned_user_id = $current_user->id : $assigned_user_id = $value;
		if (isset($dynaform_info['users'])) {
			$users_arr = array();
			$dynaform_info_users = explode(',',$dynaform_info['users']);
			foreach($dynaform_info_users as $id) {
				$users_arr[$id] = getUserName($id,$showfullusername);
			}
		} else {
			$users_arr = getCustomUserList($module_name,$fieldname);
		}
		if ($mode == '' && strpos($typeofdata,"M") !== false) {
			$assigned_user_id = '';
			if (!in_array('',$users_arr)) $users_arr = array(''=>'') + $users_arr;
		}
		$users_combo = get_select_options_array($users_arr, $assigned_user_id);
		$fieldvalue[] = $users_combo;
		break;
	case 'relatedlist':
	case 'list':
		if (!empty($sdk_value)) $value = getUserName($sdk_value);
		break;
}