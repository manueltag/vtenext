<?php
global $adb, $table_prefix;
$myNotesInstance = Vtecrm_Module::getInstance('MyNotes');
$adb->pquery("update {$table_prefix}_field set readonly = ?, displaytype = ? where tabid = ? and fieldname = ?", array(1, 1, $myNotesInstance->id, 'assigned_user_id'));
SDK::addView('MyNotes', 'modules/SDK/src/modules/MyNotes/View.php', 'constrain', 'continue');