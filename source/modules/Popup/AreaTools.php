<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@54707 */

require_once('Smarty_setup.php');
require_once('modules/Area/Area.php');
$areaManager = AreaManager::getInstance();
$block_area_layout = $areaManager->getToolValue('block_area_layout');
if ($block_area_layout == 1) {
	$block_area_layout = 'checked';
} else {
	$block_area_layout = '';
}

$smarty = new vtigerCRM_Smarty();
$smarty->assign('BLOCK_AREA_LAYOUT', $block_area_layout);
$smarty->display('modules/Popup/AreaTools.tpl');
?>