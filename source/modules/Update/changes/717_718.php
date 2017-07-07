<?php
$_SESSION['modules_to_update']['Charts'] = 'packages/vte/mandatory/Charts.zip';
$_SESSION['modules_to_update']['Ddt'] = 'packages/vte/mandatory/Ddt.zip';
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter','Targets'));
$_SESSION['modules_to_update']['SLA'] = 'packages/vte/mandatory/SLA.zip';
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';

$res = $adb->query("SELECT tabid FROM {$table_prefix}_tab WHERE name = 'Projects'");
if ($res && $adb->num_rows($res)>0) $_SESSION['modules_to_update']['Projects'] = 'packages/vte/optional/Projects.zip';

@unlink('Smarty/templates/EmailDetailView.tpl');
@unlink('modules/Calendar/OccupationBar.php');
@unlink('Smarty/templates/ActivityOccupation.tpl');

SDK::setLanguageEntries('Import','DUPLICATE_COLUMNS',Array('it_it'=>'Il file contiene colonne duplicate','en_us'=>'Duplicate columns found'));

SDK::deleteLanguageEntry('ProjectPlan', 'en_us', 'SINGLE_Project');
SDK::setLanguageEntry('ProjectPlan', 'en_us', 'SINGLE_ProjectPlan', 'Project');
?>