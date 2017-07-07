<?php
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter', 'Targets'));
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';

global $adb, $table_prefix;

$res = $adb->query("SELECT tabid FROM {$table_prefix}_tab WHERE name = 'RecycleBin'");
if ($res && $adb->num_rows($res)>0) $_SESSION['modules_to_update']['RecycleBin'] = 'packages/vte/optional/RecycleBin.zip';

$adb->pquery("UPDATE {$table_prefix}_field SET masseditable = ? WHERE tabid = ? AND masseditable = ? AND fieldname IN (?,?,?,?)",array(1,8,0,'notes_title','assigned_user_id','filestatus','folderid'));

$em = new VTEventsManager($adb);
$em->registerHandler('vtiger.entity.aftersave','modules/Newsletter/NewsletterHandler.php','NewsletterHandler');

$fields = array();
$fields[] = array('module'=>'Leads','block'=>'LBL_LEAD_INFORMATION','name'=>'newsletter_unsubscrpt','label'=>'Receive newsletter','uitype'=>'56','columntype'=>'I(1)','typeofdata'=>'C~O');
$fields[] = array('module'=>'Accounts','block'=>'LBL_ACCOUNT_INFORMATION','name'=>'newsletter_unsubscrpt','label'=>'Receive newsletter','uitype'=>'56','columntype'=>'I(1)','typeofdata'=>'C~O');
$fields[] = array('module'=>'Contacts','block'=>'LBL_CONTACT_INFORMATION','name'=>'newsletter_unsubscrpt','label'=>'Receive newsletter','uitype'=>'56','columntype'=>'I(1)','typeofdata'=>'C~O');
include('modules/SDK/examples/fieldCreate.php');

$schema_table =
'<schema version="0.3">
	<table name="tbl_s_newsletter_g_unsub">
		<opt platform="mysql">ENGINE=InnoDB</opt>
		<field name="email" type="C" size="100">
			<KEY/>
		</field>
		<field name="unsub_date" type="T">
	      <DEFAULT value="0000-00-00 00:00:00"/>
	    </field>
	</table>
