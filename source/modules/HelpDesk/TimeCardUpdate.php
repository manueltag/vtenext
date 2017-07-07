<?php
/*********************************************************************************
** The contents of this file are subject to the Mozilla Public License. You may
*not use this file except in compliance with the License
* The Original Code is: JPLTSolucio, S,L, Open Source based on vTiger CRM
* The Initial Developer of the Original Code is JPLTSolucio, S.L.
* Portions created by vtiger are Copyright (C) vtiger. All Rights Reserved.
*
 ********************************************************************************/

require_once('include/utils/utils.php');
require_once('include/utils/InventoryUtils.php');

global $log;
global $table_prefix;
if(isset($_REQUEST['record']) && $_REQUEST['record'] !='')
{
  $timecardid = $_REQUEST['return_id'];
  $tticketid = $_REQUEST['record'];

  // First we fix the time input as we can accept "#h #m" format
    $wt = $_REQUEST['worktime'];
    if (stripos($wt,'h') > 0) {
        $hr = substr($wt,0,stripos($wt,'h'));
        $rt = substr($wt,stripos($wt,'h')+1);
        if (stripos($rt,'m') > 0) {
            $min = intval(substr($rt,0,stripos($rt,'m')));
            if ($min > 59) {
                $hr  = $hr + intval($min / 60);
                $min = $min % 60;
                $wt  = $hr.':'.$min;
            } else {
                $wt = $hr.':'.$min;
            }
        } else {
            $wt = $hr.':00';
        }
    }
    if (stripos($wt,'m') > 0) {
        $min = intval(substr($wt,0,stripos($wt,'m')));
        if ($min > 59) {
            $hr  = intval($min / 60);
            $min = $min % 60;
            $wt  = $hr.':'.$min;
        } else {
            $wt = '0:'.$min;
        }
    }
  // From here on we have correct time format in variable $wt

  if ($_REQUEST['mode'] == 'edit' ) {
    // Update the ticket
    $sql = "UPDATE `".$table_prefix."_tttimecards` SET ";
    $sql.= " `workdate`='".getDBInsertDateValue($_REQUEST['workdate'])."',";
    $sql.= " `workerid`=".$_REQUEST['assigned_user_id'].",";
    $sql.= " `tcunits`=".$_REQUEST['tcunits'].",";
    $sql.= " `worktime`='".$wt."',";
    $sql.= " `product_id`=";
    if (isset($_REQUEST['product_id']) and is_numeric($_REQUEST['product_id'])) {
        $sql.= $_REQUEST['product_id'].",";
    } else {
        $sql.= 'NULL,';
    }
    $sql.= " `description`='".addslashes($_REQUEST['description'])."',";
    $sql.= " `type`='".$_REQUEST['timecardtypes']."'";
    $sql.= " WHERE `tttimecardid` =$timecardid";
    $result = $adb->query($sql);
  } else {  // Create
	$srtordsql   ='SELECT max(sortorderid) as max FROM '.$table_prefix.'_tttimecards WHERE `ticketid`='.$tticketid;
    $rstsrtord = $adb->query($srtordsql);
    $sortorderid =$adb->query_result($rstsrtord,0,'max')+1;
    $sql = "INSERT into `".$table_prefix."_tttimecards` (`ticketid`, `sortorderid`, `creatorid`, `createdtime`, `workerid`, `workdate`, `worktime`, `product_id`, `tcunits`, `type`, `description`) VALUES (";
    $sql.= $tticketid.",";
    $sql.= $sortorderid.",";
    $sql.= $_SESSION['authenticated_user_id'].",";
    $sql.= "NOW(),";
    $sql.= $_REQUEST['assigned_user_id'].",";
    $sql.= "'".getDBInsertDateValue($_REQUEST['workdate'])."',";
    $sql.= "'".$wt."',";
    if (isset($_REQUEST['product_id']) and is_numeric($_REQUEST['product_id'])) {
    	$sql.= $_REQUEST['product_id'].",";
    } else {
        $sql.= 'NULL,';
    }
    $sql.= $_REQUEST['tcunits'].",";
    $sql.= "'".$_REQUEST['timecardtypes']."',";
    $sql.= "'".addslashes($_REQUEST['description'])."')";
    $result = $adb->query($sql);
  }

  // 'ticketstatus'!='Maintain' Change troubleticket status and add to update info
  // $_REQUEST['reports_to_name'] Change troubleticket responsable and add to update info
  if ($_REQUEST['ticketstatus']!='Maintain' or (isset($_REQUEST['reports_to_name']) and !empty($_REQUEST['reports_to_name']))) {

     // Get all the orignal values
     $focus = CRMEntity::getInstance('HelpDesk');
     $focus->id = $_REQUEST['record'];
     $focus->retrieve_entity_info($_REQUEST['record'],"HelpDesk");
     $focus->name=$focus->column_fields['ticket_title'];
     $focus->mode = 'edit';

     // Change those that we have been told to change
     if ($_REQUEST['ticketstatus']!='Maintain') $focus->column_fields['ticketstatus'] = $_REQUEST['ticketstatus'];
     if (!empty($_REQUEST['reports_to_name'])) {
     	$focus->column_fields['assigned_user_id'] = $_REQUEST['reports_to_id'];
        $focus->column_fields['assigntype']='U';
     }

     // Empty Comments
     $_REQUEST['comments']='';
     $focus->column_fields['comments'] = '';
     // Set these just in case
     $_REQUEST['mode'] = 'edit';
     $_REQUEST['action'] = 'Save';
     $_REQUEST['parenttab'] = 'Support';
     $_REQUEST['return_module'] = 'HelpDesk';
     $_REQUEST['return_id'] = $tticketid;
     $_REQUEST['return_action'] = 'CallTimeCardList';
     $_REQUEST['return_viewname'] = '';

     // Save changes
     $focus->save("HelpDesk");

   }  // End Change TroubleTicket
}  // End isset RECORD

if (isset($_REQUEST['newtc'])) {
 	// redirect to create new ticket
    print '<script language=javascript>window.location="index.php?action=TimeCardNew&module=HelpDesk&record='.$tticketid.'&parenttab=Support&tticketid='.$tticketid.'";</script>';
} else {
    // refresh view
    print '<script language=javascript>window.location="index.php?action=CallTimeCardList&module=HelpDesk&record='.$tticketid.'&parenttab=Support";</script>';
}

?>
