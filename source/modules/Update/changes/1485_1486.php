<?php
global $adb, $table_prefix;

require_once('include/utils/VTEProperties.php');
$VP = VTEProperties::getInstance();

if(Vtiger_Utils::CheckTable("{$table_prefix}_config_layout")) {
	$layout_configuration = array();
	$result = $adb->query("select * from {$table_prefix}_config_layout");
	if ($result && $adb->num_rows($result) > 0) {
		$layout_configuration = $adb->fetchByAssoc($result);
	}
	if (!empty($layout_configuration)) {
		foreach($layout_configuration as $prop => $value) {
			$VP->set("layout.$prop", $value);
		}
	}
}
$VP->set('layout.enable_always_mandatory_css', 0);

$adb->query("drop table {$table_prefix}_config_layout");