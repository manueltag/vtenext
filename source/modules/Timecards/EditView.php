<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

/* crmv@30447 crmv@104568 */
 
require_once('modules/VteCore/EditView.php');

global $adb, $table_prefix;

if($disp_view != 'edit_view') {
    if(!$isduplicate) {
        $focus->column_fields['tcunits']='1';
        $focus->column_fields['worktime']='00:05';	//crmv@14132
        $focus->column_fields['timecardtypes']='Comment';
        $focus->column_fields['newtc']=0;	//crmv@14132
        //crmv@19396
		if ($_REQUEST['ticket_id'] != '') {
			global $adb;
			$res = $adb->pquery("select status from {$table_prefix}_troubletickets where ticketid = ?",array($_REQUEST['ticket_id']));
			if ($res && $adb->num_rows($res)>0)
				$focus->column_fields['ticketstatus']=$adb->query_result($res,0,'status');
			$focus->column_fields['ticket_id']=$_REQUEST['ticket_id'];
		}
		//crmv@19396e
		
		$smarty->assign('BLOCKS', getBlocks($currentModule, $disp_view, $focus->mode, $focus->column_fields));
    }
}

$smarty->assign("UMOD", array('LBL_CHANGE'=>$mod_strings['LBL_CHANGE']));
$smarty->assign("FCKEDITOR_DISPLAY",$FCKEDITOR_DISPLAY);

$smarty->display('salesEditView.tpl');
