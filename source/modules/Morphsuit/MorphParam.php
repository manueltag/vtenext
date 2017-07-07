<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/
include_once('../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
global $adb, $table_prefix;
$focus = CRMEntity::getInstance("Morphsuit");
$value = vtlib_purify($_POST['value']);
echo $focus->morph_par($value);
exit;
?>