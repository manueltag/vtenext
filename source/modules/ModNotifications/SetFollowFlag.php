<?php
$record = vtlib_purify($_REQUEST['record']);
$type = vtlib_purify($_REQUEST['type']);
if (vtlib_purify($_REQUEST['mode']) != 'get_image') {
	$focus = CRMEntity::getInstance('ModNotifications');
	$focus->toggleFollowFlag($current_user->id,$record,$type);
}
echo ':#:SUCCESS';
echo getFollowCls($record,$type);
exit;
