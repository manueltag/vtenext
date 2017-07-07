<?php
/*********************************************************************************
** The contents of this file are subject to the Mozilla Public License. You may
*not use this file except in compliance with the License
* The Original Code is: JPLTSolucio, S,L, Open Source based on vTiger CRM
* The Initial Developer of the Original Code is JPLTSolucio, S.L.
* Portions created by vtiger are Copyright (C) vtiger. All Rights Reserved.
*
 ********************************************************************************/
global $table_prefix;
require_once('include/database/PearDatabase.php');
$timecardid = $_REQUEST['timecardid'];
$tticketid = $_REQUEST['tticketid'];
$sortorderid = $_REQUEST['sortorderid'];

 // obtain min timecard
 $sql = "select min(sortorderid) as minimum from ".$table_prefix."_tttimecards where ticketid=$tticketid";
 $result = $adb->query($sql);
 $ord_min= $adb->query_result($result,0,'minimum');
 $ord_tc = $sortorderid;
 if ($ord_tc>$ord_min) { // we can move up, if not, nothing to do
     // Get timecard one under
     $sql = "select tttimecardid from ".$table_prefix."_tttimecards where ticketid=$tticketid and sortorderid=".($ord_tc-1);
     $result = $adb->query($sql);
     $mvdn_tc= $adb->query_result($result,0,'tttimecardid');
     // Update moving down this timecard
     $sql = "update ".$table_prefix."_tttimecards set sortorderid=$ord_tc where tttimecardid=$mvdn_tc";
     $result = $adb->query($sql);
     // Update moving up requested timecard
     $sql = "update ".$table_prefix."_tttimecards set sortorderid=".($ord_tc-1)." where tttimecardid=$timecardid";
     $result = $adb->query($sql);
 }
 // refresh view
 print '<script language=javascript>window.location="index.php?action=CallTimeCardList&module=HelpDesk&record='.$tticketid.'&parenttab=Support";</script>';

?>
