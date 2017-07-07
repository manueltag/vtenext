<?php
$translations = array(
	array('module'=>'APP_STRINGS','label'=>'PurchaseOrders','trans_label'=>'Ordini di Acquisto'),
	array('module'=>'PurchaseOrder','label'=>'LBL_MODULE_NAME','trans_label'=>'Ordini di Acquisto'),
	array('module'=>'PurchaseOrder','label'=>'PurchaseOrder','trans_label'=>'Ordini di Acquisto'),
	array('module'=>'Settings','label'=>'PurchaseOrder','trans_label'=>'Ordini di Acquisto'),
	array('module'=>'APP_STRINGS','label'=>'SalesOrders','trans_label'=>'Ordini di Vendita'),
	array('module'=>'SalesOrder','label'=>'LBL_MODULE_NAME','trans_label'=>'Ordini di Vendita'),
	array('module'=>'SalesOrder','label'=>'SalesOrder','trans_label'=>'Ordini di Vendita'),
	array('module'=>'Settings','label'=>'SalesOrder','trans_label'=>'Ordini di Vendita'),
);
foreach($translations as $t) {
	SDK::setLanguageEntry($t['module'], 'it_it', $t['label'], $t['trans_label']);
}

global $adb, $table_prefix;
$adb->pquery("UPDATE {$table_prefix}_relatedlists SET label = ? where label = ?",array('PurchaseOrder','Purchase Order'));

$pLInstance = Vtiger_Module::getInstance('ProductLines');
$result = $adb->pquery("SELECT parenttabid FROM {$table_prefix}_parenttab WHERE parenttab_label = ?",array('Inventory'));
if ($result && $adb->num_rows($result) > 0) {
	$parenttabid = $adb->query_result($result,0,'parenttabid');
	$result1 = $adb->pquery("SELECT * FROM {$table_prefix}_parenttabrel WHERE parenttabid = ? AND tabid = ?",array($parenttabid,$pLInstance->id));
	if ($result1 && $adb->num_rows($result1) > 1) {
		$adb->pquery("delete from {$table_prefix}_parenttabrel where parenttabid = ? and tabid = ? and sequence > ?",array($parenttabid,$pLInstance->id,$adb->query_result($result1,0,'sequence')));
	}
}

$result = $adb->query("select templatename, body from {$table_prefix}_emailtemplates where body LIKE '%CRMVILLAGE.BIZ%'");
if ($result && $adb->num_rows($result) > 0) {
	while($row=$adb->fetchByAssoc($result,-1,false)) {
		$body = $row['body'];
		$replace = '<p>Grazie per esserti iscritto al supporto annuale offerto da CRMVILLAGE.BIZ<br />';
		if (strpos($body, $replace) !== false) {
			$body = str_replace($replace,'<p>Grazie per esserti iscritto al nostro supporto annuale.<br />',$body);
			$adb->updateClob($table_prefix.'_emailtemplates','body',"templatename = '".$row['templatename']."'",$body);
		}
	}
}
?>