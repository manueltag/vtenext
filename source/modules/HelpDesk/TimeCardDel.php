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
global $table_prefix;
$timecardid = $_REQUEST['timecardid'];
$tticketid = $_REQUEST['tticketid'];
$sortorderid = $_REQUEST['sortorderid'];

 // If we get here it is because they have already confirmed elimination
 $sql = "delete from ".$table_prefix."_tttimecards where tttimecardid=$timecardid";
 $result = $adb->query($sql);
 // reorder timecards above deleted one
    $sql = "select tttimecardid from ".$table_prefix."_tttimecards where ticketid=$tticketid and sortorderid>$sortorderid order by sortorderid";
    $result = $adb->query($sql);
    $num_row = $adb->num_rows($result);
    for($i=0; $i<$num_row; $i++)
    {
    	$tttcid = $adb->query_result($result,$i,'tttimecardid');
        $sql = "update ".$table_prefix."_tttimecards set sortorderid=".($sortorderid+$i)." where tttimecardid=$tttcid";
        $rdo = $adb->query($sql);
    }
 // refresh view
 print '<script language=javascript>window.location="index.php?action=CallTimeCardList&module=HelpDesk&record='.$tticketid.'&parenttab=Support";</script>';

?>
