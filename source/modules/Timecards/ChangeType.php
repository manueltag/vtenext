<?php

global $currentModule, $current_user;

$record = $_REQUEST["record"];
$type = $_REQUEST["type"];

$focus = CRMEntity::getInstance($currentModule);
$focus->retrieve_entity_info($record, $currentModule);
$focus->id = $record;

$focus->column_fields["timecardtype"] = $type;
$focus->save($currentModule);

print "<script language=javascript>window.location=\"index.php?action=Showcard&module=Timecards\";</script>";

?>
