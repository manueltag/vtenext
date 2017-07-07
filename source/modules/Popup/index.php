<?php
/***************************************************************************************
* The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with the License
* The Original Code is:  CRMVILLAGE.BIZ VTECRM
* The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
* Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
* All Rights Reserved.
***************************************************************************************/
/* crmv@42752 crmv@43050 crmv@43864 crmv@56603 */

require_once('Smarty_setup.php');
require_once('modules/Popup/Popup.php');

global $adb, $table_prefix;
global $mod_strings, $app_strings, $theme;
global $currentModule, $current_user;

$from_module = vtlib_purify($_REQUEST['from_module']);
if ($from_module == 'Emails') $from_module = 'Messages';
$from_crmid = intval($_REQUEST['from_crmid']);
$mode = $_REQUEST['mode'];
$show_module = vtlib_purify($_REQUEST['show_module']);

$parentid = intval($_REQUEST['record']);
$contentid = $_REQUEST['contentid'];
$idlistReq = vtlib_purify($_REQUEST['idlist']);
$onlypeople = ($_REQUEST['onlypeople'] == true);
$showOnly = $_REQUEST['show_only'];
$relationId = intval($_REQUEST['relation_id']); // this is the relationid of the relation which opened the popup
$modules_list = array_filter(explode(',', $_REQUEST['modules_list']));

if (empty($from_module)) die('Module not specified');

$popup = Popup::getInstance();
$focus = CRMEntity::getInstance($from_module);

$smarty = new vtigerCRM_Smarty();
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('THEME', $theme);

// callback javascript functions to be called when a user click on save in create or link
// params are "module" and "crmid" [per link]
// TODO: mettere nella classe, e generalizzare, più parametri
$callback_link = vtlib_purify($_REQUEST['callback_link']);
$callback_create = vtlib_purify($_REQUEST['callback_create']);
$callback_close = vtlib_purify($_REQUEST['callback_close']);

if (empty($callback_link)) $callback_link = 'LPOP.link';
if (empty($callback_create)) $callback_create = 'LPOP.create';

if ($showOnly == 'create') {
	$defaultLinkAction = 'create';
	($callback_create == 'LPOP.convert') ? $pageTitle = getTranslatedString('LBL_CONVERT_ACTION','APP_STRINGS') : $pageTitle = getTranslatedString('LBL_CREATE','APP_STRINGS');	//crmv@44609
} else {
	$defaultLinkAction = 'link';
	$pageTitle = getTranslatedString('LBL_LINK_ACTION',$currentModule);
	if ($mode == 'linkdocument') {
		$pageTitle .= " ".getTranslatedString('LBL_ATTACHMENT');
	}
}

if ($from_module == 'Messages' && $mode != 'compose' && !empty($from_crmid)) {
	// get related ids from messageid
	$emails = array();
	$idlist = array();

	// retrieve linked ids (use recipients for sent, sender/cc for inbox
	$focus->retrieve_entity_info($from_crmid, $from_module, false);
	$focus->id = $from_crmid;

	// retrieve name and email
	$folder = $focus->column_fields['folder'];
	$specialFolders = $focus->getSpecialFolders();
	if (in_array($folder,array($specialFolders['Sent'],$specialFolders['Drafts']))) {
		$emails = array_filter(array_map('trim', explode(',', $focus->column_fields['mto'])));
		$emails = array_merge($emails, array_filter(array_map('trim', explode(',', $focus->column_fields['mcc']))));
		$name = trim(trim($focus->column_fields['mto_n']),'"');
	} else {
		$emails = array_filter(array_map('trim', explode(',', $focus->column_fields['mfrom'])));
		$emails = array_merge($emails, array_filter(array_map('trim', explode(',', $focus->column_fields['mcc']))));
		$name = trim(trim($focus->column_fields['mfrom_n']),'"');
	}

	// remove myself from emails - REMOVED
	/*
	$my_addresses[] = getSingleFieldValue($table_prefix.'_users', 'email1', 'id', $current_user->id);
	$my_addresses[] = getSingleFieldValue($table_prefix.'_users', 'email2', 'id', $current_user->id);
	$emails = array_diff($emails, $my_addresses);
	*/

	$email = $emails[0];

	if (!empty($email) && $email != 'undisclosed-recipients:;') {
		// use all possible emails to retrieve entitities, and also use a deep search
		foreach ($emails as $em) {
			$list = $focus->getEntitiesFromEmail($em);
			foreach ($list as $mod => $ids) {
				if (!is_array($idlist[$mod])) {
					$idlist[$mod] = $ids;
				} else {
					$idlist[$mod] = array_unique(array_merge($idlist[$mod], $ids));
				}
			}
		}
	}

} elseif (!empty($idlistReq)) {
	// get related ids from request (and related accounts/contacts)

	$idlist = array();
	$RM = RelationManager::getInstance();

	$idlistReq = array_filter(explode(';', $idlistReq));
	$others = array();
	foreach ($idlistReq  as $id) {
		if (strpos($id,'@') !== false) $id = substr($id,0,strpos($id,'@'));	//crmv@86304
		$mod = getSalesEntityType($id);
		if ($mod) {
			$others = array_merge($others, $RM->getRelatedIds($mod, $id, array('Accounts', 'Contacts')));
		}
	}

	$idlistReq = array_unique(array_merge($idlistReq, $others));

	foreach ($idlistReq  as $id) {
		$mod = getSalesEntityType($id);
		if ($mod) $idlist[$mod][] = $id;
	}

}