</schema>';
if(!Vtiger_Utils::CheckTable('tbl_s_newsletter_g_unsub')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

$adb->pquery("insert into tbl_s_newsletter_status (id, name) values (?,?)",array(5,'LBL_ERROR_MAIL_UNSUBSCRIBED'));

$translations = array(
	array('Accounts', 'Email Opt Out'),
	array('Accounts', 'LBL_EMAIL_OPT_OUT'),
	array('APP_STRINGS', 'Email Opt Out'),
	array('Contacts', 'Email Opt Out'),
	array('Contacts', 'LBL_EMAIL_OPT_OUT'),
	array('CustomView', 'Email Opt Out'),
);
foreach ($translations as $t) {
	SDK::setLanguageEntries($t[0], $t[1], array(
		'it_it'=>'Blocca Email Automatiche',
		'en_us'=>'Lock Automatic Emails',
		'de_de'=>'Sperren automatischen E-Mails',
		'nl_nl'=>'Lock Automatic Emails',
		'pt_br'=>'Bloqueie e-mails automáticos',
	));
}

SDK::setLanguageEntries('Newsletter', 'LBL_NEWSLETTER_UNSUBSCRIPTION', array(
	'it_it'=>'Vuoi disiscriverti da questa newsletter?',
	'en_us'=>'Do you want to unsubscribe from this newsletter?',
	'de_de'=>'Wollen Sie diesen Newsletter abbestellen?',
	'nl_nl'=>'Wilt u zich afmelden voor deze nieuwsbrief?',
	'pt_br'=>'Você quer se descadastrar da newsletter?',
));
SDK::setLanguageEntries('Newsletter', 'LBL_NEWSLETTER_UNSUBSCRIPTION_BUTTON', array(
	'it_it'=>'Si, disiscrivimi',
	'en_us'=>'Yes, unsubscribe me',
	'de_de'=>'Ja, mich abmelden',
	'nl_nl'=>'Ja, af te melden me',
	'pt_br'=>'Sim, me descadastrar',
));
SDK::setLanguageEntries('Newsletter', 'LBL_GENERAL_UNSUBSCRIPTION', array(
	'it_it'=>'Non desideri più ricevere nostre comunicazioni?',
	'en_us'=>'No longer wish to receive our communications?',
	'de_de'=>'Nicht mehr wünschen, unsere Kommunikation zu erhalten?',
	'nl_nl'=>'Niet langer wenst om onze berichten te ontvangen?',
	'pt_br'=>'Não deseja mais receber nossas comunicações?',
));
SDK::setLanguageEntries('Newsletter', 'LBL_GENERAL_UNSUBSCRIPTION_BUTTON', array(
	'it_it'=>'Si, disiscrivimi da tutte le newsletter',
	'en_us'=>'Yes, unsubscribe me from all newsletters',
	'de_de'=>'Ja, streichen Sie mich aus allen Newslettern',
	'nl_nl'=>'Ja, af te melden me van alle nieuwsbrieven',
	'pt_br'=>'Sim, Baixa-me de todos os boletins',
));
SDK::setLanguageEntries('Newsletter', 'LBL_UNSUCCESS_UNSUBSCRIPTION', array(
	'it_it'=>'Disiscrizione non avvenuta. Contattare %s',
	'en_us'=>'Unsubscription failed. Please contact %s',
	'de_de'=>'Abmeldung fehlgeschlagen. Bitte kontaktieren Sie %s',
	'nl_nl'=>'Uitschrijven mislukt. Neemt u a.u.b. contact op met %s',
	'pt_br'=>'Cancelamento falhou. Entre em contato %s',
));
SDK::setLanguageEntries('Newsletter', 'LBL_SUCCESS_GENERAL_UNSUBSCRIPTION', array(
	'it_it'=>'Disiscrizione avvenuta con successo',
	'en_us'=>'Successfully Unsubscribed',
	'de_de'=>'Erfolgreich abgemeldet',
	'nl_nl'=>'Uitschrijving gelukt',
	'pt_br'=>'Sobras com sucesso',
));
SDK::setLanguageEntries('Newsletter', 'LBL_ALREADY_GENERAL_UNSUBSCRIPTION', array(
	'it_it'=>'Disiscrizione già effettuata',
	'en_us'=>'Unsubscription already done',
	'de_de'=>'Abmeldung bereits vollzogen',
	'nl_nl'=>'Uitschrijving is reeds gebeurd',
	'pt_br'=>'Cancelamento já realizado',
));

SDK::setLanguageEntries('Newsletter', 'LBL_ERROR_MAIL_UNSUBSCRIBED', array(
	'it_it'=>'Indirizzo disiscritto',
	'en_us'=>'Unsubscribed email',
	'de_de'=>'E-Mail abgemeldet',
	'nl_nl'=>'uitgeschreven email',
	'pt_br'=>'endereço não subscritas',
));

SDK::setLanguageEntries('ALERT_ARR', 'LBL_TEMPLATE_MUST_HAVE_UNSUBSCRIPTION_LINK', array(
	'it_it'=>'Manca il link per la disiscrizione. Procedere comunque?',
	'en_us'=>'Missing link for unsubscribing. Proceed anyway?',
	'de_de'=>'Missing Link abzubestellen. Trotzdem fortfahren?',
	'nl_nl'=>'Missing link voor afmelden. Toch doorgaan?',
	'pt_br'=>'Faltando link para cancelar a assinatura. Continuar mesmo assim?',
));

SDK::setLanguageEntries('ALERT_ARR', 'LBL_TEMPLATE_MUST_HAVE_PREVIEW_LINK', array(
	'it_it'=>'Manca il link per l\'anteprima. Procedere comunque?',
	'en_us'=>'Missing link for the preview. Proceed anyway?',
	'de_de'=>'Missing Link für die Vorschau. Trotzdem fortfahren?',
	'nl_nl'=>'Missing link voor de preview. Toch doorgaan?',
	'pt_br'=>'Faltando link para visualização. Continuar mesmo assim?',
));

SDK::setLanguageEntries('Newsletter', 'OpenNewsletterWizard', array(
	'it_it'=>'Crea newsletter...',
	'en_us'=>'Create newsletter...',
	'nl_nl'=>'Maak nieuwsbrief aan...',
	'de_de'=>'Erstelle Newsletter...',
	'pt_br'=>'Criar boletim informativo...',
));
SDK::setLanguageEntries('Campaigns', 'OpenNewsletterWizard', array(
	'it_it'=>'Crea newsletter...',
	'en_us'=>'Create newsletter...',
	'nl_nl'=>'Maak nieuwsbrief aan...',
	'de_de'=>'Erstelle Newsletter...',
	'pt_br'=>'Criar boletim informativo...',
));

SDK::setLanguageEntries('Leads', 'Receive newsletter', array(
 'it_it'=>'Riceve newsletter',
 'en_us'=>'Receive newsletter',
 'de_de'=>'Newsletter abonnieren',
 'nl_nl'=>'nieuwsbrief ontvangen',
 'pt_br'=>'Ricevere boletim',
));
SDK::setLanguageEntries('Accounts', 'Receive newsletter', array(
 'it_it'=>'Riceve newsletter',
 'en_us'=>'Receive newsletter',
 'de_de'=>'Newsletter abonnieren',
 'nl_nl'=>'nieuwsbrief ontvangen',
 'pt_br'=>'Ricevere boletim',
));
SDK::setLanguageEntries('Contacts', 'Receive newsletter', array(
 'it_it'=>'Riceve newsletter',
 'en_us'=>'Receive newsletter',
 'de_de'=>'Newsletter abonnieren',
 'nl_nl'=>'nieuwsbrief ontvangen',
 'pt_br'=>'Ricevere boletim',
));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_NEWSLETTER_UNSUB_ENABLE', array(
 'it_it'=>'Disattiva ricezione newsletter',
 'en_us'=>'Disable receiving newsletter',
 'de_de'=>'Empfangen deaktivieren Newsletter',
 'nl_nl'=>'Uitschakelen nieuwsbrief ontvangen',
 'pt_br'=>'Desativar Boletim recepção',
));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_NEWSLETTER_UNSUB_DISABLE', array(
 'it_it'=>'Attiva ricezione newsletter',
 'en_us'=>'Enable receiving newsletter',
 'de_de'=>'Aktivieren Empfangen Newsletter',
 'nl_nl'=>'Enable nieuwsbrief ontvangen',
 'pt_br'=>'Ativar boletim recepção',
));

