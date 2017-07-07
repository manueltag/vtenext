<?php
$_SESSION['modules_to_update']['ChangeLog'] = 'packages/vte/mandatory/ChangeLog.zip';
$_SESSION['modules_to_update']['Ddt'] = 'packages/vte/mandatory/Ddt.zip';
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';

global $adb;
$result = $adb->pquery('UPDATE sdk_language SET label = ? WHERE module = ? AND label like ?',array('Due date','ServiceContracts','Due Date'));

SDK::setLanguageEntries('Products','LBL_NO_TAXES_ASSOCIATED',array(
'it_it'=>'Nessuna tassa associata a questo prodotto',
'en_us'=>'No taxes associated with this product',
'pt_br'=>'Nenhum Imposto associado a este produto',
));
SDK::setLanguageEntries('Products','Parent Product',array(
'it_it'=>'Prodotto padre',
'en_us'=>'Parent product',
'pt_br'=>'Produto pai',
));
SDK::setLanguageEntries('ChangeLog','LBL_LINKED_TO',array(
'it_it'=>'è stato collegato a',
'en_us'=>'has been linked to',
'pt_br'=>'foi ligado a',
));
SDK::setLanguageEntries('Leads','LBL_DESIGNATION',array(
'it_it'=>'Titolo',
'en_us'=>'Title',
'pt_br'=>'Título',
));
SDK::setLanguageEntries('Leads','Designation',array(
'it_it'=>'Titolo',
'en_us'=>'Title',
'pt_br'=>'Título',
));
SDK::setLanguageEntries('Calendar','Start Time',array(
'it_it'=>'Orario di inizio',
'en_us'=>'Start Time',
'pt_br'=>'Hora Inicial',
));
SDK::setLanguageEntries('Calendar','End Time',array(
'it_it'=>'Orario di fine',
'en_us'=>'End Time',
'pt_br'=>'Hora Final',
));
SDK::setLanguageEntries('com_vtiger_workflow','com_vtiger_workflow',array(
'it_it'=>'Workflow',
'en_us'=>'Workflow',
'pt_br'=>'Workflow',
));
SDK::setLanguageEntries('Potentials','Related To',array(
'it_it'=>'Collegato a',
'en_us'=>'Related To',
'pt_br'=>'Relacionado a',
));
SDK::setLanguageEntries('Accounts','Website',array('it_it'=>'Sito Web','en_us'=>'Website','pt_br'=>'Site',));
SDK::setLanguageEntries('Leads','Website',array('it_it'=>'Sito Web','en_us'=>'Website','pt_br'=>'Site',));
SDK::setLanguageEntries('Products','Website',array('it_it'=>'Sito Web','en_us'=>'Website','pt_br'=>'Site',));
SDK::setLanguageEntries('Vendors','Website',array('it_it'=>'Sito Web','en_us'=>'Website','pt_br'=>'Site',));
SDK::setLanguageEntries('Services','Website',array('it_it'=>'Sito Web','en_us'=>'Website','pt_br'=>'Site',));
?>