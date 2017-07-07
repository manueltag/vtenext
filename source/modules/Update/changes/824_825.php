<?php
$_SESSION['modules_to_update']['Timecards'] = 'packages/vte/mandatory/Timecards.zip';

SDK::setLanguageEntries('Potentials','LBL_OTHER_CONTACTS',array('it_it'=>'Altri contatti','en_us'=>'Other contacts'));
SDK::setLanguageEntries('Potentials','LBL_PARTNERS',array('it_it'=>'Partner','en_us'=>'Partners'));

@unlink('themes/images/custom.gif');
@unlink('themes/images/custom_small.png');
@unlink('themes/images/softed/picklist_multilanguage.gif');
@unlink('themes/images/softed/picklist_multilanguage_small.png');

$moduleHD = Vtiger_Module::getInstance('HelpDesk');
$moduleHD->deleteLink('DETAILVIEW','LBL_PDF_WITH_COMMENTS');
$moduleHD->deleteLink('DETAILVIEW','LBL_PDF_WITHOUT_COMMENTS');
$moduleHD->deleteLink('DETAILVIEW','LBL_HelpDesk_Receipt');
$moduleHD->deleteLink('DETAILVIEW','sortWO');

SDK::deleteLanguageEntry('HelpDesk', null, 'LBL_PDF_WITH_COMMENTS');
SDK::deleteLanguageEntry('HelpDesk', null, 'LBL_PDF_WITHOUT_COMMENTS');
SDK::deleteLanguageEntry('HelpDesk', null, 'LBL_HelpDesk_Receipt');
SDK::deleteLanguageEntry('HelpDesk', null, 'sortWO');

@unlink('modules/HelpDesk/chatHDReceipt.php');
@unlink('modules/HelpDesk/chatHDTicket.php');
@unlink('modules/HelpDesk/chatSortTT.php');

SDK::setLanguageEntry('APP_STRINGS', 'en_us', 'Timecard', 'Timecard');
SDK::setLanguageEntry('APP_STRINGS', 'en_us', 'Timecards', 'Timecards');
?>