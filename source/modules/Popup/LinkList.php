<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@43864 */
require_once('include/ListView/SimpleListView.php');

$from_module = vtlib_purify($_REQUEST['from_module']);
$from_crmid = intval($_REQUEST['from_crmid']);
$module = vtlib_purify($_REQUEST['mod']);
$mode = $_REQUEST['popup_mode'];
$relationId = intval($_REQUEST['relation_id']); // crmv@56603

$callback_link = vtlib_purify($_REQUEST['callback_link']);
if (empty($callback_link)) $callback_link = 'LPOP.link';

$popup = Popup::getInstance();

$createMods = $popup->getCreateModules($from_module, $from_crmid, $mode);
$canCreate = ($mode != 'compose' && in_array($module, $createMods)); // crmv@43050

if ($mode = 'linkrecord' && empty($from_crmid)) $canCreate = false;

// crmv@56603
$Slv = SimpleListView::getInstance($module); // fake module, but works as well
$Slv->entriesPerPage = 20;
$Slv->showSuggested = ($from_module == 'Messages');
$Slv->showCreate = $canCreate;
$Slv->showCheckboxes = true;
if ($from_module == 'ModComments') $Slv->showCheckboxes = false;	//crmv@58208
if ($Slv->showSuggested && !empty($from_crmid)) {
	$fromFocus = CRMEntity::getInstance($from_module);
	$fromFocus->retrieve_entity_info($from_crmid, $from_module);
	$fromFocus->id = $from_crmid;
	$suggestedIds = $fromFocus->getSuggestedRelIds();
	$Slv->setSuggestedIds($suggestedIds);
	$Slv->setParentId($from_crmid);
	$Slv->hideLinkedRecords = true;
} elseif (!empty($from_crmid)) {
	$Slv->setParentId($from_crmid);
}
$Slv->selectFunction = 'LPOP.select';
$Slv->createFunction = 'LPOP.showCreatePanel';
$Slv->addSelectedFunction = 'LPOP.linkSelected';
// hide the already linked records
if ($relationId > 0) {
	$Slv->setRelationId($relationId);
	$Slv->hideLinkedRecords = true;
}
// crmv@56603e

// crmv@44323
// query alter functions
$for_focus = CRMEntity::getInstance($from_module);
if (method_exists($for_focus, 'PopupQueryChange')) {
	$Slv->queryChangeFunction = array($from_module, 'PopupQueryChange'); // crmv@45949
}
if (isset($_REQUEST['extra_list_params'])) {
	$Slv->extraInputs = $_REQUEST['extra_list_params'];
}
// crmv@44323e

//crmv@48964
$sdk_file = SDK::getPopupQuery('related',$from_module,$module);
if ($sdk_file != '' && Vtiger_Utils::checkFileAccess($sdk_file)) {
	$Slv->sdkPopupQuery = $sdk_file;
}
//crmv@48964e

$list = $Slv->render();
echo $list;
?>