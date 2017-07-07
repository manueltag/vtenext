<?php
global $adb;

$columns = array_keys($adb->datadict->MetaColumns('vtiger_mailscanner'));
if (!in_array('SUCC_MOVETO',$columns)) {
	$sqlarray = $adb->datadict->AddColumnSQL('vtiger_mailscanner','succ_moveto C(100)');
	$adb->datadict->ExecuteSQLArray($sqlarray);
}
if (!in_array('NO_SUCC_MOVETO',$columns)) {
	$sqlarray = $adb->datadict->AddColumnSQL('vtiger_mailscanner','no_succ_moveto C(100)');
	$adb->datadict->ExecuteSQLArray($sqlarray);
}

SDK::setLanguageEntries('Settings', 'LBL_MOVE_MESSAGE', array('it_it'=>'Se la regola è stata applicata con successo sposta messaggio in','en_us'=>'If the rule was applied successfully move message to','pt_br'=>'Se a regra foi aplicada com sucesso mover mensagem para'));
SDK::setLanguageEntries('Settings', 'LBL_MOVE_MESSAGE_ELSE', array('it_it'=>'altrimenti sposta messaggio in','en_us'=>'else move message to','pt_br'=>'caso contrário mover mensagem para'));

$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Targets','Newsletter'));
$_SESSION['modules_to_update']['Projects'] = 'packages/vte/mandatory/Projects.zip';
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';
$_SESSION['modules_to_update']['Fax'] = 'packages/vte/mandatory/Fax.zip';
$_SESSION['modules_to_update']['Sms'] = 'packages/vte/mandatory/Sms.zip';
$_SESSION['modules_to_update']['Assets'] = 'packages/vte/mandatory/Assets.zip';
$_SESSION['modules_to_update']['Ddt'] = 'packages/vte/mandatory/Ddt.zip';
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['ProjectsStandard'] = Array('location'=>'packages/vte/mandatory/ProjectsStandard.zip','modules'=>Array('ProjectPlan','ProjectMilestone','ProjectTask'));
$_SESSION['modules_to_update']['ServiceContracts'] = 'packages/vte/mandatory/ServiceContracts.zip';
$_SESSION['modules_to_update']['Services'] = 'packages/vte/mandatory/Services.zip';
$_SESSION['modules_to_update']['Timecards'] = 'packages/vte/mandatory/Timecards.zip';
$_SESSION['modules_to_update']['Visitreport'] = 'packages/vte/mandatory/Visitreport.zip';

global $adb;
$schema_tables = array(
'vte_listview_check'=>
	'<schema version="0.3">
	  <table name="vte_listview_check">
	  <opt platform="mysql">ENGINE=InnoDB</opt>
	    <field name="userid" type="I" size="19">
	      <KEY/>
	    </field>
	    <field name="tabid" type="I" size="19">
	      <KEY/>
	    </field>
	    <field name="crmid" type="i" size="19">
	      <KEY/>
	    </field>
	  </table>
	</schema>'
);
foreach($schema_tables as $table_name => $schema_table) {
	if(!Vtiger_Utils::CheckTable($table_name)) {
		$schema_obj = new adoSchema($adb->database);
		$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
	}
}
SDK::setLanguageEntries('ALERT_ARR', 'LBL_MASS_EDIT_WITHOUT_WF_1', array('it_it'=>'Hai selezionato più di ','en_us'=>'You have selected more than ','pt_br'=>'Você selecionou mais de itens '));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_MASS_EDIT_WITHOUT_WF_2', array('it_it'=>' elementi, questo potrebbe sovraccaricare il server. Procedere all\'aggiornamento escludendo i Workflow?','en_us'=>' items, this may overload the server. Proceed to update excluding the Workflow?','pt_br'=>', isso pode sobrecarregar o servidor. Vá para a update excluindo o fluxo de Workflow?'));

$result = $adb->query("SELECT * FROM vtiger_language WHERE prefix = 'pt_br'");
if ($result && $adb->num_rows($result) > 0) {
	$languageInstance = new Vtiger_Language();
	$languageInstance->import('packages/vte/optional/PTBrasil.zip', true);
}

SDK::setLanguageEntry('Contacts', 'it_it', 'Other Phone', 'Altro Telefono');
SDK::setLanguageEntry('Products', 'it_it', 'Part Number', 'Codice Prodotto');
?>