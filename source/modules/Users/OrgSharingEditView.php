<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
require_once('include/utils/utils.php');
global $mod_strings;
global $app_strings;
global $app_list_strings;

global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$smarty = new vtigerCRM_Smarty; 
$defSharingPermissionData = getDefaultSharingEditAction();

$row=1;
$entries = Array();
foreach($defSharingPermissionData as $tab_id => $def_perr)
{
	$entity_name = getTabname($tab_id);
	if($tab_id == 6)
	{
		$cont_name = getTabname(4);
		$entity_name .= ' & '.$cont_name;
	}
	if ($entity_name == 'Messages') $entity_name = getTranslatedString('LBL_RELATED_MESSAGES','Messages');	//crmv@61173
	$defActionArr=getModuleSharingActionArray($tab_id);

	$entries[] = $entity_name;

	if($tab_id != 6)
	{
		$output = '<select class="detailedViewTextBox" id="'.$tab_id.'_perm_combo" name="'.$tab_id.'_per">';
	}
	else
	{
		$output = '<select class="detailedViewTextBox" id="'.$tab_id.'_perm_combo" name="'.$tab_id.'_per" onchange="checkAccessPermission(this.value)">';
	}
	$entries[] = $tab_id;
	
	//crmv@47243	crmv@56114	crmv@61173
	if (in_array($entity_name,array(getTranslatedString('LBL_RELATED_MESSAGES','Messages'),'MyNotes'))) {
		$defActionArr[0] = 'Inherited';
		unset($defActionArr[1]);
		unset($defActionArr[2]);
		if ($entity_name == getTranslatedString('LBL_RELATED_MESSAGES','Messages')) {
			$defActionArr[8] = 'LBL_ASSIGNED';
		} elseif ($entity_name == 'MyNotes') {
			$defActionArr[3] = 'LBL_ASSIGNED';
		}
	}
	//crmv@47243e	crmv@56114e	crmv@61173e
	foreach($defActionArr as $shareActId=>$shareActName)
	{
		$selected='';
		if($shareActId == $def_perr)
		{
			$selected='selected';
		}
		$output .= '<option value="'.$shareActId.'" '.$selected. '>'.$mod_strings[$shareActName].'</option>';
	}

	$output .= '</select>';
	$entries[] = $output;
	$row++;
}

$list_entries=array_chunk($entries,3);
$smarty->assign("ORGINFO",$list_entries);
$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("APP", $app_strings);
$smarty->assign("CMOD", $mod_strings);

$smarty->display("OrgSharingEditView.tpl");
?>