<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@98484 */
require_once('include/utils/AlertNotifications.php');
$focus = AlertNotifications::getInstance();
$mode = vtlib_purify($_REQUEST['mode']);
switch($mode) {
	case 'getlabel':
		$label = $focus->getLabel(intval($_REQUEST['id']));
		if (!$label) $label = '';
		die($label);
		break;
	case 'isseen':
		$isSeen = $focus->isSeen(intval($_REQUEST['id']),intval($_REQUEST['userid']));
		($isSeen) ? $return = 'yes' : $return = 'no';
		die($return);
		break;
	case 'setseen':
		$focus->setSeen(intval($_REQUEST['id']),intval($_REQUEST['userid']));
		break;
}