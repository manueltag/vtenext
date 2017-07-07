<?php

global $adb, $table_prefix;

// fix quickcreate values for some old inventory modules

// custom fields
$adb->pquery("UPDATE {$table_prefix}_field SET quickcreate = 1 WHERE tabid = ? AND fieldname like 'cf_%' AND quickcreate = 3", array(getTabid('Quotes')));
$adb->pquery("UPDATE {$table_prefix}_field SET quickcreate = 1 WHERE tabid = ? AND fieldname like 'cf_%' AND quickcreate = 3", array(getTabid('PurchaseOrder')));
$adb->pquery("UPDATE {$table_prefix}_field SET quickcreate = 1 WHERE tabid = ? AND fieldname like 'cf_%' AND quickcreate = 3", array(getTabid('SalesOrder')));
$adb->pquery("UPDATE {$table_prefix}_field SET quickcreate = 1 WHERE tabid = ? AND fieldname like 'cf_%' AND quickcreate = 3", array(getTabid('Invoice')));

// fixed fields
$changes = array(
	'Quotes' => array(
		array('old' => 3, 'new' => 1, 'fields' => array(
			'contact_id',
			'bill_city', 'bill_code', 'bill_state', 'bill_country', 'bill_pobox',
			'ship_city', 'ship_code', 'ship_state', 'ship_country', 'ship_pobox',
			'description', 'terms_conditions',
		)),
	),
	'PurchaseOrder' => array(
		array('old' => 3, 'new' => 1, 'fields' => array(
			'requisition_no', 'tracking_no', 'duedate', 'carrier',
			'bill_city', 'bill_code', 'bill_state', 'bill_country', 'bill_pobox',
			'ship_city', 'ship_code', 'ship_state', 'ship_country', 'ship_pobox',
			'description', 'terms_conditions',
		)),
	),
	'SalesOrder' => array(
		array('old' => 3, 'new' => 1, 'fields' => array(
			'potential_id', 'customerno', 'quote_id', $table_prefix.'_purchaseorder',
			'duedate', 'carrier', 'pending', 'salescommission',  
			'bill_city', 'bill_code', 'bill_state', 'bill_country', 'bill_pobox',
			'ship_city', 'ship_code', 'ship_state', 'ship_country', 'ship_pobox',
			'description', 'terms_conditions',
		)),
		array('old' => 3, 'new' => 2, 'fields' => array(
			'sostatus',
		)),
	),
	'Invoice' => array(
		array('old' => 3, 'new' => 1, 'fields' => array(
			'salesorder_id', 'customerno', 'invoicedate', 'duedate', $table_prefix.'_purchaseorder',
			'salescommission', 'exciseduty', 
			'bill_city', 'bill_code', 'bill_state', 'bill_country', 'bill_pobox',
			'ship_city', 'ship_code', 'ship_state', 'ship_country', 'ship_pobox',
			'description', 'terms_conditions',
		)),
		array('old' => 3, 'new' => 2, 'fields' => array(
			'invoicestatus'
		)),
	),
);

foreach ($changes as $module => $cs) {
	$tabid = getTabid($module);
	if ($tabid > 0) {
		foreach ($cs as $change) {
			$sql = "UPDATE {$table_prefix}_field SET quickcreate = ? WHERE tabid = ? AND quickcreate = ? AND fieldname IN (".generateQuestionMarks($change['fields']).")";
			$params = array($change['new'], $tabid, $change['old'], $change['fields']);
			$res = $adb->pquery($sql, $params);
		}
	}
}

// fix regression due to crmv@77395
$res = $adb->query("select queryid, columnindex, columnname from {$table_prefix}_selectcolumn where columnname like '{$table_prefix}_%Rel%:%'");
if ($res && $adb->num_rows($res) > 0) {
	while ($row = $adb->FetchByAssoc($res, -1, false)) {
		$queryid = $row['queryid'];
		$cname = $row['columnname'];
		$cidx = $row['columnindex'];
		$pieces = explode(':', $cname);
		if (preg_match('/[0-9]+$/', $pieces[0], $matches)) {
			// looks like a fieldid, let's check!
			$fieldid = $matches[0];
			$res2 = $adb->pquery(
				"SELECT tablename, columnname, fieldname, uitype, t.name as modulename
				FROM {$table_prefix}_field f
				INNER JOIN {$table_prefix}_tab t ON t.tabid = f.tabid
				WHERE fieldid = ? AND uitype = ?", 
				array($fieldid, 10)
			);
			if ($res2 && $adb->num_rows($res2) > 0) {
				// ok, it's our field, let's fix the table!
				$row2 = $adb->FetchByAssoc($res2, -1, false);
				$tname = fixRealReportTableName($row2['modulename'], $row2['fieldname'], $row2['tablename'], $row2['columnname']);
				if ($tname != $pieces[0]) {
					// update the row
					$pieces[0] = $tname;
					$params = array(implode(':', $pieces), $queryid, $cidx);
					$adb->pquery("UPDATE {$table_prefix}_selectcolumn SET columnname = ? WHERE queryid = ? AND columnindex = ?", $params);
				}

			}
		}
	}
}

// similar to the reports.php code
function fixRealReportTableName($module, $fieldname, $tablename, $columnname) {
	global $table_prefix;
	
	$fieldtablename = $tablename;
	
	$product_id_tables = array(
		$table_prefix."_troubletickets"=>$table_prefix."_productsRel",
		$table_prefix."_campaign"=>$table_prefix."_productsCampaigns",
		$table_prefix."_faq"=>$table_prefix."_productsFaq",
	);
	
	if($fieldtablename == $table_prefix."_crmentity"){
		$fieldtablename = $fieldtablename.$module;
	} elseif($fieldname == "assigned_user_id") {
		$fieldtablename = $table_prefix."_users".$module;
	} elseif($fieldname == "account_id") {
		$fieldtablename = $table_prefix."_account".$module;
	} elseif($fieldname == "contact_id") {
		$fieldtablename = $table_prefix."_contactdetails".$module;
	} elseif($fieldname == "parent_id") {
		$fieldtablename = $table_prefix."_crmentityRel".$module;
	} elseif($fieldname == "vendor_id") {
		$fieldtablename = $table_prefix."_vendorRel".$module;
	} elseif($fieldname == "potential_id") {
		$fieldtablename = $table_prefix."_potentialRel".$module;
	} elseif($fieldname == "assigned_user_id1") {
		$fieldtablename = $table_prefix."_usersRel1";
	} elseif($fieldname == 'quote_id') {
		$fieldtablename = $table_prefix."_quotes".$module;
	} elseif($fieldname == "productlineid") {
		$fieldtablename = $table_prefix."_productlinesRel".$module;
	} elseif($fieldname == 'product_id' && isset($product_id_tables[$fieldtablename])) {
		$fieldtablename = $product_id_tables[$fieldtablename];
	} elseif($fieldname == 'campaignid' && $module=='Potentials') {
		$fieldtablename = $table_prefix."_campaign".$module;
	} elseif($fieldname == 'currency_id' && $fieldtablename==$table_prefix.'_pricebook') {
		$fieldtablename = $table_prefix."_currency_info".$module;
	}
	
	return $fieldtablename;
}