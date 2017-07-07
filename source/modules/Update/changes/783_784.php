<?php
//crmv@3078m
$result = $adb->query("SELECT * FROM sdk_menu_fixed WHERE title = 'Events'");
if ($result) {
	if ($adb->num_rows($result)>0){
		$adb->query("delete FROM sdk_menu_fixed WHERE title = 'Events'");
	}
	SDK::setMenuButton('fixed','Events',"fnvshobj(this,'events');getEventList(this);",'btnL3Calendar.png');
}
$SDK = Vtiger_Module::getInstance('SDK');
Vtiger_Link::addLink($SDK->id,'HEADERSCRIPT','EventUtils','modules/SDK/src/Events/js/Utils.js');
$translations = Array(
	'Calendar'=>Array(
		'LBL_RAPID_CALENDAR'=>Array(
			'it_it'=>'Calendario rapido',
			'en_us'=>'Fast Calendar',
			'br_br'=>utf8_encode('Calendário rápida'),
		),
		'Will begin'=>Array(
			'it_it'=>utf8_encode('Comincerà'),
			'en_us'=>'Will begin',
			'br_br'=>utf8_encode('Vai começar'),
		),
		'Begun'=>Array(
			'it_it'=>'E\' cominciato',
			'en_us'=>'Begun',
			'br_br'=>utf8_encode('Começado'),
		),
		'LBL_EVENTS_FROM'=>Array(
			'it_it'=>'Eventi dal',
			'en_us'=>'Events from',
			'br_br'=>utf8_encode('Eventos de'),
		),
		'LBL_EVENTS_TO'=>Array(
			'it_it'=>'a',
			'en_us'=>'to',
			'br_br'=>'a',
		),
		'LBL_NEXT_DAYS'=>Array(
			'it_it'=>'Prossimi giorni',
			'en_us'=>'Next Days',
			'br_br'=>utf8_encode('Nos próximos dias'),
		),
	),
);
foreach ($translations as $module=>$trans_arr){
	foreach ($trans_arr as $label=>$trans){
		SDK::setLanguageEntries($module,$label,$trans);
	}
}
//crmv@3078me
//crmv@3079m
global $table_prefix;
$index_name = 'idx_stufftitle';
$adb->datadict->ExecuteSQLArray((Array)$adb->datadict->CreateIndexSQL('idx_stufftitle',$table_prefix."_homestuff",'stufftitle'));
Vtiger_Utils::AlterTable($table_prefix.'_homedefault','hometype C(100)');
require_once('modules/SDK/InstallTables.php');
$_SESSION['modules_to_install']['Myfiles'] = 'packages/vte/mandatory/Myfiles.zip';
//crmv@3079me
?>