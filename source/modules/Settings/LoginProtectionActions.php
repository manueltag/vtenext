<?php
/*+*************************************************************************************
* The contents of this file are subject to the VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with the License
* The Original Code is: VTECRM
* The Initial Developer of the Original Code is VTECRM LTD.
* Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
* All Rights Reserved.
***************************************************************************************/

/* crmv@56023 */

require_once("include/utils/utils.php");
require_once('modules/Settings/LoginProtectionViewer.php');
global $adb,$current_user;

if (!is_admin($current_user)) return false;

$focus = LoginProtectionViewer::getInstance();
if($focus->getLoginProtectionStatus()){
	$mode = vtlib_purify($_REQUEST['mode']);
	
	switch ($mode){
		case 'whitelist':
			$recordid = vtlib_purify($_REQUEST['id']);
			$user = CRMEntity::getInstance('Users');
			$adb->pquery("update {$user->track_login_table} set status = ?, date_whitelist = ? where id = ?",array('W',date('Y-m-d H:i:s'),$recordid));
		break;
		default:
			return false;
		break;
	}
}
?>