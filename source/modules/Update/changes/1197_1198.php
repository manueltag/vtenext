<?php

global $adb, $table_prefix;

// fix some icons

$replacements = array(
	'favorites.png' => 'grade',
	'btnL3Calendar.png' => 'event',
	'mynotes.png' => 'description',
	'track_manager.png' => 'timer',
	'settingsBox.png' => 'settings_applications',
	'tbarImport.png' => 'file_download',
	'tbarExport.png' => 'file_upload',
	'btnL3Add.png' => 'add',
);

foreach ($replacements as $search => $replace) {
	$adb->pquery("UPDATE sdk_menu_fixed SET image = ? WHERE image = ?", array($replace, $search));
	$adb->pquery("UPDATE sdk_menu_contestual SET image = ? WHERE image = ?", array($replace, $search));
}