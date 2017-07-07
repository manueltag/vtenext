<?php
global $currentModule;
$focus = CRMEntity::getInstance($currentModule);
$focus->mode = 'edit';
$focus->id = $_REQUEST['record'];
$focus->retrieve_entity_info($_REQUEST['record'], $currentModule);
$focus->column_fields['templateemailid'] = $_REQUEST['templateid'];
$focus->save($currentModule);
?>