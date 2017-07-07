<?php
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';

SDK::setLanguageEntries('Users', 'LBL_USER_BLOCKED', array('it_it'=>"L'utente è stato bloccato in quanto non operativo da più di %s mesi. Contatta l'amministratore per riattivarlo.",'en_us'=>'The user has been blocked because it is not operational for more than %s months. Contact the administrator to reactivate it.','pt_br'=>'O usuário foi bloqueado porque não é operacional por mais de %s meses. Contate o administrador para reativá-lo.'));
SDK::setLanguageEntries('Users', 'LBL_PASSWORD_TO_BE_CHANGED', array('it_it'=>'La password va cambiata ogni %s mesi.','en_us'=>'The password must be changed every %s months.','pt_br'=>'A senha deve ser trocada a cada %s meses.'));
SDK::setLanguageEntries('Users', 'LBL_NOT_SAFETY_PASSWORD', array('it_it'=>'La password non soddisfa i criteri di sicurezza: almeno %s caratteri, nessun riferimento a Nome Utente, Nome o Cognome.','en_us'=>'The password doesn\'t satisfy the safety criteria: at least %s characters, no reference to User Name, Name or Last name.','pt_br'=>'A senha não satisfaz os critérios de segurança: pelo menos %s caracteres, sem referência ao nome do usuário, nome ou sobrenome.'));
SDK::setLanguageEntries('Users', 'LBL_CLICK_TO_RECOVER', array('it_it'=>'Clicca "Hai dimenticato la password?" per cambiarla ora.','en_us'=>'Please click to "Forgot your password?" to change it now.','pt_br'=>'Clique no link "Esqueceu sua senha?" para mudá-la agora.'));

$schema_table = '<schema version="0.3">
				  <table name="vte_check_pwd">
				  	<opt platform="mysql">ENGINE=InnoDB</opt>
					<field name="userid" type="I" size="19">
					  <KEY/>
					</field>
					<field name="last_login" type="T">
					  <DEFAULT value="0000-00-00 00:00:00"/>
				    </field>
					<field name="last_change_pwd" type="T">
					  <DEFAULT value="0000-00-00 00:00:00"/>
				    </field>
				  </table>
				</schema>';
if(!Vtiger_Utils::CheckTable('vte_check_pwd')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}
?>