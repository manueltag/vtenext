<?php
include_once('include/utils/utils.php');
global $adb,$default_charset;
$sql = "select fieldname from vtiger_field where uitype in (15,33)";
$res = $adb->query($sql);
if ($res){
	while ($row = $adb->fetchByAssoc($res,-1,false)){
		$tableName = $row['fieldname'];
		$qry="select tablename,columnname from vtiger_field where fieldname=? and presence in (0,2)";
		$result = $adb->pquery($qry, array($tableName));
		$num = $adb->num_rows($result);
		if ($adb->table_exist("vtiger_$tableName")){
			$sql2 = "select $tableName from vtiger_$tableName";
			$res2 = $adb->query($sql2);
			if ($res2){
				while($row2 = $adb->fetchByAssoc($res2,-1,false)){
					$oldVal = $row2[$tableName];
					$newVal = html_entity_decode($oldVal, ENT_QUOTES, $default_charset);
					if($newVal != $oldVal){
						$sql = "UPDATE vtiger_$tableName SET $tableName=? WHERE $tableName=?";
						$adb->pquery($sql, array($newVal, $oldVal));
						//replace the value of this piclist with new one in all records
						if($num > 0){
							for($n=0;$n<$num;$n++){
								$table_name = $adb->query_result($result,$n,'tablename');
								$columnName = $adb->query_result($result,$n,'columnname');
								$sql = "update $table_name set $columnName=? where $columnName=?";
								$adb->pquery($sql, array($newVal, $oldVal));
							}
						}
					}	
				}
			}
		}
	}
}
?>