<?php

/* crmv@102879 */

SDK::setUitype(220, 'modules/SDK/src/220/220.php', 'modules/SDK/src/220/220.tpl', 'modules/SDK/src/220/220.js', 'table');

// translations
$trans = array(
	'Settings' => array(
		'it_it' => array(
			'LBL_PM_ACTION_Email' => 'Invia email',	// replace!
			'LBL_PM_ACTION_Cycle' => 'Ciclo',
		),
		'en_us' => array(
			'LBL_PM_ACTION_Email' => 'Send email',	// replace!
			'LBL_PM_ACTION_Cycle' => 'Cycle',
		),
	),
	'ALERT_ARR' => array(
		'it_it' => array(
			'LBL_PLEASE_ADD_COLUMNS' => 'Aggiungere almeno una colonna',
			'LBL_PLEASE_NAME_ALL_COLUMNS' => 'Dai un nome a tutte le colonne',
			'LBL_PLEASE_CHOOSE_FIELDNAME' => 'Inserisci un nome per il campo',
			'HAS_EXACTLY_ROWS' => 'ha esattamente',
			'HAS_LESS_ROWS' => 'ha meno di',
			'HAS_MORE_ROWS' => 'ha più di',
			'LBL_ROWS' => 'righe',
		),
		'en_us' => array(
			'LBL_PLEASE_ADD_COLUMNS' => 'Please add at least one column',
			'LBL_PLEASE_NAME_ALL_COLUMNS' => 'Please give a name to all the columns',
			'LBL_PLEASE_CHOOSE_FIELDNAME' => 'Please give a name to the field',
			'HAS_EXACTLY_ROWS' => 'has exactly',
			'HAS_LESS_ROWS' => 'has less than',
			'HAS_MORE_ROWS' => 'has more than',
			'LBL_ROWS' => 'rows',
		),
	),
	'APP_STRINGS' => array(
		'it_it' => array(
			'LBL_ON_FIELD'=>'sul campo',
			'LBL_FOREACH_ROW'=>'per ogni riga',
			'LBL_ADD_FIELD_TABLE'=>'Aggiungi campo tabella',
			'LBL_FIELD_TABLE'=>'Campo tabella',
			'LBL_COLUMNS' => 'Colonne',
			'LBL_COLUMN_NAME' => 'Nome colonne',
			'LBL_ADD_COLUMN' => 'Aggiungi colonna',
			'LBL_ADD_ROW' => 'Aggiungi riga',
			'LBL_DELETE_ROW' => 'Elimina riga',
		),
		'en_us' => array(
			'LBL_ON_FIELD'=>'on field',
			'LBL_FOREACH_ROW'=>'for each row',
			'LBL_ADD_FIELD_TABLE'=>'Add table field',
			'LBL_FIELD_TABLE'=>'Table field',
			'LBL_COLUMNS' => 'Columns',
			'LBL_COLUMN_NAME' => 'Column name',
			'LBL_ADD_COLUMN' => 'Add column',
			'LBL_ADD_ROW' => 'Add row',
			'LBL_DELETE_ROW' => 'Delete row',
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