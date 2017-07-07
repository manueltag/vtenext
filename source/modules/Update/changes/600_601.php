<?php
global $adb, $table_prefix;

$_SESSION['modules_to_update']['Conditionals'] = 'packages/vte/mandatory/Conditionals.zip';
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';
$_SESSION['modules_to_update']['Morphsuit'] = 'packages/vte/mandatory/Morphsuit.zip';
$_SESSION['modules_to_update']['PBXManager'] = 'packages/vte/mandatory/PBXManager.zip';
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';
$_SESSION['modules_to_update']['ProjectsStandard'] = Array('location'=>'packages/vte/mandatory/ProjectsStandard.zip','modules'=>Array('ProjectPlan'));
$_SESSION['modules_to_update']['Services'] = 'packages/vte/mandatory/Services.zip';
$_SESSION['modules_to_update']['SLA'] = 'packages/vte/mandatory/SLA.zip';
$_SESSION['modules_to_update']['Webforms'] = 'packages/vte/mandatory/Webforms.zip';
$res = $adb->query("SELECT tabid FROM {$table_prefix}_tab WHERE name = 'CustomerPortal'");
if ($res && $adb->num_rows($res)>0) $_SESSION['modules_to_update']['CustomerPortal'] = 'packages/vte/optional/CustomerPortal.zip';
$res = $adb->query("SELECT tabid FROM {$table_prefix}_tab WHERE name = 'Projects'");
if ($res && $adb->num_rows($res)>0) $_SESSION['modules_to_update']['Projects'] = 'packages/vte/optional/Projects.zip';

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
$_SESSION['vtiger_hash_version'] = $hash_version;

SDK::setLanguageEntries('Morphsuit', 'LBL_FUNCTION_BLOCKED', array('it_it'=>'Questa funzione e` disponibile soltanto su VTE BUSINESS ONSITE. Se desideri proseguire ed adeguare la tua posizione puoi contattare il servizio commerciale di CRMVILLAGE all\'indirizzo email %s specificando per quanti utenti desideri attivare il tuo VTE.','en_us'=>'This function is only available on VTE ONSITE BUSINESS. If you want to continue and adjust your position you can contact CRMVILLAGE to the email address %s specifying how many users you want to enable.','pt_br'=>'Esta função è disponivel sò em VTE BUSINESS ONSITE. Se você quiser continuar e ajustar a sua posição entre em contato com o escritório de vendas CRMVILLAGE ao endereço de email s% especificando quantos usuários você deseja ativar em VTE.'));
SDK::setLanguageEntries('Morphsuit', 'LBL_MORPHSUIT_ROLE_NUMBER_EXCEEDED', array('it_it'=>'Hai superato il numero di ruoli previsto per la versione FREE.','en_us'=>'You have exceeded the number of roles expected the FREE version.','pt_br'=>'Você ultrapassou o número de funções previstos para a versão GRÁTIS.'));
SDK::setLanguageEntries('Morphsuit', 'LBL_MORPHSUIT_PROFILE_NUMBER_EXCEEDED', array('it_it'=>'Hai superato il numero di profili previsto per la versione FREE.','en_us'=>'You have exceeded the number of profiles expected the FREE version.','pt_br'=>'Você ultrapassou o número de perfis previstos para a versão GRÁTIS.'));
SDK::setLanguageEntries('Morphsuit', 'LBL_MORPHSUIT_PDF_NUMBER_EXCEEDED', array('it_it'=>'Hai superato il pdf previsto per la versione FREE.','en_us'=>'You have exceeded the number of pdf expected the FREE version.','pt_br'=>'Você ultrapassou o número de pdf previstos para a versão GRÁTIS.'));
SDK::setLanguageEntries('Morphsuit', 'LBL_MORPHSUIT_ADV_SHARING_RULE_NUMBER_EXCEEDED', array('it_it'=>'Hai superato il numero di regole di condivisione avanzata previsto per la versione FREE.','en_us'=>'You have exceeded the number of advanced sharing rules expected the FREE version.','pt_br'=>'Você ultrapassou o número de regras de compartilhamento avançado previstas para a versão GRÁTIS.'));
SDK::setLanguageEntries('Morphsuit', 'LBL_MORPHSUIT_SHARING_RULE_USER_NUMBER_EXCEEDED', array('it_it'=>'Hai superato il numero di regole di condivisione basate sul proprietario previsto per la versione FREE.','en_us'=>'You have exceeded the number of sharing rules owner based expected the FREE version.','pt_br'=>'Você ultrapassou o número de regras de compartilhamento baseadas no proprietário previstas para a versão GRÁTIS.'));
SDK::setLanguageEntries('Morphsuit', 'LBL_MORPHSUIT_UPDATE', array('it_it'=>'Aggiornamento licenza','en_us'=>'License upgrade','pt_br'=>'Atualização licença'));
SDK::setLanguageEntries('Morphsuit', 'LBL_ERROR_VTE_FREE_NOT_ACTIVABLE', array('it_it'=>'Questa versione non è più attivabile. Scarica la 4.3 o successiva.','en_us'=>'This version is no longer activatable. Download 4.3 or later.','pt_br'=>'Esta versão não pode mais ser ativada. Baixe a versão 4.3 ou superior.'));
SDK::setLanguageEntries('Morphsuit', 'LBL_ZOMBIE_MODE', array('it_it'=>'Continua a usare VTE in sola lettura','en_us'=>'Continue to use VTE in read-only mode','pt_br'=>'Continua a usar VTE em somente leitura'));

