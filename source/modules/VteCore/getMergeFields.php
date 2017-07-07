<?php
//crmv@69201
$module = vtlib_purify($_REQUEST['forModule']);

$fields = array();
$fields[] = getMergeFields($module,"available_fields");
$fields[] = getMergeFields($module,"fileds_to_merge");

echo Zend_Json::encode($fields);
//crmv@69201e
?>