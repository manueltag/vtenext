<?php
global $adb,$current_user;
$adb->pquery('update tbl_s_showncalendar set selected = ? where userid = ?',array(0,$current_user->id));
$checkedUsers = explode(',',$_REQUEST['checkedUsers']);
foreach($checkedUsers as $shownid)
	$adb->pquery('update tbl_s_showncalendar set selected = ? where userid = ? and shownid = ?',array(1,$current_user->id,$shownid));
?>