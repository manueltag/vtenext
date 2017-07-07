<?php
global $adb, $table_prefix;

$adb->pquery("UPDATE sdk_uitype SET old_style = ? WHERE uitype = ?", array(0, 300));