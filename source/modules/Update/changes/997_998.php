<?php
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter' => 'Targets'));
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';

global $adb, $table_prefix;
$idxs_messages = array_keys($adb->database->MetaIndexes($table_prefix.'_messages'));
$indexes = array(
	array("{$table_prefix}_messages", "{$table_prefix}_messages_adoptchildren", 'folder, mreferences(200)'),
	array("{$table_prefix}_messages", "{$table_prefix}_messages_referencechildren_idx", 'mdate, folder, mreferences(200)'),
);
foreach($indexes as $index) {
	if (in_array($index[1], $idxs_messages)) $adb->datadict->ExecuteSQLArray((Array)$adb->datadict->DropIndexSQL($index[1], $index[0]));
	$adb->datadict->ExecuteSQLArray((Array)$adb->datadict->CreateIndexSQL($index[1], $index[0], $index[2]));
}

$adb->query("delete from {$table_prefix}_relatedlists where tabid = 10");

/* Translations */
$newLabelModule = array(
	'Quotes' => array(
		'it_it' => 'Crea Preventivo',
		'en_us' => 'Create Quote',
		'de_de' => 'Neues Angebot',
		'nl_nl' => 'Aanmaken Offerte',
		'pt_br' => 'Criar Cotação'
	),
	'Reports' => array(
		'it_it' => 'Crea Report',
		'en_us' => 'Create Report',
		'de_de' => 'Neuer Bericht',
		'nl_nl' => 'Aanmaken Rapport',
		'pt_br' => 'Criar Relatório'
	),
	'Assets' => array(
		'it_it' => 'Crea Installazione',
		'en_us' => 'Create Asset',
		'de_de' => 'Neue Bestandsverwaltung',
		'nl_nl' => 'Aanmaken Credit',
		'pt_br' => 'Criar Instalação'
	),
	'Visitreport' => array(
		'it_it' => 'Crea Report visite',
		'en_us' => 'Create Visitreport',
		'de_de' => 'Neuer Besucherbericht',
		'nl_nl' => 'Aanmaken Bezoekverslag',
		'pt_br' => 'Criar Relatório Visita'
	),
	'Charts' => array(
		'it_it' => 'Crea Grafico',
		'en_us' => 'Create Chart',
		'de_de' => 'Neues Chart',
		'nl_nl' => 'Aanmaken Grafisch',
		'pt_br' => 'Criar Gráfico'
	),
	'Ddt' => array(
		'it_it' => 'Crea Ddt',
		'en_us' => 'Create Delivery Note',
		'de_de' => 'Neuer Lieferschein',
		'nl_nl' => 'Aanmaken Ddt',
		'pt_br' => 'Criar Documento de Frete'
	),
	'Services' => array(
		'it_it' => 'Crea Servizio',
		'en_us' => 'Create Service',
		'de_de' => 'Neue Dienstleistung',
		'nl_nl' => 'Aanmaken Service',
		'pt_br' => 'Criar Serviço'
	),
	'Documents' => array(
		'it_it' => 'Crea Documento',
		'en_us' => 'Create Document',
		'de_de' => 'Neues Dokument',
		'nl_nl' => 'Aanmaken Document',
		'pt_br' => 'Criar Documento'
	),
	'PurchaseOrder' => array(
		'it_it' => 'Crea Ordine di Acquisto',
		'en_us' => 'Create PurchaseOrder',
		'de_de' => 'Neue Einkaufsbestellung',
		'nl_nl' => 'Aanmaken inkooporder',
		'pt_br' => 'Criar Pedido de Compra'
	),
	'Faq' => array(
		'it_it' => 'Crea Faq',
		'en_us' => 'Create Faq',
		'de_de' => 'Neue Frage',
		'nl_nl' => 'Aanmaken FAQ',
		'pt_br' => 'Criar Faq'
	),
	'Campaigns' => array(
		'it_it' => 'Crea Campagna',
		'en_us' => 'Create Campaign',
		'de_de' => 'Neue Kampagne',
		'nl_nl' => 'Aanmaken Campagne',
		'pt_br' => 'Criar Campanha'
	),
	'SalesOrder' => array(
		'it_it' => 'Crea Ordine di Vendita',
		'en_us' => 'Create SalesOrder',
		'de_de' => 'Neue Kundenbestellung',
		'nl_nl' => 'Aanmaken Verkooporder',
		'pt_br' => 'Criar Pedido de Venda'
	),
	'Leads' => array(
		'it_it' => 'Crea Lead',
		'en_us' => 'Create Lead',
		'de_de' => 'Neuer Lead',
		'nl_nl' => 'Aanmaken Lead',
		'pt_br' => 'Criar Lead'
	),
	'Vendors' => array(
		'it_it' => 'Crea Fornitore',
		'en_us' => 'Create Vendor',
		'de_de' => 'Neuer Lieferant',
		'nl_nl' => 'Aanmaken Leverancier',
		'pt_br' => 'Criar Fornecedor'
	),
	'Newsletter' => array(
		'it_it' => 'Crea Newsletter',
		'en_us' => 'Create Newsletter',
		'de_de' => 'Neuer Newsletter',
		'nl_nl' => 'Aanmaken Nieuwsbrief',
		'pt_br' => 'Criar Newsletter'
	),
	'Accounts' => array(
		'it_it' => 'Crea Azienda',
		'en_us' => 'Create Account',
		'de_de' => 'Neue Organisation',
		'nl_nl' => 'Aanmaken Relatie',
		'pt_br' => 'Criar Conta'
	),
	'PBXManager' => array(
		'it_it' => 'Crea Gestore chiamate',
		'en_us' => 'Create PBX Manager',
		'de_de' => 'Neuer PBX Manager',
		'nl_nl' => 'Aanmaken PBX Manager',
		'pt_br' => 'Criar Gestor de Chamadas'
	),
	'Contacts' => array(
		'it_it' => 'Crea Contatto',
		'en_us' => 'Create Contact',
		'de_de' => 'Neue Person',
		'nl_nl' => 'Aanmaken Contactpersoon',
		'pt_br' => 'Criar Contato'
	),
	'PriceBooks' => array(
		'it_it' => 'Crea Listino',
		'en_us' => 'Create PriceBook',
		'de_de' => 'Neue Preisliste',
		'nl_nl' => 'Aanmaken prijslijst',
		'pt_br' => 'Criar Lista de Preços'
	),
	'Products' => array(
		'it_it' => 'Crea Prodotto',
		'en_us' => 'Create Product',
		'de_de' => 'Neues Produkt',
		'nl_nl' => 'Aanmaken Product',
		'pt_br' => 'Criar Produto'
	),
	'ProjectTask' => array(
		'it_it' => 'Crea Operazione',
		'en_us' => 'Create Project Task',
		'de_de' => 'Neue Projektaufgabe',
		'nl_nl' => 'Aanmaken Projecttaak',
		'pt_br' => 'Criar Tarefa Projeto'
	),
	'ProjectMilestone' => array(
		'it_it' => 'Crea Scadenza',
		'en_us' => 'Create Project Milestone',
		'de_de' => 'Neuer Projektmeilenstein',
		'nl_nl' => 'Aanmaken Project Mijlpaal',
		'pt_br' => 'Criar Marco Projeto'
	),
	'ProjectPlan' => array(
		'it_it' => 'Crea Pianificazione',
		'en_us' => 'Create Project',
		'de_de' => 'Neues Projektkonzept',
		'nl_nl' => 'Aanmaken Project',
		'pt_br' => 'Criar Planejamento'
	),
	'Invoice' => array(
		'it_it' => 'Crea Fattura',
		'en_us' => 'Create Invoice',
		'de_de' => 'Neue Rechnung',
		'nl_nl' => 'Aanmaken Factuur',
		'pt_br' => 'Criar Fatura'
	),
	'ServiceContracts' => array(
		'it_it' => 'Crea Servizio a Contratto',
		'en_us' => 'Create Service Contract',
		'de_de' => 'Neuer Servicevertrag',
		'nl_nl' => 'Aanmaken Service Contract',
		'pt_br' => 'Criar Serv. Contrato'
	),
	'ProductLines' => array(
		'it_it' => 'Crea Linea di prodotto',
		'en_us' => 'Create Product line',
		'de_de' => 'Neues Sortiment',
		'nl_nl' => 'Aanmaken Product groep',
		'pt_br' => 'Criar Linha de Produtos'
	),
	'HelpDesk' => array(
		'it_it' => 'Crea Ticket',
		'en_us' => 'Create Ticket',
		'de_de' => 'Neues Ticket',
		'nl_nl' => 'Aanmaken Ticket',
		'pt_br' => 'Criar Ticket'
	),
	'Potentials' => array(
		'it_it' => 'Crea Opportunita`',
		'en_us' => 'Create Potential',
		'de_de' => 'Neues Verkaufspotential',
		'nl_nl' => 'Aanmaken Verkoopkans',
		'pt_br' => 'Criar Oportunidade'
	),
	'Timecards' => array(
		'it_it' => 'Crea Intervento',
		'en_us' => 'Create Timecard',
		'de_de' => 'Neue Zeiterfassungskarte',
		'nl_nl' => 'Aanmaken Urenregistratie',
		'pt_br' => 'Criar Intervenção'
	),
	'Targets' => array(
		'it_it' => 'Crea Target',
		'en_us' => 'Create Target',
		'de_de' => 'Neues Ziel',
		'nl_nl' => 'Aanmaken Doelstelling',
		'pt_br' => 'Criar Alvo'
	),
);
foreach($newLabelModule as $module => $values) {
	foreach($values as $lang => $value) {
		SDK::setLanguageEntry('APP_STRINGS', $lang, 'LBL_NEW_'.strtoupper($module), $value);
	}
}

