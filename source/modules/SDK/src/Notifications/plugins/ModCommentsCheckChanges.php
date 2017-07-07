<?php
global $current_user;
require_once('modules/SDK/src/Notifications/Notifications.php');
$focus = new Notifications($current_user->id,'ModComments');
echo $focus->getUserNotificationNo();
?>