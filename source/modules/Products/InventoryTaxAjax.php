<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/
// crmv@42024 - many changes
require_once('Smarty_setup.php');

global $theme, $app_strings, $mod_strings;

$productid = vtlib_purify($_REQUEST['productid']);
$rowid = vtlib_purify($_REQUEST['curr_row']);
$product_total = vtlib_purify($_REQUEST['productTotal']);

$InventoryUtils = InventoryUtils::getInstance();

// retrieve tax info
$tax_details = $InventoryUtils->getTaxDetailsForProduct($productid,'all');//we should pass available instead of all if we want to display only the available taxes.

// set the array for the calculations
if (is_array($tax_details)) {
	$prodTaxes = array();
	foreach ($tax_details as $td) {
		$prodTaxes[$td['taxname']] = $td['percentage'];
	}
}

// populate array for calculations
$prodinfo = array(
	'listprice' => $product_total,
	'quantity' => 1,
	'discount_percent' => 0,
	'discount_amount' => 0,
	'taxes' => $prodTaxes,
);

// calculate taxes for a single product (to have the partial values)
$prodPrices = $InventoryUtils->calcProductTotals($prodinfo);
for ($i=0; $i<count($tax_details); ++$i) {
	$tax_details[$i]['taxtotal'] = $prodPrices['taxes'][$i]['amount'];
}

// set the array for smarty
$taxdata = array(
	'totalAfterDiscount' => 'totalAfterDiscount'.$rowid,
	'totalAfterDiscount'.$rowid => $product_total,
	'taxTotal' => 'taxTotal'.$rowid,
	'taxTotal'.$rowid => $prodPrices['total_taxes'],
	'taxes' => $tax_details,
);

$smarty = new vtigerCRM_Smarty;
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('THEME', $theme);
$smarty->assign('row_no', $rowid);
$smarty->assign('data', $taxdata);

$smarty->display('Inventory/ProductTaxDetail.tpl');
?>