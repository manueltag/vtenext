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
$timecardid = $_REQUEST['record'];
// obtain timecard info
$sql = "select * from ".$table_prefix."_timecards where timecardsid=$timecardid";
$result = $adb->query($sql);
$tticketid = $adb->query_result($result,0,'ticket_id');
$sortorderid = $adb->query_result($result,0,'sortorder');

 // obtain min timecard
 $sql = "select min(sortorder) as minimum from ".$table_prefix."_timecards where ticket_id=$tticketid";
 $result = $adb->query($sql);
 $ord_min= $adb->query_result($result,0,'minimum');
 $ord_tc = $sortorderid;
 if ($ord_tc>$ord_min) { // we can move up, if not, nothing to do
     // Get timecard one under
     $sql = "select timecardsid from ".$table_prefix."_timecards where ticket_id=$tticketid and sortorder=".($ord_tc-1);
     $result = $adb->query($sql);
     $mvdn_tc= $adb->query_result($result,0,'timecardsid');
     // Update moving down this timecard
     $sql = "update ".$table_prefix."_timecards set sortorder=$ord_tc where timecardsid=$mvdn_tc";
     $result = $adb->query($sql);
     // Update moving up requested timecard
     $sql = "update ".$table_prefix."_timecards set sortorder=".($ord_tc-1)." where timecardsid=$timecardid";
     $result = $adb->query($sql);
 }
 // refresh view
//crmv@fix
global $singlepane_view;
if($singlepane_view == 'true')
	print '<script language=javascript>window.location="index.php?action=DetailView&module=HelpDesk&record='.$tticketid.'&parenttab=Support";</script>';
else
	print '<script language=javascript>window.location="index.php?action=CallRelatedList&module=HelpDesk&record='.$tticketid.'&parenttab=Support";</script>';
//crmv@fix end
?>
