<?php
global $adb, $table_prefix;
$adb->pquery("DELETE FROM {$table_prefix}_relatedlists WHERE name = ?", array('get_gantt_chart'));

@unlink('modules/ProjectPlan/BURAK_Gantt.class.php');