SDK::setLanguageEntries('APP_STRINGS', 'NEWSLETTER_G_UNSUBSCRIBE_DIR', array(
	'it_it'=>'Newsletter Custom Report',
	'en_us'=>'Newsletter Custom Report folder',
	'de_de'=>'Ordner Custom Report Newsletter',
	'nl_nl'=>'Nieuwsbrief Custom Report map',
	'pt_br'=>'Pasta Boletim relatório personalizado',
));
SDK::setLanguageEntries('Newsletter', 'NEWSLETTER_UNSUB_EMAIL', array(
	'it_it'=>'Email',
	'en_us'=>'Email',
	'de_de'=>'E-mail',
	'nl_nl'=>'E-mail',
	'pt_br'=>'Email',
));
SDK::setLanguageEntries('Newsletter', 'NEWSLETTER_UNSUB_DATE', array(
	'it_it'=>'Data disiscrizione',
	'en_us'=>'Unsubscription date',
	'de_de'=>'Abmeldung datum',
	'nl_nl'=>'Afmelding datum',
	'pt_br'=>'Data de cancelamento',
));
SDK::setLanguageEntries('Newsletter', 'NEWSLETTER_ENTTITY', array(
	'it_it'=>'Record',
	'en_us'=>'Record',
	'de_de'=>'Record',
	'nl_nl'=>'Record',
	'pt_br'=>'Record',
));
SDK::setLanguageEntries('Newsletter', 'NEWSLETTER_ENTTITY_NUM', array(
	'it_it'=>'Numero record',
	'en_us'=>'Record number',
	'de_de'=>'Rekordzahl',
	'nl_nl'=>'Record aantal',
	'pt_br'=>'Número recorde',
));
SDK::setLanguageEntries('Newsletter', 'NEWSLETTER_G_UNSUBSCRIBE', array(
	'it_it'=>'Discrizioni totali',
	'en_us'=>'Newsletter unsubscriptions',
	'de_de'=>'Newsletter abmeldungen',
	'nl_nl'=>'Nieuwsbrief afmeldingen',
	'pt_br'=>'Newsletter unsubscriptions',
));
SDK::setLanguageEntries('Reports', 'NEWSLETTER_G_UNSUBSCRIBE', array(
	'it_it'=>'Discrizioni totali',
	'en_us'=>'Newsletter unsubscriptions',
	'de_de'=>'Newsletter abmeldungen',
	'nl_nl'=>'Nieuwsbrief afmeldingen',
	'pt_br'=>'Newsletter unsubscriptions',
));
SDK::setLanguageEntries('Reports', 'NEWSLETTER_G_UNSUBSCRIBE_DESC', array(
	'it_it'=>'Visualizza i Contatti, Aziende e Lead disiscritti globalmente dalle campagne newsletter',
	'en_us'=>'Show Contacts, Accounts and Leads globally unsubscribed',
	'de_de'=>'Ausstellung Personen, Organisationen und Leads global abgemeldet',
	'nl_nl'=>'Toon Contactpersoon, Relaties en Leads wereldwijd uitgeschreven',
	'pt_br'=>'Mostrar Contato, Conta e Leads globalmente retirado',
));

