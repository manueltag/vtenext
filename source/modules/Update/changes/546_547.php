<?php
global $adb;
$res = $adb->query("SELECT tabid FROM vtiger_tab WHERE name = 'Projects'");
if ($res && $adb->num_rows($res)>0) {
	$_SESSION['modules_to_update']['Projects'] = 'packages/vte/optional/Projects.zip';
}
$_SESSION['modules_to_update']['ProjectsStandard'] = Array('location'=>'packages/vte/mandatory/ProjectsStandard.zip','modules'=>Array('ProjectPlan','ProjectTask'));

$result = $adb->query('SELECT * FROM vtiger_field WHERE tabid = 29 AND fieldname = \'use_ldap\'');
if (!$result || $adb->num_rows($result) == 0) {
	$fields = array();
	$fields[] = array('module'=>'Users','block'=>'LBL_USERLOGIN_ROLE','name'=>'use_ldap','label'=>'Ldapuser','uitype'=>'56','columntype'=>'I(1)','typeofdata'=>'C~O');
	include('modules/SDK/examples/fieldCreate.php');
}

$fields = array();
$fields[] = array('module'=>'Users','block'=>'LBL_USERLOGIN_ROLE','name'=>'allow_generic_talks','label'=>'Allow generic talks','uitype'=>'56','columntype'=>'I(1)','typeofdata'=>'C~O');
include('modules/SDK/examples/fieldCreate.php');
SDK::setLanguageEntries('Users', 'Allow generic talks', array('it_it'=>'Permetti conversazioni generiche','en_us'=>'Allow generic talks','pt_br'=>'Permitir conversas genéricas'));
$adb->query('update vtiger_users set allow_generic_talks = 1');

$fields = array();
$fields[] = array('module'=>'Users','block'=>'LBL_USERLOGIN_ROLE','name'=>'receive_public_talks','label'=>'Receive public talks','uitype'=>'56','columntype'=>'I(1)','typeofdata'=>'C~O');
include('modules/SDK/examples/fieldCreate.php');
SDK::setLanguageEntries('Users', 'Receive public talks', array('it_it'=>'Ricevi conversazioni pubbliche','en_us'=>'Receive public talks','pt_br'=>'Receber conversas públicas'));
$adb->query('update vtiger_users set receive_public_talks = 1');

SDK::addView('Users', 'modules/SDK/src/modules/Users/UsersView.php', 'constrain', 'continue');

SDK::setLanguageEntries('Webmails', 'Convert', array('it_it'=>'Converti','en_us'=>'Convert','pt_br'=>'Converter'));

require_once('vtlib/Vtiger/Module.php');
$EmailsInstance = Vtiger_Module::getInstance('Emails');

$ProjectTaskInstance = Vtiger_Module::getInstance('ProjectTask');
$result = $adb->pquery('SELECT * FROM vtiger_relatedlists WHERE tabid = ? AND related_tabid = ?',array($ProjectTaskInstance->id,$EmailsInstance->id));
if (!$result || $adb->num_rows($result) == 0) {
	$ProjectTaskInstance->setRelatedList($EmailsInstance, 'Emails', Array(''), 'get_emails');
}
$ProjectPlanInstance = Vtiger_Module::getInstance('ProjectPlan');
$result = $adb->pquery('SELECT * FROM vtiger_relatedlists WHERE tabid = ? AND related_tabid = ?',array($ProjectPlanInstance->id,$EmailsInstance->id));
if (!$result || $adb->num_rows($result) == 0) {
	$ProjectPlanInstance->setRelatedList($EmailsInstance, 'Emails', Array(''), 'get_emails');
}

$HelpDeskInstance = Vtiger_Module::getInstance('HelpDesk');
$result = $adb->pquery('select * from vtiger_field where tabid = ? and fieldname = ? and quickcreate <> 0',array($HelpDeskInstance->id,'parent_id'));
if ($result && $adb->num_rows($result) > 0) {
	$field = new Vtiger_Field();
	$field->initialize($adb->fetch_array($result), $HelpDeskInstance);
	$qcsequence = $field->__getNextQuickCreateSequence();
	$adb->pquery('update vtiger_field set quickcreate = ?, quickcreatesequence = ? where tabid = ? and fieldname = ?',array(0,$qcsequence,$HelpDeskInstance->id,'parent_id'));
}
$result = $adb->pquery('select * from vtiger_field where tabid = ? and fieldname = ? and quickcreate <> 0',array($ProjectPlanInstance->id,'linktoaccountscontacts'));
if ($result && $adb->num_rows($result) > 0) {
	$field = new Vtiger_Field();
	$field->initialize($adb->fetch_array($result), $ProjectPlanInstance);
	$qcsequence = $field->__getNextQuickCreateSequence();
	$adb->pquery('update vtiger_field set quickcreate = ?, quickcreatesequence = ? where tabid = ? and fieldname = ?',array(0,$qcsequence,$ProjectPlanInstance->id,'linktoaccountscontacts'));
}

