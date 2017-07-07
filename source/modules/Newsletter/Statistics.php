<?php
/*+*************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@101506 */

global $currentModule;
$currentModule = 'Campaigns';
$newsletterStatistics = true;

$focus = CRMEntity::getInstance('Newsletter');
$focus->retrieve_entity_info($_REQUEST['record'],'Newsletter');

$_REQUEST['statistics_newsletter'] = $_REQUEST['record'];
$_REQUEST['record'] = $focus->column_fields['campaignid'];

include('modules/Campaigns/Statistics.php');