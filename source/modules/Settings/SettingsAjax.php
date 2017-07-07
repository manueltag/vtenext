<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 ********************************************************************************/
global $table_prefix;
//crmv@30105
$file=vtlib_purify($_REQUEST['file']);
if(isset($file) && $file !='')
{
	require_once('modules/Settings/'.$file.'.php');
}
//crmv@30105e
if(isset($_REQUEST['orgajax']) && ($_REQUEST['orgajax'] !=''))
{
	require_once('modules/Settings/CreateSharingRule.php');
}
//crmv@7222
if(isset($_REQUEST['orgajaxusr']) && ($_REQUEST['orgajaxusr'] !=''))
{
	require_once('modules/Settings/CreateSharingRuleUsers.php');
}
//crmv@7222e
//crmv@7221
if(isset($_REQUEST['orgajaxadv']) && ($_REQUEST['orgajaxadv'] !=''))
{
	if(isset($_REQUEST['advprivilege']) && ($_REQUEST['advprivilege'] !=''))
		require_once('modules/Settings/CreateAdvSharingRulePerm.php');
	else require_once('modules/Settings/CreateAdvSharingRule.php');
}
//crmv@7221e
?>