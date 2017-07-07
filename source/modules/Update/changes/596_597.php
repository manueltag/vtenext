<?php
/* crmv@30014 - parte 2 (installazione modulo) */
global $adb, $table_prefix;

$_SESSION['modules_to_install']['Charts'] = 'packages/vte/mandatory/Charts.zip';

// assicuro l'esistenza dell'uitype 206
SDK::setUitype(206, 'modules/SDK/src/206/206.php', 'modules/SDK/src/206/206.tpl', 'modules/SDK/src/206/206.js', 'integer');
?>