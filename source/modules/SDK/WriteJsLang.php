<?php
include_once('include/utils/utils.php');
$language = vtlib_purify($_REQUEST['language']);
$mod_strings = Zend_Json::decode(vtlib_purify($_REQUEST['params']));
insert_language('ALERT_ARR',$language,$mod_strings);
exit;
?>