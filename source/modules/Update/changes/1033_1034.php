<?php
//crmv@65238
global $adb, $table_prefix;

$sql = "SELECT name,tablabel FROM {$table_prefix}_tab";
$res = $adb->query($sql);
while($row = $adb->fetchByAssoc($res)){
	$module = $row['name'];
	$label = $row['tablabel'];
	$sdk_lang_sql = "SELECT language,trans_label FROM sdk_language WHERE module = ? AND label = ?";
	$params = array($module,$label);
	$sdk_lang_result = $adb->pquery($sdk_lang_sql,$params);
	$num_rows = $adb->num_rows($sdk_lang_result);
	if($num_rows > 0){
		while($lang_row = $adb->fetchByAssoc($sdk_lang_result)){
			SDK::setLanguageEntry('APP_STRINGS', $lang_row['language'] , $label, $lang_row['trans_label']);
		}
	}
}

?>