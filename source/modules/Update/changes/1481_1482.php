<?php

/* crmv@114260 */

global $adb, $table_prefix;

// add smtp columns
$adb->addColumnToTable($table_prefix.'_messages_account', 'smtp_account', 'C(30)');
$adb->addColumnToTable($table_prefix.'_messages_account', 'smtp_server', 'C(100)');
$adb->addColumnToTable($table_prefix.'_messages_account', 'smtp_port', 'I(5)');
$adb->addColumnToTable($table_prefix.'_messages_account', 'smtp_username', 'C(100)');
$adb->addColumnToTable($table_prefix.'_messages_account', 'smtp_password', 'C(255)');
$adb->addColumnToTable($table_prefix.'_messages_account', 'smtp_auth', 'C(5)');


$trans = array(
	'Messages' => array(
		'it_it' => array(
			'LBL_USE_SMTP' => 'Usa server SMTP',
			'LBL_SMTP_SERVER' => 'Server SMTP',
		),
		'en_us' => array(
			'LBL_USE_SMTP' => 'Use SMTP server',
			'LBL_SMTP_SERVER' => 'SMTP server',
		),
	),
);
$languages = vtlib_getToggleLanguageInfo();
foreach ($trans as $module=>$modlang) {
	foreach ($modlang as $lang=>$translist) {
		if (array_key_exists($lang,$languages)) {
			foreach ($translist as $label=>$translabel) {
				SDK::setLanguageEntry($module, $lang, $label, $translabel);
			}
			if ($module == 'ALERT_ARR') {
				$recalculateJsLanguage[$lang] = $lang;
			}
		}
	}
}

