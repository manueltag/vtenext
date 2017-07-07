<?php
require_once('vtlib/Vtiger/Module.php');
$EmailsInstance = Vtiger_Module::getInstance('Emails');
$CalendarInstance = Vtiger_Module::getInstance('Calendar');
$CalendarInstance->setRelatedList($EmailsInstance, 'Emails', Array(''), 'get_emails');

SDK::setPopupQuery('related', 'Webmails', 'Calendar', 'modules/SDK/src/modules/Webmails/CalendarQuery.php');
?>