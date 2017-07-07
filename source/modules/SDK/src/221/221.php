<?php
require_once('modules/SDK/src/221/221Utils.php');
global $sdk_mode, $current_user;

switch($sdk_mode) {
	case 'insert':
		break;
	case 'detail':
		$value = $col_fields[$fieldname];

		$uitype221 = new UitypeRoleUtils();
		$roles = $uitype221->getAllRoles();
		$display_value = '';
		foreach($roles as $role) {
			$chk_val = '';
			if ($value == $role['roleid']) {
				$display_value = $role['rolename'];
				$chk_val = 'selected';
			}
			if(isset($_REQUEST['file']) && $_REQUEST['file'] == 'QuickCreate')
				$options[] = array(htmlentities($role['rolename'],ENT_QUOTES,$default_charset),to_html($role['roleid']),$chk_val);	
			else
				$options[] = array($role['rolename'],to_html($role['roleid']),$chk_val);
		}
		$link = '';
		if (!empty($display_value)) {
			if (is_admin($current_user))
				$link = '<a href="index.php?module=Settings&action=RoleDetailView&parenttab=Settings&roleid='.$value.'">'.$display_value.'</a>';
			else
				$link = $display_value;
		}
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$label_fld[] = $link;
		$label_fld ["options"] = $options;
		break;
	case 'edit':
		$uitype221 = new UitypeRoleUtils();
		$roles = $uitype221->getAllRoles();
		foreach($roles as $role) {
			$chk_val = '';
			if ($value == $role['roleid']) {
				$chk_val = 'selected';
			}
			if(isset($_REQUEST['file']) && $_REQUEST['file'] == 'QuickCreate')
				$options[] = array(htmlentities($role['rolename'],ENT_QUOTES,$default_charset),to_html($role['roleid']),$chk_val );	
			else
				$options[] = array($role['rolename'] ,to_html($role['roleid']),$chk_val );
		}
		$editview_label[] = getTranslatedString($fieldlabel, $module_name);
		$fieldvalue [] = $options;
		break;
	case 'relatedlist':
	case 'list':
		$uitype221 = new UitypeRoleUtils();
		$value = $uitype221->getRoleName($value);
		break;
}
?>