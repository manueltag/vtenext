<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@43942 crmv@54707 */

$function = vtlib_purify($_REQUEST['function']);

require_once('modules/Area/Area.php');
$areaManager = AreaManager::getInstance();

switch($function){
	case 'propagateLayout':
		$areaManager->$function();
		break;
	case 'blockLayout':
		$areaManager->$function(vtlib_purify($_REQUEST['value']));
		break;
}
exit;
?>