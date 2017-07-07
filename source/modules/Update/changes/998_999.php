<?php
global $adb;
$adb->pquery('UPDATE sdk_uitype SET old_style = ? WHERE uitype = ?',array(0,204));
SDK::clearSessionValue('sdk_uitype');

SDK::setLanguageEntry('ALERT_ARR', 'it_it', 'LENGTH', 'la lunghezza di');
?>