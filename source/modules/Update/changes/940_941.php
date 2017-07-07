<?php
global $enterprise_current_version,$enterprise_mode;
SDK::setLanguageEntries('APP_STRINGS', 'LBL_BROWSER_TITLE', array(
	'it_it'=>"$enterprise_mode $enterprise_current_version",
	'en_us'=>"$enterprise_mode $enterprise_current_version",
	'pt_br'=>"$enterprise_mode $enterprise_current_version",
	'de_de'=>"$enterprise_mode $enterprise_current_version",
	'nl_nl'=>"$enterprise_mode $enterprise_current_version"
));

global $adb, $table_prefix;
$result = $adb->pquery("SELECT blockid FROM {$table_prefix}_settings_blocks WHERE label = ?",array('LBL_STUDIO'));
if ($result && $adb->num_rows($result) > 0) {
	$blockid = $adb->query_result($result,0,'blockid');
	$adb->pquery("UPDATE {$table_prefix}_settings_field SET sequence = sequence+1 WHERE blockid = ?",array($blockid));
	$adb->pquery("UPDATE {$table_prefix}_settings_field SET blockid = ?, sequence = ? WHERE name = ?",array($blockid,1,'LBL_MENU_TABS'));
}

$arr = array(
	'LBL_RECOVER_INTRO' => 'Inserisci il tuo nome utente.<br />Ti verrà inviata una mail con le istruzioni per impostare una nuova password.',
	'LBL_RECOVER_MAIL_SENT' => 'La mail è stata inviata.',
	'LBL_RECOVER_MAIL_ERROR' => 'Non è stato possibile inviare la mail.<br />Contatta l\'amministratore e richiedi il cambio password.',
	'LBL_RECOVERY_SYSTEM3' => 'altrimenti compila i campi sottostanti con una nuova password che sostituirà la vecchia.',
	'LBL_RECOVERY_PASSWORD_SAVED' => 'La nuova password è stata salvata.',
	'LBL_SAVELOGIN_HELP' => 'Assicurati che il server abbia session.gc_maxlifetime = 2592000 nel php.ini per attivare la funzionalità.',
	'LBL_USER_BLOCKED' => "L'utente è stato bloccato in quanto non operativo da più di %s mesi. Contatta l'amministratore per riattivarlo.",
	'LBL_AVATAR_INSTRUCTIONS'=>'Per impostare la miniatura è necessario inserire la fotografia.',
	'LBL_WAIT_FOR_LOGIN' => 'Entro pochi secondi verrà automaticamente effettuato l\'accesso con la nuova password. In caso di problemi, accedi con il pulsante sottostante.',
);
foreach($arr as $label => $trans) {
	SDK::setLanguageEntry('Users', 'it_it', $label, $trans);
}

SDK::setLanguageEntries('APP_STRINGS', 'LBL_AREAS', array(
	'it_it'=>'Aree',
	'en_us'=>'Areas',
));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_MODULES', array(
	'it_it'=>'Moduli',
	'en_us'=>'Modules',
));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_AREA_TOOLS', array(
	'it_it'=>'Configurazione aree',
	'en_us'=>'Areas configuration',
));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_PROPAGATE_AREA', array(
	'it_it'=>'Puoi propagare questa configurazione di aree sugli altri utenti tramite il pulsante "Propaga"',
	'en_us'=>'You can propagate this area configuration on the other users by the button "Propagate"',
));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_PROPAGATE_AREA_BUTTON', array(
	'it_it'=>'Propaga',
	'en_us'=>'Propagate',
));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_BLOCK_AREA_LAYOUT', array(
	'it_it'=>'Puoi bloccare il layout selezionando questa checkbox',
	'en_us'=>'You can lock the layout by selecting this checkbox',
));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_SEARCH_MODULE', array(
	'it_it'=>'Cerca modulo',
	'en_us'=>'Search module',
));
SDK::setLanguageEntries('Home', 'CRMVNEWS', array('it_it'=>'News da VTECRM','en_us'=>'News from VTECRM'));
SDK::setLanguageEntries('Users', 'CRMVNEWS', array('it_it'=>'News da VTECRM','en_us'=>'News from VTECRM'));
SDK::setLanguageEntries('Settings', 'LBL_MENU_TABS_DESCRIPTION', array('it_it'=>'Mostra e/o nasconde le voci di menu, tenere presente che questo non influenza gli amministratori!'));
SDK::setLanguageEntries('Settings', 'LBL_ENABLE_AREAS', array(
	'it_it'=>'Abilita menù aree',
	'en_us'=>'Enable menu areas',
));

