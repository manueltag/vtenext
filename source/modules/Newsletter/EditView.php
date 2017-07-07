<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'modules/VteCore/EditView.php';	//crmv@30447

//crmv@58337
if($isduplicate == 'true') {
	$focus->column_fields['scheduled'] = 0;
	
	$smarty->assign("BLOCKS",getBlocks($currentModule,$disp_view,$mode,$focus->column_fields,'',$blockVisibility));	//crmv@99316
}
//crmv@58337e

$smarty->display('salesEditView.tpl');
