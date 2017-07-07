<?php
global $adb;
$adb->pquery('UPDATE sdk_uitype SET old_style = ? WHERE uitype = ?',array(0,210));
SDK::clearSessionValue('sdk_uitype');
?>