<?php 
require_once('include/utils/utils.php');

global $current_user;

$module_list = vtlib_purify($_REQUEST['modules']);
$module_list = explode(',', $module_list);

$_SESSION['__UnifiedSearch_SelectedModules__'] = $module_list;
$current_user->saveSearchModules($module_list);

?>