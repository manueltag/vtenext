<?php
/* crmv@125351 */
global $app_strings;

require_once "Smarty_setup.php";
$smarty = new vtigerCRM_Smarty();
$smarty->assign('APP',$app_strings);
$smarty->display("modules/SDK/src/Events/EventContainer.tpl");