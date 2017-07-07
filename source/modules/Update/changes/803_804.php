<?php
$_SESSION['modules_to_update']['SLA'] = 'packages/vte/mandatory/SLA.zip';
$_SESSION['modules_to_update']['Timecards'] = 'packages/vte/mandatory/Timecards.zip';

@unlink('include/js/Area.js');

SDK::setLanguageEntries('APP_STRINGS', 'LBL_AREAS_SETTINGS_NOTE', array(
	'it_it'=>'Spostando un modulo in Ulteriori o Moduli principali sarà nascosto dalle altre aree.',
	'en_us'=>'If you move a module in More or Main modules it will be hide in others areas.')
);
SDK::setLanguageEntries('APP_STRINGS', 'LBL_ALL_MENU_TITLE', array('it_it'=>'Tutti','en_us'=>'All','pt_br'=>'Todos'));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_ALL_MENU_ALT', array('it_it'=>'Tutti','en_us'=>'All','pt_br'=>'Todos'));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_HIGHTLIGHT_MENU_MODULE', array('it_it'=>'Rendi principale','en_us'=>'Hightlight module'));
SDK::setLanguageEntries('Messages', 'LBL_DETACH_MESSAGE', array('it_it'=>'Apri in nuova finestra','en_us'=>'Open in new tab'));
?>