$de_de_translations = array(
	'ModComments' => array(
		'lbl_days' => 'Tagen',
	),
	'Messages' => array(
		'LBL_FORWARD_ACTION' => 'Erneut senden',
		'Messageid' => 'Nachrichten-ID',
		'In-Reply-To' => 'Reaktion auf',
		'References' => 'Referenzen',
		'Record' => 'Aufzeichnung',
	),
	'Import' => array(
		'LBL_NEXT_BUTTON_LABEL' => 'weiter',
	),
	'APP_STRINGS' => array(
		'LBL_EDIT_BUTTON' => 'Template bearbeiten',
		'LBL_DUPLICATING' => 'Kopieren von',
		'Add Ddt' => 'Erstelle Lieferschein',
		'LBL_TOOLS' => ' ',
		'LBL_SAVE_AND_BACK_BUTTON_LABEL' => 'Speichern und zurück',
		'LBL_PERIOD' => 'Zeitraum',
	),
	'CustomView' => array(
		'less than' => 'kleiner',
		'greater than' => 'größer',
	),
	'Newsletter' => array(
		'NowChooseATemplate' => 'Nun wählen Sie eine Vorlage oder erstellen eine Neue',
		'OkWhenDoWeScheduleIt' => 'Wann soll der Newsletter gesendet werden?',
		'NewsletterData' => 'Newsletter Daten',
		'No Views' => 'Anzahl Aufrufe',
		'No Click' => 'Anzahl Klicks',
	),
	'Campaigns' => array(
		'Message Queue' => 'Nachrichten anstehend',
		'Sent Messages' => 'Nachrichten gesendet',
		'Viewed Messages' => 'Nachrichten geöffnet',
		'Tracked Link' => 'Links verfolgt',
		'Unsubscriptions' => 'Abmeldungen',
		'Bounced Messages' => 'Nachrichten fehlerhaft',
		'Suppression list' => 'Ausblendungsliste',
		'Failed Messages' => 'Nachrichten fehlgeschlagen',
	),
	'ProductLines' => array(
		'YearlyBudget' => 'jährliches  Budget',
	),
	'PDFMaker' => array(
		'LBL_SAVEASDOC' => 'Abspeichern der PDF als Dokument',
	),
	'Charts' => array(
		'LBL_CREATE_CHART' => 'Erstelle Charts',
	),
	'Reports' => array(
		'LBL_SELECT_FILTERS_TO_STREAMLINE_REPORT_DATA' => 'Legen Sie die Benutzungsart Ihres Berichtes fest',
	),
	'Ddt' => array(
		'Ddt' => 'Lieferscheine',
	),
	'HelpDesk' => array(
		'LBL_HELPINFO_HOURS' => 'Voraussichtliche Anzahl an Stunden für das Ticket. Wenn dieses Ticket zu einem Dienstleistungs-Vertrag hinzugefügt wird, aktualisiert die Nachverfolgung bei der Schließung eines Tickets die gebrauchte Zeit.',
		'Dear' => 'Lieber',
		'LBL_REGARDS' => 'Liebe Grüße,',
		'LBL_TEAM' => 'das Helpdesk Team',
		'Big Problem' => 'Großes Problem',
		'Small Problem' => 'Kleines Problem',
		'Big Problem' => 'Großes Problem',
		'Other Problem' => 'Anderes Problem',
		'Low' => 'Niedrig',
		'Normal' => 'Normal',
		'High' => 'Hoch',
		'Urgent' => 'Dringend',
		'Minor' => 'Niedrig',
		'Major' => 'Hoch',
		'Critical' => 'Kritisch',
	),
	'Users' => array(
		'LBL_RECOVER_EMAIL_BODY1' => 'das Passwort für deinen Account wurde wiederhergestellt.<br/>Um weiter fortzufahren klicke bitte',
		'LBL_RECOVER_EMAIL_BODY2' => 'und gib dann das neue Passwort ein.<br /> Der Prozess der Passwort-Wiederherstellung muss in 24 Stunden beendet werden. <br />Verstreicht diese Frist, musst du den Vorgang erneut starten indem du auf der Login-Seite auf den Link „Passwort vergessen?“ klickst.',
	),
	'Settings' => array(
		'HIDDEN_FIELDS' => 'Verborgene Felder',
		'LBL_UNABLE_TO_CONNECT_MAILSCANNER' => 'Verbindung zum Mailscanner nicht möglich',
		'LBL_UNABLE_TO_CONNECT' => 'Verbindung nicht möglich',
		'LBL_ENABLE_ALL' => 'Sämtliche Einträge aktivieren',
		'LBL_DISABLE_ALL' => 'Sämtliche Einträge deaktivieren',
		'LBL_CHANGE' => 'Bearbeite',
	),
	'FieldFormulas' => array(
		'NEED_TO_ADD_A' => 'Es besteht kein benutzerdefiniertes Feld. Um eines zu erstellen klicken Sie',
		'LBL_CUSTOM_FIELD' => 'hier',
	),
	'ALERT_ARR' => array(
		'INVALID' => 'Ungültige ',
		'CANNOT_BE_EMPTY' => '%s fehlt',
		'CANNOT_BE_NONE' => '%s fehlt',
		'DELETE_RECORDS' => 'Wirklich wollen, um die %s Datensätze löschen?',
		'SHOULDBE_LESS_1' => '%s muss kürzer als %d Zeichen sein',
		'SHOULDBE_LESS_EQUAL_1' => '%s darf nicht länger als %d Zeichen sein',
		'SHOULDBE_EQUAL_1' => '%s muss genau %d Zeichen lang sein',
		'SHOULDBE_GREATER_1' => '%s muss länger als %d Zeichen sein',
		'SHOULDBE_GREATER_EQUAL_1' => '%s darf nicht kürzer als %d Zeichen sein',
		'SHOULDNOTBE_EQUAL_1' => '%s darf nicht genau %d Zeichen enthalten',
		'DATE_SHOULDBE_LESS' => '%s muss kürzer als %s Zeichen sein',
		'DATE_SHOULDBE_LESS_EQUAL' => '%s darf nicht länger als %s Zeichen sein',
		'DATE_SHOULDBE_EQUAL' => '%s muss genau %s Zeichen lang sein',
		'DATE_SHOULDBE_GREATER' => '%s muss länger als %s Zeichen sein',
		'DATE_SHOULDBE_GREATER_EQUAL' => '%s darf nicht kürzer als %s Zeichen sein',
		'DATE_SHOULDNOTBE_EQUAL' => '%s darf nicht genau %s Zeichen enthalten',
		'LENGTH_SHOULDBE_LESS' => '%s %s muss kürzer als %d Zeichen sein %s',
		'LENGTH_SHOULDBE_LESS_EQUAL' => '%s %s darf nicht länger als %d Zeichen sein %s',
		'LENGTH_SHOULDBE_EQUAL' => '%s %s muss genau %d Zeichen lang sein %s',
		'LENGTH_SHOULDBE_GREATER' => '%s %s muss länger als %d Zeichen sein %s',
		'LENGTH_SHOULDBE_GREATER_EQUAL' => '%s %s darf nicht kürzer als %d Zeichen sein %s',
		'LENGTH_SHOULDNOTBE_EQUAL' => '%s %s darf nicht genau %d Zeichen enthalten %s',
	),
);
foreach($de_de_translations as $module => $values) {
	foreach($values as $label => $value) {
		SDK::setLanguageEntry($module, 'de_de', $label, $value);
	}
}

