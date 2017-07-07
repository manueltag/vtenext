<?php
/*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
  * ("License"); You may not use this file except in compliance with the License
  * The Original Code is:  vtiger CRM Open Source
  * The Initial Developer of the Original Code is vtiger.
  * Portions created by vtiger are Copyright (C) vtiger.
  * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
  * All Rights Reserved.
  *
  ********************************************************************************/

/* crmv@sdk-25183	crmv@25671	crmv@37463	crmv@40799 */

$tmp_action = '';
$tmp_module = $_REQUEST['module'];
if (isModuleInstalled('SDK')) {
	$tmp_action = SDK::getFile($tmp_module,$_REQUEST['file']);
}
if ($tmp_action == '') {
	$tmp_action = $_REQUEST['file'];
}
$tmp_action = str_replace('..', '', $tmp_action);

$is_action = false;
$in_dir = @scandir($root_directory.'modules/'.$tmp_module);
$temp_arr = Array("CVS","Attic");
$res_arr = @array_intersect($in_dir,$temp_arr);
if(count($res_arr) == 0 && !preg_match("/[\/.]/",$tmp_module)) {
	if(@in_array($tmp_action.".php",$in_dir))
		$is_action = true;
}
if(!$is_action) {
	$in_dir = @scandir($root_directory.'modules/VteCore');
	$res_arr = @array_intersect($in_dir,$temp_arr);
	if(count($res_arr) == 0 && !preg_match("/[\/.]/",'VteCore')) {
		if(@in_array($tmp_action.".php",$in_dir)) {
			$tmp_module = 'VteCore';
		}
	}
}

checkFileAccess('modules/'.$tmp_module.'/'.$tmp_action.'.php');
require_once('modules/'.$tmp_module.'/'.$tmp_action.'.php');
?>