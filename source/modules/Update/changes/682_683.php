<?php
$_SESSION['modules_to_update']['ChangeLog'] = 'packages/vte/mandatory/ChangeLog.zip';
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';
$_SESSION['modules_to_update']['Morphsuit'] = 'packages/vte/mandatory/Morphsuit.zip';
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';

SDK::setLanguageEntries('Users','Receive public talks',array('it_it'=>'Abilita conversazioni pubbliche','en_us'=>'Enable public talks','pt_br'=>'Ativa conversas pњblicas'));

SDK::setLanguageEntry('Webmails','it_it',"The server couldn't find the message you requested.",'Il server non ш in grado di trovare il messaggio richiesto.');
SDK::setLanguageEntry('Webmails','it_it',"Most probably your message list was out of date and the message has been moved away or deleted (perhaps by another program accessing the same mailbox).","Molto probabilmente l'elenco dei messaggi non era aggiornato e il messaggio ш stato spostato o cancellato (forse da un altro programma accedendo alla stessa cassetta postale).");

@unlink('modules/Morphsuit/CheckAvailableVersion.php');
@unlink('modules/Morphsuit/GetFreeKey.php');

global $adb, $table_prefix;
if (file_exists('hash_version.txt')) {
	$hash_version = file_get_contents('hash_version.txt');
	$adb->updateClob($table_prefix.'_version','hash_version','id=1',$hash_version);
	@unlink('hash_version.txt');
} else {
	$result = $adb->query('select hash_version from '.$table_prefix.'_version where id=1');
	if ($result) {
		$hash_version = $adb->query_result($result,0,'hash_version');
	}
}
$result = $adb->pquery('SELECT id FROM '.$table_prefix.'_users WHERE status = ?',array('Active'));
if ($result && $adb->num_rows($result) > 0) {
	$adb->pquery('delete from '.$table_prefix.'_reload_session where session_var = ?',array('vtiger_hash_version'));
	while($row=$adb->fetchByAssoc($result)) {
		$adb->pquery('insert into '.$table_prefix.'_reload_session (userid,session_var) values (?,?)',array($row['id'],'vtiger_hash_version'));
	}
}
$result = $adb->query("SELECT * FROM ".$table_prefix."_version");
$_SESSION['vtiger_hash_version'] = Users::m_encryption(Users::de_cryption($adb->query_result_no_html($result, 0, 'hash_version')));

SDK::setLanguageEntries('Users', 'LBL_CLICK_OK_TO_RECOVER', array('it_it'=>'Clicca "OK" per cambiarla ora.','en_us'=>'Please click to "OK" to change it now.','pt_br'=>'Clique no link "OK" para mudс-la agora.'));
SDK::setLanguageEntry('Morphsuit','en_us','LBL_MORPHSUIT_ACTIVATE','Activate');
SDK::setLanguageEntries('Morphsuit', 'LBL_MORPHSUIT_SITE_REGISTRATION', array('it_it'=>'Se non sei ancora registrato clicca','en_us'=>'If you are not yet registered click','pt_br'=>'Se vocъ ainda nуo щ registado clique'));
SDK::setLanguageEntries('Morphsuit', 'LBL_ERROR_VTE_FREE_NOT_ACTIVABLE', array('it_it'=>'Questa versione non ш piљ attivabile. Scarica/Aggiorna all\'ultima disponibile.','en_us'=>'This version is no longer activable. Install/Update to the last version.','pt_br'=>'Esta versуo nуo щ mais disponэvel. Instalar / Atualizar a њltima versуo.'));
SDK::setLanguageEntries('Morphsuit', 'LBL_MORPHSUIT_CODE', array('it_it'=>'Incolla qui il Codice di attivazione','en_us'=>'Past here the Activation code','pt_br'=>'Cola aqui o cѓdigo de ativaчуo'));
SDK::setLanguageEntries('Morphsuit', 'LBL_MORPHSUIT_ADMIN_CONFIG', array('it_it'=>"Configura l'utente amministratore",'en_us'=>'Set the administrator user','pt_br'=>'Configure o usuсrio administrador'));
SDK::setLanguageEntries('Morphsuit', 'LBL_CONNECT_TO_ENABLE_VTE', array('it_it'=>'Verifica la connessione a internet di VTE e rifare il login per sbloccare VTECRM','en_us'=>'Check the internet connection of the VTE and login again in order to unlock VTECRM','pt_br'=>'Verifique a conexуo de internet do VTE e se logar novamente, a fim de desbloquear'));
SDK::setLanguageEntries('Morphsuit', 'LBL_OTHER_FREE_VERSION', array('it_it'=>'Ci risulta sia stata installato un altro VTE Free per questo utente del sito. Alcune funzionalitр saranno quindi ridotte.','en_us'=>'Probably you have moved your VTE installation. Some functionality of this old installation are limited.','pt_br'=>'Provavelmente vocъ mudou o caminho de sua instalaчуo do VTE. Algumas funcionalidades deste antiga instalaчуo sуo limitadas.'));
SDK::setLanguageEntries('Morphsuit', 'LBL_MORPHSUIT_SITE_LOGIN', array('it_it'=>'Inserisci username e password di','en_us'=>'Insert username and password of','pt_br'=>'Use usuсrio e senha de'));
SDK::setLanguageEntry('Morphsuit','it_it','LBL_MORPHSUIT_ACTIVATION_MAIL_ERROR','In caso di problemi nell`invio automatico e` necessario spedire manualmente la mail con la chiave di attivazione.');
SDK::setLanguageEntry('Morphsuit','en_us','LBL_MORPHSUIT_ACTIVATION_MAIL_ERROR','I was unable to send the request, please send an email with the activation key');
SDK::setLanguageEntry('Morphsuit','en_us','LBL_MORPHSUIT_ACTIVATION_MAIL_OK','Mail has been sent to VTECRM Network service, check your inbox for the activation code.');
SDK::setLanguageEntries('Morphsuit', 'LBL_MORPHSUIT_OVERWRITE_CREDENTIALS', array('it_it'=>'Le credenziali di accesso dell\'utente admin saranno sovrascritte con quelle di vtecrm.com','en_us'=>'Admin user credentials will be overwrite with the same of vtecrm.com','pt_br'=>'As credenciais do usuсrio admin serуo serуo substituэdas com aqueles do site vtecrm.com'));