$newsletterModule = Vtiger_Module::getInstance('Newsletter');
Vtiger_Link::addLink($newsletterModule->id,'HEADERSCRIPT','ReportGlobalUnsubscribe','modules/SDK/src/modules/Newsletter/ReportGlobalUnsubscribe.js');
SDK::setReportFolder('NEWSLETTER_G_UNSUBSCRIBE_DIR', '');
SDK::setReport('NEWSLETTER_G_UNSUBSCRIBE', 'NEWSLETTER_G_UNSUBSCRIBE_DESC', 'NEWSLETTER_G_UNSUBSCRIBE_DIR', 'modules/SDK/src/modules/Newsletter/ReportGlobalUnsubscribe.php', 'GlobalUnsubscribeReportRun', 'FilterUnsubReport');

$q = "SELECT reportid FROM sdk_reports WHERE runclass = ?";
$res = $adb->pquery($q,array('GlobalUnsubscribeReportRun'));
if($res && $adb->num_rows($res) > 0){
	$reportid = $adb->query_result($res,0,'reportid');

	$q1 = "SELECT folderid FROM {$table_prefix}_crmentityfolder WHERE foldername =?";
	$res1 = $adb->pquery($q1,array('NEWSLETTER_G_UNSUBSCRIBE_DIR'));
	if($res1 && $adb->num_rows($res1) > 0){
		$folderid = $adb->query_result($res1,0,'folderid');
		$onclick = "window.location='index.php?module=Reports&action=SaveAndRun&record=".$reportid."&folderid=".$folderid."';";
		SDK::setMenuButton('contestual', 'NEWSLETTER_G_UNSUBSCRIBE', $onclick, 'sharkPanel.png', 'Newsletter');
	}
}
?>