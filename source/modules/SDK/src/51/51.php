<?php
/* crmv@101683 crmv@104988 */
global $sdk_mode, $current_user, $showfullusername;
switch($sdk_mode) {
	case 'detail':
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$user_id = $col_fields[$fieldname];
		$user_name = getUserName($user_id, $showfullusername);
		$assigned_user_id = $current_user->id;
		if(is_admin($current_user))
		{
			$label_fld[] = '<a href="index.php?module=Users&action=DetailView&record='.$user_id.'">'.$user_name.'</a>';
		}
		else
		{
			$label_fld[] = $user_name;
		}
		$users_combo = get_select_options_array(get_user_array(false, "Active", $user_id), $assigned_user_id);
		$label_fld["options"] = $users_combo;
		break;
	case 'edit':
		$editview_label[] = getTranslatedString($fieldlabel, $module_name);
		($value != '') ? $assigned_user_id = $value : $assigned_user_id = $current_user->id;
		$add_blank = false;
		if ($mode == '' && strpos($typeofdata,"M") !== false) {
			$add_blank = true;
			$assigned_user_id = '';
		}
		$users_combo = get_select_options_array(get_user_array($add_blank, "Active", $assigned_user_id), $assigned_user_id);
		$fieldvalue[] = $users_combo;
		break;
	case 'relatedlist':
	case 'list':
		(!empty($sdk_value)) ? $value = getUserName($sdk_value, $showfullusername) : $value = '';
		break;
}