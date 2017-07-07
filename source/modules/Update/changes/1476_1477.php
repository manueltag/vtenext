<?php

// crmv@115445
SDK::setLanguageEntries('APP_STRINGS', 'LBL_MODHOME_VIS', array('it_it'=>'Vista d\'insieme','en_us'=>'Overview'));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_MODHOME_AGGR', array('it_it'=>'Riassuntivo','en_us'=>'Summary'));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_MODHOME_PROC', array('it_it'=>'Processi','en_us'=>'Processes'));

$adb->pquery("update {$table_prefix}_modulehome set name=? where name=?",array('LBL_MODHOME_VIS','Visione d\'insieme'));
$adb->pquery("update {$table_prefix}_modulehome set name=? where name=?",array('LBL_MODHOME_AGGR','Riassuntivo'));
$adb->pquery("update {$table_prefix}_modulehome set name=? where name=?",array('LBL_MODHOME_PROC','Processi'));

if (file_exists('extract.php.txt')) {
	@unlink('extract.php.txt');
}