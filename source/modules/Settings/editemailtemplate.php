<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ********************************************************************************/

require_once('Smarty_setup.php');
require_once('data/Tracker.php');
require_once('include/utils/UserInfoUtil.php');
require_once('include/database/PearDatabase.php');
require_once('include/CustomFieldUtil.php');

global $mod_strings;
global $app_strings;
global $theme;
global $current_language;
global $adb, $table_prefix;

global $log,$default_charset;

$mode = 'create';

if(isset($_REQUEST['templateid']) && $_REQUEST['templateid']!='')
{
	$mode = 'edit';
	$templateid = $_REQUEST['templateid'];
	$log->debug("the templateid is set to the value ".$templateid);
}
$sql = "select * from ".$table_prefix."_emailtemplates where templateid=?";
$result = $adb->pquery($sql, array($templateid));
$emailtemplateResult = str_replace('"','&quot;',$adb->fetch_array($result));
$smod_strings = return_module_language($current_language,'Settings');

//To get Email Template variables -- Pavani
$allOptions=getEmailTemplateVariables();
$smarty = new vtigerCRM_smarty;

$smarty->assign("UMOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign("MOD", $smod_strings);
$smarty->assign("FOLDERNAME", $emailtemplateResult["foldername"]);
$smarty->assign("TEMPLATENAME", $emailtemplateResult["templatename"]);
$smarty->assign("TEMPLATEID", ($_REQUEST['duplicate'] == 'true')?'':$emailtemplateResult["templateid"]); //crmv@36773
$smarty->assign("DESCRIPTION", $emailtemplateResult["description"]);
$smarty->assign("SUBJECT", $emailtemplateResult["subject"]);
$smarty->assign("BODY", $emailtemplateResult["body"]);
$smarty->assign("MODULE", 'Settings');
$smarty->assign("PARENTTAB", getParentTab());
$smarty->assign("EMODE", ($_REQUEST['duplicate'] == 'true')?'duplicate':$mode); //crmv@36773
$smarty->assign("ALL_VARIABLES", $allOptions);
$smarty->assign("TEMPLATETYPE", getTemplateTypeValues($emailtemplateResult["templatetype"]));	//crmv@22700
//crmv@80155
$smarty->assign("USE_SIGNATURE", intval($emailtemplateResult["use_signature"]));
$smarty->assign("OVERWRITE_MESSAGE", intval($emailtemplateResult["overwrite_message"]));

$res = $adb->query("select * from ".$table_prefix."_field where fieldname = 'bu_mc'");
if ($res && $adb->num_rows($res) > 0) {
	$saved_bu_mc = explode(' |##| ', $emailtemplateResult["bu_mc"]);
	$pick_bu_mc = array();
	$bumc_res = $adb->query("SELECT bu_mc FROM {$table_prefix}_bu_mc GROUP BY bu_mc");
	while($row_bumc = $adb->fetchByAssoc($bumc_res)){
		(in_array($row_bumc['bu_mc'], $saved_bu_mc)) ? $selected = 'selected' : $selected = '';
		$pick_bu_mc[] = array('value'=>$row_bumc['bu_mc'],'label'=>getTranslatedString($row_bumc['bu_mc'], 'Users'),'selected'=>$selected);
	}
	$smarty->assign("BU_MC_ENABLED", true);
	$smarty->assign("BU_MC", $pick_bu_mc);
}
//crmv@80155e

$smarty->display("CreateEmailTemplate.tpl");
?>