<?php
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';

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


// creazione indice mancante (nelle nuove installazioni si chiama NewIndex2)
$sql = $adb->datadict->CreateIndexSQL('NewIndex2', $table_prefix.'_modcomments', 'parent_comments');
if ($sql) @$adb->datadict->ExecuteSQLArray($sql);

// aggiunta colonna
addColumnToTable("{$table_prefix}_modcomments", 'lastchild', 'I(19)');

// first set the lastchild for all parents
$adb->query("update {$table_prefix}_modcomments set lastchild = modcommentsid where (parent_comments = 0 or parent_comments is null)");

// now update the ones with children (also the deleted ones)
$res = $adb->query(
"select m.modcommentsid, max(children.modcommentsid) as lastchildid 
from {$table_prefix}_modcomments m
inner join {$table_prefix}_crmentity c on c.crmid = m.modcommentsid
inner join {$table_prefix}_modcomments children on children.parent_comments = m.modcommentsid
where (m.parent_comments = 0 or m.parent_comments is null)
group by m.modcommentsid");

while ($row = $adb->FetchByAssoc($res, -1, false)) {
	$adb->pquery("update {$table_prefix}_modcomments set lastchild = ? where modcommentsid = ?", array($row['lastchildid'], $row['modcommentsid']));
}

?>