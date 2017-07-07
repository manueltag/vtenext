<?php
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['SLA'] = 'packages/vte/mandatory/SLA.zip';

global $adb, $table_prefix;
$result = $adb->pquery("SELECT {$table_prefix}_tab.tabid, {$table_prefix}_tab.name FROM {$table_prefix}_relatedlists 
INNER JOIN {$table_prefix}_tab ON {$table_prefix}_tab.tabid = {$table_prefix}_relatedlists.tabid
WHERE {$table_prefix}_relatedlists.related_tabid = ? 
GROUP BY {$table_prefix}_relatedlists.tabid HAVING COUNT(*) > 1",array(9));
if ($result && $adb->num_rows($result) > 0) {
	while($row=$adb->fetchByAssoc($result)) {
		$adb->pquery("delete from {$table_prefix}_relatedlists where tabid = ? and related_tabid = ? and label = ?",array($row['tabid'],9,'Calendar'));
	}
}
$adb->pquery("update {$table_prefix}_relatedlists set label = ? where related_tabid = ? and label = ?",array('Activities',9,'Calendar'));

@unlink('modules/SDK/doc/VTE-SDK-howto.pdf');
@unlink('modules/SDK/examples/script.php');
?>