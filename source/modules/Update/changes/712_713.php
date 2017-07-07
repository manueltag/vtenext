<?php
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';
$_SESSION['modules_to_update']['M'] = 'packages/vte/mandatory/M.zip';
$_SESSION['modules_to_update']['Webforms'] = 'packages/vte/mandatory/Webforms.zip';
$_SESSION['modules_to_update']['SLA'] = 'packages/vte/mandatory/SLA.zip';
if (isModuleInstalled('Projects')) {
	$_SESSION['modules_to_update']['Projects'] = 'packages/vte/optional/Projects.zip';
}

include_once('vtlib/Vtiger/SettingsField.php');

@unlink('modules/Utilities/Merge.php');

// Campi cifrati

// aggiunge voce nelle impostazioni
$block = Vtiger_SettingsBlock::getInstance('LBL_STUDIO');

$res = $adb->pquery("select fieldid from {$table_prefix}_settings_field where name = ?", array('LBL_EDIT_UITYPE208'));
if ($res && $adb->num_rows($res) == 0) {
	$field = new Vtiger_SettingsField();
	$field->name = 'LBL_EDIT_UITYPE208';
	$field->iconpath = 'uitype208.png';
	$field->description = 'LBL_EDIT_UITYPE208_DESC';
	$field->linkto = 'index.php?module=Settings&action=EncryptedFields&parenttab=Settings';
	$block->addField($field);
}

