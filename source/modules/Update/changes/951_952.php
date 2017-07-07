<?php
$_SESSION['modules_to_update']['Assets'] = 'packages/vte/mandatory/Assets.zip';
$_SESSION['modules_to_update']['ChangeLog'] = 'packages/vte/mandatory/ChangeLog.zip';
$_SESSION['modules_to_update']['Charts'] = 'packages/vte/mandatory/Charts.zip';
$_SESSION['modules_to_update']['Ddt'] = 'packages/vte/mandatory/Ddt.zip';
$_SESSION['modules_to_update']['Fax'] = 'packages/vte/mandatory/Fax.zip';
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';
$_SESSION['modules_to_update']['Myfiles'] = 'packages/vte/mandatory/Myfiles.zip';
$_SESSION['modules_to_update']['MyNotes'] = 'packages/vte/mandatory/MyNotes.zip';
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter','Targets'));
$_SESSION['modules_to_update']['PBXManager'] = 'packages/vte/mandatory/PBXManager.zip';
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';
$_SESSION['modules_to_update']['ProjectsStandard'] = Array('location'=>'packages/vte/mandatory/ProjectsStandard.zip','modules'=>Array('ProjectPlan','ProjectMilestone','ProjectTask'));
$_SESSION['modules_to_update']['ServiceContracts'] = 'packages/vte/mandatory/ServiceContracts.zip';
$_SESSION['modules_to_update']['Services'] = 'packages/vte/mandatory/Services.zip';
$_SESSION['modules_to_update']['Timecards'] = 'packages/vte/mandatory/Timecards.zip';
$_SESSION['modules_to_update']['Visitreport'] = 'packages/vte/mandatory/Visitreport.zip';
$_SESSION['modules_to_update']['Webforms'] = 'packages/vte/mandatory/Webforms.zip';

SDK::setLanguageEntries('Products', 'LBL_EDITLISTPRICE', array('it_it'=>'Modifica prezzo di listino','en_us'=>'Edit price'));
SDK::setLanguageEntries('Settings', 'LBL_EDIT_EMAIL_TEMPLATES', array('it_it'=>'Modifica Template Email','en_us'=>'Edit Email Template'));
SDK::setLanguageEntries('APP_STRINGS', 'discount', array(
	'it_it'=>'Sconto',
	'en_us'=>'Discount',
	'pt_br'=>'Desconto',
	'de_de'=>'Rabatt',
	'nl_nl'=>'Korting',
));
SDK::setLanguageEntries('APP_STRINGS', 'Discount', array(
	'it_it'=>'Sconto',
	'en_us'=>'Discount',
	'pt_br'=>'Desconto',
	'de_de'=>'Rabatt',
	'nl_nl'=>'Korting',
));
SDK::setLanguageEntry('Ddt', 'de_de', 'Service Name', 'Name der Dienstleistung');
SDK::setLanguageEntry('Invoice', 'de_de', 'Service Name', 'Name der Dienstleistung');
SDK::setLanguageEntry('PurchaseOrder', 'de_de', 'Service Name', 'Name der Dienstleistung');
SDK::setLanguageEntry('Quotes', 'de_de', 'Service Name', 'Name der Dienstleistung');
SDK::setLanguageEntry('SalesOrder', 'de_de', 'Service Name', 'Name der Dienstleistung');
SDK::setLanguageEntry('Calendar', 'it_it', 'LBL_EVENTNAME', 'Nome Evento');
SDK::setLanguageEntries('com_vtiger_workflow', 'LBL_NO_METHOD_AVAILABLE', array(
	'it_it'=>'Nessuna funzione disponibile per questo modulo',
	'en_us'=>'No method is available for this module',
));
SDK::setLanguageEntries('com_vtiger_workflow', 'LBL_METHOD_NAME', array(
	'it_it'=>'Funzione',
	'en_us'=>'Method',
));
SDK::setLanguageEntries('PDFMaker', 'LBL_CREATE_RELATED_BLOCK', array('it_it'=>'Crea Blocco','en_us'=>'Create block'));
?>