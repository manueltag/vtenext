<?php
global $adb, $table_prefix;
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';

// fix related buttons for messages (remove "select")
$adb->query("update {$table_prefix}_relatedlists set actions = 'ADD' where name = 'get_messages_list' and actions like '%SELECT%'");
?>