$en_us_translations = array(
	'APP_STRINGS' => array(
		'LBL_PERIOD' => 'Period',
	),
	'ALERT_ARR' => array(
		'SHOULDBE_LESS_1' => '%s should be less than %d',
		'SHOULDBE_LESS_EQUAL_1' => '%s should be less than or equal to %d',
		'SHOULDBE_EQUAL_1' => '%s should be equal to %d',
		'SHOULDBE_GREATER_1' => '%s should be greater than %d',
		'SHOULDBE_GREATER_EQUAL_1' => '%s should be greater than or equal to %d',
		'SHOULDNOTBE_EQUAL_1' => '%s should not be equal to %d',
		'DATE_SHOULDBE_LESS' => '%s should be less than %s',
		'DATE_SHOULDBE_LESS_EQUAL' => '%s should be less than or equal to %s',
		'DATE_SHOULDBE_EQUAL' => '%s should be equal to %s',
		'DATE_SHOULDBE_GREATER' => '%s should be greater than %s',
		'DATE_SHOULDBE_GREATER_EQUAL' => '%s should be greater than or equal to %s',
		'DATE_SHOULDNOTBE_EQUAL' => '%s should not be equal to %s',
		'LENGTH_SHOULDBE_LESS' => '%s %s should be less than %d %s',
		'LENGTH_SHOULDBE_LESS_EQUAL' => '%s %s should be less than or equal to %d %s',
		'LENGTH_SHOULDBE_EQUAL' => '%s %s should be equal to %d %s',
		'LENGTH_SHOULDBE_GREATER' => '%s %s should be greater than %d %s',
		'LENGTH_SHOULDBE_GREATER_EQUAL' => '%s %s should be greater than or equal to %d %s',
		'LENGTH_SHOULDNOTBE_EQUAL' => '%s %s should not be equal to %d %s',
	),
);
foreach($en_us_translations as $module => $values) {
	foreach($values as $label => $value) {
		SDK::setLanguageEntry($module, 'en_us', $label, $value);
	}
}

