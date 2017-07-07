<?php
//crmv@32357
global $adb, $table_prefix;
$result = $adb->query("select id from {$table_prefix}_users");
if ($result && $adb->num_rows($result) > 0) {
	while($row=$adb->fetchByAssoc($result)) {
		$userid = $row['id'];
		$result1 = $adb->pquery("SELECT hometype
								FROM {$table_prefix}_homedefault
								INNER JOIN {$table_prefix}_homestuff ON {$table_prefix}_homedefault.stuffid = {$table_prefix}_homestuff.stuffid
								WHERE userid = ?
								GROUP BY hometype HAVING COUNT(*) > 1",array($userid));
		if ($result1 && $adb->num_rows($result1) > 0) {
			while($row1=$adb->fetchByAssoc($result1)) {
				$hometype = $row1['hometype'];
				$result2 = $adb->pquery("SELECT {$table_prefix}_homedefault.stuffid FROM {$table_prefix}_homedefault
										INNER JOIN {$table_prefix}_homestuff ON {$table_prefix}_homedefault.stuffid = {$table_prefix}_homestuff.stuffid
										WHERE userid = 1 AND hometype = ?
										ORDER BY {$table_prefix}_homedefault.stuffid",array($hometype));
				$num_rows2 = $adb->num_rows($result2);
				if ($result2 && $num_rows2 > 1) {
					for($i=1;$i<$num_rows2;$i++) {
						$stuffid = $adb->query_result($result2,$i,'stuffid');
						$adb->pquery("delete from {$table_prefix}_homedefault where stuffid = ?",array($stuffid));
						$adb->pquery("delete from {$table_prefix}_homestuff where stuffid = ?",array($stuffid));
					}
				}
			}
		}
	}
}
//crmv@32357e
SDK::setLanguageEntries('Accounts', 'LBL_NO_PARENT_HIERARCHY', array('it_it'=>'Nessuna controllante','en_us'=>'No parent','pt_br'=>'Nenhum pai'));

$adb->query('DELETE FROM vte_mailcache_folders');
$adb->query('DELETE FROM vte_mailcache_list');
$adb->query('DELETE FROM vte_mailcache_messages');
?>