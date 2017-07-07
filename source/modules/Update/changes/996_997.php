<?php
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';


global $adb, $table_prefix;


/* crmv@59610 */
$tablename = $table_prefix.'_geolocation_users';
$schema_table =
'<schema version="0.3">
	<table name="'.$tablename.'">
		<opt platform="mysql">ENGINE=InnoDB</opt>
		<field name="userid" type="I" size="19">
			<KEY/>
		</field>
		<field name="timestamp" type="T">
			<DEFAULT value="0000-00-00 00:00:00" />
		</field>
		<field name="latitude" type="N" size="20,6"/>
		<field name="longitude" type="N" size="20,6"/>
		<field name="data" type="X" />
	</table>
</schema>';
if(!Vtiger_Utils::CheckTable($tablename)) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

// fix also comments
$r = $adb->query("
select modcommentsid, related_to, parent_comments 
from {$table_prefix}_modcomments 
inner join {$table_prefix}_crmentity on crmid = modcommentsid 
where deleted = 0 and parent_comments = 0 and related_to > 0");
if ($r && $adb->num_rows($r) > 0) {
	while ($row = $adb->FetchByAssoc($r, -1, false)) {
		$adb->pquery("update {$table_prefix}_modcomments set related_to = ? where parent_comments = ?", array(intval($row['related_to']), intval($row['modcommentsid'])));
	}
}


?>