$linkMods = array();

if ($onlypeople) {
	$popAction = ($showOnly == 'create' ? 'create' : 'list');
	foreach ($focus->peopleModules as $m) {
		$linkMods[] = array('module'=>$m, 'action'=>$popAction);
	}
} else {
	$createMods = $popup->getCreateModules($from_module, $from_crmid, $mode);

	if ($showOnly == 'create') {
		// modules with create
		foreach ($createMods as $m) {
			$linkMods[] = array('module'=>$m, 'action'=>'create');
		}
	} else {
		// modules with select only or select and create
		$lMods = $popup->getLinkModules($from_module, $from_crmid, $mode);
		if ($showOnly == 'link') {
			$allMods = $lMods;
		} else {
			$allMods =  $popup->getAllModules($from_module, $from_crmid, $mode);
		}

		foreach ($allMods as $m) {
			$act = (in_array($m, $lMods) ? 'list' : 'create');
			$linkMods[] = array('module'=>$m, 'action'=>$act);
		}

	}
}

if (!empty($modules_list)) {
	foreach ($linkMods as $k=>$lmod) {
		if (!in_array($lmod['module'], $modules_list)) unset($linkMods[$k]);
	}
}

// now try to guess the relation id for the linked modules
$linkModsNames = array_map(create_function('$m', 'return $m["module"];'), $linkMods);
if (!$RM) $RM = RelationManager::getInstance();
$rels = $RM->getRelations($from_module, null, $linkModsNames);
if ($rels) {
	foreach ($linkMods as $k=>&$lmod) {
		$foundRel = null;
		foreach ($rels as $rel) {
			if ($rel->getSecondModule() == $lmod['module']) {
				$foundRel = $rel;
				break;
			}
		}
		if ($foundRel && $foundRel->relationid > 0) {
			$lmod['relation_id'] = $foundRel->relationid;
		} else {
			$lmod['relation_id'] = 0;
		}
	}
}

$smarty->assign('LINK_MODULES', $linkMods);

$smarty->assign('BROWSER_TITLE', $pageTitle);
$smarty->assign('PAGE_TITLE', $pageTitle);
$smarty->assign('HEADER_Z_INDEX', 10);

//crmv@46678 crmv@65506
if (empty($show_module) && !empty($linkMods)) {
	if (count($linkMods) == 1 || (count($linkMods)>1 && PerformancePrefs::getBoolean('POPUP_AUTOSELECT_MODULE',true))) {
		$show_module = $linkMods[0]['module'];
	}
}
//crmv@46678e crmv@65506e

$extraInputs = array(
	'show_module' => $show_module,
	'modules_list' => Zend_Json::encode($modules_list),
	'popup_mode' => $mode,
	'from_module' => $from_module,
	'from_crmid' => $from_crmid,
	'original_email' => $email,
	'original_name' => $name,
	'uikey_from' => $_REQUEST['uikey'], // crmv@43050 for conversations: TODO: generalize
	'relevant_ids' => Zend_Json::encode($idlist),
	'callback_link' => $callback_link,
	'callback_create' => $callback_create,
	'callback_close' => $callback_close,
	'contentid' => $contentid,
	'default_link_action' => $defaultLinkAction,
	'extra_popup_params' => $_REQUEST['extra_popup_params'], // crmv@44323 - you can use this to pass your parameters to the popup
	'extra_list_params' => $_REQUEST['extra_list_params'], // crmv@44323
	'sdk_view_all' => $_REQUEST['sdk_view_all'],	//crmv@48964
	'original_relation_id' => $relationId,	// relation_id of the opener related list
);

// and now attachment block (if permitted and not from direct attachment link)

if ($from_module == 'Messages' && $contentid == '' && isPermitted('Documents', 'EditView') == 'yes' && $focus->haveAttachments($from_crmid)) {
	$attachments = $focus->getAttachments();
	$smarty->assign('ATTACHMENTS', $attachments);
}

include_once('vtlib/Vtiger/Link.php');
$hdrcustomlink_params = Array('MODULE'=>$from_module);
$COMMONHDRLINKS = Vtiger_Link::getAllByType(Vtiger_Link::IGNORE_MODULE, Array('HEADERSCRIPT', 'HEADERCSS'), $hdrcustomlink_params);
$smarty->assign('HEADERSCRIPTS', $COMMONHDRLINKS['HEADERSCRIPT']);
$smarty->assign('HEADERCSS', $COMMONHDRLINKS['HEADERCSS']);

// crmv@43050
// add extra js default js for the parent module
$extraJs = array();
if ($from_module) {
	$path = "modules/$from_module/$from_module.js";
	if (file_exists($path)) {
		$extraJs[] = $path;
	}
}

$smarty->assign('EXTRA_JS', array_unique($extraJs));
$smarty->assign('EXTRA_INPUTS', $extraInputs);
// crmv@43050e

$JSGlobals = ( function_exists('getJSGlobalVars') ? getJSGlobalVars() : array() );
$smarty->assign('JS_GLOBAL_VARS',Zend_Json::encode($JSGlobals));

$smarty->display('modules/Popup/Popup.tpl');
?>