<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@48501 */

require('config.inc.php');
require_once('include/utils/utils.php');
require_once('include/logging.php');

ini_set('memory_limit','256M');

global $log;
$log =& LoggerManager::getLogger('Messages');
$log->debug("invoked sending emails procedure");

$_REQUEST['service'] = 'Messages';	//crmv@53929
$focus = CRMEntity::getInstance('Emails');
$focus->processSendingQueue($_REQUEST['ustart'],$_REQUEST['uend']);	//crmv@71322

$log->debug("end sending emails procedure");
?>