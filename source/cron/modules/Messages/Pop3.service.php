<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/
// crmv@42264

require('config.inc.php');
require_once('include/utils/utils.php');
require_once('include/logging.php');

// Get the list of Invoice for which Recurring is enabled.

global $log;
$log =& LoggerManager::getLogger('Messages');
$log->debug("invoked Messages");

$focus = CRMEntity::getInstance('Messages');
$focus->fetchPop3();

$log->debug("end Messages procedure");
?>