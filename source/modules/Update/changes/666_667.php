<?php
$_SESSION['modules_to_update']['ChangeLog'] = 'packages/vte/mandatory/ChangeLog.zip';
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter', 'Targets'));
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';
$_SESSION['modules_to_update']['ServiceContracts'] = 'packages/vte/mandatory/ServiceContracts.zip';
$_SESSION['modules_to_update']['SLA'] = 'packages/vte/mandatory/SLA.zip';
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';
$_SESSION['modules_to_update']['Webforms'] = 'packages/vte/mandatory/Webforms.zip';

global $enterprise_current_version,$enterprise_mode;
SDK::setLanguageEntries('APP_STRINGS', 'LBL_BROWSER_TITLE', array(
'it_it'=>"$enterprise_mode $enterprise_current_version",
'en_us'=>"$enterprise_mode $enterprise_current_version",
'pt_br'=>"$enterprise_mode $enterprise_current_version",
'de_de'=>"$enterprise_mode $enterprise_current_version"
));
?>