$result = $adb->pquery('SELECT * FROM vtiger_blocks WHERE tabid = ? AND blocklabel = ?',array($HelpDeskInstance->id,'LBL_TICKET_INFORMATION'));
if (!$result || $adb->num_rows($result) == 0) {
	$block = new Vtiger_Block();
	$block->label = 'LBL_TICKET_INFORMATION';
	$block->save($HelpDeskInstance);
}
$result = $adb->pquery('SELECT * FROM vtiger_fieldmodulerel WHERE module = ? AND relmodule = ?',array('HelpDesk','ProjectPlan'));
if (!$result || $adb->num_rows($result) == 0) {
	$fields = array();
	$fields[] = array('module'=>'HelpDesk','block'=>'LBL_TICKET_INFORMATION','name'=>'projectplanid','label'=>'ProjectPlan','uitype'=>'10','columntype'=>'I(19)','typeofdata'=>'V~O','quickcreate'=>'2','masseditable'=>'0','relatedModules'=>array('ProjectPlan'),'relatedModulesAction'=>array('ProjectPlan'=>array('ADD','SELECT')));
	include('modules/SDK/examples/fieldCreate.php');
}
$result = $adb->pquery('SELECT * FROM vtiger_fieldmodulerel WHERE module = ? AND relmodule = ?',array('HelpDesk','ProjectTask'));
if (!$result || $adb->num_rows($result) == 0) {
	$fields = array();
	$fields[] = array('module'=>'HelpDesk','block'=>'LBL_TICKET_INFORMATION','name'=>'projecttaskid','label'=>'ProjectTask','uitype'=>'10','columntype'=>'I(19)','typeofdata'=>'V~O','quickcreate'=>'2','masseditable'=>'0','relatedModules'=>array('ProjectTask'),'relatedModulesAction'=>array('ProjectTask'=>array('ADD','SELECT')));
	include('modules/SDK/examples/fieldCreate.php');
}
SDK::setLanguageEntries('HelpDesk', 'ProjectPlan', array('it_it'=>'Pianificazione','en_us'=>'Project','pt_br'=>'Planejamento'));
SDK::setLanguageEntries('HelpDesk', 'ProjectTask', array('it_it'=>'Operazione','en_us'=>'Project Task','pt_br'=>'Tarefa Projeto'));

$HelpDeskInstance->unsetRelatedList($ProjectPlanInstance, 'Project Plans', 'get_related_list');
$ProjectPlanInstance->unsetRelatedList($HelpDeskInstance, 'Trouble Tickets', 'get_related_list');