$it_it_translations = array(
	'APP_STRINGS' => array(
		'LBL_PERIOD' => 'Periodo',
	),
	'ALERT_ARR' => array(
		'SHOULDBE_LESS_1' => '%s deve essere minore di %d',
		'SHOULDBE_LESS_EQUAL_1' => '%s deve essere minore o uguale a %d',
		'SHOULDBE_EQUAL_1' => '%s deve essere uguale a %d',
		'SHOULDBE_GREATER_1' => '%s deve essere maggiore di %d',
		'SHOULDBE_GREATER_EQUAL_1' => '%s deve essere maggiore o uguale a %d',
		'SHOULDNOTBE_EQUAL_1' => '%s non deve essere uguale a %d',
		'DATE_SHOULDBE_LESS' => '%s deve essere minore di %s',
		'DATE_SHOULDBE_LESS_EQUAL' => '%s deve essere minore o uguale a %s',
		'DATE_SHOULDBE_EQUAL' => '%s deve essere uguale a %s',
		'DATE_SHOULDBE_GREATER' => '%s deve essere maggiore di %s',
		'DATE_SHOULDBE_GREATER_EQUAL' => '%s deve essere maggiore o uguale a %s',
		'DATE_SHOULDNOTBE_EQUAL' => '%s non deve essere uguale a %s',
		'LENGTH_SHOULDBE_LESS' => '%s %s deve essere minore di %d %s',
		'LENGTH_SHOULDBE_LESS_EQUAL' => '%s %s deve essere minore o uguale a %d %s',
		'LENGTH_SHOULDBE_EQUAL' => '%s %s deve essere uguale a %d %s',
		'LENGTH_SHOULDBE_GREATER' => '%s %s deve essere maggiore di %d %s',
		'LENGTH_SHOULDBE_GREATER_EQUAL' => '%s %s deve essere maggiore o uguale a %d %s',
		'LENGTH_SHOULDNOTBE_EQUAL' => '%s %s non deve essere uguale a %d %s',
	),
);
foreach($it_it_translations as $module => $values) {
	foreach($values as $label => $value) {
		SDK::setLanguageEntry($module, 'it_it', $label, $value);
	}
}

