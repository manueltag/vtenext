<?php
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter','Targets'));
$_SESSION['modules_to_update']['SLA'] = 'packages/vte/mandatory/SLA.zip';
global $adb, $table_prefix;
$sql = "SELECT f.fieldid,f.typeofdata FROM {$table_prefix}_ws_fieldtype w
INNER JOIN {$table_prefix}_field f ON f.uitype = w.uitype
WHERE w.fieldtype = 'reference' AND f.typeofdata NOT LIKE 'V%'";
$res = $adb->query($sql);
if ($res){
	while($row = $adb->fetchByAssoc($res,-1,false)){
		$typeofdata_arr = explode("~",$row['typeofdata'],2);
		$new_typeofdata = "V~".$typeofdata_arr[1];
		$sql_update = "update {$table_prefix}_field set typeofdata = ? where fieldid = ?";
		$params_update = Array($new_typeofdata,$row['fieldid']);
		$adb->pquery($sql_update,$params_update);
	}
}
?>