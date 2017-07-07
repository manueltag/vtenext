<?php
global $adb;
$sqlarray = $adb->datadict->DropTableSQL('vtiger_ws_userauthtoken');
$adb->datadict->ExecuteSQLArray($sqlarray);
$schema_tables = array(
'vtiger_ws_userauthtoken'=>
	'<schema version="0.3">
	  <table name="vtiger_ws_userauthtoken">
	  <opt platform="mysql">ENGINE=InnoDB</opt>
	    <field name="userid" type="I" size="19">
	      <KEY/>
	    </field>
	    <field name="token" type="C" size="36">
	      <KEY/>
	    </field>
	    <field name="expiretime" type="I" size="19">
	      <KEY/>
	    </field>
	  </table>
	</schema>',
'vte_userauthtoken'=>
	'<schema version="0.3">
	  <table name="vte_userauthtoken">
	  <opt platform="mysql">ENGINE=InnoDB</opt>
	    <field name="type" type="C" size="50">
	      <KEY/>
	    </field>
	    <field name="userid" type="I" size="19">
	      <KEY/>
	    </field>
	    <field name="token" type="C" size="36"/>
	    <field name="expiretime" type="I" size="19"/>
	  </table>
	</schema>'
);
foreach($schema_tables as $table_name => $schema_table) {
	if(!Vtiger_Utils::CheckTable($table_name)) {
		$schema_obj = new adoSchema($adb->database);
		$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
	}
}

SDK::setLanguageEntries('Calendar', 'LBL_ACTIVITY_OCCUPATION', array('it_it'=>'Occupazione','en_us'=>'Occupation','pt_br'=>'Ocupação'));
SDK::setLanguageEntries('Users', 'LBL_PASSWORD', array('it_it'=>'Password','en_us'=>'Password','pt_br'=>'Senha'));

$trans_it = array(
	'LBL_KEEP_ME_LOGGED_IN' => 'Resta collegato',
	'LBL_FORGOT_YOUR_PASSWORD' => 'Hai dimenticato la password?',
	'LBL_RECOVER_INTRO' => 'Inserisci il tuo nome utente.<br />Ti verrà inviata una mail con le istruzioni per impostare una nuova password.',
	'LBL_RECOVER_EMAIL_SUBJECT' => 'VTE Recupero password',
	'LBL_RECOVER_EMAIL_BODY1' => 'è stata eseguita una richiesta di recupero password per il tuo account.<br />Se hai eseguito tu questa richiesta clicca',
	'LBL_RECOVER_EMAIL_BODY2' => 'per proseguire ed immettere la nuova password.<br />Hai a disposizione 24 ore per terminare questo processo di recupero password. Passate le 24 ore dovrai ripetere la procedura dall\'inizio cliccando nuovamente il link "Hai dimenticato la password?" nella pagina di login.',
	'LBL_RECOVER_MAIL_SENT' => 'La mail è stata inviata.',
	'LBL_RECOVER_MAIL_ERROR' => 'Non è stato possibile inviare la mail.<br />Contatta l\'amministratore e richiedi il cambio password.',
	'LBL_RECOVERY_SYSTEM1' => 'Benvenuto nel sistema di recupero password.<br />Se non sei l\'utente',
	'LBL_RECOVERY_SYSTEM2' => 'ti preghiamo di cliccare',
	'LBL_RECOVERY_SYSTEM3' => 'altrimenti compila i campi sottostanti con una nuova password che sostituirà la vecchia.',
	'LBL_RECOVERY_PASSWORD_SAVED' => 'La nuova password è stata salvata.',
	'LBL_RECOVERY_SESSION_EXPIRED' => 'Sessione scaduta. Ti preghiamo di ripetere la procedura di recupero password.',
);
$trans_en = array(
	'LBL_KEEP_ME_LOGGED_IN' => 'Keep me logged in',
	'LBL_FORGOT_YOUR_PASSWORD' => 'Forgot your password?',
	'LBL_RECOVER_INTRO' => 'Please enter your user name.<br />You will receive an email with instructions on how to set a new password.',
	'LBL_RECOVER_EMAIL_SUBJECT' => 'VTE Password recovery',
	'LBL_RECOVER_EMAIL_BODY1' => 'it was performed a recovery password for your account.<br />If you have run this operation please click',
	'LBL_RECOVER_EMAIL_BODY2' => 'to continue and enter the new password.<br />You have 24 hours to finish the password recovery process. Passed the 24 hours you will have to start by clicking again on the link "Forgot your password?" at the login page.',
	'LBL_RECOVER_MAIL_SENT' => 'The mail was sent',
	'LBL_RECOVER_MAIL_ERROR' => 'We are unable to send mail.<br />Contact the administrator and request the password change.',
	'LBL_RECOVERY_SYSTEM1' => 'Welcome to the password recovery system.<br />If you\'re not user',
	'LBL_RECOVERY_SYSTEM2' => 'please click',
	'LBL_RECOVERY_SYSTEM3' => 'otherwise fill out the fields below with a new password that will replace the old one.',
	'LBL_RECOVERY_PASSWORD_SAVED' => 'New password saved.',
	'LBL_RECOVERY_SESSION_EXPIRED' => 'Expired session. Please repeat the password recovery process.',
);
$trans_pt = array(
	'LBL_KEEP_ME_LOGGED_IN' => 'Permanecer conectado',
	'LBL_FORGOT_YOUR_PASSWORD' => 'Esqueceu sua senha?',
	'LBL_RECOVER_INTRO' => 'Digite seu nome de usuário.<br />Você receberá um email com instruções sobre como definir uma nova senha.',
	'LBL_RECOVER_EMAIL_SUBJECT' => 'VTE Recuperação senha',
	'LBL_RECOVER_EMAIL_BODY1' => 'foi feita uma solicitação de recuperação de senha para sua conta.<br />Se você executou esse pedido clique',
	'LBL_RECOVER_EMAIL_BODY2' => 'para continuar e digitar sua nova senha.<br />Você tem 24 horas para terminar o processo de recuperação de senha. Terminada às 24 horas você terá que repetir o procedimento do início, clicando novamente no link "Esqueceu sua senha?" na página de login.',
	'LBL_RECOVER_MAIL_SENT' => 'O e-mail foi enviado.',
	'LBL_RECOVER_MAIL_ERROR' => 'Não foi possível enviar o e-mail.<br />Entre em contato com o administrador e solicite a alteração da senha.',
	'LBL_RECOVERY_SYSTEM1' => 'Bem-vindo ao sistema de recuperação de senha.<br />Se você não é',
	'LBL_RECOVERY_SYSTEM2' => 'por favor clique',
	'LBL_RECOVERY_SYSTEM3' => 'ou preencha os campos abaixo com uma nova senha que irá substituir a antiga.',
	'LBL_RECOVERY_PASSWORD_SAVED' => 'A nova senha foi salva.',
	'LBL_RECOVERY_SESSION_EXPIRED' => 'Sessão expirada. Por favor, repita o procedimento de recuperação de senha.',
);
foreach ($trans_it as $label=>$trans) {
	SDK::setLanguageEntry('Users', 'it_it', $label, $trans);
	SDK::setLanguageEntry('Users', 'en_us', $label, $trans_en[$label]);
	SDK::setLanguageEntry('Users', 'pt_br', $label, $trans_pt[$label]);
}

