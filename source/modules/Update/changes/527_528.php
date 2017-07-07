<?php
global $adb;
$instanceProjects = Vtiger_Module::getInstance('Projects');
$adb->pquery('UPDATE vtiger_field SET readonly = ?, displaytype = ? WHERE fieldname = ? AND tabid = ?',array(99,2,'project_end',$instanceProjects->id));

$_SESSION['modules_to_update']['Projects'] = 'packages/vte/mandatory/Projects.zip';
?>