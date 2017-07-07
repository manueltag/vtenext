<?php
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';

SDK::setLanguageEntry('Webmails', 'it_it', 'Unknown date', 'Data sconosciuta');

global $enterprise_current_version,$enterprise_mode;
SDK::setLanguageEntries('APP_STRINGS', 'LBL_BROWSER_TITLE', array('it_it'=>"$enterprise_mode $enterprise_current_version",'en_us'=>"$enterprise_mode $enterprise_current_version",'pt_br'=>"$enterprise_mode $enterprise_current_version"));

$hash_version = file_get_contents('hash_version.txt');
$adb->updateClob('vtiger_version','hash_version','id=1',$hash_version);
@unlink('hash_version.txt');
?>