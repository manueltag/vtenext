<?php
/*********************************************************************************
** The contents of this file are subject to the Mozilla Public License. You may
*not use this file except in compliance with the License
* The Original Code is: JPLTSolucio, S,L, Open Source based on vTiger CRM
* The Initial Developer of the Original Code is JPLTSolucio, S.L.
* Portions created by vtiger are Copyright (C) vtiger. All Rights Reserved.
*
 ********************************************************************************/

require_once('Smarty_setup.php');
require_once('modules/HelpDesk/HelpDesk.php');
require_once('include/utils/utils.php');
require_once('include/database/PearDatabase.php');

$focus = CRMEntity::getInstance('HelpDesk');
$currentmodule = $_REQUEST['module'];
$RECORD = $_REQUEST['record'];
if(isset($_REQUEST['record']) && $_REQUEST['record']!='') {
    $focus->retrieve_entity_info($_REQUEST['record'],"HelpDesk");
    $focus->id = $_REQUEST['record'];
}

global $mod_strings;
global $app_strings;
global $currentModule;
global $theme;
global $table_prefix;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
$modimage_path=basename($GLOBALS['root_directory']).'/modules/HelpDesk';
require_once('modules/VteCore/layout_utils.php');	//crmv@30447

$smarty = new vtigerCRM_Smarty;

if(isset($_REQUEST['isDuplicate']) && $_REQUEST['isDuplicate'] == 'true') {
        $focus->id = "";
}
if(isset($_REQUEST['mode']) && $_REQUEST['mode'] != ' ') {
        $smarty->assign("OP_MODE",$_REQUEST['mode']);
}

$check_button = Button_Check($module);
$smarty->assign("CHECK", $check_button);

