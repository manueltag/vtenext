<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/

/* crmv@86304 */

require_once('Smarty_setup.php');

$focus = CRMEntity::getInstance($currentModule);

$idlist = $_REQUEST['idlist'];
if (strpos($idlist,';') !== false) {
	$idlist = explode(';',$idlist);
} elseif (strpos($idlist,',') !== false) {
	$idlist = explode(',',$idlist);
} else {
	$idlist = array($idlist);
}
$idlist = array_filter($idlist);
if (empty($idlist)) die('Empty ID');

$smarty = new vtigerCRM_Smarty();

foreach($idlist as $id) {
	$info = $focus->getEntityPreview($id);
	$smarty->assign('CARDID','preView'.$id);
	$smarty->assign('CARDMODULE',$info['module']);
	$smarty->assign('CARDMODULE_LBL',$info['modulelbl']);
	$smarty->assign('CARDNAME',$info['name']);
	$smarty->assign('IMG',$info['img']);
	$smarty->assign('CARDDETAILS',$info['details']);
	$smarty->assign('CARDONCLICK','');
	$smarty->display('Card.tpl');
}
?>