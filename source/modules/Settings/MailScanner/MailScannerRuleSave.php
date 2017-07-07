<?php
/*********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 ********************************************************************************/
require_once('modules/Settings/MailScanner/core/MailScannerInfo.php');
require_once('modules/Settings/MailScanner/core/MailScannerRule.php');
require_once('modules/Settings/MailScanner/core/MailScannerAction.php');
require_once('Smarty_setup.php');

global $app_strings, $mod_strings, $currentModule, $theme, $current_language;

$scannername = $_REQUEST['scannername'];
$scannerruleid= $_REQUEST['ruleid'];
$scanneractionid=$_REQUEST['actionid'];

$scannerinfo = new Vtiger_MailScannerInfo($scannername);
$scannerrule = new Vtiger_MailScannerRule($scannerruleid);

$scannerrule->scannerid   = $scannerinfo->scannerid;
$scannerrule->fromaddress = $_REQUEST['rule_from'];
$scannerrule->toaddress = $_REQUEST['rule_to'];
$scannerrule->subjectop = $_REQUEST['rule_subjectop'];
$scannerrule->subject   = $_REQUEST['rule_subject'];
$scannerrule->bodyop    = $_REQUEST['rule_bodyop'];
$scannerrule->body      = $_REQUEST['rule_body'];
$scannerrule->matchusing= $_REQUEST['rule_matchusing'];
($_REQUEST['compare_parentid'] == 'on') ? $scannerrule->compare_parentid = 1 : $scannerrule->compare_parentid = 0;	//crmv@78745
($scannerrule->subjectop == 'Regex' && $_REQUEST['rule_actiontext'] == 'UPDATE,HelpDesk,SUBJECT') ? $scannerrule->match_field = $_REQUEST['match_field'] : $scannerrule->match_field = '';	//crmv@81643

$scannerrule->update();

$scannerrule->updateAction($scanneractionid, $_REQUEST['rule_actiontext']);

include('modules/Settings/MailScanner/MailScannerRule.php');