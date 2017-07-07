<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ********************************************************************************/
session_start();
require_once('include/CustomFieldUtil.php');
require_once('Smarty_setup.php');
require_once('include/database/PearDatabase.php');
require_once 'include/utils/ListViewUtils.php';
require_once('modules/CustomView/CustomView.php');

global $mod_strings,$app_strings,$app_list_strings,$theme,$adb,$current_user;
global $list_max_entries_per_page;
global $table_prefix;

require_once('modules/VteCore/layout_utils.php');	//crmv@30447

$iCurRecord = vtlib_purify($_REQUEST['CurRecordId']);
$sModule = vtlib_purify($_REQUEST['CurModule']);

require_once('data/CRMEntity.php');
$foc_obj = CRMEntity::getInstance($sModule);

//crmv@40590
$query = $adb->pquery("SELECT tabid,tablename,entityidfield,fieldname from ".$table_prefix."_entityname WHERE modulename = ?",array($sModule));
$tabid = $adb->query_result($query,0,'tabid');
//crmv@40590e
$table_name = $adb->query_result($query,0,'tablename');
$field_name = $adb->query_result($query,0,'fieldname');
$id_field = $adb->query_result($query,0,'entityidfield');
$fieldname = explode(",",$field_name);
$fields_array = array($sModule=>$fieldname);
$id_array = array($sModule=>$id_field);
$tables_array = array($sModule=>$table_name);

$permittedFieldNameList = array();
//crmv@40590
$result = $adb->pquery("SELECT fieldname,columnname FROM {$table_prefix}_field WHERE tabid = ? AND fieldname IN (".generateQuestionMarks($fieldname).")",array($tabid,$fieldname));
if ($result && $adb->num_rows($result) > 0) {
	while($row=$adb->fetchByAssoc($result)) {
		if(getFieldVisibilityPermission($sModule,$current_user->id,$row['fieldname']) == '0'){
			$permittedFieldNameList[] = $row['columnname'];
		}
	}
}
//crmv@40590e

$cv = new CustomView();
$viewId = $cv->getViewId($sModule);
if(!empty($_SESSION[$sModule.'_DetailView_Navigation'.$viewId])){
	$recordNavigationInfo = Zend_Json::decode($_SESSION[$sModule.'_DetailView_Navigation'.$viewId]);
	$recordList = array();
	$recordIndex = null;
	$recordPageMapping = array();
	foreach ($recordNavigationInfo as $start=>$recordIdList){
		foreach ($recordIdList as $index=>$recordId) {
			$recordList[] = $recordId;
			$recordPageMapping[$recordId] = $start;
			if($recordId == $iCurRecord){
				$recordIndex = count($recordList)-1;
			}
		}
	}
}else{
	$recordList = array();
}
$output = '<table border="0" cellpadding="5" cellspacing="0" width="100%">
			<tr height="34">
				<td style="padding:5px" class="level3Bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="100%"><b>'.$app_strings['JUMP_TO'].' '.$app_strings[$sModule].'</b></td>
					</tr>
					</table>
				</td>
			</tr>
		</table>';
$output .= '<table width="100%" border="0" cellpadding="5" cellspacing="0" class="layerHeadingULine">
			</table><table border=0 cellspacing=0 cellpadding=0 width=100% align=center> 
							<tr>
								<td class=small >
									<table border=0 celspacing=0 cellpadding=0 width=100% align=center >
										<tr><td>';
$output .= '<div style="height:270px;overflow-y:auto;">';
$output .= '<table cellpadding="2">';

if(count($recordList) > 0){
	$displayRecordCount = 10;
	$count = count($recordList);
	$idListEndIndex = ($count < ($recordIndex+$displayRecordCount))? ($count+1) : ($recordIndex+$displayRecordCount+1);
	$idListStartIndex = $recordIndex-$displayRecordCount;
	if($idListStartIndex < 0){
		$idListStartIndex = 0;
	}
	$idsArray = array_slice($recordList,$idListStartIndex,($idListEndIndex - $idListStartIndex));
	
	$selectColString = implode(',',$permittedFieldNameList).', '.$id_array[$sModule];
	$fieldQuery = "SELECT $selectColString from ".$tables_array[$sModule]." WHERE ".$id_array[$sModule]." IN (". generateQuestionMarks($idsArray) .")";
	
	$fieldResult = $adb->pquery($fieldQuery,$idsArray);
	$numOfRows = $adb->num_rows($fieldResult);
	$recordNameMapping = array();
	for($i=0; $i<$numOfRows; ++$i) {
		$recordId = $adb->query_result($fieldResult,$i,$id_array[$sModule]);
		$fieldValue = '';
		foreach ($permittedFieldNameList as $fieldName) {
			$fieldValue .= " ".$adb->query_result($fieldResult,$i,$fieldName);
		}
		$fieldValue = textlength_check($fieldValue);
		$recordNameMapping[$recordId] = $fieldValue;
	}
	foreach ($idsArray as $id) {
		if($id===$iCurRecord){
			$output .= '<tr><td style="text-align:left;font-weight:bold;">'.$recordNameMapping[$id].'</td></tr>';
		}else{
			$output .= '<tr><td style="text-align:left;"><a href="index.php?module='.$sModule.
				'&action=DetailView&parenttab='.vtlib_purify($_REQUEST['CurParentTab']).'&record='.$id.
				'&start='.$recordPageMapping[$id].'">'.$recordNameMapping[$id].'</a></td></tr>';
		}
	}
}
$output .= '</table>';
$output .= '</div></td></tr></table></td></tr></table>';
$output .= '<div class="closebutton" onClick="javascript:fninvsh(\'lstRecordLayout\');"></div>';
	
echo $output;
?>