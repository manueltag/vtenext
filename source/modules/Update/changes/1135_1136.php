<?php

$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';

/* crmv@83305 */

// translations

$trans = array(
	'ModNotifications' => array(
		'it_it' => array(
			'LBL_UNFOLLOW' => 'Non notificarmi le modifiche',
		),
		'en_us' => array(
			'LBL_UNFOLLOW' => 'Don\'t notify me of changes',
		),
	),
	'ALERT_ARR' => array(
		'it_it' => array(
			'LBL_FOLLOW' => 'Notificami modifiche',
			'LBL_UNFOLLOW' => 'Non notificarmi le modifiche',
		),
		'en_us' => array(
			'LBL_FOLLOW' => 'Notify me of changes',
			'LBL_UNFOLLOW' => 'Don\'t notify me of changes',
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