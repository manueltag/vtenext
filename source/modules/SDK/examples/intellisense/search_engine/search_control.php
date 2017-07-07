<?php
$fieldname = $_REQUEST["field"];
$id = $_REQUEST["id"];
$session_name_global = "global_search_cache_$fieldname";
$value = str_replace("'","\'",$_REQUEST["value"]);
$found = false;
if (!isset($_SESSION[$session_name_global])){
	require_once('plugin_'.$_REQUEST["field"].'.php');
}
foreach ($_SESSION[$session_name_global] as $key=>$arr){
	if ($arr[$fieldname] == $id){
		if ($arr['calltype'] == utf8_encode($value) || $arr['calltype'] == $value) {
			$found = true;
			break;
		}
	}
}
if ($found) echo ":#:SUCCESS";
else echo ":#:FAILURE";
?>