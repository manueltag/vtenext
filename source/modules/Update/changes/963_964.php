<?php
global $adb, $table_prefix;

$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['ProjectsStandard'] = Array('location'=>'packages/vte/mandatory/ProjectsStandard.zip','modules'=>Array('ProjectPlan','ProjectMilestone','ProjectTask'));
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';

$res = $adb->query("SELECT tabid FROM {$table_prefix}_tab WHERE name = 'RecycleBin'");
if ($res && $adb->num_rows($res)>0) $_SESSION['modules_to_update']['RecycleBin'] = 'packages/vte/optional/RecycleBin.zip';

SDK::setLanguageEntry('APP_STRINGS', 'it_it', 'LBL_RESEND', 'Reinvia');
?>