$nl_nl_translations = array(
	'APP_STRINGS' => array(
		'LBL_PERIOD' => 'Periode',
		'LBL_CLICK_TO_CONFIGURE_MODENTITYNUM' => 'Klik <a href="%s">%s</a> om %s te configureren',
		'LBL_LISTVIEW_NAVIGATION_STR1' => 'Toon %s - %s van %s',
		'LBL_LISTVIEW_NAVIGATION_STR2' => 'Toon %s - %s ',
		'LBL_POPUP_RECORDS_NOT_SELECTABLE' => 'Bestaande %s kan niet worden geselecteerd. U kan een nieuwe maken',
	),
	'ALERT_ARR' => array(
		'SHOULDBE_LESS_1', '%s moet kleiner zijn dan %d',
		'SHOULDBE_LESS_EQUAL_1' => '%s moet kleiner of gelijk zijn aan %d',
		'SHOULDBE_EQUAL_1' => '%s moet gelijk zijn aan %d',
		'SHOULDBE_GREATER_1' => '%s moet groter zijn dan %d',
		'SHOULDBE_GREATER_EQUAL_1' => '%s moet groter of gelijk zijn aan %d',
		'SHOULDNOTBE_EQUAL_1' => '%s is niet gelijk aan %d',
		'DATE_SHOULDBE_LESS' => '%s moet kleiner zijn dan %s',
		'DATE_SHOULDBE_LESS_EQUAL' => '%s moet kleiner of gelijk zijn aan %s',
		'DATE_SHOULDBE_EQUAL' => '%s moet gelijk zijn aan %s',
		'DATE_SHOULDBE_GREATER' => '%s moet groter zijn dan %s',
		'DATE_SHOULDBE_GREATER_EQUAL' => '%s moet groter of gelijk zijn aan %s',
		'DATE_SHOULDNOTBE_EQUAL' => '%s is niet gelijk aan %s',
		'LENGTH_SHOULDBE_LESS' => '%s %s moet kleiner zijn dan %d %s',
		'LENGTH_SHOULDBE_LESS_EQUAL' => '%s %s moet kleiner of gelijk zijn aan %d %s',
		'LENGTH_SHOULDBE_EQUAL' => '%s %s moet gelijk zijn aan %d %s',
		'LENGTH_SHOULDBE_GREATER' => '%s %s moet groter zijn dan %d %s',
		'LENGTH_SHOULDBE_GREATER_EQUAL' => '%s %s moet groter of gelijk zijn aan %d %s',
		'LENGTH_SHOULDNOTBE_EQUAL' => '%s %s is niet gelijk aan %d %s',
		'CANNOT_BE_EMPTY' => '%s mag niet leeg zijn',
		'CANNOT_BE_NONE' => '%s mag niet geen zijn',
		'DELETE_RECORDS' => 'Weet u zeker dat u de %s geselecteerde regels wilt verwijderen?',
	),
	'Calendar' => array(
		'LBL_APP_ERR001'=>'Foutieve datum in veld %s!',
	),
	'Messages' => array(
		'ERR_IMAP_CONNECTION_FAILED_DESCR'=>'Klik %s om de configuratie van uw mailserver te controleren',
		'ERR_IMAP_CREDENTIALS_EMPTY_DESCR'=>'Klik %s om uw gebruikersnaam en wachtwoord in te stellen',
		'ERR_IMAP_LOGIN_FAILED_DESCR'=>'Klik %s om uw gebruikersnaam en wachtwoord te controleren',
		'ERR_IMAP_SERVER_EMPTY_DESCR'=>'Klik %s om uw mailserver in te stellen',
		'LBL_ERROR_SPECIALFOLDERS_DESCR'=>'Klik %s om uw hoofdmappen in te stellen',
		'On %s, %s wrote:'=>'Op %s schreef %s ',
	),
	'ModComments' => array(
		'LBL_AGO'=>'%s ago',
	),
	'ModNotifications' => array(
		'MSG_NOTIFICATIONS_UNSEEN'=>'U heeft %s ongelezen meldingen',
	),
	'Newsletter' => array(
		'LBL_UNSUCCESS_UNSUBSCRIPTION'=>'Uitschrijven mislukt. Neem contact op met %s',
	),
	'Settings' => array(
		'LBL_PROFILE_TO_BE_USED_FOR_MOBILE'=>'Sta "%s" in als uw exclusieve profiel voor de Mobile App',
		'LBL_UNABLE_TO_CONNECT'=>'Verbinding maken met %s onmogelijk',
	),
	'Users' => array(
		'LBL_NOT_SAFETY_PASSWORD'=>'Het wachtwoord voldoet niet aan de veiligheidscriteria: gebruik tenminste %s tekens en niet gerelateerd aan gebruikersnaam, voornaam of achternaam.',
	),
);
foreach($nl_nl_translations as $module => $values) {
	foreach($values as $label => $value) {
		SDK::setLanguageEntry($module, 'nl_nl', $label, $value);
	}
}

