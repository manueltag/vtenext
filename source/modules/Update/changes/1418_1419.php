<?php

/* crmv@107531 */

// hide in create_view and massedit
$adb->pquery("UPDATE {$table_prefix}_blocks SET create_view = 1 WHERE blocklabel = ?", array('LBL_SIGNATURE_BLOCK'));
