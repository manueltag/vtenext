<?php
$_SESSION['modules_to_update']['Fax'] = 'packages/vte/mandatory/Fax.zip';
$_SESSION['modules_to_update']['Transitions'] = 'packages/vte/mandatory/Transitions.zip';

global $adb;
$adb->pquery("UPDATE vtiger_links SET linkurl=? WHERE tabid=? and linklabel=?",array('index.php?module=Visitreport&action=EditView&return_module=$MODULE$&return_action=$ACTION$&return_id=$RECORD$&accountid=$RECORD$&parenttab=Sales',6,'LBL_ADD_VISITREPORT'));
?>