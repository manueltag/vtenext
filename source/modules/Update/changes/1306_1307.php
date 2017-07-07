<?php
global $adb, $table_prefix;

// crmv@88444 - remove tax type from filters
$adb->query("DELETE FROM {$table_prefix}_cvcolumnlist WHERE columnname LIKE '%taxtype:hdnTaxType%'");
