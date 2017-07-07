<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@97566 */

include('modules/Settings/ProcessMaker/Metadata/TimerIntermediate.php');

$smarty->assign("START_LABEL", getTranslatedString('LBL_PM_AFTER','Settings'));
$smarty->assign("END_LABEL", getTranslatedString('LBL_PM_GO_TO_NEXT_STEP','Settings'));