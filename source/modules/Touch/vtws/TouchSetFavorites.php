<?php
/* crmv@34559 */

global $login, $userId;

if (!$login || !$userId) {
	echo 'Login Failed';
} else {

	require_once('modules/SDK/src/Favorites/Utils.php');

	$ids = array_map('intval', explode(':', $_REQUEST['records']));
	foreach ($ids as $id) {
		setFavorite($userId, $id);
	}

	echo "SUCCESS";
}
?>