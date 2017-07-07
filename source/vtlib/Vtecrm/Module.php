<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
require_once('vtlib/Vtiger/Module.php');
include_once('vtlib/Vtecrm/Access.php');
include_once('vtlib/Vtecrm/Panel.php'); // crmv@104568
include_once('vtlib/Vtecrm/Block.php');
include_once('vtlib/Vtecrm/Field.php');
include_once('vtlib/Vtecrm/Filter.php');
include_once('vtlib/Vtecrm/Profile.php');
include_once('vtlib/Vtecrm/Menu.php');
include_once('vtlib/Vtecrm/Link.php');
include_once('vtlib/Vtecrm/Event.php');
include_once('vtlib/Vtecrm/Webservice.php');
include_once('vtlib/Vtecrm/Version.php');

class Vtecrm_Module extends Vtiger_Module {
	const EVENT_MODULE_ENABLED     = 'module.enabled';
	const EVENT_MODULE_DISABLED    = 'module.disabled';
	const EVENT_MODULE_POSTINSTALL = 'module.postinstall';
	const EVENT_MODULE_PREUNINSTALL= 'module.preuninstall';
	const EVENT_MODULE_PREUPDATE   = 'module.preupdate';
	const EVENT_MODULE_POSTUPDATE  = 'module.postupdate';
}

