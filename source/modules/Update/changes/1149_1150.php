<?php
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter' => 'Targets'));

global $adb, $table_prefix;

$moduleInstance = Vtiger_Module::getInstance('Targets');
$result = $adb->pquery("SELECT relation_id, name FROM {$table_prefix}_relatedlists WHERE tabid = ? AND related_tabid = ?", array($moduleInstance->id, $moduleInstance->id));
if ($result && $adb->num_rows($result) > 0) {
	$relation_id = $adb->query_result($result,0,'relation_id');
	$method = $adb->query_result($result,0,'name');
	SDK::setTurboliftCount($relation_id, $method);
}
?>