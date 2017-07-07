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
//crmv@30447
global $table_prefix;
require_once('modules/VteCore/EditView.php');
if(isset($_REQUEST['record']) && $_REQUEST['record'] !='')
{
	$focus->id = $_REQUEST['record'];
	$focus->mode = 'edit';
	$focus->retrieve_entity_info($_REQUEST['record'],"HelpDesk");
	$focus->name=$focus->column_fields['ticket_title'];

	$opened_modifiedtime = $focus->column_fields['modifiedtime']; // DS-CR RaMa 05.03.3008 //ds@19s
}

if($isduplicate == 'true') {
	$focus->id = '';
	$focus->mode = '';
}

//sk@2
if($disp_view == 'edit_view')
{
	$projects_result = $adb->query('select * from '.$table_prefix.'_projects_tickets where ticket_id='.$focus->id);
	$noofrows = $adb->num_rows($projects_result);
	if($noofrows > 0){
		global $projects_names;
		$projects_ids = $projects_names = '';
		for($i=0;$i<$noofrows;$i++){
			$project_id = $adb->query_result($projects_result,$i,'project_id');
			$projects_ids = $project_id.';'.$projects_ids;
			$projects_result1 = $adb->query("select project_name from ".$table_prefix."_projects inner join ".$table_prefix."_crmentity on ".$table_prefix."_projects.projectid = ".$table_prefix."_crmentity.crmid left join ".$table_prefix."_projectscf on ".$table_prefix."_projects.projectid = ".$table_prefix."_projectscf.projectid where ".$table_prefix."_crmentity.deleted=0 and ".$table_prefix."_projects.projectid=$project_id");
			$projects_names = $adb->query_result($projects_result1,0,'project_name')."\n".$projects_names;
		}
		$smarty->assign("PROJECTS_IDS",$projects_ids);
	}
}

$smarty->assign("ID", $focus->id);
$smarty->assign("OLD_ID", $old_id );
if($focus->mode == 'edit')
{
	$smarty->assign("UPDATEINFO",updateInfo($focus->id));
	$smarty->assign("MODE", $focus->mode);
	$smarty->assign("OLDSMOWNERID", $focus->column_fields['assigned_user_id']);
  
}

if($_REQUEST['record'] != '')
{
	//Added to display the ticket comments information
	$smarty->assign("COMMENT_BLOCK",$focus->getCommentInformation($_REQUEST['record']));
}

$smarty->display("salesEditView.tpl");