$res = $adb->query("SELECT tabid FROM vtiger_tab WHERE name = 'Webforms'");
if ($res && $adb->num_rows($res)>0) {
	$_SESSION['modules_to_update']['Webforms'] = 'packages/vte/mandatory/Webforms.zip';

	$schema_table = '<schema version="0.3">
				  <table name="vtiger_webforms">
				  <opt platform="mysql">ENGINE=InnoDB</opt>
				    <field name="id" type="R" size="19">
				      <KEY/>
				    </field>
				    <field name="name" type="C" size="100"/>
				    <field name="publicid" type="C" size="100"/>
				    <field name="enabled" type="I" size="1">
				      <DEFAULT value="1"/>
				    </field>
				    <field name="targetmodule" type="C" size="50"/>
				    <field name="description" type="C" size="250"/>
				    <field name="ownerid" type="I" size="19"/>
				    <field name="returnurl" type="C" size="250"/>
				    <index name="webformname">
				      <UNIQUE/>
				      <col>name</col>
				    </index>
				    <index name="publicid">
				      <UNIQUE/>
					      <col>id</col>
					    </index>
					    <index name="webforms_webforms_id_idx">
					      <col>id</col>
					    </index>
					  </table>
					</schema>';
	if(!Vtiger_Utils::CheckTable('vtiger_webforms')) {
		$schema_obj = new adoSchema($adb->database);
		$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
	}

	$schema_table = '<schema version="0.3">
				  <table name="vtiger_webforms_field">
				  <opt platform="mysql">ENGINE=InnoDB</opt>
				    <field name="id" type="R" size="19">
				      <KEY/>
				    </field>
				    <field name="webformid" type="I" size="19"/>
				    <field name="fieldname" type="C" size="50"/>
				    <field name="neutralizedfield" type="C" size="50"/>
				    <field name="defaultvalue" type="C" size="200"/>
				    <field name="required" type="I" size="10">
				      <DEFAULT value="0"/>
				    </field>
				    <index name="webforms_webforms_field_idx">
				      <col>id</col>
				    </index>
				    <index name="fk_1_vtiger_webforms_field">
				      <col>webformid</col>
				    </index>
				    <index name="fk_2_vtiger_webforms_field">
				      <col>fieldname</col>
				    </index>
				  </table>
				</schema>';
	if(!Vtiger_Utils::CheckTable('vtiger_webforms_field')) {
		$schema_obj = new adoSchema($adb->database);
		$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
	}
	
	SDK::setLanguageEntries('APP_STRINGS', 'LBL_WEBFORMS_DESCRIPTION', array('it_it'=>'Gestione dei Webforms','en_us'=>'Allows you to manage Webforms','pt_br'=>'Gerencia dos Webforms'));
	SDK::setLanguageEntries('ALERT_ARR', 'LBL_MANDATORY_FIELDS_WF', array('it_it'=>'Inserire il valore per i campi obbligatori','en_us'=>'Please enter value for mandatory fields','pt_br'=>'Digite um valor para campos obrigatórios'));
	SDK::setLanguageEntries('ALERT_ARR', 'LBL_DELETE_MSG', array('it_it'=>'Sei sicuro di volere eliminare il webform?','en_us'=>'Are you sure, you want to delete the webform?','pt_br'=>'Você tem certeza que deseja excluir o webform?'));
	SDK::setLanguageEntries('ALERT_ARR', 'LBL_DUPLICATE_NAME', array('it_it'=>'Il Webform esiste gia\'','en_us'=>'Webform already exists','pt_br'=>'Webform ja\' existe'));
	
	$it_it = array(
		'Webform'=>'Webform',
		'Webforms'=>'Webforms',
		'LBL_SUCCESS'=>'form aggiunto a VTE CRM.',
		'LBL_FAILURE'=>'Errore durante l\'inserimento del form in VTE CRM.',
		'LBL_ERROR_CODE'=>'Codice Errore',
		'LBL_ERROR_MESSAGE'=>'Messaggio Errore',
		'LBL_WEBFORM_NAME'=>'Nome Webform',
		'LBL_DESCRIPTION'=>'Descrizione',
		'LBL_MODULE'=>'Modulo',
		'LBL_RETURNURL'=>'URL di ritorno',
		'LBL_ACTION'=>'Azione',
		'LBL_ASSIGNED_TO'=>'Assegnato A',
		'LBL_EDIT'=>'Modifica',
		'LBL_DELETE'=>'Elimina',
		'LBL_SOURCE'=>'Visualizza Form',
		'LBL_MODULE_INFORMATION'=>'Informazioni Webform',
		'LBL_FIELD_INFORMATION'=>'Informazioni Campi',
		'LBL_ENABLE'=>'Abilita',
		'LBL_ENABLED'=>'Abilitato',
		'LBL_FIELDLABEL'=>'Nome campo',
		'LBL_DEFAULT_VALUE'=>'Valore di default',
		'LBL_NEUTRALIZEDFIELD'=>'Webforms Reference Field',
		'LBL_PUBLICID'=>'Id Pubblico',
		'LBL_NO_WEBFORM'=>'Nessun webform trovato!',
		'LBL_CREATE_WEBFORM'=>'Crea un Webform',
		'LBL_POSTURL'=>'Post URL',
		'LBL_REQUIRED'=>'Obbligatorio',
		'LBL_STATUS'=>'Stato',
		'LBL_EMBED_MSG'=>'Includi il seguente form nel tuo sito internet',
		'LBL_CANCEL'=>'Annulla',
		'LBL_SAVE'=>'Salva',
		'LBL_SELECT_VALUE'=>'--Selezionare--',
		'LBL_DUPLICATE_NAME'=>'Esiste gia` un Webform con lo stesso nome',
		'ERR_CREATE_WEBFORM'=>'Creazione Webform fallita',
		'LBL_SELECT_USER'=>'Seleziona Utente',
	);
	foreach($it_it as $key => $value){
		SDK::setLanguageEntry('Webforms', 'it_it', $key, $value);
	}
	
	$en_us = array(
		'Webforms'=>'Webforms',
		'LBL_SUCCESS'=>'entry is added to vtiger CRM.',
		'LBL_FAILURE'=>'Failed to add entry in to vtiger CRM.',
		'LBL_ERROR_CODE'=>'Error Code',
		'LBL_ERROR_MESSAGE'=>'Error Message',
		'LBL_WEBFORM_NAME'=>'Webform Name',
		'LBL_DESCRIPTION'=>'Description',
		'LBL_MODULE'=>'Module',
		'LBL_RETURNURL'=>'Return URL',
		'LBL_ACTION'=>'Action',
		'LBL_ASSIGNED_TO'=>'Assigned To',
		'LBL_EDIT'=>'Edit',
		'LBL_DELETE'=>'Delete',
		'LBL_SOURCE'=>'Show Form',
		'LBL_MODULE_INFORMATION'=>'Webforms Information',
		'LBL_FIELD_INFORMATION'=>'Field Information',
		'LBL_ENABLE'=>'Enable',
		'LBL_ENABLED'=>'Enabled',
		'LBL_FIELDLABEL'=>'Field Name',
		'LBL_DEFAULT_VALUE'=>'Override Value',
		'LBL_NEUTRALIZEDFIELD'=>'Webforms Reference Field',
		'LBL_PUBLICID'=>'Public Id',
		'LBL_NO_WEBFORM'=>'No Webforms Found!',
		'LBL_CREATE_WEBFORM'=>'Create a Webform',
		'LBL_POSTURL'=>'Post URL',
		'LBL_REQUIRED'=>'Required',
		'LBL_STATUS'=>'Status',
		'LBL_EMBED_MSG'=>'Embed the following form in your website',
		'LBL_CANCEL'=>'Cancel',
		'LBL_SAVE'=>'Save',
		'LBL_SELECT_VALUE'=>'--Select Value--',
		'LBL_DUPLICATE_NAME'=>'Webform with same name exists',
		'ERR_CREATE_WEBFORM'=>'Webform creation failed',
		'LBL_SELECT_USER'=>'Select User',
	);
	foreach($en_us as $key => $value){
		SDK::setLanguageEntry('Webforms', 'en_us', $key, $value);
	}
	
	$pt_br = array(
		'Webforms' => 'Webforms',
		'LBL_SUCCESS' => 'Os dados foram adicionados no VTE CRM.',
		'LBL_FAILURE' => 'Falha ao adicionar dados no VTE CRM.',
		'LBL_ERROR_CODE' => 'Código de Erro',
		'LBL_ERROR_MESSAGE' => 'Mensagem de Erro',
		'LBL_WEBFORM_NAME'=>'Nome Webform',
		'LBL_DESCRIPTION'=>'Descrição',
		'LBL_MODULE'=>'Módulo',
		'LBL_RETURNURL'=>'URL de Retorno',
		'LBL_ACTION'=>'Ação',
		'LBL_ASSIGNED_TO'=>'Responsável',
		'LBL_EDIT'=>'editar',
		'LBL_DELETE'=>'apagar',
		'LBL_SOURCE'=>'Mostrar Form',
		'LBL_MODULE_INFORMATION'=>'Informação Webforms',
		'LBL_FIELD_INFORMATION'=>'Informação Campo',
		'LBL_ENABLE'=>'Permitir',
		'LBL_ENABLED'=>'Permitido',
		'LBL_FIELDLABEL'=>'Nome Campo',
		'LBL_DEFAULT_VALUE'=>'Substitui Valor',
		'LBL_NEUTRALIZEDFIELD'=>'Campo de Referência do Webform',
		'LBL_PUBLICID'=>'ID Público',
		'LBL_NO_WEBFORM'=>'Nenhum Webform Encontrado!',
		'LBL_CREATE_WEBFORM'=>'Criar um Webform',
		'LBL_POSTURL'=>'URL para Enviar',
		'LBL_REQUIRED'=>'Obrigatório',
		'LBL_STATUS'=>'Status',
		'LBL_EMBED_MSG'=>'Inserir o seguinte formulário no seu website',
		'LBL_CANCEL'=>'Cancelar',
		'LBL_EDIT'=>'Editar',
		'LBL_DELETE'=>'Apagar',
		'LBL_SAVE'=>'Salvar',
		'LBL_SELECT_VALUE'=>'--Selecionar Valor--',
		'LBL_DUPLICATE_NAME' => 'Já existe um Webform com nome idêntico',
		'ERR_CREATE_WEBFORM' => 'Falha ao criar o Webform',
		'LBL_SELECT_USER'=>'Selecionar Usuário',
	);
	foreach($pt_br as $key => $value){
		SDK::setLanguageEntry('Webforms', 'pt_br', $key, $value);
	}
} else {
	$_SESSION['modules_to_install']['Webforms'] = 'packages/vte/mandatory/Webforms.zip';
}

$sqlarray = $adb->datadict->AddColumnSQL('vtiger_convertleadmapping','editable I(19) DEFAULT 1');
$adb->datadict->ExecuteSQLArray($sqlarray);
?>