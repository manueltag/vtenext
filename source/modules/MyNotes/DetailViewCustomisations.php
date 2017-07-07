<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/

if ($_REQUEST['mode'] == 'SimpleView') {
	$_SESSION['mynote_selected'] = $focus->id;
	$smarty->assign('SHOW_TURBOLIFT_BACK_BUTTON','no');
	$smarty->assign('TURBOLIFT_HREF_TARGET_LOCATION','window.top.location.href');
	$smarty_template = "modules/$currentModule/MyNotesDetailView.tpl";
} elseif ($_REQUEST['mode'] == 'DetailViewMyNotesWidget') {
	$smarty->assign('NAVIGATION',$focus->getDetailViewNavigation($_REQUEST['parent']));
	$smarty->assign('NOTEPARENTID',$_REQUEST['parent']);
	$smarty->assign('PAGE_TITLE','SKIP_TITLE');
	$smarty->assign('SHOW_TURBOLIFT','no');
	// crmv@97692
	$JSGlobals = ( function_exists('getJSGlobalVars') ? getJSGlobalVars() : array() );
	$smarty->assign('JS_GLOBAL_VARS', Zend_Json::encode($JSGlobals));
	// crmv@97692e
	$smarty->assign('MYNOTESWIDGET',true);	//crmv@115268
	$smarty_template = "modules/$currentModule/MyNotesDetailView.tpl";
} elseif ($_REQUEST['action'] == 'DetailView' || $_REQUEST['file'] == 'DetailView') {
	include("modules/$currentModule/SimpleView.php");
	exit;
}
?>