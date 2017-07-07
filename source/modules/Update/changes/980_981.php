<?php
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';

global $adb, $table_prefix;

$schema_table =
'<schema version="0.3">
	<table name="'.$table_prefix.'_check_logins">
		<opt platform="mysql">ENGINE=InnoDB</opt>
		<field name="id" type="I" size="19">
			<KEY/>
		</field>
		<field name="userid" type="I" size="19"/>
		<field name="first_attempt" type="DT"/>
		<field name="last_attempt" type="DT"/>
		<field name="ip" type="C" size="50"/>
		<field name="type" type="C" size="50"/>
		<field name="attempts" type="I" size="5">
			<DEFAULT value="0"/>
		</field>
		<field name="status" type="C" size="5"/>
		<field name="date_whitelist" type="DT"/>
		<field name="mailkey" type="C" size="50"/>
		<index name="CheckLoginIndex1">
	      <col>ip</col>
	      <col>status</col>
	    </index>
	    <index name="CheckLoginIndex2">
	      <col>userid</col>
	      <col>ip</col>
	      <col>type</col>
	      <col>status</col>
	    </index>
	    <index name="CheckLoginIndex3">
	      <col>userid</col>
	      <col>ip</col>
	      <col>type</col>
	    </index>
	</table>
</schema>';
if(!Vtiger_Utils::CheckTable($table_prefix.'_check_logins')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

SDK::setLanguageEntries('Users', 'LBL_MAIL_LOCKED_LOGIN_SUBJECT', array(
	'it_it'=>'Importante: troppi tentati di accesso effettuati',
	'en_us'=>'Important: too many login attempts'
));
SDK::setLanguageEntries('Users', 'LBL_MAIL_LOCKED_LOGIN_BODY', array(
	'it_it'=>"Salve, sono stati effettuati più di %s tentativi di accesso non corretti con la tua utenza dal seguente indirizzo ip %s.
<br />Per precauzione abbiamo bloccato l'accesso del tuo utente da questo indirizzo.
<br />
<br />Se sei al corrente di questi tentativi falliti e vuoi ripristinare l'accesso da questo ip clicca %s.
<br />Se non sei al corrente di questi tentativi falliti puoi ignorare questo messaggio ed eventualmente sbloccare l'accesso successivamente contattando l'amministratore di sistema.
<br />
<br />Saluti,
<br />Lo Staff VTECRM",
	'en_us'=>'Hi, more than %s unsuccessful login attempts were made by this ip address %s with your account name.
<br />As security measure, we have locked your account access from this ip.
<br />
<br />If you are aware of this unsuccessful login and you want to rehabilitate the access from this ip click %s.
<br />If you are not aware of this unsuccessful login, you can ignore this message and if you need to rehabilitate it in the future contact your system adminitrator.
<br />
<br />Regards,
<br />VTECRM Team'
));
SDK::setLanguageEntries('Users', 'LBL_MAIL_LOCKED_LOGIN_BODY_AS', array(
	'it_it'=>"Salve, sono stati effettuati tentativi di accesso non corretti da più di %s ore con la tua utenza su un dispositivo Active Sync compatibile dal seguente indirizzo ip %s.
<br />Per precauzione abbiamo bloccato l'accesso del tuo utente da questo indirizzo.
<br />
<br />Se sei al corrente di questi tentativi falliti e vuoi ripristinare l'accesso da questo ip clicca %s.
<br />Se non sei al corrente di questi tentativi falliti puoi ignorare questo messaggio ed eventualmente sbloccare l'accesso successivamente contattando l'amministratore di sistema.
<br />
<br />Saluti,
<br />Lo Staff VTECRM",
	'en_us'=>'Hi, unsuccessful login attempts were made from more than %s hours on an Active Sync device by this ip address %s with your account name.
<br />As security measure, we have locked your account access from this ip.
<br />
<br />If you are aware of this unsuccessful login and you want to rehabilitate the access from this ip click %s.
<br />If you are not aware of this unsuccessful login, you can ignore this message and if you need to rehabilitate it in the future contact your system adminitrator.
<br />
<br />Regards,
<br />VTECRM Team'
));
SDK::setLanguageEntries('Users', 'LBL_LOCKED_LOGIN_RESTORED', array(
	'it_it'=>'Accesso da indirizzo %s ripristinato',
	'en_us'=>'Login from address %s restored',
	'de_de'=>'Anmeldung von Adresse %s restauriert',
	'nl_nl'=>'Log in op het adres van %s hersteld',
	'pt_br'=>'Entrada de endereço %S restaurado',
));

require_once('vtlib/Vtiger/SettingsField.php');
$block = Vtiger_SettingsBlock::getInstance('LBL_USER_MANAGEMENT');
$field = new Vtiger_SettingsField();
$field->name = 'LoginProtectionPanel';
$field->iconpath = 'ico-profile.gif';
$field->description = 'LoginProtectionPanel_description';
$field->linkto = 'index.php?module=Settings&action=LoginProtectionPanel&parenttab=Settings';
$block->addField($field);

SDK::setLanguageEntries('APP_STRINGS', 'LoginProtectionPanel', array(
	'it_it'=>'Controllo Login Utente',
	'en_us'=>'Check User Login',
	'de_de'=>'Login User Control',
	'nl_nl'=>'Inloggen Gebruiker Controle',
	'pt_br'=>'Entrada de Controle do Usuário',
));

SDK::setLanguageEntries('APP_STRINGS', 'LoginProtectionPanel_description', array(
	'it_it'=>'Mostra account bloccati e ne permette il whitelist',
	'en_us'=>'Show locked account and provides whitelist\'s action',
	'de_de'=>'Show gesperrtes Konto und bietet Whitelist-Aktion',
	'nl_nl'=>'Toon opgesloten gehouden en biedt whitelist actie',
	'pt_br'=>'Mostrar bloqueada conta e proporciona ação whitelist',
));

SDK::setLanguageEntries('Settings', 'First Attempt', array(
	'it_it'=>'Primo tentativo',
	'en_us'=>'First attempt',
	'de_de'=>'Erster versuch',
	'nl_nl'=>'Eerste poging',
	'pt_br'=>'Primeira tentativa',
));
SDK::setLanguageEntries('Settings', 'Last Attempt', array(
	'it_it'=>'Ultimo tentativo',
	'en_us'=>'Last attempt',
	'de_de'=>'Letzten versuch',
	'nl_nl'=>'Laatste poging',
	'pt_br'=>'Última tentativa',
));
SDK::setLanguageEntries('Settings', 'Attempts', array(
	'it_it'=>'Tentativi falliti',
	'en_us'=>'Failed attempts',
	'de_de'=>'Fehlversuche',
	'nl_nl'=>'Mislukte pogingen',
	'pt_br'=>'Tentativas fracassadas',
));
SDK::setLanguageEntries('Settings', 'Whitelist Date', array(
	'it_it'=>'Data Whitelist',
	'en_us'=>'Whitelist date',
	'de_de'=>'Whitelist-Datum',
	'nl_nl'=>'Whitelist datum',
	'pt_br'=>'Whitelist data',
));
SDK::setLanguageEntries('Settings', 'LBL_ADD_TO_WHITELIST', array(
	'it_it'=>'Aggiungi a whitelist',
	'en_us'=>'Add to whitelist',
	'de_de'=>'Zur Whitelist hinzufügen',
	'nl_nl'=>'Voeg toe aan de witte lijst',
	'pt_br'=>'Adicionar à lista branca',
));
SDK::setLanguageEntries('Settings', 'LBL_STATUS_WHITELIST', array(
	'it_it'=>'Whitelist',
	'en_us'=>'Whitelist',
	'de_de'=>'Whitelist',
	'nl_nl'=>'Whitelist',
	'pt_br'=>'Whitelist',
));
SDK::setLanguageEntries('Settings', 'LBL_STATUS_LOCKED', array(
	'it_it'=>'Bloccato',
	'en_us'=>'Locked',
	'de_de'=>'Whitelist',
	'nl_nl'=>'Vergrendelde',
	'pt_br'=>'Trancado',
));
SDK::setLanguageEntries('Settings', 'LBL_STATUS_BANNED', array(
	'it_it'=>'Bannato',
	'en_us'=>'Banned',
	'de_de'=>'Verboten',
	'nl_nl'=>'Verboden',
	'pt_br'=>'Banido',
));
?>