$trans = array(
	'Settings' => array(
		'it_it' => array(
			'LBL_EDIT_UITYPE208' => 'Editor di campi cifrati',
			'LBL_EDIT_UITYPE208_DESC' => 'Editor di campi cifrati',
			'LBL_UT208_SELFTEST_FAILED' => 'Test automatico fallito - possibile perdita di dati! Controllare file e database',
			'LBL_UT208_NOFIELDS' => 'Nessun campo cifrato. Premere Aggiungi per crearne uno',
			'LBL_UT208_TYPEPWD' => 'Inserisci la password usata per cifrare il campo',
			'LBL_UT208_TYPEPWD_DESC' => 'Il campo verrà ripristinato rimuovendo la protezione con password.',
			'LBL_CONFIRM_PASSWORD' => 'Conferma password',
			'LBL_ALERT' => 'Avviso',
			'LBL_FORGET_PWD_ALERT' => 'Se ti dimentichi la password, i dati non saranno recuperabili.',
			'LBL_UT208_CHOOSEFIELD' => 'Scegli il campo da cifrare',
			'LBL_UT208_CHOOSEPWD' => 'Scegli la password che verrà chiesta per decifrarlo',
			'LBL_UT208_CHANGEPWD' => 'Cambio password',
			'LBL_UT208_CHANGEPWD_DESC' => 'Lasciare vuoto per non modificare la password',
			'LBL_UT208_CURRENT_PWD' => 'Password attuale',
			'LBL_UT208_NEW_PWD' => 'Nuova password',
			'LBL_UT208_ADVANCED_OPTIONS' => 'Opzioni avanzate',
			'LBL_UT208_TIMEOUT' => 'Scadenza',
			'LBL_UT208_TIMEOUT_DESC' => 'Una volta inserita la password per decifrare il campo, essa rimmarà valida per questo periodo di tempo.',
			'LBL_UT208_FILTER_ROLE' => 'Filtro per ruolo',
			'LBL_UT208_FILTER_ROLE_DESC' => 'Solo i ruoli indicati potranno decifrare il campo.',
			'LBL_UT208_FILTER_IP' => 'Filtro per IP',
			'LBL_UT208_FILTER_IP_DESC' => 'Solo gli indirizzi IP indicati possono vedere il campo. Se ne può inserire più di uno, separati da spazi. Esempio: 192.168.1.45 10.0.1.0/24',
			'LBL_UT208_RESTORE_FAILED' => 'Ripristino fallito',
			'LBL_UT208_PWD_TOO_SHORT' => 'Password troppo corta',
			'LBL_UT208_PWD_NOT_SUITABLE' => 'La password contiene caratteri non validi, operazione annullata.',
			'LBL_UT208_GENERIC_ERROR' => 'Errore interno',
		),
		'en_us' => array(
			'LBL_EDIT_UITYPE208' => 'Encrypted Field Editor',
			'LBL_EDIT_UITYPE208_DESC' => 'Encrypted Field Editor',
			'LBL_UT208_SELFTEST_FAILED' => 'Self test failed - possible data corruption! Check files and database integrity',
			'LBL_UT208_NOFIELDS' => 'No encrypted fields. Click Add to create a new one',
			'LBL_UT208_TYPEPWD' => 'Type the password used to encrypt the field',
			'LBL_UT208_TYPEPWD_DESC' => 'The field will be restored by removing the password protection.',
			'LBL_CONFIRM_PASSWORD' => 'Confirm password',
			'LBL_ALERT' => 'Alert',
			'LBL_FORGET_PWD_ALERT' => 'If you forget the password, data will be irremediably lost.',
			'LBL_UT208_CHOOSEFIELD' => 'Choose a field to encrypt',
			'LBL_UT208_CHOOSEPWD' => 'Choose the password used to decrypt it',
			'LBL_UT208_CHANGEPWD' => 'Change password',
			'LBL_UT208_CHANGEPWD_DESC' => 'Leave this field empty to keep the current password',
			'LBL_UT208_CURRENT_PWD' => 'Current password',
			'LBL_UT208_NEW_PWD' => 'New password',
			'LBL_UT208_ADVANCED_OPTIONS' => 'Advanced options',
			'LBL_UT208_TIMEOUT' => 'Timeout',
			'LBL_UT208_TIMEOUT_DESC' => 'After the field has been decrypted, the password will be valid for this amount of time.',
			'LBL_UT208_FILTER_ROLE' => 'Role filter',
			'LBL_UT208_FILTER_ROLE_DESC' => 'Only specified roles can decrypt the field.',
			'LBL_UT208_FILTER_IP' => 'IP filter',
			'LBL_UT208_FILTER_IP_DESC' => 'Only specified IP addresses can decrypt the field. Several values can be specified, separated by a space. Example: 192.168.1.45 10.0.1.0/24',
			'LBL_UT208_RESTORE_FAILED' => 'Restore failed',
			'LBL_UT208_PWD_TOO_SHORT' => 'Password too short',
			'LBL_UT208_PWD_NOT_SUITABLE' => 'Password contains invalid characters, operation aborted.',
			'LBL_UT208_GENERIC_ERROR' => 'Internal error',
		),
	),
	'APP_STRINGS' => array(
		'it_it' => array(
			'LBL_CIPHERED' => 'Cifrato',
		),
		'en_us' => array(
			'LBL_CIPHERED' => 'Ciphered',
		)
	),
	'ALERT_ARR' => array(
		'it_it' => array(
			'LBL_UT208_PASSWORDEMPTY' => 'Scrivi una password',
			'LBL_UT208_INVALIDSRV' => 'Risposta non valida dal server',
			'LBL_UT208_WRONGPWD' => 'Password errata',
			'LBL_UT208_DIFFPWD' => 'Le password non coincidono',
			'LBL_UT208_PWDCRITERIA' => 'La password deve essere di almeno 6 caratteri',
		),
		'en_us' => array(
			'LBL_UT208_PASSWORDEMPTY' => 'Type a password',
			'LBL_UT208_INVALIDSRV' => 'Invalid server answer',
			'LBL_UT208_WRONGPWD' => 'Wrong password',
			'LBL_UT208_DIFFPWD' => 'Passwords are not equal',
			'LBL_UT208_PWDCRITERIA' => 'Password must be at least 6 characters long',
		)
	),
);


foreach ($trans as $module=>$modlang) {
	foreach ($modlang as $lang=>$translist) {
		foreach ($translist as $label=>$translabel) {
			SDK::setLanguageEntry($module, $lang, $label, $translabel);
		}
	}
}
?>