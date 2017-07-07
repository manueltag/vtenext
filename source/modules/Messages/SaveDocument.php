<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/

/* crmv@63475 */

global $currentModule;
$record = vtlib_purify($_REQUEST['record']);	// message record
$contentid = vtlib_purify($_REQUEST['contentid']);	// attachment id
$linkto = vtlib_purify($_REQUEST['linkto']);
$linkto_module = vtlib_purify($_REQUEST['linkto_module']);

$focus = CRMEntity::getInstance($currentModule);
$focus->saveDocument($record,$contentid,$linkto,$linkto_module);

die('SUCCESS');
?>