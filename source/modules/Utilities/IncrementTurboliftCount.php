<?php
//crmv@26896
global $current_user;
$relation_id = $_REQUEST['relation_id'];
$tabid = $_REQUEST['tabid'];
if ($relation_id != '' & $tabid != '') {
	$tb_count = 1;
	$result = $adb->pquery('select tb_count from vte_turbolift_count where userid = ? and relation_id = ? and tabid = ?',array($current_user->id,$relation_id,$tabid));
	if ($result && $adb->num_rows($result) > 0) {
		$tb_count = $adb->query_result($result,0,'tb_count')+1;
		$adb->pquery('update vte_turbolift_count set tb_count = ? where userid = ? and relation_id = ? and tabid = ?',array($tb_count,$current_user->id,$relation_id,$tabid));
	} else {
		$adb->pquery('insert into vte_turbolift_count values (?,?,?,?)',array($current_user->id,$relation_id,$tabid,$tb_count));
	}
}
//crmv@26896e
?>