SDK::deleteLanguageEntry('Users', '', 'LBL_READ_LICENSE');
SDK::setLanguageEntries('APP_STRINGS', 'LNK_READ_LICENSE', array('it_it'=>'Licenza','en_us'=>'License','pt_br'=>'Licença'));

SDK::setLanguageEntry('Emails', 'it_it', 'LBL_PRINT', 'Stampa');
SDK::setLanguageEntry('Emails', 'en_us', 'LBL_PRINT', 'Print');
SDK::setLanguageEntry('Emails', 'pt_br', 'LBL_PRINT', 'Imprimir');
SDK::setLanguageEntry('Emails', 'it_it', 'LBL_SIZE', 'Dimensione');
SDK::setLanguageEntry('Emails', 'en_us', 'LBL_SIZE', 'Size');
SDK::setLanguageEntry('Emails', 'pt_br', 'LBL_SIZE', 'Tamanho');
SDK::setLanguageEntry('Emails', 'it_it', 'LBL_TYPE', 'Tipo');
SDK::setLanguageEntry('Emails', 'en_us', 'LBL_TYPE', 'Type');
SDK::setLanguageEntry('Emails', 'pt_br', 'LBL_TYPE', 'Tipo');

SDK::setLanguageEntry('ProjectPlan', 'it_it', 'LBL_PROGRESS_CHART', 'Grafico Pianificazione');

SDK::deleteLanguage('Import');
SDK::file2DbLanguages('Import');
SDK::setLanguageEntries('ModNotifications', 'Import Completed', array('it_it'=>'Importazione completata per il modulo','en_us'=>'Import completed for','pt_br'=>'Importação completada para o modulo'));