$pt_br_translations = array(
	'APP_STRINGS' => array(
		'LBL_PERIOD' => 'Período',
	),
	'ALERT_ARR' => array(
		'SHOULDBE_LESS_1', '%s deve ser menos que %d',
		'SHOULDBE_LESS_EQUAL_1' => '%s deve ser menos que ou igual a %d',
		'SHOULDBE_EQUAL_1' => '%s deve ser igual a %d',
		'SHOULDBE_GREATER_1' => '%s deve ser maior que %d',
		'SHOULDBE_GREATER_EQUAL_1' => '%s deve ser maior que ou igual a %d',
		'SHOULDNOTBE_EQUAL_1' => '%s não deve ser igual a %d',
		'DATE_SHOULDBE_LESS' => '%s deve ser menos que %s',
		'DATE_SHOULDBE_LESS_EQUAL' => '%s deve ser menos que ou igual a %s',
		'DATE_SHOULDBE_EQUAL' => '%s deve ser igual a %s',
		'DATE_SHOULDBE_GREATER' => '%s deve ser maior que %s',
		'DATE_SHOULDBE_GREATER_EQUAL' => '%s deve ser maior que ou igual a %s',
		'DATE_SHOULDNOTBE_EQUAL' => '%s não deve ser igual a %s',
		'LENGTH_SHOULDBE_LESS' => '%s %s deve ser menos que %d %s',
		'LENGTH_SHOULDBE_LESS_EQUAL' => '%s %s deve ser menos que ou igual a %d %s',
		'LENGTH_SHOULDBE_EQUAL' => '%s %s deve ser igual a %d %s',
		'LENGTH_SHOULDBE_GREATER' => '%s %s deve ser maior que %d %s',
		'LENGTH_SHOULDBE_GREATER_EQUAL' => '%s %s deve ser maior que ou igual a %d %s',
		'LENGTH_SHOULDNOTBE_EQUAL' => '%s %s não deve ser igual a %d %s',
		'CANNOT_BE_EMPTY'=>'%s não pode estar vazio',
		'CANNOT_BE_NONE'=>'%s não pode ser nula',
		'DELETE_RECORDS'=>'Tem certeza de que deseja excluir os registros %s?',
	),
);
foreach($pt_br_translations as $module => $values) {
	foreach($values as $label => $value) {
		SDK::setLanguageEntry($module, 'pt_br', $label, $value);
	}
}

