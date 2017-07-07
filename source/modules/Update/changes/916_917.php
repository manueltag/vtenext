<?php
$_SESSION['modules_to_update']['Assets'] = 'packages/vte/mandatory/Assets.zip';
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter','Targets'));
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';

global $adb, $table_prefix;

$result = $adb->pquery("SELECT * FROM sdk_language WHERE module = ? AND label IN (?,?)",array('APP_STRINGS','LBL_VIEW','LBL_ASSIGNED_TO'));
if ($result && $adb->num_rows($result) > 0) {
	while($row=$adb->fetchByAssoc($result)) {
		SDK::setLanguageEntry('APP_STRINGS', $row['language'], $row['label'], str_replace(':','',$row['trans_label']));
	}
}

if (!function_exists('addColumnToTable')) {
	function addColumnToTable($tablename, $columnname, $type, $extra = '') {
		global $adb;
	
		// check if already present
		$cols = $adb->getColumnNames($tablename);
		if (in_array($columnname, $cols)) {
			return;
		}
	
		$col = $columnname.' '.$type.' '.$extra;
		$adb->alterTable($tablename, $col, 'Add_Column');
	}
}
$tablename = $table_prefix.'_messages_cron_uid';
if (Vtiger_Utils::CheckTable($tablename)) {
	addColumnToTable($tablename, 'cdate', 'T', 'DEFAULT 0000-00-00 00:00:00');
	$adb->query("update {$tablename} set cdate = date");
}
$tablename = $table_prefix.'_messages_cron_uidi';
if (Vtiger_Utils::CheckTable($tablename)) {
	addColumnToTable($tablename, 'cdate', 'T', 'DEFAULT 0000-00-00 00:00:00');
	$adb->query("update {$tablename} set cdate = date");
}
$tablename = $table_prefix.'_emails_send_queue';
if (Vtiger_Utils::CheckTable($tablename)) {
	addColumnToTable($tablename, 'date', 'T', 'DEFAULT 0000-00-00 00:00:00');
	$adb->pquery("update {$tablename} set date = ?",array(date('Y-m-d H:i:s')));
}

// delete duplicates
$result = $adb->query("SELECT l1.tabid, l1.linktype, l1.linklabel
FROM {$table_prefix}_links l1
INNER JOIN {$table_prefix}_links l2 ON l1.tabid = l2.tabid
AND l1.linktype = l2.linktype
AND l1.linklabel = l2.linklabel
AND l1.linkid <> l2.linkid
AND l1.linklabel LIKE 'PDF%'
GROUP BY l1.tabid, l1.linktype, l1.linklabel");
$links = array();
if ($result && $adb->num_rows($result) > 0) {
	while($row=$adb->fetchByAssoc($result)) {
		$result1 = $adb->limitpQuery("SELECT linkid FROM {$table_prefix}_links WHERE tabid = ? AND linktype = ? AND linklabel = ?",1,100,array($row['tabid'],$row['linktype'],$row['linklabel']));
		if ($result1 && $adb->num_rows($result1) > 0) {
			while($row1=$adb->fetchByAssoc($result1)) {
				$links[] = $row1['linkid'];
			}
		}
	}
}
if (!empty($links)) {
	$adb->pquery("delete from {$table_prefix}_links where linkid in (".generateQuestionMarks($links).")",$links);
}

$adb->datadict->ExecuteSQLArray((Array)$adb->datadict->CreateIndexSQL("{$table_prefix}_messages_seen_idx", "{$table_prefix}_messages", 'seen'));
$adb->datadict->ExecuteSQLArray((Array)$adb->datadict->CreateIndexSQL("{$table_prefix}_messages_acc_fold_mtype_idx", "{$table_prefix}_messages", 'account,folder,mtype'));
$adb->datadict->ExecuteSQLArray((Array)$adb->datadict->CreateIndexSQL("{$table_prefix}_messages_acc_list_idx", "{$table_prefix}_messages", 'account,folder,mtype,seen'));

if(!Vtiger_Utils::CheckTable('tbl_s_logtime')) {
	$schema = '<?xml version="1.0"?>
				<schema version="0.3">
				  <table name="tbl_s_logtime">
				    <opt platform="mysql">ENGINE=InnoDB</opt>
				    <field name="id" type="I" size="11"/>
				    <field name="request" type="XL"/>
				    <field name="caller" type="XL"/>
				    <field name="type" type="C" size="3"/>
				    <field name="content" type="XL"/>
				    <field name="start" type="N" size="20,6"/>
				    <field name="end" type="N" size="20,6"/>
				    <field name="time_elapsed" type="N" size="20,6"/>
				    <field name="loggedon" type="T"/>
				    <index name="id_idx">
				      <col>id</col>
				    </index>
				  </table>
				</schema>';
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema));
}
?>