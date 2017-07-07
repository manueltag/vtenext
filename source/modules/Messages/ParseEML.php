<?php
/*+*************************************************************************************
* The contents of this file are subject to the VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with the License
* The Original Code is: VTECRM
* The Initial Developer of the Original Code is VTECRM LTD.
* Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
* All Rights Reserved.
***************************************************************************************/
/* crmv@88981 */

include_once('include/utils/utils.php');
global $adb,$current_user,$currentModule,$table_prefix, $default_charset;

$record = intval($_REQUEST['record']);
$contentid = intval($_REQUEST['contentid']);

$focus = CRMEntity::getInstance($currentModule);
$focus->retrieve_entity_info($record,$currentModule);

$messagesid = 0;
$error = '';
$success = $focus->parseEML($contentid, $messagesid, $error);

$return_array = array('success'=>$success,'messageid'=>$messagesid, 'error' => $error);
echo Zend_Json::encode($return_array);
die();
