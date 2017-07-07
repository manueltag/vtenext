<?php
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';

global $adb, $table_prefix;
$adb->query("DELETE FROM vte_favorites WHERE crmid IN (SELECT leadid FROM {$table_prefix}_leaddetails WHERE converted = 1)");

include('modules/SDK/src/CalendarTracking/install.php');	// crmv@62394 - install CalendarTracking

$trans = array(
	'APP_STRINGS' => array(
		'it_it' => array(
			'LBL_DONE_BUTTON_TITLE'=>'Fatto',
			'LBL_SELECT_TIME_AND_USERS'=>'Selezionare un periodo di tempo e gli utenti che si vogliono invitare.',
		),
		'en_us' => array(
			'LBL_DONE_BUTTON_TITLE'=>'Done',
			'LBL_SELECT_TIME_AND_USERS'=>'Select a time period and the users you who want to invite',
		),
		'de_de' => array(
			'LBL_DONE_BUTTON_TITLE'=>'Erledigt',
			'LBL_SELECT_TIME_AND_USERS'=>'Wählen Sie einen Zeitraum und die Benutzer, die Sie einladen möchten.',
		),
		'nl_nl' => array(
			'LBL_DONE_BUTTON_TITLE'=>'Gedaan',
			'LBL_SELECT_TIME_AND_USERS'=>'Selecteer een periode en de gebruikers die u die wilt uitnodigen.',
		),
		'pt_br' => array(
			'LBL_DONE_BUTTON_TITLE'=>'Feito',
			'LBL_SELECT_TIME_AND_USERS'=>'Selecione um período de tempo e os usuários que querem convidar.',
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
?>