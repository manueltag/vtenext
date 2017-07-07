<?php
global $current_user;
require_once('modules/SDK/src/Notifications/Notifications.php');
$focus = new Notifications($current_user->id,vtlib_purify($_REQUEST['plugin']));
if (strpos(vtlib_purify($_REQUEST['id']),',') !== false) {
	$ids = array_filter(explode(',',vtlib_purify($_REQUEST['id'])));
	foreach($ids as $id) {
		$focus->deleteNotification($id);
	}
} else {
	$focus->deleteNotification(vtlib_purify($_REQUEST['id']));
}
echo '|##|'.$focus->getUserNotificationNo();
?>