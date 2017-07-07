<?php
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter','Targets'));

if (isModuleInstalled('Telemarketing')) echo "<br>\n<b>Notice:</b> you have to upgrade the module Telemarketing from Settings > Module Manager.<br>\n";
if (isModuleInstalled('Fiere')) echo "<br>\n<b>Notice:</b> you have to upgrade the module Fairs/Fiere from Settings > Module Manager<br>\n";
?>