//In alcune versioni manca Impostazioni > Impostazioni Linee di prodotto
$res = $adb->pquery('SELECT * FROM vtiger_settings_field WHERE name = ?',array('LBL_REPORT_TITLE'));
if ($res && $adb->num_rows($res) > 0) {
	//do nothing
} else {
	$result = $adb->pquery('SELECT blockid FROM vtiger_settings_blocks WHERE label = ?',array('LBL_OTHER_SETTINGS'));
	if ($result && $adb->num_rows($result) > 0) {
		$blockid = $adb->query_result($result,0,'blockid');
		$result1 = $adb->limitQuery("SELECT sequence FROM vtiger_settings_field WHERE blockid = '$blockid' ORDER BY sequence DESC",0,1);
		if ($result1 && $adb->num_rows($result1) > 0) {
			$seq = $adb->query_result($result1,0,'sequence')+1;
			$adb->query("insert into vtiger_settings_field (fieldid, blockid, name, iconpath, description, linkto, sequence) values (".$adb->getUniqueID('vtiger_settings_field').", ".$blockid.", 'LBL_REPORT_TITLE', 'report_icon.gif', 'LBL_PROD_DESC', 'index.php?module=Settings&action=ProductLines&parenttab=Settings', $seq)");
		}
	}
}

$moduleInstance = Vtiger_Module::getInstance('SDK');
$adb->pquery('DELETE FROM vtiger_profile2tab WHERE tabid = ?',array($moduleInstance->id));
$adb->pquery('DELETE FROM vtiger_profile2standardperm WHERE tabid = ?',array($moduleInstance->id));
$adb->pquery('DELETE FROM vtiger_profile2utility WHERE tabid = ?',array($moduleInstance->id));
$adb->pquery('DELETE FROM vtiger_profile2field WHERE tabid = ?',array($moduleInstance->id));
?>