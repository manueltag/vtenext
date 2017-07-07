<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/

global $currentModule, $theme, $app_strings;

if(isPermitted($currentModule, 'EditView') != 'yes') exit;

require_once('Smarty_setup.php');
$smartyCreate = new vtigerCRM_Smarty;
$smartyCreate->assign('APP', $app_strings);
$smartyCreate->assign('THEME', $theme);
$smartyCreate->assign('MODULE', $currentModule);
$smartyCreate->assign('PAGE_TITLE', 'SKIP_TITLE');
$smartyCreate->assign('PARENT', $_REQUEST['parent']);

$JSGlobals = ( function_exists('getJSGlobalVars') ? getJSGlobalVars() : array() );
$smartyCreate->assign('JS_GLOBAL_VARS',Zend_Json::encode($JSGlobals));

$_REQUEST['hide_button_list'] = 1;
require_once 'modules/VteCore/EditView.php';
$createCode = $smarty->fetch('CreateView.tpl');

$createCode = str_replace('padding:5px;padding-top:15px;','',$createCode);
$createCode = str_replace('<tr style="height:25px"><td>&nbsp;</td></tr>','',$createCode);
$createCode = str_replace('<td align=right valign=top></td>','',$createCode);

$smartyCreate->assign('CODE', $createCode);
$smartyCreate->display('modules/MyNotes/widgets/Create.tpl');
