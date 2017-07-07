<?php
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';

SDK::setLanguageEntries('Emails', 'LBL_CONF_MAILSERVER_ERROR', array(
	'it_it'=>'Prego configura il server di posta in uscita da\\nImpostazioni > Server Posta in Uscita',
	'en_us'=>'Please configure your outgoing mailserver under\\nSettings > Outgoing Server link',
	'pt_br'=>'Por favor configure seu Servidor de Correio em\\nConfigurações > Servidor de Envio',
	'de_de'=>'Bitte konfigurieren Sie den Server für\\ngesendete E-Mails > Server Link',
	'nl_nl'=>'Configureer uw uitgaande e-mail server in\\ninstellingen > Uitgaande server link',
));

if (isModuleInstalled('Projects')) {
	vtlib_toggleModuleAccess('Projects',false);
}
@unlink('packages/vte/optional/Projects.zip');
unset($_SESSION['modules_to_update']['Projects']);

global $adb, $table_prefix;

$adb->pquery("UPDATE {$table_prefix}_field SET presence = ? WHERE uitype = ? AND readonly = ?",array(1,13,100));

$res = $adb->pquery("SELECT * FROM sdk_menu_contestual WHERE module = ? AND title = ? AND image = ?",array('Potentials','Budget','sharkPanel.png'));
if ($res && $adb->num_rows($res) == 0) {
	$result = $adb->pquery("SELECT {$table_prefix}_report.reportid, {$table_prefix}_report.folderid
							FROM sdk_reports
							INNER JOIN {$table_prefix}_report ON sdk_reports.reportid = {$table_prefix}_report.reportid
							WHERE runclass = ?", array('BudgetReportRun'));
	if ($result && $adb->num_rows($result) > 0) {
		$sharkReportId = $adb->query_result($result,0,'reportid');
		$sharkReportFolder = $adb->query_result($result,0,'folderid');
		SDK::setMenuButton('contestual', 'Budget', "window.location='index.php?module=Reports&action=SaveAndRun&record={$sharkReportId}&folderid={$sharkReportFolder}';", 'sharkPanel.png', 'Potentials');
	}
}

// riapplico le traduzioni perchè c'era un errore nello script
include('modules/Update/changes/835_836.php');

$new_en_us_translations = array(
	array('module'=>'Visitreport','label'=>'Conoscenza','trans_label'=>'Contact'),
	array('module'=>'Visitreport','label'=>'Presentazione','trans_label'=>'Presentation'),
	array('module'=>'Visitreport','label'=>'Prodotti','trans_label'=>'Products'),
	array('module'=>'Visitreport','label'=>'Consegna','trans_label'=>'Delivery'),
	array('module'=>'Visitreport','label'=>'Offerta','trans_label'=>'Offer'),
	array('module'=>'Visitreport','label'=>'Ritiro offerta','trans_label'=>'Withdrawal offer'),
	array('module'=>'Visitreport','label'=>'Consulenza','trans_label'=>'Advice'),
	array('module'=>'Visitreport','label'=>'Voci di capitolato','trans_label'=>'Items of specifications'),
	array('module'=>'Visitreport','label'=>'Analisi scientifiche','trans_label'=>'Scientific analysis'),
	array('module'=>'Visitreport','label'=>'Campioni','trans_label'=>'Samples'),
	array('module'=>'Visitreport','label'=>'Altro','trans_label'=>'Other'),
	array('module'=>'Visitreport','label'=>'Consegna Offerta','trans_label'=>'Delivery offer'),
	array('module'=>'Visitreport','label'=>'Presentazione Prodotti','trans_label'=>'Presentation of products'),
	array('module'=>'Visitreport','label'=>'Stesura capitolato','trans_label'=>'Drawing up specifications'),
	array('module'=>'Visitreport','label'=>'Controllo avanzamento','trans_label'=>'Progress control'),
);
foreach($new_en_us_translations as $t) {
	SDK::setLanguageEntry($t['module'], 'en_us', $t['label'], $t['trans_label']);
}
?>