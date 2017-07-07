<?php

global $adb, $table_prefix;

// crmv@97914
$adb->pquery("UPDATE {$table_prefix}_activitytype SET activitytype = ? WHERE activitytype = ?", array('Tracked', 'Tracciato'));
$adb->pquery("UPDATE {$table_prefix}_activity SET activitytype = ? WHERE activitytype = ?", array('Tracked', 'Tracciato'));  


SDK::setLanguageEntry('APP_STRINGS','it_it','LBL_ACTIVITY_REMINDER_DESCRIPTION','Questo messaggio notifica un promemoria per un\'attivita`!');
SDK::setLanguageEntry('APP_STRINGS','en_us','LBL_ACTIVITY_REMINDER_DESCRIPTION','This is an upcoming activity reminder');