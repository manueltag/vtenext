<?php
$_SESSION['modules_to_update']['SLA'] = 'packages/vte/mandatory/SLA.zip';

global $adb, $table_prefix;
$cronjobs = array('MessagesPropagateToImap','MessagesSend','MessagesUids','Messages','MessagesInboxUids','MessagesInbox','MessagesSyncFolders');
$adb->pquery("update {$table_prefix}_cronjobs set max_attempts = ? where cronname in (".generateQuestionMarks($cronjobs).")",array(2147483647,$cronjobs));
?>