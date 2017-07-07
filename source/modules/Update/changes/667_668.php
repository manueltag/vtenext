<?php
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';
global $table_prefix;
Vtiger_Utils::AlterTable($table_prefix.'_modcomments','parent_comments I(19)');
?>