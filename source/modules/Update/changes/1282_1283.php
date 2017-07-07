<?php
// translations
$trans = array(
	'Settings' => array(
		'it_it' => array(
			'LBL_ON_MANUAL_MODE'=>'al click del tasto Invia',
		),
		'en_us' => array(
			'LBL_ON_MANUAL_MODE'=>'when click the Run button',
		),
	),
	'Processes' => array(
		'it_it' => array(
			'LBL_RUN_PROCESSES'=>'Avvia processi',
			'LBL_SAVE_AND_RUN_PROCESSES_BUTTON_TITLE'=>'Salva e avvia processi',
			'LBL_SAVE_AND_RUN_PROCESSES_BUTTON_LABEL'=>'Salva e avvia',
		),
		'en_us' => array(
			'LBL_RUN_PROCESSES'=>'Run processes',
			'LBL_SAVE_AND_RUN_PROCESSES_BUTTON_TITLE'=>'Save and run processes',
			'LBL_SAVE_AND_RUN_PROCESSES_BUTTON_LABEL'=>'Save and run',
		),
	),
	'ALERT_ARR' => array(
		'it_it' => array(
			'LBL_NO_RUN_PROCESSES'=>'Nessun processo eseguito',
			'LBL_RUN_PROCESSES_OK'=>'Processo eseguito con successo',
			'LBL_RUN_PROCESSES_ERROR'=>'E\' stato riscontrato un errore nell\'esecuzione del processo',
		),
		'en_us' => array(
			'LBL_NO_RUN_PROCESSES'=>'No process runs',
			'LBL_RUN_PROCESSES_OK'=>'Process executed successfully',
			'LBL_RUN_PROCESSES_ERROR'=>'Error occurred in the execution of the process',
		),
	),
);
foreach ($trans as $module=>$modlang) {
	foreach ($modlang as $lang=>$translist) {
		foreach ($translist as $label=>$translabel) {
			SDK::setLanguageEntry($module, $lang, $label, $translabel);
		}
	}
}