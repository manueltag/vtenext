<?php
global $adb,$table_prefix;
$result = $adb->pquery("SELECT max(sequence) as maxsequence FROM ".$table_prefix."_relatedlists WHERE tabid=?", Array($this->id));
if($adb->num_rows($result)) $max_sequence = $adb->query_result($result, 0, 'maxsequence');
$max_sequence++;
$tabid = $adb->getUIniqueId($table_prefix.'_tab');
$params = Array(
tabid=>$tabid,
name=>'Update',
presence=>1,
tabsequence=>$max_sequence,
modifiedby=>NULL,
modifiedtime=>NULL,
customized=>0,
ownedby=>1,
version=>1.0,
isentitytype=>0
);
$adb->pquery("INSERT INTO ".$table_prefix."_tab (".implode(",",array_keys($params)).") VALUES (".generateQuestionMarks($params).")",$params);
?>