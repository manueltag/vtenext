<?php
/* crmv@33545 */
/* imposta le notifiche o i commenti come lette */

include('api/wslogin.php');
require_once('modules/SDK/src/Notifications/Notifications.php');

global $login, $userId, $current_user;

// il modulo, commenti o notifiche
$module = $_REQUEST['module'];

if (!$login || !$userId) {
	echo 'Login Failed';
} elseif (in_array($module, $touchInst->excluded_modules)) {
	echo "Module not permitted";
} else {

	$notif = 0;

	$ids = array_map('intval', explode(':', $_REQUEST['records']));
	$forced = ($_REQUEST['forced'] == '1');

	// segno come lette
	if ($module == 'ModComments') {
		$focus = new Notifications($current_user->id,$module);
		if (count($ids) > 0) {
			foreach ($ids as $id) {
				$focus->deleteNotification($id, $forced);
			}
		}
		// conteggio
		$unseen =  $focus->getUserNotificationNo();
		if (!empty($unseen)) {
			$notif = $unseen;
		}

	} elseif ($module == 'ModNotifications') {
		$focus = CRMEntity::getInstance($module);
		if (count($ids) > 0) {
			foreach ($ids as $id) {
				$focus->setRecordSeen($id);
			}
		}
		// conteggio
		$widgetInstance = $focus->getWidget('DetailViewBlockCommentWidget');
		$widgetInstance->setDefaultCriteria(0);
		$unseen = $widgetInstance->getUnseenComments('',array('ID'=>''));
		if (!empty($unseen) && is_array($unseen)) {
			$notif = count($unseen);
		}
	}

	// return
	echo Zend_Json::encode($notif);
}
?>