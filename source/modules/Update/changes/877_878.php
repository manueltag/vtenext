<?php
SDK::setLanguageEntry('Messages', 'it_it', 'Body', 'Corpo');
SDK::setLanguageEntries('Messages', 'Senders', array('it_it'=>'Mittenti','en_us'=>'Senders'));
SDK::setLanguageEntries('Messages', 'Recipients', array('it_it'=>'Destinatari','en_us'=>'Recipients'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ADVSEARCH_STARTDATE', array('it_it'=>'da','en_us'=>'from'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ADVSEARCH_ENDDATE', array('it_it'=>'a','en_us'=>'to'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ADVSEARCH_DATE_CUSTOM', array('it_it'=>'personalizzato','en_us'=>'custom'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ADVSEARCH_DATE_YESTARDAY', array('it_it'=>'ieri','en_us'=>'yesterday'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ADVSEARCH_DATE_TODAY', array('it_it'=>'oggi','en_us'=>'today'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ADVSEARCH_DATE_LASTWEEK', array('it_it'=>'settimana scorsa','en_us'=>'lastweek'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ADVSEARCH_DATE_THISWEEK', array('it_it'=>'questa settimana','en_us'=>'thisweek'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ADVSEARCH_DATE_LASTMONTH', array('it_it'=>'mese scorso','en_us'=>'lastmonth'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ADVSEARCH_DATE_THISMONTH', array('it_it'=>'questo mese','en_us'=>'thismonth'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ADVSEARCH_DATE_LAST60DAYS', array('it_it'=>'scorsi 60 giorni','en_us'=>'last60days'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ADVSEARCH_DATE_LAST90DAYS', array('it_it'=>'scorsi 90 giorni','en_us'=>'last90days'));

global $adb, $table_prefix;
$moduleInstance = Vtiger_Module::getInstance('Messages');
$adb->pquery("update {$table_prefix}_field set typeofdata = ? where tabid = ? and fieldname = ?",array('T~O',$moduleInstance->id,'mdate'));
?>