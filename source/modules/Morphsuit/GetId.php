<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@54179 */
include_once('../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
$saved_morphsuit = getSavedMorphsuit();
if (!empty($saved_morphsuit)) {
	$saved_morphsuit = urldecode(trim($saved_morphsuit));
	$private_key = substr($saved_morphsuit,0,strpos($saved_morphsuit,'-----'));
	$enc_text = substr($saved_morphsuit,strpos($saved_morphsuit,'-----')+5);
	$saved_morphsuit = @decrypt_morphsuit($private_key,$enc_text);
	$saved_morphsuit = Zend_Json::decode($saved_morphsuit);
	$saved_morphsuit_id = $saved_morphsuit['id'];
	setCacheMorphsuitNo($saved_morphsuit_id);
	echo $saved_morphsuit_id;
}
if ($_REQUEST['resetTime2check'] == 'yes') {
	global $adb, $table_prefix;
	$adb->pquery("delete from {$table_prefix}_time2check where cwhat = ?",array('trackVTEInfo'));
	$adb->pquery("insert into {$table_prefix}_time2check (cwhat,cwhen) values (?,?)",array('trackVTEInfo',time()+864000));	//10 days
}
exit;
?>