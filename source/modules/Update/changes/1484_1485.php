<?php


// crmv@92112

// fix product/services uitypes
$fnames = array('qty_per_unit', 'qtyinstock');
$adb->pquery("UPDATE {$table_prefix}_field SET uitype = 7 WHERE uitype = 1 AND fieldname IN (?,?)", $fnames);