<?php
global $adb, $table_prefix;

SDK::setLanguageEntries('ALERT_ARR', 'CANNOT_BE_EMPTY', array(
	'it_it'=>'%s non può essere vuoto',
	'en_us'=>'%s cannot be empty',
));
SDK::setLanguageEntries('ALERT_ARR', 'CANNOT_BE_NONE', array(
	'it_it'=>'%s non può essere nullo',
	'en_us'=>'%s cannot be none',
));
SDK::setLanguageEntries('ALERT_ARR', 'DELETE_RECORD', array(
	'it_it'=>'Sicuro di voler cancellare il record selezionato?',
	'en_us'=>'Are you sure you want to delete the selected record?',
));
SDK::setLanguageEntries('ALERT_ARR', 'DELETE_RECORDS', array(
	'it_it'=>'Sicuro di voler cancellare i %s record selezionati?',
	'en_us'=>'Are you sure you want to delete the %s selected records?',
));
SDK::setLanguageEntries('ALERT_ARR', 'DELETE_ACCOUNT', array(
	'it_it'=>'Cancellando questa azienda verranno cancellate anche le opportunità e i preventivi associati. Sicuro di voler eliminarla?',
	'en_us'=>'Deleting this account will remove its related potentials and quotes. Are you sure you want to delete it?',
));
SDK::setLanguageEntries('ALERT_ARR', 'DELETE_ACCOUNTS', array(
	'it_it'=>'Cancellando queste aziende verranno cancellate anche le opportunità e i preventivi associati. Sicuro di voler eliminarla?',
	'en_us'=>'Deleting these accounts will remove its related potentials and quotes. Are you sure you want to delete them?',
));
SDK::setLanguageEntries('ALERT_ARR', 'DELETE_VENDOR', array(
	'it_it'=>'Cancellando questa azienda verranno cancellate anche le opportunità e i preventivi associati. Sicuro di voler eliminarla?',
	'en_us'=>'Deleting this vendor will remove its related purchase orders. Are you sure you want to delete it?',
));
SDK::setLanguageEntries('ALERT_ARR', 'DELETE_VENDORS', array(
	'it_it'=>'Cancellado questi fornitori verranno rimossi anche gli ordini di acquisto correlati. Sicuro di voler eliminarli?',
	'en_us'=>'Deleting these vendors will remove its related purchase orders. Are you sure you want to delete them?',
));
SDK::setLanguageEntries('ALERT_ARR', 'LINE_ITEM', array(
	'it_it'=>'Riga prodotto',
	'en_us'=>'Product',
));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_FPOFV_RULE_NAME', array(
	'it_it'=>'Nome Regola',
	'en_us'=>'Rule name',
));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_LEAST_ONE_CONDITION', array(
	'it_it'=>'Manca almeno una condizione su un campo',
	'en_us'=>'Insert at least one condition',
));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_SELECT_A_MODULE', array(
	'it_it'=>'Seleziona un modulo',
	'en_us'=>'Select a module',
));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_SAVE_AND_BACK_BUTTON_LABEL', array(
	'it_it'=>'Salva e torna',
	'en_us'=>'Save and returns',
));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_EMPTY_LIST_YOU_CAN_CREATE_RECORD_NOW', array(
	'it_it'=>'Puoi creare un record adesso cliccando il seguente link',
	'en_us'=>'You can create a record now by click the following link',
));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_CLICK_TO_CONFIGURE_MODENTITYNUM', array(
	'it_it'=>'Clicca <a href="%s">%s</a> per configurare il %s',
	'en_us'=>'Click <a href="%s">%s</a> in order to configure the %s',
));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_LISTVIEW_NAVIGATION_STR1', array(
	'it_it'=>'Visualizzando da %s a %s di %s',
	'en_us'=>'Showing %s - %s of %s',
));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_LISTVIEW_NAVIGATION_STR2', array(
	'it_it'=>'Visualizzando da %s a %s',
	'en_us'=>'Showing %s - %s',
));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_ADJUSTMENT_ADD', array(
	'it_it'=>'Aggiungi',
	'en_us'=>'Add',
));
SDK::setLanguageEntries('ModComments', 'LBL_AGO', array(
	'it_it'=>'%s fa',
	'en_us'=>'%s ago',
));
SDK::setLanguageEntries('Sms', 'LBL_MESSAGE', array(
	'it_it'=>'Messaggio',
	'en_us'=>'Message',
));
SDK::setLanguageEntries('Sms', 'LBL_CHARACTERS', array(
	'it_it'=>'caratteri',
	'en_us'=>'characters',
));
SDK::setLanguageEntries('Settings', 'LBL_PROFILE_TO_BE_USED_FOR_MOBILE', array(
	'it_it'=>'Permette "%s" di essere utilizzato esclusivamente come profilo per l\'App Mobile',
	'en_us'=>'Allow "%s" to be used exclusively as a profile for Mobile App',
));
SDK::setLanguageEntries('Settings', 'LBL_EDIT_GROUP_PROPERTIES', array(
	'it_it'=>'Modifica le proprietà del gruppo',
	'en_us'=>'Edit properties of the group',
));
SDK::setLanguageEntries('Settings', 'LBL_VIEWING_GROUP_PROPERTIES', array(
	'it_it'=>'Visualizzando le proprietà del gruppo',
	'en_us'=>'Viewing the properties of the group',
));
SDK::setLanguageEntries('Settings', 'LBL_IMPORT_NEW_MODULE', array(
	'it_it'=>'Importa nuovo modulo',
	'en_us'=>'Import new module',
));
SDK::setLanguageEntries('Settings', 'LBL_LDAP_SERVER_ADDRESS', array(
	'it_it'=>'Indirizzo Server',
	'en_us'=>'Server',
));
SDK::setLanguageEntries('Settings', 'LBL_LDAP_LDAPBSEDN', array(
	'it_it'=>'BaseDN LDAP',
	'en_us'=>'LDAP BaseDn',
));
SDK::setLanguageEntries('Settings', 'LBL_LDAP_OBJCLASS', array(
	'it_it'=>'Classe della ricerca utente',
	'en_us'=>'Bind Attribute',
));
SDK::setLanguageEntries('Settings', 'LBL_LDAP_LDAPACCOUNT', array(
	'it_it'=>'Attributo per il login utente',
	'en_us'=>'Login Attribute',
));
SDK::setLanguageEntries('Settings', 'LBL_LDAP_LDAPFULLNAME', array(
	'it_it'=>'Formato del nome completo utente',
	'en_us'=>'Full Name user format',
));
SDK::setLanguageEntries('Settings', 'LBL_LDAP_LDAPFILTER', array(
	'it_it'=>'Attributi di ricerca utente',
	'en_us'=>'User search filters',
));
SDK::setLanguageEntries('Settings', 'LBL_LDAP_LDAPROLE', array(
	'it_it'=>'Ruolo predefinito',
	'en_us'=>'Default role',
));
SDK::setLanguageEntries('Settings', 'LBL_UNABLE_TO_CONNECT', array(
	'it_it'=>'Impossibile connettersi a %s',
	'en_us'=>'Unable to connect to %s',
));
SDK::setLanguageEntries('Settings', 'LBL_UNABLE_TO_CONNECT_MAILSCANNER', array(
	'it_it'=>'Impossibile connettersi a alla casella email',
	'en_us'=>'Connecting to mailbox failed',
));
?>