<?php
require_once('include/utils/utils.php');
$record = vtlib_purify($_REQUEST['myfilesid']);
if ($record){
	$obj = CRMEntity::getInstance('Myfiles');
	$obj->id = $record;
	$obj->trash('Myfiles',$record);
}
?>