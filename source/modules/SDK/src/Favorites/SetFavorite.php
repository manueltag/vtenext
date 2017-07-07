<?php
//crmv@26986
global $current_user;
$record = (int)$_REQUEST['record'];
if ($record != '' && $record != 0) {
	require_once('modules/SDK/src/Favorites/Utils.php');
	echo setFavorite($current_user->id,$record);
	echo '###'.getHtmlFavoritesList($current_user->id);
	exit;
}
//crmv@26986e
?>