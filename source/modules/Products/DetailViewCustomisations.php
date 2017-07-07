<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/

$InventoryUtils = InventoryUtils::getInstance();
	
$tax_details = $InventoryUtils->getTaxDetailsForProduct($focus->id);
for($i=0;$i<count($tax_details);$i++) {
	$tax_details[$i]['percentage'] = $InventoryUtils->getProductTaxPercentage($tax_details[$i]['taxname'],$focus->id); // crmv@42024
}
$smarty->assign("TAX_DETAILS", $tax_details);

$price_details = $InventoryUtils->getPriceDetailsForProduct($focus->id, $focus->unit_price, 'available_associated',$currentModule);
$smarty->assign("PRICE_DETAILS", $price_details);
?>