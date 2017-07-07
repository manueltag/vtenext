<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@35153 */
/* crmv@103922 */
global $current_language, $path;
if ($installation_mode) {
	global $currentModule, $mod_strings, $app_strings, $default_language, $theme;
	$currentModule = 'Morphsuit';
	$current_language = $default_language;
	$path = '../../';
	$mod_strings = return_module_language($current_language, $currentModule);
	$app_strings = return_application_language($current_language);
	$small_page_path = $path;
}
$small_browser_title = 'VTE Activation';
$small_page_title = 'SKIP_TITLE';
include('themes/SmallHeader.php');
?>

<script language="javascript" type="text/javascript" src="<?php echo $path; ?>include/js/<?php echo $current_language; ?>.lang.js"></script>	
<script language="javascript" type="text/javascript" src="<?php echo $path; ?>include/js/general.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $path; ?>themes/<?php echo $theme; ?>/vte_bootstrap.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $path; ?>themes/<?php echo $theme; ?>/style.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $path; ?>themes/<?php echo $theme; ?>/morphsuit.css" />
