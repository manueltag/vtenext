<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@106857 */
require_once('include/utils/ModLightUtils.php');
$MLUtils = ModLightUtils::getInstance();

$action = $_REQUEST['subaction'];
if ($action == 'addfield') {
	$MLUtils->addTableField($_REQUEST['blockno'], $_REQUEST['addfieldno'], Zend_Json::decode($_REQUEST['properties']));
} elseif ($action == 'editfield') {
	$MLUtils->editTableField($_REQUEST['blockno'], $_REQUEST['editfieldno'], Zend_Json::decode($_REQUEST['properties']));
} elseif ($action == 'deletefield') {
	$MLUtils->deleteTableField($_REQUEST['blockno'], $_REQUEST['editfieldno']);
}

$blockInstance = Vtecrm_Block::getInstance($_REQUEST['blockno']);
$_REQUEST['sub_mode'] = '';
$_REQUEST['formodule'] = $blockInstance->module->name;
$_REQUEST['ajax'] = 'true';
include('modules/Settings/LayoutBlockList.php');
exit;