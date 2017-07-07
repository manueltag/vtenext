<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/

// crmv@119414

global $current_user;

require_once "Smarty_setup.php";
require_once "data/Tracker.php";
$tracker = new Tracker();
$history = $tracker->get_recently_viewed($current_user->id);

$smarty = new vtigerCRM_Smarty();
$smarty->assign("HISTORY", $history);
$smarty->display("FastLastViewed.tpl");

?>