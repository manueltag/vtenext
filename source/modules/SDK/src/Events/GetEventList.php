<?php
global $current_user,$current_language;
require_once('modules/SDK/src/Events/Utils.php');
echo getHtmlEventList($current_user->id,$_REQUEST['mode'],$_REQUEST['year'],$_REQUEST['month'],$_REQUEST['day']);
?>