require_once('modules/Area/Area.php');
$focus = AreaManager::getInstance();
$focus->createTables();

$result = $adb->query("select templatename, body from {$table_prefix}_emailtemplates where body LIKE '%crmvillage%'");
if ($result && $adb->num_rows($result) > 0) {
	while($row=$adb->fetchByAssoc($result,-1,false)) {
		$body = $row['body'];
		$body = str_replace('VTE 4.0</td>','VTE 5.1</td>',$body);
		$body = str_replace('www.crmvillage.biz</td>','www.vtecrm.com</td>',$body);
		$body = str_replace('Lo Staff CRMVILLAGE.BIZ</td>','Lo Staff VTECRM</td>',$body);
		$body = str_replace('<a href="http://www.crmvillage.biz" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: rgb(66, 66, 253);">www.crmvillage.biz</a></td>','<a href="http://www.vtecrm.com" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: rgb(66, 66, 253);">www.vtecrm.com</a></td>',$body);
		$body = str_replace('<strong>CRMVILLAGE</strong><strong>.BIZ S.r.L.</strong> - Via Ciro Menotti 3, c/o Via Fontanelle - San Bonifacio (VR), 37047</td>','VTECRM LIMITED - 38 Craven Street London WC2N 5NG - Registration No. 08337393</td>',$body);
		$body = str_replace('<td align="center"><strong>CRMVILLAGE</strong><strong>.BIZ S.r.L.</strong> - Via Ciro   Menotti 3, c/o Via Fontanelle - San Bonifacio (VR), 37047</td>','<td align="center">VTECRM LIMITED - 38 Craven Street London WC2N 5NG - Registration No. 08337393</td>',$body);
		$body = str_replace('Tel +39 045 5116489 - Fax +39 045 5111073 - P.IVA: 03641400233</td>','VAT No. 166 1940 00 - Phone (+44) 2035298324</td>',$body);
		$body = str_replace('<td align="center">Tel +39 045   5116489 - Fax +39 045 5111073 - P.IVA: 03641400233</td>','<td align="center">VAT No. 166 1940 00 - Phone (+44) 2035298324</td>',$body);
		$body = str_replace('E-Mail: <a href="mailto:info@crmvillage.biz" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: #666;">info@crmvillage.biz</a></td>','E-Mail: <a href="mailto:info@vtecrm.com" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: #666;">info@vtecrm.com</a></td>',$body);
		$adb->updateClob($table_prefix.'_emailtemplates','body',"templatename = '".$row['templatename']."'",$body);
	}
}

$idxs = $adb->database->MetaIndexes($table_prefix.'_changelog');
$idx_found = false;
foreach($idxs as $idx) {
	if (count($idx['columns']) == 1 && $idx['columns'][0] == 'parent_id') {
		$idx_found = true;
		break;
	}
}
if (!$idx_found) $adb->datadict->ExecuteSQLArray((Array)$adb->datadict->CreateIndexSQL("changelog_parent_id_idx", "{$table_prefix}_changelog", 'parent_id'));

$idxs = $adb->database->MetaIndexes($table_prefix.'_seactivityrel');
$idx_found = false;
foreach($idxs as $idx) {
	if (count($idx['columns']) == 2 && $idx['columns'][0] == 'activityid' && $idx['columns'][1] == 'crmid') {
		$idx_found = true;
		break;
	}
}
if (!$idx_found) $adb->datadict->ExecuteSQLArray((Array)$adb->datadict->CreateIndexSQL("seactivityrel_inversekey_idx", "{$table_prefix}_seactivityrel", 'activityid,crmid'));

$idxs = $adb->database->MetaIndexes($table_prefix.'_crmentityrel');
$idx_found = false;
foreach($idxs as $idx) {
	if (count($idx['columns']) == 2 && $idx['columns'][0] == 'relcrmid' && $idx['columns'][1] == 'crmid') {
		$idx_found = true;
		break;
	}
}
if (!$idx_found) $adb->datadict->ExecuteSQLArray((Array)$adb->datadict->CreateIndexSQL("crmentityrel_inversekey_idx", "{$table_prefix}_crmentityrel", 'relcrmid,crmid'));
?>