/*
 * TODO Translations Accounts and ProjectPlan
 * 
 * quando sarà rilasciata la versione core applicare questo script ai file di lingua
 * e applicare patch nel doc collegato al TT-60062
 */

/* Translate Pianificazioni -> Progetti
$translation_like = '%pianificaz%';
$translations_it = array(
	'Pianificazione'=>'Progetto',
	'pianificazione'=>'progetto',
	'Pianificazioni'=>'Progetti',
	'pianificazioni'=>'progetti',
);
$res = $adb->pquery('select * from sdk_language where trans_label like ?', array($translation_like));
if ($res && $adb->num_rows($res) > 0) {
	while ($row = $adb->fetch_array($res)) {
		if ($module == 'ServiceContracts' && $row['label'] == 'In Planning') continue;
		$count = 0;
		$newstr = str_replace(array_keys($translations_it), array_values($translations_it), $row['trans_label'], $count);
		if ($count > 0) {
			SDK::setLanguageEntry($row['module'], $row['language'], $row['label'], $newstr);
		}
	}
}
SDK::setLanguageEntries('HelpDesk', 'ProjectPlan', array('it_it'=>'Progetto','en_us'=>'Project'));
SDK::setLanguageEntries('APP_STRINGS', 'ProjectPlan', array('it_it'=>'Progetti','en_us'=>'Projects'));
*/