SDK::setLanguageEntries('Settings', 'LBL_PRIVACY', array('it_it'=>'Privacy','en_us'=>'Privacy','pt_br'=>'Privacy'));
SDK::setLanguageEntries('Settings', 'LBL_PRIVACY_DESC', array(
'it_it'=>"Per migliorare la versione community di VTE, memorizziamo alcune informazioni riguardo la tua installazione come l'utente di vtecrm.com che ha attivato il sistema, la cartella in cui ш stato installato e il numero di utenti che lo usa, che sono le informazioni necessarie per l'installazione. Non salviamo nessun'altra informazione riguardo dati che hai inserito o server mail che usi. E' solo un modo per conoscere quante persone utilizzano VTE nel mondo. Questa informazione ш aggiornata ad ogni login dell'utente amministratore, quindi il numero di utenti attivi viene sempre aggiornato.",
'en_us'=>"In order to improve the community version of VTE, we collect a minimum set of information about your installation like the vtecrm.com user that activate the system, the folder it has been installed and the number of users using it, which are the same information needed to activate it. We don't collect any information about the data you insert or the mail server you use. It is only a way to understand how many people are enjoying the VTE in the world. This information is updated when the administrator logs in the VTE, so the number of active users is always up to date.",
'pt_br'=>"A fim de melhorar a versуo comunidade de VTE, coletamos um conjunto mэnimo de informaчѕes sobre a sua instalaчуo como o usuсrio de vtecrm.com que ativa o sistema, a pasta de instalaчуo e o nњmero de usuсrios que o utilizam, que sуo a mesma informaчуo necessсrio para ativс-lo. Nѓs nуo coleta informaчѕes sobre os dados que vocъ insere ou o servidor de correio que vocъ usa. Щ somente uma maneira de entender como muitas pessoas estуo aproveitando do VTE no mundo. Esta informaчуo щ atualizada quando o administrador faz autenticaчуo no VTE, de modo que o nњmero de usuсrios ativos щ sempre atualizado."
));
SDK::setLanguageEntries('Settings', 'LBL_PRIVACY_FLAG', array('it_it'=>'Acconsento','en_us'=>'I agree','pt_br'=>'Estou de acordo'));

$fieldid = $adb->getUniqueID($table_prefix.'_settings_field');
$blockid = getSettingsBlockId('LBL_OTHER_SETTINGS');
$seq_res = $adb->query("SELECT max(sequence) AS max_seq FROM ".$table_prefix."_settings_field");
$seq = 1;
if ($adb->num_rows($seq_res) > 0) {
	$cur_seq = $adb->query_result($seq_res, 0, 'max_seq');
	if ($cur_seq != null)	$seq = $cur_seq + 1;
}
$adb->pquery('INSERT INTO '.$table_prefix.'_settings_field(fieldid, blockid, name, iconpath, description, linkto, sequence) VALUES (?,?,?,?,?,?,?)', array($fieldid, $blockid, 'LBL_PRIVACY', 'themes/images/PrivacySettings.png', 'LBL_PRIVACY_DES', 'index.php?module=Settings&action=Privacy&parenttab=Settings', $seq));

SDK::setLanguageEntry('HelpDesk','it_it','LBL_SLA','Tempistiche SLA');
?>