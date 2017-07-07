<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@106857 */
require_once('modules/Settings/ModuleMaker/ModuleMakerUtils.php');
require_once('modules/Settings/ProcessMaker/ProcessMakerUtils.php');
require_once('Smarty_setup.php');

global $mod_strings, $app_strings, $theme, $adb, $table_prefix;

$blockid = vtlib_purify($_REQUEST['blockid']);
$fieldid = vtlib_purify($_REQUEST['fieldid']);

global $small_page_title, $small_page_title;
$small_page_title = $app_strings['LBL_ADD_FIELD_TABLE'];
$small_page_buttons = '
<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td width="100%" style="padding:5px"></td>
 	<td align="right" style="padding: 5px;" nowrap>
 		<input type="button" class="crmbutton small save" value="'.getTranslatedString('LBL_SAVE_BUTTON_LABEL').'" onclick="MlTableFieldConfig.saveConfig()">
 		<input type="button" class="crmbutton small cancel" value="'.getTranslatedString('LBL_CANCEL_BUTTON_LABEL').'" onclick="MlTableFieldConfig.cancelConfig()">';
if (!empty($fieldid)) {
	$small_page_buttons .= ' <input type="button" class="crmbutton small cancel" value="'.getTranslatedString('LBL_DELETE_BUTTON_LABEL').'" onclick="MlTableFieldConfig.deleteConfig()">';
}
$small_page_buttons .= '
 	</td>
</tr>
</table>';
include('themes/SmallHeader.php');

$smarty = new vtigerCRM_Smarty;
$smarty->assign("MOD",$mod_strings);
$smarty->assign("APP",$app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", "themes/$theme/images/");
$smarty->assign("LAYOUT_MANAGER", true);
$smarty->assign("BLOCKID", $blockid);
if (!empty($fieldid)) {
	$result = $adb->pquery("select fieldname, fieldlabel, name from {$table_prefix}_field inner join {$table_prefix}_tab on {$table_prefix}_tab.tabid = {$table_prefix}_field.tabid where fieldid = ?", array($fieldid));
	$module = $adb->query_result($result,0,'name');
	$fieldname = $adb->query_result($result,0,'fieldname');
	$fieldlabel = $adb->query_result($result,0,'fieldlabel');
	
	require_once('include/utils/ModLightUtils.php');
	$MLUtils = ModLightUtils::getInstance();
	$columns = $MLUtils->getColumns($module,$fieldname);
	
	$fieldinfo = array(
		'label'=>$fieldlabel,
		'columns'=>$columns,
	);
	$smarty->assign("FIELDINFO", Zend_Json::encode($fieldinfo));
	$smarty->assign("FIELDID", $fieldid);
}

$MMUtils = new ModuleMakerUtils();
$MMSteps = new ProcessModuleMakerSteps($MMUtils);
$smarty->assign("NEWFIELDS", $MMSteps->getNewFields());
$smarty->assign("NEWTABLEFIELDCOLUMNS", $MMSteps->getNewTableFieldColumns()); // crmv@102879

$smarty->display("Settings/ModuleMaker/LayoutTableField.tpl");