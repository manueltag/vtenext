<?php
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';
require_once("modules/Update/Update.php");
Update::change_field($table_prefix.'_quotes','discount_percent','C','255');
Update::change_field($table_prefix.'_invoice','discount_percent','C','255');
Update::change_field($table_prefix.'_salesorder','discount_percent','C','255');
Update::change_field($table_prefix.'_purchaseorder','discount_percent','C','255');
?>