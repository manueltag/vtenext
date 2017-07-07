<?php
/*********************************************************************************
** The contents of this file are subject to the Mozilla Public License. You may
*not use this file except in compliance with the License
* The Original Code is: JPLTSolucio, S,L, Open Source based on vTiger CRM
* The Initial Developer of the Original Code is JPLTSolucio, S.L.
* Portions created by vtiger are Copyright (C) vtiger. All Rights Reserved.
*
 ********************************************************************************/
require_once('include/database/PearDatabase.php');
require_once('Smarty_setup.php');
require_once('include/utils/utils.php');
require_once('modules/HelpDesk/HelpDesk.php');
require_once('include/FormValidationUtil.php');
global $table_prefix;
global $app_strings,$mod_strings,$theme,$currentModule;
$timecardid = $_REQUEST['timecardid'];
$tticketid = $_REQUEST['tticketid'];

$focus = CRMEntity::getInstance('HelpDesk');
$smarty = new vtigerCRM_Smarty();

$focus->id = $_REQUEST['record'];
$focus->mode = 'edit'; 	
$focus->retrieve_entity_info($_REQUEST['record'],"HelpDesk");
$focus->name=$focus->column_fields['ticket_title'];		

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once('modules/VteCore/layout_utils.php');	//crmv@30447

$disp_view = "edit_view";
$sql = "select * from ".$table_prefix."_tttimecards where tttimecardid=$timecardid";
$result = $adb->query($sql);
$default_values=array(
  'workdate'=>$adb->query_result($result,0,'workdate'),
  'workerid'=>$adb->query_result($result,0,'workerid'),
  'tcunits'=>$adb->query_result($result,0,'tcunits'),
  'worktime'=>$adb->query_result($result,0,'worktime'),
  'product_id'=>$adb->query_result($result,0,'product_id'),
  'description'=>$adb->query_result($result,0,'description'),
  'timecardtypes'=>$adb->query_result($result,0,'type'),
  'newtc'=>0,
  'ticketstatus'=>'Maintain'
);

$smarty->assign("BLOCKS",getBlocks('TimeCard',$disp_view,'edit',$default_values));

$smarty->assign("OP_MODE",$disp_view);

$smarty->assign("MODULE",$currentModule);
$smarty->assign("SINGLE_MOD",$app_strings['Ticket']);
//Display the FCKEditor or not? -- configure $FCKEDITOR_DISPLAY in config.php 
$smarty->assign("FCKEDITOR_DISPLAY",$FCKEDITOR_DISPLAY);
$smarty->assign("MODTAB",'TimeCard');

$category = getParentTab();
$smarty->assign("CATEGORY",$category);

$smarty->assign("MOD", $mod_strings);
$smarty->assign("UMOD", array('LBL_CHANGE'=>$mod_strings['LBL_CHANGE']));
$smarty->assign("APP", $app_strings);
$smarty->assign("CALENDAR_LANG", $app_strings['LBL_JSCALENDAR_LANG']);

if (empty($focus->column_fields['parent_id'])) {
        $ref_name=$mod_strings['MSG_NoSalesEntity'];
} elseif (getSalesEntityType($focus->column_fields['parent_id'])=='Accounts') {
        $ref_name=getAccountName($focus->column_fields['parent_id']);
} else {
        $ref_name=getContactName($focus->column_fields['parent_id']);
}
$smarty->assign("NAME", '&nbsp;'.$ref_name.'&nbsp;'.$focus->column_fields['ticket_title']);

if(isset($cust_fld))
{
        $smarty->assign("CUSTOMFIELD", $cust_fld);
}
$smarty->assign("ID", $focus->id);
$smarty->assign("OLD_ID", $old_id );
$smarty->assign("UPDATEINFO",updateInfo($focus->id));
$smarty->assign("MODE", $focus->mode);
$smarty->assign("OLDSMOWNERID", $focus->column_fields['assigned_user_id']);

$smarty->assign("RETURN_MODULE", 'HelpDesk');
$smarty->assign("RETURN_ACTION", 'CallTimeCardList');
$smarty->assign("RETURN_ID", $timecardid);

$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);
$smarty->assign("PRINT_URL", "phprint.php?jt=".session_id().$GLOBALS['request_string']);
$smarty->assign("TC_Cancel_URL", "index.php?action=CallTimeCardList&module=HelpDesk&parenttab=Support&record=$tticketid");

// crmv@83877 crmv@112297
// Field Validation Information
$tabid = getTabid($currentModule);
$otherInfo = array();
$validationData = getDBValidationData($focus->tab_name,$tabid,$otherInfo,$focus);	//crmv@96450
$validationArray = split_validationdataArray($validationData, $otherInfo);
$smarty->assign("VALIDATION_DATA_FIELDNAME",$validationArray['fieldname']);
$smarty->assign("VALIDATION_DATA_FIELDDATATYPE",$validationArray['datatype']);
$smarty->assign("VALIDATION_DATA_FIELDLABEL",$validationArray['fieldlabel']);
$smarty->assign("VALIDATION_DATA_FIELDUITYPE",$validationArray['fielduitype']);
$smarty->assign("VALIDATION_DATA_FIELDWSTYPE",$validationArray['fieldwstype']);
// crmv@83877e crmv@112297e

//crmv@112297 TODO check Conditionals::existsConditionalPermissions

$check_button = Button_Check($module);
$smarty->assign("CHECK", $check_button);

$smarty->display('TimecardEditNewView.tpl');

?>