//limits - i
if (!function_exists('isFreeVersion')){
	@include_once('modules/Morphsuit/utils/RSA/Crypt/RSA.php');
	if (!function_exists('getSavedMorphsuit')){
		function getSavedMorphsuit(){
			global $adb;
			$res = $adb->query('select morphsuit from tbl_s_morphsuit');
			if ($res && $adb->num_rows($res) > 0)
			$value = $adb->query_result_no_html($res,0,'morphsuit');
			return $value;
		}
	}
	if (!function_exists('decrypt_morphsuit')){
		function decrypt_morphsuit($private_key,$enc_text){
			$rsa = new Crypt_RSA();
			$rsa->loadKey($private_key);
			$plain_text = $rsa->decrypt($enc_text);
			return $plain_text;
		}
	}
	if (!function_exists('generate_key_pair_morphsuit')){
		function generate_key_pair_morphsuit(){
			$key_length = 512;
			$rsa = new Crypt_RSA();
			extract($rsa->createKey(512));
			return array('public_key'=>$publickey,'private_key'=>$privatekey);
		}
	}
	if (!function_exists('encrypt_morphsuit')){
		function encrypt_morphsuit($public_key,$plain_text){
			$rsa = new Crypt_RSA();
			$rsa->loadKey($public_key);
			$enc_text = $rsa->encrypt($plain_text);
			return $enc_text;
		}
	}
	function isFreeVersion($saved_morphsuit='') {
		if (isset($_SESSION['isFreeVersion'])) {
			return $_SESSION['isFreeVersion'];
		}
		if (!vtlib_isModuleActive("Morphsuit")) {
			return false;
		}
		if ($saved_morphsuit == '') {
			$saved_morphsuit = getSavedMorphsuit();
			$saved_morphsuit = urldecode(trim($saved_morphsuit));
			$private_key = substr($saved_morphsuit,0,strpos($saved_morphsuit,'-----'));
			$enc_text = substr($saved_morphsuit,strpos($saved_morphsuit,'-----')+5);
			$saved_morphsuit = @decrypt_morphsuit($private_key,$enc_text);
			$saved_morphsuit = Zend_Json::decode($saved_morphsuit);
		}
		if ($saved_morphsuit['tipo_installazione'] == 'Free') {
			$_SESSION['isFreeVersion'] = true;
		} else {
			$_SESSION['isFreeVersion'] = false;
		}
		return $_SESSION['isFreeVersion'];
	}
	
}
if (isFreeVersion()) {
	$limits = array(
		'numero_utenti'=>0,
		'roles'=>3,	//Organisation + 2
		'profiles'=>2,
		'pdf'=>1,
		'adv_sharing_rules'=>1,
		'sharing_rules_user'=>1,
	);
	
	$result = $adb->query("select * from {$table_prefix}_role");
	if ($result && $adb->num_rows($result) > 0) {
		if ($adb->num_rows($result) > $limits['roles']) {
			$limits['roles'] = $adb->num_rows($result);
		}
	}
	
	$result = $adb->query("select * from {$table_prefix}_profile");
	if ($result && $adb->num_rows($result) > 0) {
		if ($adb->num_rows($result) > $limits['profiles']) {
			$limits['profiles'] = $adb->num_rows($result);
		}
	}
	
	$result = $adb->query("SELECT COUNT(*) as count FROM {$table_prefix}_pdfmaker GROUP BY module");
	if ($result && $adb->num_rows($result) > 0) {
		$count = array();
		while($row=$adb->fetchByAssoc($result)) {
			$count[] = $row['count'];
		}
		if (!empty($count) && max($count) > $limits['pdf']) {
			$limits['pdf'] = max($count);
		}
	}
	
	$othermodules = getSharingModuleList();
	if(!empty($othermodules)) {
		$count = array();
		foreach($othermodules as $moduleresname) {
			$tmp = getAdvSharingRuleList($moduleresname);
			$count[] = count($tmp);
		}
		if (!empty($count) && max($count) > $limits['adv_sharing_rules']) {
			$limits['adv_sharing_rules'] = max($count);
		}
	}
				
	$othermodules = getSharingModuleList(Array('Contacts'));
	if(!empty($othermodules)) {
		$result = $adb->query("SELECT id FROM {$table_prefix}_users WHERE status = 'Active' AND user_name <> 'admin'");
		if ($result) {
			$count = array();
			while($row=$adb->fetchByAssoc($result)) {
				foreach($othermodules as $moduleresname) {
					$tmp = getSharingRuleListUser($moduleresname,$row['id']);
					$count[] = count($tmp);
				}
			}
			if (!empty($count) && max($count) > $limits['sharing_rules_user']) {
				$limits['sharing_rules_user'] = max($count);
			}
		}
	}
} else {
	$limits = array(
		'roles'=>'',
		'profiles'=>'',
		'pdf'=>'',
		'adv_sharing_rules'=>'',
		'sharing_rules_user'=>'',
	);
}

$saved_morphsuit = getSavedMorphsuit();
$saved_morphsuit = urldecode(trim($saved_morphsuit));
$private_key = substr($saved_morphsuit,0,strpos($saved_morphsuit,'-----'));
$enc_text = substr($saved_morphsuit,strpos($saved_morphsuit,'-----')+5);
$saved_morphsuit = @decrypt_morphsuit($private_key,$enc_text);
$saved_morphsuit = Zend_Json::decode($saved_morphsuit);
foreach($limits as $key => $limit) {
	$saved_morphsuit[$key] = $limit;
}
$new_key = generate_key_pair_morphsuit();
$new_enc_text = encrypt_morphsuit($new_key['public_key'],Zend_Json::encode($saved_morphsuit));
$new_chiave = urlencode($new_key['private_key']."-----$new_enc_text");
$adb->query('delete from tbl_s_morphsuit');
$adb->pquery('insert into tbl_s_morphsuit (morphsuit) values (?)',array($new_chiave));
//limits - e

$result = $adb->pquery("select * from {$table_prefix}_field where tabid = 13 and fieldname in (?,?)",array('projecttaskid','projectplanid'));
if ($result && $adb->num_rows($result) == 2) {
	SDK::setPopupQuery('field', 'HelpDesk', 'projecttaskid', 'modules/SDK/src/modules/HelpDesk/ProjectTaskQuery.php', array('projectplanid'=>'getObj("projectplanid").value'));
}
?>