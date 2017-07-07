<?php
$_SESSION['modules_to_update']['Fax'] = 'packages/vte/mandatory/Fax.zip';
$_SESSION['modules_to_update']['Sms'] = 'packages/vte/mandatory/Sms.zip';
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter'));

SDK::clearSessionValues();

$camInstance = Vtiger_Module::getInstance('Campaigns');
$newslInstance = Vtiger_Module::getInstance('Newsletter');
Vtiger_Link::addLink($camInstance->id, 'LISTVIEWBASIC', 'OpenNewsletterWizard', "openNewsletterWizard('\$MODULE\$', '');", '', 1);
Vtiger_Link::addLink($newslInstance->id, 'LISTVIEWBASIC', 'OpenNewsletterWizard', "openNewsletterWizard('\$MODULE\$', '');", '', 1);
Vtiger_Link::addLink($camInstance->id, 'DETAILVIEWBASIC', 'OpenNewsletterWizard', "javascript:openNewsletterWizard('\$MODULE\$', '\$RECORD\$');", '', 1);

$trans = array(
	'APP_STRINGS' => array(
		'it_it' => array(
			'LBL_FORWARD' => 'Avanti',
			'LBL_ADD_ALL' => 'Aggiungi tutti',
			'LBL_RESEND' => 'Revinvia',
			'LBL_NOW' => 'Adesso',
		),
		'en_us' => array(
			'LBL_FORWARD' => 'Next',
			'LBL_ADD_ALL' => 'Add all',
			'LBL_RESEND' => 'Resend',
			'LBL_NOW' => 'Now',
		),
	),
	'ALERT_ARR' => array(
		'it_it' => array(
			'LBL_FILTER' => 'Filtro',
			'LBL_TEMPLATE_MUST_HAVE_NAME' => 'Devi dare un nome al template',
			'LBL_MUST_TYPE_SUBJECT' => 'Devi specificare un oggetto',
			'LBL_SELECT_RECIPIENTS' => 'Seleziona almeno un destinatario',
			'LBL_SELECT_TEMPLATE' => 'Seleziona un template',
			'LBL_FILL_FIELDS' => 'Compilare i seguenti campi',
			'LBL_SEND_TEST_EMAIL' => 'Devi inviare l\'email di test prima',
			'LBL_INVALID_EMAIL' => 'Indirizzo email non valido',
			'LBL_TEST_EMAIL_SENT' => 'Email di test spedita correttamente',
			'LBL_ERROR_SENDING_TEST_EMAIL' => 'Errore durante la spedizione dell\'email di test',
			'LBL_ERROR_SAVING' => 'Errore durante il salvataggio',
			'LBL_NEWSLETTER_SCHEDULED' => 'Newsletter creata e pianificata per l\'ora indicata',
		),
		'en_us' => array(
			'LBL_FILTER' => 'Filter',
			'LBL_TEMPLATE_MUST_HAVE_NAME' => 'You have to give a name to the template',
			'LBL_MUST_TYPE_SUBJECT' => 'You have to type a subject',
			'LBL_SELECT_RECIPIENTS' => 'Select at least one recipient',
			'LBL_SELECT_TEMPLATE' => 'Select a template',
			'LBL_FILL_FIELDS' => 'Fill the following fields',
			'LBL_SEND_TEST_EMAIL' => 'You have to send the test email first',
			'LBL_INVALID_EMAIL' => 'Invalid email address',
			'LBL_TEST_EMAIL_SENT' => 'Test Email sent correctly',
			'LBL_ERROR_SENDING_TEST_EMAIL' => 'Error while sending test email',
			'LBL_ERROR_SAVING' => 'Error while saving',
			'LBL_NEWSLETTER_SCHEDULED' => 'Newsletter created and scheduled for the specified time',
		),
	),
	'Campaigns' => array(
		'it_it' => array(
			'OpenNewsletterWizard' => 'Crea newsletter',
			'NewsletterWizard' => 'Creazione newsletter',
		),
		'en_us' => array(
			'OpenNewsletterWizard' => 'Create newsletter',
			'NewsletterWizard' => 'Newsletter creation wizard',
		),
	),
	'Newsletter' => array(
		'it_it' => array(
			'OpenNewsletterWizard' => 'Crea newsletter',
			'ChooseRecipients' => 'Scelta destinatari',
			'NewsletterData' => 'Dati Newsletter',
			'ScheduleNewsletter' => 'Pianifica',
			'NewsletterProgress' => 'Avanzamento',
			'Recipients' => 'Destinatari',
			'TestEmail' => 'Email di prova',
			'NewsletterStatus' => 'Stato Newsletter',
			'WhichRecipientsToAdd' => 'Quali destinatari vuoi aggiungere?',
			'SelectedRecipients' => 'Destinatari selezionati',
			'NowChooseATemplate' => 'Adesso scegli un template o creane uno nuovo',
			'InsertVariable' => 'Inserisci variabile',
			'InsertNewsletterData' => 'Inserisci i dati della newsletter',
			'SendTestEmailTo' => 'Invia un email di test al seguente indirizzo',
			'OkWhenDoWeScheduleIt' => 'Ok, quando la facciamo partire?',
			'AnotherTime' => 'In un altro momento',
			'SaveAndSend' => 'Salva e invia',
			'SaveAndSchedule' => 'Salva e pianifica',
			'YouCanSeeNewsletterPreview' => 'Puoi anche vedere un\'anteprima della newsletter cliccando sul seguente pulsante',
		),
		'en_us' => array(
			'OpenNewsletterWizard' => 'Create newsletter',
			'ChooseRecipients' => 'Recipients selection',
			'NewsletterData' => 'Newsletter fields',
			'ScheduleNewsletter' => 'Schedule',
			'NewsletterProgress' => 'Progress',
			'Recipients' => 'Recipients',
			'TestEmail' => 'Test Email',
			'NewsletterStatus' => 'Newsletter status',
			'WhichRecipientsToAdd' => 'Wich recipients do you want to add?',
			'SelectedRecipients' => 'Selected recipients',
			'NowChooseATemplate' => 'Now choose a template or create a new one',
			'InsertVariable' => 'Insert variable',
			'InsertNewsletterData' => 'Insert newsletter informations',
			'SendTestEmailTo' => 'Send a test email to the following address',
			'OkWhenDoWeScheduleIt' => 'Ok, when do we schedule it?',
			'AnotherTime' => 'Later',
			'SaveAndSend' => 'Save and send',
			'SaveAndSchedule' => 'Save and schedule',
			'YouCanSeeNewsletterPreview' => 'You can also see a preview of the newsletter by clicking on the following button',
		),
	)
);

foreach ($trans as $module=>$modlang) {
	foreach ($modlang as $lang=>$translist) {
		foreach ($translist as $label=>$translabel) {
			SDK::setLanguageEntry($module, $lang, $label, $translabel);
		}
	}
}

?>