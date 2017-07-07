<?php
/* +*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 * ************************************************************************************* */

// crmv@98866

$longFields = array('subject', 'assigned_user_id');
$editSkipFields = array('parent_id', 'contact_id', 'exp_duration', 'date_start', 'due_date');

$smarty->assign("LONG_FIELDS", $longFields);
$smarty->assign("EDIT_SKIP_FIELDS", $editSkipFields);
$smarty->assign("CALENDAR_POPUP", true);

$smarty_template = "modules/Calendar/AddTodoForm.tpl";

?>