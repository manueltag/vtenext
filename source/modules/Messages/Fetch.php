<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/
global $current_user;

$account = vtlib_purify($_REQUEST['account']);
$folder = mb_convert_encoding($_REQUEST['folder'] , "UTF7-IMAP", "UTF-8" ); //crmv@61520
$only_news = ($_REQUEST['only_news'] == 'yes');

$focus = CRMEntity::getInstance('Messages');
$result = $focus->fetch($account, $folder, $only_news);
//crmv@62821
if (empty($result)) {
	$focus->interval_schedulation = '';
	$result = $focus->fetch($account, $folder, $only_news);
}
//crmv@62821e

echo $result;
exit();
?>