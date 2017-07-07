<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/

global $currentModule;
$account = vtlib_purify($_REQUEST['account']);
$folder = $_REQUEST['folder'];
$action = $_REQUEST['faction'];
$focus = CRMEntity::getInstance($currentModule);
$focus->flagFolder($account,$folder,$action);
exit;
?>