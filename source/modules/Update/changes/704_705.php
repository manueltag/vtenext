<?php
global $adb, $table_prefix;
$adb->datadict->ExecuteSQLArray((Array)$adb->datadict->CreateIndexSQL('reminder_sent_idx',"{$table_prefix}_activity_reminder", 'reminder_sent'));
$adb->datadict->ExecuteSQLArray((Array)$adb->datadict->CreateIndexSQL('modifiedtime_idx', "{$table_prefix}_crmentity", 'modifiedtime'));
SDK::setLanguageEntries('Invoice', 'Service Name', array('it_it'=>'Nome Servizio','en_us'=>'Service Name','pt_br'=>utf8_encode('Nome Serviço')));
SDK::setLanguageEntries('SalesOrder', 'Service Name', array('it_it'=>'Nome Servizio','en_us'=>'Service Name','pt_br'=>utf8_encode('Nome Serviço')));
SDK::setLanguageEntries('PurchaseOrder', 'Service Name', array('it_it'=>'Nome Servizio','en_us'=>'Service Name','pt_br'=>utf8_encode('Nome Serviço')));
SDK::setLanguageEntries('Quotes', 'Service Name', array('it_it'=>'Nome Servizio','en_us'=>'Service Name','pt_br'=>utf8_encode('Nome Serviço')));

SDK::setLanguageEntries('Invoice', 'List Price', array('it_it'=>'Prezzo di Listino','en_us'=>'List Price','pt_br'=>utf8_encode('Lista de Preços')));
SDK::setLanguageEntries('SalesOrder', 'List Price', array('it_it'=>'Prezzo di Listino','en_us'=>'List Price','pt_br'=>utf8_encode('Lista de Preços')));
SDK::setLanguageEntries('PurchaseOrder', 'List Price', array('it_it'=>'Prezzo di Listino','en_us'=>'List Price','pt_br'=>utf8_encode('Lista de Preços')));
SDK::setLanguageEntries('Quotes', 'List Price', array('it_it'=>'Prezzo di Listino','en_us'=>'List Price','pt_br'=>utf8_encode('Lista de Preços')));

SDK::setLanguageEntries('Invoice', 'Quantity', array('it_it'=>utf8_encode('Quantità'),'en_us'=>'Quantity','pt_br'=>utf8_encode('Quantidade')));
SDK::setLanguageEntries('SalesOrder', 'Quantity', array('it_it'=>utf8_encode('Quantità'),'en_us'=>'Quantity','pt_br'=>utf8_encode('Quantidade')));
SDK::setLanguageEntries('PurchaseOrder', 'Quantity', array('it_it'=>utf8_encode('Quantità'),'en_us'=>'Quantity','pt_br'=>utf8_encode('Quantidade')));
SDK::setLanguageEntries('Quotes', 'Quantity', array('it_it'=>utf8_encode('Quantità'),'en_us'=>'Quantity','pt_br'=>utf8_encode('Quantidade')));

SDK::setLanguageEntries('Invoice', 'Comments', array('it_it'=>'Commenti','en_us'=>'Comments','pt_br'=>utf8_encode('Comentários')));
SDK::setLanguageEntries('SalesOrder', 'Comments', array('it_it'=>'Commenti','en_us'=>'Comments','pt_br'=>utf8_encode('Comentários')));
SDK::setLanguageEntries('PurchaseOrder', 'Comments', array('it_it'=>'Commenti','en_us'=>'Comments','pt_br'=>utf8_encode('Comentários')));
SDK::setLanguageEntries('Quotes', 'Comments', array('it_it'=>'Commenti','en_us'=>'Comments','pt_br'=>utf8_encode('Comentários')));

SDK::setLanguageEntries('Invoice', 'Net Price', array('it_it'=>'Prezzo Netto','en_us'=>'Net Price','pt_br'=>utf8_encode('Preço Líquido')));
SDK::setLanguageEntries('SalesOrder', 'Net Price', array('it_it'=>'Prezzo Netto','en_us'=>'Net Price','pt_br'=>utf8_encode('Preço Líquido')));
SDK::setLanguageEntries('PurchaseOrder', 'Net Price', array('it_it'=>'Prezzo Netto','en_us'=>'Net Price','pt_br'=>utf8_encode('Preço Líquido')));
SDK::setLanguageEntries('Quotes', 'Net Price', array('it_it'=>'Prezzo Netto','en_us'=>'Net Price','pt_br'=>utf8_encode('Preço Líquido')));
SDK::setLanguageEntries('Services', 'Net Price', array('it_it'=>'Prezzo Netto','en_us'=>'Net Price','pt_br'=>utf8_encode('Preço Líquido')));
SDK::setLanguageEntries('Products', 'Net Price', array('it_it'=>'Prezzo Netto','en_us'=>'Net Price','pt_br'=>utf8_encode('Preço Líquido')));
?>