if (empty($focus->column_fields['parent_id'])) {
        $ref_name=$mod_strings['MSG_NoSalesEntity'];
} elseif (getSalesEntityType($focus->column_fields['parent_id'])=='Accounts') {
        $ref_name=getAccountName($focus->column_fields['parent_id']);
} else {
        $ref_name=getContactName($focus->column_fields['parent_id']);
}
$smarty->assign("NAME", '&nbsp;'.$ref_name.'&nbsp;'.$focus->column_fields['ticket_title']);

    $sql1 = "select * from ".$table_prefix."_tttimecards where ticketid=$RECORD order by sortorderid";
    $result = $adb->query($sql1);
    $num_row = $adb->num_rows($result);
    $timecards_array=array();
    $itemseparator=5;
	$tabid= getTabid('TimeCard');
    for($i=0; $i<$num_row; $i++)
    {
        $tttimecardid  = $adb->query_result($result,$i,'tttimecardid');
        $tcsortorderid = $adb->query_result($result,$i,'sortorderid');
        $tcproductid   = (is_null($adb->query_result($result,$i,'product_id')) ? '' : $adb->query_result($result,$i,'product_id'));
        $tcunits       = $adb->query_result($result,$i,'tcunits');
        $tclinetype    = $adb->query_result($result,$i,'type');
        $worker  = getUserName($adb->query_result($result,$i,'workerid'));
        $header  = '<b>'.$tcsortorderid.'</b></td><td class="lvtCol">'.$adb->query_result($result,$i,'workdate').str_repeat('&nbsp;',$itemseparator).$worker;
        if ($check_button['EditView']=='yes') {
        $header .= '</td><td class="lvtCol" align="right" width="20%">';
        $url     = "index.php?action=TimeCardNew&module=HelpDesk&record=$RECORD&parenttab=Support&tticketid=$RECORD";
        $header .= '<a href="'.$url.'"><img src="modules/HelpDesk/images/new.png" border=0 alt="'.$app_strings['LBL_CREATE_BUTTON_LABEL'].'" title="'.$app_strings['LBL_CREATE_BUTTON_LABEL'].'"></a>&nbsp;'; // Create
        $url     = "index.php?action=TimeCardEdit&module=HelpDesk&record=$RECORD&parenttab=Support&tticketid=$RECORD&timecardid=".$tttimecardid;
        $header .= '<a href="'.$url.'"><img src="modules/HelpDesk/images/edit.png" border=0 alt="'.$app_strings['LBL_EDIT_BUTTON'].'" title="'.$app_strings['LBL_EDIT_BUTTON'].'"></a>&nbsp;'; // Edit
        $url     = "index.php?action=TimeCardDel&module=HelpDesk&record=$RECORD&parenttab=Support&tticketid=$RECORD&timecardid=$tttimecardid&sortorderid=$tcsortorderid";
        if ($tcproductid!='' and $tclinetype=='InvoiceLine') {
           $url .= "&productid=$tcproductid&tcunits=$tcunits";
        }
        $DELETE_CONFIRMATION=$mod_strings['TimeCard_DELETE_CONFIRMATION'].$tcsortorderid.$mod_strings['TimeCard_Question'];
        $header .= '<a href="'.$url.'" accessKey="'.$app_strings['LBL_DELETE_BUTTON_KEY'].'" onclick="return confirm(\''.$DELETE_CONFIRMATION.'\')"><img src="modules/HelpDesk/images/del.png" border=0 alt="'.$app_strings['LBL_DELETE_BUTTON'].'" title="'.$app_strings['LBL_DELETE_BUTTON'].'"></a>&nbsp;'; // Delete
        $url     = "index.php?action=TimeCardMvUp&module=HelpDesk&record=$RECORD&parenttab=Support&tticketid=$RECORD&timecardid=$tttimecardid&sortorderid=$tcsortorderid";
        $header .= '<a href="'.$url.'"><img src="modules/HelpDesk/images/sortup.png" border=0 alt="'.$mod_strings['LBL_TCMoveUp'].'" title="'.$mod_strings['LBL_TCMoveUp'].'"></a>&nbsp;'; // Sort up
        $url     = "index.php?action=TimeCardMvDown&module=HelpDesk&record=$RECORD&parenttab=Support&tticketid=$RECORD&timecardid=$tttimecardid&sortorderid=$tcsortorderid";
        $header .= '<a href="'.$url.'"><img src="modules/HelpDesk/images/sortdown.png" border=0 alt="'.$mod_strings['LBL_TCMoveDown'].'" title="'.$mod_strings['LBL_TCMoveDown'].'"></a>'; // Sort down
        }
        // template closes cell & row
        $entries = '</td><td colspan=2 width=100%>'.$mod_strings['LBL_TCUnits'].':&nbsp;';
        $entries.= $tcunits.str_repeat('&nbsp;',$itemseparator);
        $entries.= $mod_strings['LBL_TCTime'].':&nbsp;';
        $entries.= $adb->query_result($result,$i,'worktime').str_repeat('&nbsp;',$itemseparator);
        $entries.= $mod_strings['LBL_PRODUCT'].':&nbsp;';
        if ($tcproductid=='') {
        	$entries.=$app_strings['NTC_NO_ITEMS_DISPLAY'];
        } else {
            $entries.= getProductName($tcproductid).str_repeat('&nbsp;',$itemseparator);
        }
        $entries.='&nbsp;&nbsp;&nbsp;&nbsp;';
        $entries.= $mod_strings['LBL_TCType'].':&nbsp;';
        $entries.= $mod_strings[$tclinetype];
	// Custom fields
        $sql = "select * from ".$table_prefix."_field where tabid=? and tablename=? and displaytype in (1,3) group by columnname"; 
        $params = array($tabid, $table_prefix.'_timecardcf');   
        $rdocf = $adb->pquery($sql, $params);
        $noofrows = $adb->num_rows($rdocf);
        if ($noofrows>0) { // we have custom fields
          $sql = "select * from ".$table_prefix."_timecardcf where tttimecardid=?"; 
          $params = array($tttimecardid);   
          $rdotc = $adb->pquery($sql, $params);
          $entries.= '<br/>';
          for($ncol=0; $ncol<$noofrows; $ncol++)  // for each column
          {
            $entries.= getTranslatedString($adb->query_result($rdocf,$ncol,'fieldlabel')).':&nbsp;';
            $fieldname=$adb->query_result($rdocf,$ncol,'fieldname');
            $entries.= $adb->query_result($rdotc,0,$fieldname);
            $entries.= str_repeat('&nbsp;',$itemseparator);
          }
        }
        $entries.= '<br/>'.nl2br(decode_html($adb->query_result($result,$i,'description')));
        
        // template closes cell & row
        $timecards_array['TimeCard'.$i]=array('header'=>$header,'entries'=>$entries);
    }
if ($num_row>0)  $smarty->assign("RELATEDLISTS", $timecards_array);
$smarty->assign("MODTAB",'TimeCard');
$category = getParentTab();
$smarty->assign("CATEGORY",$category);

$smarty->assign("id",$focus->id);
$smarty->assign("ID",$RECORD );
$smarty->assign("MODULE",$currentmodule);
$smarty->assign("SINGLE_MOD",$app_strings['Ticket']);
$smarty->assign("UPDATEINFO",updateInfo($focus->id));
$smarty->assign("MOD",$mod_strings);
$smarty->assign("APP",$app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);

global $singlepane_view;
$smarty->assign("SinglePane_View", $singlepane_view);

$smarty->display("RelatedLists.tpl");
?>
