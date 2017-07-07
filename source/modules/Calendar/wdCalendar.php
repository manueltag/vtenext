<?php
//crmv@17001
if ($_REQUEST['subfile'] != '')
	$file = $_REQUEST['subfile'];
else
	$file = "sample";
	
//crmv@20324
global $adb,$current_user,$current_user_cal_color,$table_prefix;
$res = $adb->query('select cal_color from '.$table_prefix.'_users where id = '.$current_user->id);
$current_user_cal_color = $adb->query_result($res,0,'cal_color');
//crmv@20324e

include("modules/Calendar/wdCalendar/$file.php");
//crmv@17001e
?>