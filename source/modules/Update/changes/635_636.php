<?php
$_SESSION['modules_to_update']['Transitions'] = 'packages/vte/mandatory/Transitions.zip';

$trans = array(
	'Users' => array(
		'it_it' => array(
			'LBL_RECOVER_INTRO' => 'Inserisci il tuo nome utente.<br />Ti verrà inviata una mail con le istruzioni per impostare una nuova password.',
			'LBL_RECOVER_EMAIL_BODY1' => 'E\' stata eseguita una richiesta di recupero password per il tuo account.<br />Se hai eseguito tu questa richiesta clicca',
			'LBL_RECOVER_MAIL_SENT' => 'La mail è stata inviata.',
			'LBL_RECOVER_MAIL_ERROR' => 'Non è stato possibile inviare la mail.<br />Contatta l\'amministratore e richiedi il cambio password.',
			'LBL_RECOVERY_SYSTEM3' => 'altrimenti compila i campi sottostanti con una nuova password che sostituirà la vecchia.',
			'LBL_RECOVERY_PASSWORD_SAVED' => 'La nuova password è stata salvata.',
			'LBL_SAVELOGIN_HELP' => 'Assicurati che il server abbia session.gc_maxlifetime = 2592000 nel php.ini per attivare la funzionalità.',
			'LBL_USER_BLOCKED' => "L'utente è stato bloccato in quanto non operativo da più di %s mesi. Contatta l'amministratore per riattivarlo.",
			'LBL_AVATAR_INSTRUCTIONS'=>'Per impostare la miniatura è necessario inserire la fotografia.',
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

SDK::setLanguageEntries('Settings', 'LBL_OUTGOING_MAIL_PORT', array(
'it_it'=>'Porta',
'en_us'=>'Port',
'pt_br'=>'Porta',
));
SDK::setLanguageEntries('Settings', 'LBL_ACCOUNT_MAIL_OTHER', array(
'it_it'=>'Altro',
'en_us'=>'Other',
'pt_br'=>'Outro',
));
SDK::setLanguageEntries('Settings', 'LBL_ACCOUNT_MAIL_UNDEFINED', array(
'it_it'=>'Non impostato',
'en_us'=>'Undefined',
'pt_br'=>'Indefinido',
));
SDK::setLanguageEntries('Settings', 'LBL_YAHOO_SMTP_INFO', array(
'it_it'=>'Soltanto questo mittente o gli altri autorizzati su yahoo potranno inviare email. (Vedi "553" in http://help.yahoo.com per maggiori informazioni)',
'en_us'=>'Only the sender or other authorized by yahoo can send emails. (See "553" on http://help.yahoo.com for more information)',
'pt_br'=>'Somente o remetente ou outros autorizado, podem enviar e-mail com yahoo. (Ver "553" em http://help.yahoo.com para mais informações)',
));
SDK::setLanguageEntries('Settings', 'LBL_GMAIL_SMTP_INFO', array(
'it_it'=>'Tutti i messaggi di posta inviati riporteranno questo mittente, come da policy GMail.',
'en_us'=>'All sent emails will report this sender, as policy GMail.',
'pt_br'=>'Todos os e-mails enviados irá reportar este remetente, como política de GMail.',
));
SDK::setLanguageEntries('Settings', 'LBL_DOMAIN', array(
'it_it'=>'Dominio',
'en_us'=>'Domain',
'pt_br'=>'Domínio',
));
$sqlarray = $adb->datadict->AddColumnSQL($table_prefix.'_systems','ssl_tls C(5)');
$adb->datadict->ExecuteSQLArray($sqlarray);
$result = $adb->pquery("update {$table_prefix}_systems set account = ? where server_type in (?,?)",array('Other','email','email_imap'));
?>