<?php

/* crmv@97209 */

// hide the menu field
$adb->pquery("UPDATE {$table_prefix}_field SET readonly = ? WHERE fieldname = ? and tabid = ?", array(100, 'menu_view', getTabid('Users')));

