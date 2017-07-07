<?php
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/

require_once('include/utils/utils.php');
require_once('Smarty_setup.php');
//crmv@8056
require_once('user_privileges/CustomQuoteNo.php');
require_once('user_privileges/CustomPorderNo.php');
require_once('user_privileges/CustomSorderNo.php');
require_once('user_privileges/CustomInvoiceNo.php');
require_once('user_privileges/CustomNoteNo.php'); //vtc

global $app_strings;
global $mod_strings;
global $currentModule;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
global $current_language;

$smarty = new vtigerCRM_Smarty;

$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("CMOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("IMAGE_PATH",$image_path);

/*
if($singlepane_view == 'true')
	$viewstatus = 'enabled';
else
	$viewstatus = 'disabled';

$smarty->assign("ViewStatus", $viewstatus);
*/

$smarty->assign("inv_str", $inv_str);
$smarty->assign("inv_no", $inv_no);

$smarty->assign("quote_str", $quote_str);
$smarty->assign("quote_no", $quote_no);

$smarty->assign("porder_str", $porder_str);
$smarty->assign("porder_no", $porder_no);

$smarty->assign("sorder_str", $sorder_str);
$smarty->assign("sorder_no", $sorder_no);

//vtc
$smarty->assign("note_str", $note_str);
$smarty->assign("note_no", $note_no);
//vtc e

$smarty->display('Settings/CustomInvoiceNo.tpl');
//crmv@8056e

?>
