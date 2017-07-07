<?php
	global $table_prefix;
	// crmv@30967
	$sql="select foldername,folderid from ".$table_prefix."_crmentityfolder where tabid = ? order by foldername";
	$res=$adb->pquery($sql,array(getTabId('Documents')));
	// crmv@30967e
	for($i=0;$i<$adb->num_rows($res);$i++) {
		$fid=$adb->query_result($res,$i,"folderid");
		$fname=$adb->query_result($res,$i,"foldername");
		$fieldvalue[]=$fid."@".$fname;
	}
    echo implode("###",$fieldvalue);
?>
