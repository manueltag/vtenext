<?php
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';

include('config.inc.php');
global $adb, $table_prefix;

SDK::setLanguageEntry('Accounts','en_us','Bank Details','Bank Details');
$adb->pquery('UPDATE sdk_menu_fixed SET onclick = ? WHERE title = ?',array("fnvshobj(this,'favorites');getFavoriteList();",'LBL_FAVORITES'));

if ($adb->isMysql()) {
	if (isModuleInstalled('ModNotifications')) {
		$primary_key = '';
		$columns = $adb->database->MetaColumns($table_prefix.'_modnotifications');
		foreach ($columns as $column) {
			if ($column->primary_key === true) {
				$primary_key = $column->name;
				break;
			}
		}
		if (empty($primary_key)) {
			$adb->query('ALTER TABLE '.$table_prefix.'_modnotifications CHANGE modnotificationsid modnotificationsid INT(19) NOT NULL, ADD PRIMARY KEY(modnotificationsid)');
		}
	}
	if (isModuleInstalled('ModComments')) {
		$primary_key = '';
		$columns = $adb->database->MetaColumns($table_prefix.'_modcomments');
		foreach ($columns as $column) {
			if ($column->primary_key === true) {
				$primary_key = $column->name;
				break;
			}
		}
		if (empty($primary_key)) {
			$adb->query('ALTER TABLE '.$table_prefix.'_modcomments CHANGE modcommentsid modcommentsid INT(19) NOT NULL, ADD PRIMARY KEY(modcommentsid)');
		}
		$idx_table = $adb->database->MetaIndexes($table_prefix.'_modcomments');
		if (!isset($idx_table['NewIndex3'])) {
			$adb->query('ALTER TABLE '.$table_prefix.'_modcomments ADD INDEX NewIndex3 (commentcontent (255))');
		}
	}
} else {
	if (isModuleInstalled('ModNotifications')) {
		$indexes[] = Array('table'=>$table_prefix.'_modnotifications','idxname'=>'NewIndex0','idxflds'=>'modnotificationsid','options'=>array('UNIQUE'));
	}
	if (isModuleInstalled('ModComments')) {
		$indexes[] = Array('table'=>$table_prefix.'_modcomments','idxname'=>'NewIndex0','idxflds'=>'modcommentsid','options'=>array('UNIQUE'));
	}
}
$indexes[] = Array('table'=>'vte_notifications','idxname'=>'NewIndex1','idxflds'=>'id');
$indexes[] = Array('table'=>'vte_notifications','idxname'=>'NewIndex2','idxflds'=>'userid');
if (isModuleInstalled('ModNotifications')) {
	$indexes[] = Array('table'=>$table_prefix.'_modnotifications','idxname'=>'NewIndex1','idxflds'=>'related_to');
	$indexes[] = Array('table'=>$table_prefix.'_modnotifications','idxname'=>'NewIndex2','idxflds'=>'mod_not_type');
}
if (isModuleInstalled('ModComments')) {
	$indexes[] = Array('table'=>$table_prefix.'_modcomments','idxname'=>'NewIndex1','idxflds'=>'related_to');
	$indexes[] = Array('table'=>$table_prefix.'_modcomments','idxname'=>'NewIndex2','idxflds'=>'parent_comments');
}
foreach ($indexes as $arr){
	$fields = explode(",",$arr['idxflds']);
	$idx_table = $adb->database->MetaIndexes($arr['table']);
	$short_name = $arr['table'];
	if (strpos($short_name,"_")!==false){
		$exp = explode("_",$short_name);
		unset($exp[0]);
		$short_name = implode("_",$exp);
	}
	$short_name=strtolower(substr($short_name,0,20));
	$idx_prefix = "idx_".$short_name."_";
	$progress = Array(0);
	if (is_array($idx_table)){
		$found = false;
		foreach ($idx_table as $name=>$arr2){
			$fields_compare = $arr2['columns'];
			$result_compare = array_udiff($fields, $fields_compare, 'strcasecmp');
			if (empty($result_compare)){
				$found = true;
			}
			$name = strtolower($name);
			$pos_start = strpos($name,$idx_prefix);
			if ($pos_start!==false){
				$pos_start+=strlen($idx_prefix);
				$progress[] = intval(substr($name,$pos_start,strlen($name)));
			}
		}
	}
	if (!$found){
		$index_name = $idx_prefix.str_pad((String)(max($progress)+1), 2, "0",STR_PAD_LEFT);
    	$adb->datadict->ExecuteSQLArray((Array)$adb->datadict->CreateIndexSQL($index_name, $arr['table'], $arr['idxflds'], $arr['options']));
	}
}
?>