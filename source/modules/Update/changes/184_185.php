<?php
global $adb;
$result = $adb->query('SELECT MAX(relation_id) AS relation_id FROM vtiger_relatedlists');
if ($result) $relation_id = $adb->query_result($result,0,'relation_id');
if ($relation_id) $adb->query('UPDATE `vtiger_relatedlists_seq` SET `id`= '.$relation_id);
?>