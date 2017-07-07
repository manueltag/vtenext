<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@80155 */

require_once('Smarty_setup.php');
require_once('include/utils/utils.php');
require_once('data/Tracker.php');
require_once('include/utils/UserInfoUtil.php');
require_once('include/CustomFieldUtil.php');

global $app_strings,$mod_strings,$current_language,$default_charset,$theme,$currentModule,$adb,$table_prefix,$current_user;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

//To get Email Template variables -- Pavani
$allOptions=getEmailTemplateVariables();
$smarty = new vtigerCRM_smarty;

$mode = $_REQUEST['mode'];
if($mode == 'edit')
{
	$focus = CRMEntity::getInstance($currentModule);
	$focus->id = $_REQUEST['record'];
	$focus->retrieve_entity_info($_REQUEST['record'], $currentModule);
	$templateid = $focus->column_fields['templateemailid'];
	$sql = "select * from {$table_prefix}_emailtemplates where templateid=?";
	$result = $adb->pquery($sql, array($templateid));
	
	//crmv@116110
	if($result && $adb->num_rows($result) > 0){
		$emailtemplateResult = str_replace('"','&quot;',$adb->fetch_array($result));
		$saved_bu_mc = explode(' |##| ', $emailtemplateResult["bu_mc"]);
	
		$smarty->assign("FOLDERNAME", $emailtemplateResult["foldername"]);
		$smarty->assign("TEMPLATENAME", $emailtemplateResult["templatename"]);
		$smarty->assign("TEMPLATEID", $emailtemplateResult["templateid"]);
		$smarty->assign("DESCRIPTION", $emailtemplateResult["description"]);
		$smarty->assign("SUBJECT", $emailtemplateResult["subject"]);
		$smarty->assign("BODY", $emailtemplateResult["body"]);
		$smarty->assign("PAGE_TITLE", getTranslatedString('LBL_EDIT_EMAIL_TEMPLATES','Settings'));
		$smarty->assign("TEMPLATETYPE", getTemplateTypeValues($emailtemplateResult["templatetype"]));
	} else {
		$smarty->assign("TEMPLATETYPE", getTemplateTypeValues('Newsletter'));
		$smarty->assign("PAGE_TITLE", getTranslatedString('LBL_CREATE_EMAIL_TEMPLATES','Settings'));
	}
	//crmv@116110e
} else {
	$saved_bu_mc = array();
	$smarty->assign("PAGE_TITLE", getTranslatedString('LBL_CREATE_EMAIL_TEMPLATES','Settings'));
}

$smarty->assign("APP", $app_strings);
$smarty->assign("IMAGE_PATH", $image_path);
$smarty->assign("THEME", $theme);
$smarty->assign("THEME_PATH", $theme_path);
$smarty->assign("PARENTTAB", getParentTab());
$smarty->assign("ALL_VARIABLES", $allOptions);
$smarty->assign("RECORD", $_REQUEST['record']);
$smarty->assign("TEMPLATETYPE", getTemplateTypeValues($emailtemplateResult["templatetype"]));
$smarty->assign("EMODE", $mode);

$res = $adb->query("select * from {$table_prefix}_field where fieldname = 'bu_mc'");
if ($res && $adb->num_rows($res) > 0) {
	$pick_bu_mc = array();
	$bu_mc = explode(' |##| ', $current_user->column_fields['bu_mc']);
	foreach($bu_mc as $b) {
		(in_array($b, $saved_bu_mc)) ? $selected = 'selected' : $selected = '';
		$pick_bu_mc[] = array('value'=>$b,'label'=>getTranslatedString($b, 'Users'),'selected'=>$selected);
	}
	$smarty->assign("BU_MC_ENABLED", true);
	$smarty->assign("BU_MC", $pick_bu_mc);
}

$smarty->display("modules/Newsletter/widgets/TemplateEmailEdit.tpl");
?>
