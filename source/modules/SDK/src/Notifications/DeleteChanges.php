<?php
$plugin = vtlib_purify($_REQUEST['plugin']);
include('modules/SDK/src/Notifications/plugins/'.$plugin.'DeleteChanges.php');
exit;
?>