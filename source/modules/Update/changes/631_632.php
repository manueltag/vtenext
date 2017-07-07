<?php
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';

include('config.inc.php');
global $adb, $table_prefix;

$result = $adb->query("SELECT {$table_prefix}_tab.name FROM vte_hide_tab
						INNER JOIN {$table_prefix}_tab ON vte_hide_tab.tabid = {$table_prefix}_tab.tabid
						WHERE hide_profile = 1");
if ($result && $adb->num_rows($result) > 0) {
	while($row=$adb->fetchByAssoc($result)) {
		if ($adb->isMysql()) {
			$sql = "UPDATE {$table_prefix}_profile2standardperm p
					INNER JOIN {$table_prefix}_tab t ON t.tabid = p.tabid AND t.name = ?
					SET permissions = 0";
			$adb->pquery($sql,array($row['name']));
			$sql = "UPDATE {$table_prefix}_profile2tab p
					INNER JOIN {$table_prefix}_tab t ON t.tabid = p.tabid AND t.name = ?
					SET permissions = 0";
			$adb->pquery($sql,array($row['name']));
		} elseif ($adb->isMssql()) {
			$sql = "UPDATE {$table_prefix}_profile2standardperm
					SET permissions = 0
					FROM {$table_prefix}_profile2standardperm
					INNER JOIN {$table_prefix}_tab t ON t.tabid = {$table_prefix}_profile2standardperm.tabid AND t.name = ?";
			$adb->pquery($sql,array($row['name']));
			$sql = "UPDATE {$table_prefix}_profile2tab
					SET permissions = 0
					FROM {$table_prefix}_profile2tab
					INNER JOIN {$table_prefix}_tab t ON t.tabid = {$table_prefix}_profile2tab.tabid AND t.name = ?";
			$adb->pquery($sql,array($row['name']));
		} elseif ($adb->isOracle()) {
			//TODO
		}
	}
}
if(!Vtiger_Utils::CheckTable($table_prefix.'_reload_session')) {
	$schema_table = '<schema version="0.3">
					  <table name="'.$table_prefix.'_reload_session">
					  <opt platform="mysql">ENGINE=InnoDB</opt>
					    <field name="userid" type="I" size="19">
					      <KEY/>
					    </field>
					    <field name="session_var" type="C" size="255"/>
					  </table>
					</schema>';
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}
$result = $adb->pquery('SELECT id FROM '.$table_prefix.'_users WHERE status = ?',array('Active'));
if ($result && $adb->num_rows($result) > 0) {
	$adb->pquery('delete from '.$table_prefix.'_reload_session where session_var = ?',array('vtiger_hash_version'));
	while($row=$adb->fetchByAssoc($result)) {
		$adb->pquery('insert into '.$table_prefix.'_reload_session (userid,session_var) values (?,?)',array($row['id'],'vtiger_hash_version'));
	}
}
?>