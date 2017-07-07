<?php
global $table_prefix;
$_SESSION['modules_to_update']['SLA'] = 'packages/vte/mandatory/SLA.zip';
Vtiger_Utils::AlterTable($table_prefix.'_troubletickets','start_sla DT,end_sla DT,time_refresh DT,time_change_status DT');
?>