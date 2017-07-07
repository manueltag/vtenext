<?php
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';

require_once("modules/Update/Update.php");
Update::change_field($table_prefix.'_inventoryproductrel','discount_percent','C','255');

$adb->pquery("UPDATE {$table_prefix}_field SET helpinfo = ? WHERE fieldname = ?",array('LBL_DISCOUNT_PERCENT_INFO','hdnDiscountPercent'));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_DISCOUNT_PERCENT_INFO', array('it_it'=>'Puoi aggiungere fino a 5 livelli di sconto separati da + (es. 30+20+50)','en_us'=>'You can add 5 discount levels separated by + (eg 30+20+50)','pt_br'=>'Você pode adicionar até 5 níveis de desconto separados por + (ex. 30+20+50)'));

require_once("modules/SDK/InstallTables.php");
SDK::setPDFCustomFunction('if-else','its4you_if',array('param1','comparator','param2','return1','return2'));
SDK::setPDFCustomFunction('Contact Image','its4you_getContactImage',array('contactid','width','height'));
SDK::setPDFCustomFunction('Net Prices Total','getTotalNetPrice',array('$CRMID$'));
SDK::setPDFCustomFunction('Discount Prices Total','getTotalDiscountPrice','$CRMID$');
SDK::setLanguageEntries('PDFMaker', 'Net Prices Total', array('it_it'=>'Totale Prezzi Netti','en_us'=>'Net Prices Total','pt_br'=>'Total de preços líquidos'));
SDK::setLanguageEntries('PDFMaker', 'Discount Prices Total', array('it_it'=>'Totale Prezzi Scontati','en_us'=>'Discount Prices Total','pt_br'=>'Total de descontos'));
?>