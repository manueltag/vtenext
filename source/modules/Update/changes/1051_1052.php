<?php

$trans = array(
	'Emails' => array(
		'it_it' => array(
			'MESSAGE_MAIL_SENT_SUCCESSFULLY_ENABLE_CRON' => 'Messaggio inviato, abilita il cron per velocizzare gli invii',
		),
		'en_us' => array(
			'MESSAGE_MAIL_SENT_SUCCESSFULLY_ENABLE_CRON' => 'Messagge sent, enable cron to speed up mail client',
		),
	),
);


foreach ($trans as $module=>$modlang) {
	foreach ($modlang as $lang=>$translist) {
		foreach ($translist as $label=>$translabel) {
			SDK::setLanguageEntry($module, $lang, $label, $translabel);
		}
	}
}