/* Translate Aziende -> Organizzazioni
$translation_like = '%aziend%';
$translations_it = array(
	'Azienda'=>'Organizzazione',
	'azienda'=>'organizzazione',
	'Aziende'=>'Organizzazioni',
	'aziende'=>'organizzazioni',
);
$translations_en = array(
	'Account'=>'Organization',
	'account'=>'organization',
	'Accounts'=>'Organizations',
	'accounts'=>'organizations',
);
$res = $adb->pquery('select * from sdk_language where trans_label like ?', array($translation_like));
if ($res && $adb->num_rows($res) > 0) {
	while ($row = $adb->fetch_array($res)) {
		
		if ($module == 'Administration') continue;
		if ($module == 'Help') continue;
		if ($module == 'Yahoo') continue;
		if ($module == 'Emails' && $row['label'] == 'LBL_MAIL_CONNECT_ERROR_INFO') continue;
		if ($module == 'Users' && $row['label'] == 'ERR_INVALID_USER') continue;
		
		$count = 0;
		$newstr = str_replace(array_keys($translations_it), array_values($translations_it), $row['trans_label'], $count);
		if ($count > 0) {
			SDK::setLanguageEntry($row['module'], $row['language'], $row['label'], $newstr);
		}

		$res1 = $adb->pquery('select * from sdk_language where module=? and language=? and label=?', array($row['module'],'en_us',$row['label']));
		if ($res1 && $adb->num_rows($res1) > 0) {
			$count = 0;
			$trans_label = $adb->query_result($res1,0,'trans_label');
			$newstr = str_replace(array_keys($translations_en), array_values($translations_en), $trans_label, $count);
			if ($count > 0) {
				SDK::setLanguageEntry($row['module'], 'en_us', $row['label'], $newstr);
			}
		}
	}
}
$arr = array(
	array('Accounts','questa organizzazione','un\'organizzazione','Nuova Organizzazione','organization','an organization','New Organization'),
	array('Contacts','questo contatto','un contatto','Nuovo Contatto','contact','a contact','New Contact'),
	array('Faq','questa faq','una faq','Nuova Faq','faq','a faq','New Faq'),
	array('Invoice','questa fattura','una fattura','Nuova Fattura','invoice','an invoice','New Invoice'),
	array('PurchaseOrder','questo ordine di acquisto','un ordine di acquisto','Nuovo Ordine di Acquisto','purchase order','a purchase order','New Purchase Order'),
	array('Quotes','questo preventivo','un preventivo','Nuovo Preventivo','quote','a quote','New Quote'),
	array('SalesOrder','questo ordine di vendita','un ordine di vendita','Nuovo Ordine di Vendita','sales order','a sales order','New Sales Order'),
);
foreach($arr as $a) {
	SDK::setLanguageEntries($a[0], 'MSG_DUPLICATE', array(
		'it_it'=>'Creando '.$a[1].' puoi potenzialmente creare un duplicato. Puoi selezionare '.$a[2].' dalla lista sottostante o cliccare su Crea '.$a[3].' per procedere con la creazione con i dati precedentemente inseriti.',
		'en_us'=>'Creating this '.$a[4].' may potentially create a duplicate. You may either select '.$a[4].' from the list below or you may click on Create '.$a[5].' to continue creating a new one with the previously entered data.'
	));
}
$arr = array(
	array('Accounts','l\'organizzazione','organization'),
	array('Calendar','l\'attività','activity'),
	array('Documents','il documento','document'),
	array('Emails','l\'email','email'),
	array('Faq','la faq','faq'),
	array('Invoice','la fattura','invoice'),
	array('PurchaseOrder','l\'ordine di acquisto','purchase order'),
	array('Quotes','il preventivo','quote'),
	array('SalesOrder','l\'ordine di vendita','sales order'),
);
foreach($arr as $a) {
	SDK::setLanguageEntries($a[0], 'ERR_DELETE_RECORD', array(
		'it_it'=>'Deve essere specificato un numero di record per cancellare '.$a[1],
		'en_us'=>'Please select a record number to delete the '.$a[2]
	));
}
SDK::setLanguageEntry('Contacts', 'en_us', 'LBL_EXISTING_ACCOUNT', 'Used an existing organization');
SDK::setLanguageEntry('Contacts', 'en_us', 'LBL_CREATED_ACCOUNT', 'Created a new organization');
SDK::setLanguageEntry('Leads', 'en_us', 'Existing Customer', 'Existing Organization');
SDK::setLanguageEntry('Assets', 'en_us', 'Customer Name', 'Organization');
*/
?>