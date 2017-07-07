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

require('config.inc.php');
require_once('Smarty_setup.php');
require_once('include/utils/utils.php');
require_once('modules/Morphsuit/utils/MorphsuitUtils.php');

global $default_theme, $theme;
global $default_language, $current_language, $app_strings;

if (empty($theme)) {
	$theme = $default_theme;
}
if (empty($current_language)) {
	$current_language = $default_language;
}

include('themes/LoginHeader.php');

$smarty = new vtigerCRM_Smarty;
$smarty->assign("APP", $app_strings);
$smarty->assign("LICENSE_FILE", 'licenza.txt');

$canUpdate = ($_REQUEST['use_current_login'] == 'yes');
$smarty->assign("CAN_UPDATE", $canUpdate);
if ($canUpdate) {
	$smarty->assign("MORPHSUIT_NO", getMorphsuitNo());
}

$freeVersion = isFreeVersion();
$smarty->assign("FREE_VERSION", $freeVersion);

$smarty->display('Copyright.tpl');
