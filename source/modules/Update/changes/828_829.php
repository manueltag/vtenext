<?php
$_SESSION['modules_to_update']['Conditionals'] = 'packages/vte/mandatory/Conditionals.zip';
$_SESSION['modules_to_update']['Transitions'] = 'packages/vte/mandatory/Transitions.zip';

global $adb, $table_prefix;

// migrate colors : old => new
$migration_colors = array(
'31c99e31c99e',
'4cd5da4cd5da',
'6ce1826ce182',
'6e65e76e65e7',
'6f96e46f96e4',
'79acdf79acdf',
'84c64284c642',
'8a9fba8a9fba',
'8bf7a78bf7a7',
'93930e93930e',
'99cccc99cccc',
'99ccff99ccff',
'a3c91ea3c91e',
'ace96face96f',
'b2f0f7b2f0f7',
'b399e6b399e6',
'c0c01dc0c01d',
'c0e3e3c0e3e3',
'c2d1e1c2d1e1',
'cca2cccca2cc',
'cccccccccccc',
'd0a400d0a400',
'd1c2f0d1c2f0',
'd3d36dd3d36d',
'd56bfed56bfe',
'd6e1f0d6e1f0',
'd9832fd9832f',
'dbc48ddbc48d',
'e0e0e0e0e0e0',
'e17272e17272',
'e1e123e1e123',
'e29394e29394',
'e4984de4984d',
'e6bc13e6bc13',
'e8e0ebe8e0eb',
'ecec5cecec5c',
'f0c2c2f0c2c2',
'f0e8c4f0e8c4',
'f8739ff8739f',
'f8a3a3f8a3a3',
'f8d4aff8d4af',
'fa9efafa9efa',
'fab066fab066',
'fc7777fc7777',
'fcdc64fcdc64',
'fceaa3fceaa3',
'fcfc82fcfc82',
'fe4b4bfe4b4b',
'ff9933ff9933',
'ffdedeffdede',
'ffe4feffe4fe',
'fff000fff000',
);
if (Vtiger_Utils::CheckTable('tbl_s_cal_color')) $adb->query("drop table tbl_s_cal_color");
$sqlarray = $adb->datadict->CreateTableSQL('tbl_s_cal_color', "id I(19) NOTNULL PRIMARY, color C(255) NOTNULL");
$adb->datadict->ExecuteSQLArray($sqlarray);
$id = 1;
foreach ($migration_colors as $new) {
	$adb->pquery('insert into tbl_s_cal_color (id,color) values (?,?)',array($id,$new));
	$id++;
}
//foreach($migration_colors as $old => $new) {
//	$adb->pquery('update '.$table_prefix.'_users set cal_color=? where cal_color=?',array($new,$old));
//}

$adb->pquery("UPDATE {$table_prefix}_relatedlists SET actions = ? WHERE actions = ? AND name = ? AND tabid = ?",array('SELECT,ADD','SELECT','get_documents_dependents_list',8));

$adb->pquery("UPDATE {$table_prefix}_field SET typeofdata = ? WHERE typeofdata = ?",array('V~O','PIVA~O'));
$adb->pquery("UPDATE {$table_prefix}_field SET typeofdata = ? WHERE typeofdata = ?",array('V~M','PIVA~M'));
$adb->pquery("UPDATE {$table_prefix}_field SET typeofdata = ? WHERE typeofdata = ?",array('V~O','CF~O'));
$adb->pquery("UPDATE {$table_prefix}_field SET typeofdata = ? WHERE typeofdata = ?",array('V~M','CF~M'));
?>