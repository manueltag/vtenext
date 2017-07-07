<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is crmvillage.biz.
 * Portions created by crmvillage.biz are Copyright (C) crmvillage.biz.
 * All Rights Reserved.
 *******************************************************************************/
require_once('include/utils/utils.php');
global $adb,$table_prefix;

$ruleid = $_REQUEST['ruleid'];
if (trim($ruleid) == '') {
	$ruleid = $adb->getUniqueID('tbl_s_conditionals');
}
else {
	$delete = "delete from tbl_s_conditionals where ruleid = ".$ruleid;
	$result = $adb->query($delete);
	$delete = "delete from tbl_s_conditionals_rules where ruleid = ".$ruleid;
	$result = $adb->query($delete);
}
$total_conditions = $_REQUEST['total_conditions'];

$module_name = $_REQUEST['module_name'];
$params[4] = $_REQUEST['workflow_name'];
$params[5] = $_REQUEST['sequence'];	
$params[6] = $_REQUEST['role_grp_check'];
$tabid = getTabid($_REQUEST['module_name']);

//crmv@fixcarel
$field_name = array();
for($i=0;$i<$total_conditions;$i++){
	$field_name[] = $_REQUEST['field'.$i];
}

$query = "select 
		fieldid,fieldname
		from ".$table_prefix."_field 
		inner join ".$table_prefix."_tab on ".$table_prefix."_tab.tabid = ".$table_prefix."_field.tabid 
		where ".$table_prefix."_tab.name = '$module_name'";	// and fieldname NOT IN ('".implode("','",$field_name)."')
//crmv@fixcarel - e
$result = $adb->query($query);

if($result && $adb->num_rows($result)>0) {
	for($i=0;$i<$adb->num_rows($result);$i++) {
		$params[0] = $adb->query_result($result,$i,'fieldid');;
		$fieldname = $adb->query_result($result,$i,'fieldname');
		
		$max_sequence = "select max(sequence)+1 as sequence from tbl_s_conditionals where fieldid = ".$params[0];
		$max_result = $adb->query($max_sequence);
		$params[7] = (int)$adb->query_result($max_result,0,'sequence');
		
		if(array_key_exists("FpovManaged".$fieldname,$_REQUEST)) {
			$read_perm = 0;
			$write_perm = 0;
			$mandatory = 0;

			if($_REQUEST['FpovReadPermission'.$fieldname] == "1")
			 	 $read_perm = 1;
			else $read_perm = 0;

			if($_REQUEST['FpovWritePermission'.$fieldname] == "1")
			 	 $write_perm = 1;
			else $write_perm = 0;
			
			if($_REQUEST['FpovMandatoryPermission'.$fieldname] == "1")
			 	 $mandatory = 1;
			else $mandatory = 0;
			$query = "insert into tbl_s_conditionals (ruleid,fieldid,sequence,active,description,read_perm,write_perm,mandatory,role_grp_check) values (
						$ruleid,'".addslashes($params[0])."','".addslashes($params[7])."','1','".addslashes($params[4])."','$read_perm','$write_perm','$mandatory','".addslashes($params[6])."'
						)";
			$adb->query($query);
		} 
		else {
			// delete
		}
	} // for
} // else very bad

if ($total_conditions != '' && $total_conditions != 0) {
	for ($j=0;$j<$total_conditions;$j++) {
		if($_REQUEST["deleted".$j] == 1) continue;
		$ruleid_rule = $adb->getUniqueID('tbl_s_conditionals_rules');
		$query = "insert into tbl_s_conditionals_rules (id,ruleid,chk_fieldname,chk_criteria_id,chk_field_value) values ($ruleid_rule,$ruleid,'".$_REQUEST['field'.$j]."','".$_REQUEST['criteria_id'.$j]."','".$_REQUEST['field_value'.$j]."')";
		$adb->query($query);
	}
}

// crmv@77249
if ($_REQUEST['included'] == true) {
	$params = array(
		'included' => 'true',
		'skip_vte_header' => 'true',
		'skip_footer' => 'true',
		'formodule' => $_REQUEST['formodule'],
		'statusfield' => $_REQUEST['statusfield'],
	);
	$otherParams = "&".http_build_query($params);
}
// crmv@77249e
			
header("Location: index.php?module=Conditionals&action=index&parenttab=Settings".$otherParams);
?>
