<?php
$_SESSION['modules_to_update']['Conditionals'] = 'packages/vte/mandatory/Conditionals.zip';
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';

global $adb, $table_prefix;

$moduleInstance = Vtiger_Module::getInstance('Invoice');
$filterInstance = Vtiger_Filter::getInstance('All', $moduleInstance);
$result = $adb->pquery("select * from {$table_prefix}_cvcolumnlist where cvid = ? and columnname like ?", array($filterInstance->id, '%invoicedate%'));
if ($result && $adb->num_rows($result) == 0) {
	$result = $adb->pquery("select max(columnindex)+1 as idx from {$table_prefix}_cvcolumnlist where cvid = ?", array($filterInstance->id));
	$fieldInstance = Vtiger_Field::getInstance('invoicedate', $moduleInstance);
	$filterInstance->addField($fieldInstance, $adb->query_result($result,0,'idx'));
}

$adb->pquery("UPDATE {$table_prefix}_field SET masseditable = ? WHERE uitype IN (?,?)", array(0,27,28));

SDK::setLanguageEntries('Products', 'LBL_EDITLISTPRICE', array(
	'de_de'=>'Preisliste bearbeiten',
	'nl_nl'=>'Wijzig lijstprijs',
	'pt_br'=>'Editar Lista Preço',
));
SDK::setLanguageEntries('Settings', 'LBL_EDIT_EMAIL_TEMPLATES', array(
	'de_de'=>'Bearbeiten E-Mail Vorlagen',
	'nl_nl'=>'Bewerk e-mail sjabloon',
	'pt_br'=>'Editar Modelo de Email',
));
SDK::setLanguageEntries('com_vtiger_workflow', 'LBL_NO_METHOD_AVAILABLE', array(
	'de_de'=>'Keine Methode ist für dieses Modul verfügbar',
	'nl_nl'=>'Geen methode beschikbaar voor deze module',
	'pt_br'=>'Nenhum método está disponível para este módulo',
));
SDK::setLanguageEntries('com_vtiger_workflow', 'LBL_METHOD_NAME', array(
	'de_de'=>'Verfahren',
	'nl_nl'=>'Methode',
	'pt_br'=>'Método',
));
SDK::setLanguageEntries('PDFMaker', 'LBL_CREATE_RELATED_BLOCK', array(
	'de_de'=>'Block erstellen',
	'nl_nl'=>'Maak block',
	'pt_br'=>'Criar bloco',
));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_AUTOMATIC', array(
	'de_de'=>'Automatisch',
	'nl_nl'=>'Automatisch',
	'pt_br'=>'Automático',
));
SDK::setLanguageEntries('Leads', 'LBL_LEADS_FIELD_MAPPING', array(
	'de_de'=>'Zuordnung der benutzerdefinierten Lead Felder',
	'nl_nl'=>'De verplichte velden zijn niet gekoppeld.',
	'pt_br'=>'Mapeamento Campos Customizados do Lead',
));
SDK::setLanguageEntries('Leads', 'LBL_FOLLOWING_ARE_POSSIBLE_REASONS', array(
	'de_de'=>'Dies kann einer, der möglichen Gründe sein',
	'nl_nl'=>'Mogelijke oorzaken',
	'pt_br'=>'Este pode ser um dos possíveis motivos',
));
SDK::setLanguageEntries('Leads', 'LBL_MANDATORY_FIELDS_ARE_EMPTY', array(
	'de_de'=>'Einige Pflichtfelder sind leer',
	'nl_nl'=>'Een aantal verplichte veldwaarden zijn leeg',
	'pt_br'=>'Alguns dos valores dos campos obrigatorios estão vazios',
));
SDK::setLanguageEntries('Leads', 'LBL_LEADS_FIELD_MAPPING_INCOMPLETE', array(
	'de_de'=>'Die Pflichtfelder sind nicht abgebildet',
	'nl_nl'=>'De verplichte velden zijn niet gekoppeld.',
	'pt_br'=>'Os campos obrigatórios não são mapeados',
));
?>