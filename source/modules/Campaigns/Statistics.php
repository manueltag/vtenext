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
/* crmv@101506 */

require_once('Smarty_setup.php');
require_once('include/utils/utils.php');
//Redirecting Header for single page layout
require_once('user_privileges/default_module_view.php');
global $singlepane_view, $currentModule;
$module = vtlib_purify($_REQUEST['module']);
$RECORD = vtlib_purify($_REQUEST['record']);
$category = getParentTab();

$focus = CRMEntity::getInstance($currentModule);
if(isset($_REQUEST['record']) && $_REQUEST['record']!='') {
	$focus->retrieve_entity_info($RECORD,$currentModule);
	$focus->id = $RECORD;
	$focus->name=$focus->column_fields['campaignname'];
	$log->debug("id is ".$focus->id);
	$log->debug("name is ".$focus->name);
}

global $mod_strings;
global $app_strings,$adb;
global $theme;
global $table_prefix;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$smarty = new vtigerCRM_Smarty;

$smarty->assign('NEWSLETTER_STATISTICS', $newsletterStatistics);

if(isset($_REQUEST['isDuplicate']) && $_REQUEST['isDuplicate'] == 'true') {
	$focus->id = "";
}
if(isset($_REQUEST['mode']) && $_REQUEST['mode'] != ' ') {
	$smarty->assign("OP_MODE",vtlib_purify($_REQUEST['mode']));
}
$related_array=getRelatedLists($currentModule,$focus);
$focus->filterStatisticRelatedLists('maintain',$related_array);//crmv@22700
// vtlib customization: Related module could be disabled, check it
if(isset($related_array)) {
	foreach($related_array as $mod_key=>$mod_val) {
		if($mod_key == "Contacts" || $mod_key == "Leads" || $mod_key == "Accounts") {
			$rel_checked=$_REQUEST[$mod_key.'_all'];
			$rel_check_split=explode(";",$rel_checked);
			if (is_array($mod_val)) {
				$mod_val["checked"]=array();
				if (isset($mod_val['entries'])) {
					foreach($mod_val['entries'] as $key=>$val) {
						if(in_array($key,$rel_check_split))
							$related_array[$mod_key]["checked"][$key] = 'checked';
						else
							$related_array[$mod_key]["checked"][$key] = '';
					}
				}
			}
		}
	}
}
// END
$smarty->assign("RELATEDLISTS", $related_array);

require_once('include/ListView/RelatedListViewSession.php');
if(!empty($_REQUEST['selected_header']) && !empty($_REQUEST['relation_id'])) {
	$relationId = vtlib_purify($_REQUEST['relation_id']);
	RelatedListViewSession::addRelatedModuleToSession($relationId,
	vtlib_purify($_REQUEST['selected_header']));
}
$open_related_modules = RelatedListViewSession::getRelatedModulesFromSession();
$smarty->assign("SELECTEDHEADERS", $open_related_modules);

require_once('modules/CustomView/CustomView.php');

// Module Sequence Numbering
$mod_seq_field = getModuleSequenceField($currentModule);
if ($mod_seq_field != null) {
	$mod_seq_id = $focus->column_fields[$mod_seq_field['name']];
} else {
	$mod_seq_id = $focus->id;
}
$smarty->assign('MOD_SEQ_ID', $mod_seq_id);
// END

$smarty->assign("SinglePane_View", $singlepane_view);
$smarty->assign("TODO_PERMISSION",CheckFieldPermission('parent_id','Calendar'));
$smarty->assign("EVENT_PERMISSION",CheckFieldPermission('parent_id','Events'));
$smarty->assign("CATEGORY",$category);
$smarty->assign("MODULE",$module);
$smarty->assign("ID",$focus->id);
$smarty->assign("CAMPAIGNID",$focus->id);
if ($newsletterStatistics) {
	$smarty->assign("NAME", getEntityName('Newsletter',$_REQUEST['statistics_newsletter'],true));
	$smarty->assign("UPDATEINFO",updateInfo($_REQUEST['statistics_newsletter']));
	$smarty->assign("SINGLE_MOD",getTranslatedString('SINGLE_Newsletter','Newsletter'));
	$smarty->assign("MODULE",'Newsletter');
	$smarty->assign("ID",$_REQUEST['statistics_newsletter']);
} else {
	if (isset($focus->name)) $smarty->assign("NAME", $focus->name);
	$smarty->assign("UPDATEINFO",updateInfo($focus->id));
	$smarty->assign("SINGLE_MOD",$app_strings['Campaign']);
}
$smarty->assign("MOD",$mod_strings);
$smarty->assign("APP",$app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);

$check_button = Button_Check($module);
$smarty->assign("CHECK", $check_button);
//crmv@22700
$ir_rel_list = isPresentRelatedLists($currentModule);
$focus->filterStatisticRelatedLists('remove',$ir_rel_list,true);
$smarty->assign("IS_REL_LIST",$ir_rel_list);

if ($newsletterStatistics) {
	$_SESSION['statistics_newsletter'] = $_REQUEST['statistics_newsletter'];
	$smarty->assign('STATISTICS_SELECT','<input type="hidden" id="statistics_newsletter" value="'.$_REQUEST['statistics_newsletter'].'">');
} else {
	//crmv@28170
	$_SESSION['statistics_newsletter'] = '';	//default: filtro All
	//crmv@36534
	$result = $adb->pquery("SELECT
					        ".$table_prefix."_newsletter.newsletterid AS newsletterid,
					        newslettername
					      FROM ".$table_prefix."_newsletter
					        INNER JOIN ".$table_prefix."_crmentity
					          ON ".$table_prefix."_crmentity.crmid = ".$table_prefix."_newsletter.newsletterid
					      WHERE ".$table_prefix."_crmentity.deleted = 0
					          AND ".$table_prefix."_newsletter.campaignid = ?
					      ORDER BY COALESCE(".$table_prefix."_newsletter.date_scheduled,CAST('' AS DATE)) DESC",array($focus->id));
	//crmv@36534 e
	$statistics_newsletter = '<select id="statistics_newsletter" class="small" onchange="filter_statistics_newsletter('.$focus->id.',this);">';
	$statistics_newsletter .= '<option value="">'.$app_strings['LBL_ALL'].'</option>';
	if ($result && $adb->num_rows($result)) {
		$_SESSION['statistics_newsletter'] = $adb->query_result($result,0,'newsletterid');
		if ((isset($_REQUEST['ajax']) && $_REQUEST['ajax'] != '')) {
			$_SESSION['statistics_newsletter'] = $_REQUEST['statistics_newsletter'];
		}
		$result->MoveFirst();
		while($row=$adb->fetchByAssoc($result)) {
			$statistics_newsletter_array[$row['newsletterid']] = $row['newslettername'];
		}
		//asort($statistics_newsletter_array);
		foreach ($statistics_newsletter_array as $newsletterid => $newslettername) {
			if ($_SESSION['statistics_newsletter'] == $newsletterid) {
				$selected = 'selected';
			} else {
				$selected = '';
			}
			$statistics_newsletter .= '<option value="'.$newsletterid.'" '.$selected.'>'.$newslettername.'</option>';
		}
	}
	$statistics_newsletter .= '</select>';
	$smarty->assign('STATISTICS_SELECT',$statistics_newsletter);
	//crmv@28170e
}

include('modules/Campaigns/StatisticsChart.php'); // crmv@38600

if(isset($_REQUEST['ajax']) && $_REQUEST['ajax'] != '')
	$smarty->display("RelatedListContents.tpl");
else
	$smarty->display("modules/Campaigns/Statistics.tpl");
//crmv@22700e
?>