<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
//crmv@30447
require_once('modules/VteCore/EditView.php');

// crmv@104568

// Added to set price book active when creating a new pricebook
if($focus->mode != 'edit' && $_REQUEST['isDuplicate'] != 'true')
	$smarty->assign('PRICE_BOOK_MODE', 'create');

$smarty->display('Inventory/InventoryEditView.tpl');
