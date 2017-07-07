<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header$
 * Description:  TODO To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('Smarty_setup.php');
require_once('data/Tracker.php');
require_once('include/CustomFieldUtil.php');
require_once('include/utils/utils.php');

global $app_strings,$mod_strings,$log,$theme,$currentModule;
global $table_prefix;
$focus = CRMEntity::getInstance($currentModule);
$smarty = new vtigerCRM_Smarty();
//added to fix the issue4600
$searchurl = getBasic_Advance_SearchURL();
$smarty->assign("SEARCH", $searchurl);
//4600 ends

$InventoryUtils = InventoryUtils::getInstance(); // crmv@42024

global $current_user;
$currencyid=fetchCurrency($current_user->id);
$rate_symbol = getCurrencySymbolandCRate($currencyid);
$rate = $rate_symbol['rate'];
if(isset($_REQUEST['record']) && $_REQUEST['record'] != '')
{
    $focus->id = $_REQUEST['record'];
    $focus->mode = 'edit';
    $focus->retrieve_entity_info($_REQUEST['record'],"PurchaseOrder");
    $focus->name=$focus->column_fields['subject'];
}
if(isset($_REQUEST['isDuplicate']) && $_REQUEST['isDuplicate'] == 'true') {
	$duplicate_from = $focus->id;	//crmv@38845
	$smarty->assign("DUPLICATE_FROM", $focus->id);
	//$PO_associated_prod = getAssociatedProducts("PurchaseOrder",$focus);	//crmv@30721
	$PO_final_details = $InventoryUtils->getFinalDetails("PurchaseOrder",$focus);	//crmv@30721
	$focus->id = "";
	$focus->mode = '';
}
if(empty($_REQUEST['record']) && $focus->mode != 'edit'){
	setObjectValuesFromRequest($focus);
}
if(isset($_REQUEST['product_id']) && $_REQUEST['product_id'] !='')
{
	$focus->column_fields['product_id'] = $_REQUEST['product_id'];
	$log->debug("Purchase Order EditView: Product Id from the request is ".$_REQUEST['product_id']);
	/*
	$associated_prod = getAssociatedProducts("Products",$focus,$focus->column_fields['product_id']);	//crmv@30721
	for ($i=1; $i<=count($associated_prod);$i++) {
		$associated_prod_id = $associated_prod[$i]['hdnProductId'.$i];
		$associated_prod_prices = getPricesForProducts($currencyid,array($associated_prod_id),'Products');
		$associated_prod[$i]['listPrice'.$i] = $associated_prod_prices[$associated_prod_id];
	}
	$smarty->assign("ASSOCIATEDPRODUCTS", $associated_prod);
	$smarty->assign("AVAILABLE_PRODUCTS", 'true');
	*/
	$final_details = $InventoryUtils->getFinalDetails("Products",$focus,$focus->column_fields['product_id']);	//crmv@30721
}
if(!empty($_REQUEST['parent_id']) && !empty($_REQUEST['return_module']))
{
    if ($_REQUEST['return_module'] == 'Services') {
	    $focus->column_fields['product_id'] = $_REQUEST['parent_id'];
	    $log->debug("Service Id from the request is ".$_REQUEST['parent_id']);
	    /*
	    $associated_prod = getAssociatedProducts("Services",$focus,$focus->column_fields['product_id']);	//crmv@30721
		for ($i=1; $i<=count($associated_prod);$i++) {
			$associated_prod_id = $associated_prod[$i]['hdnProductId'.$i];
			$associated_prod_prices = getPricesForProducts($currencyid,array($associated_prod_id),'Services');
			$associated_prod[$i]['listPrice'.$i] = $associated_prod_prices[$associated_prod_id];
		}
		$smarty->assign("ASSOCIATEDPRODUCTS", $associated_prod);
		$smarty->assign("AVAILABLE_PRODUCTS", 'true');
		*/
	    $final_details = $InventoryUtils->getFinalDetails("Services",$focus,$focus->column_fields['product_id']);	//crmv@30721
    }
}

// Get vtiger_vendor address if vtiger_vendorid is given
if(isset($_REQUEST['vendor_id']) && $_REQUEST['vendor_id']!='' && $_REQUEST['record']==''){
	$vend_focus = CRMEntity::getInstance('Vendors');
	$vend_focus->retrieve_entity_info($_REQUEST['vendor_id'],"Vendors",true);
	$focus->column_fields['bill_city']=$vend_focus->column_fields['city'];
	$focus->column_fields['ship_city']=$vend_focus->column_fields['city'];
	$focus->column_fields['bill_street']=$vend_focus->column_fields['street'];
	$focus->column_fields['ship_street']=$vend_focus->column_fields['street'];
	$focus->column_fields['bill_state']=$vend_focus->column_fields['state'];
	$focus->column_fields['ship_state']=$vend_focus->column_fields['state'];
	$focus->column_fields['bill_code']=$vend_focus->column_fields['postalcode'];
	$focus->column_fields['ship_code']=$vend_focus->column_fields['postalcode'];
	$focus->column_fields['bill_country']=$vend_focus->column_fields['country'];
	$focus->column_fields['ship_country']=$vend_focus->column_fields['country'];
	$focus->column_fields['bill_pobox']=$vend_focus->column_fields['pobox'];
	$focus->column_fields['ship_pobox']=$vend_focus->column_fields['pobox'];
}
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$disp_view = getView($focus->mode);
//crmv@9434
$mode = $focus->mode;
//crmv@9434 end

// crmv@104568
$panelid = getCurrentPanelId($currentModule);
$smarty->assign("PANELID", $panelid);
$panelsAndBlocks = getPanelsAndBlocks($currentModule);
$smarty->assign("PANEL_BLOCKS", Zend_Json::encode($panelsAndBlocks));
if ($InventoryUtils) {
	$binfo = $InventoryUtils->getInventoryBlockInfo($currentModule);
	$smarty->assign('PRODBLOCKINFO', $binfo);
}

$smarty->assign("BLOCKS",getBlocks($currentModule,$disp_view,$mode,$focus->column_fields,'',$blockVisibility));	//crmv@99316
$smarty->assign('BLOCKVISIBILITY', $blockVisibility);	//crmv@99316
// crmv@104568

$smarty->assign("OP_MODE",$disp_view);

$smarty->assign("MODULE",$currentModule);
$smarty->assign("SINGLE_MOD",'PurchaseOrder');
$category = getParentTab();
$smarty->assign("CATEGORY",$category);


$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);

$log->info("Order view");

if (isset($focus->name)) $smarty->assign("NAME", $focus->name);
else $smarty->assign("NAME", "");

if($focus->mode == 'edit')
{
	$smarty->assign("UPDATEINFO",updateInfo($focus->id));
	//$associated_prod = getAssociatedProducts("PurchaseOrder",$focus);	//crmv@30721
	//$smarty->assign("ASSOCIATEDPRODUCTS", $associated_prod);
	$smarty->assign("MODE", $focus->mode);
	$final_details = $InventoryUtils->getFinalDetails("PurchaseOrder",$focus);	//crmv@30721
}
elseif(isset($_REQUEST['isDuplicate']) && $_REQUEST['isDuplicate'] == 'true')
{
	//$smarty->assign("ASSOCIATEDPRODUCTS", $PO_associated_prod);
	//$smarty->assign("AVAILABLE_PRODUCTS", 'true');
	$smarty->assign("MODE", $focus->mode);
	$final_details = $PO_final_details;	//crmv@30721
}
elseif((isset($_REQUEST['product_id']) && $_REQUEST['product_id'] != '')) {
	//$smarty->assign("ASSOCIATEDPRODUCTS", $associated_prod);
	$InvTotal = getInventoryTotal($_REQUEST['return_module'],$_REQUEST['return_id']);
	$smarty->assign("MODE", $focus->mode);

	//this is to display the Product Details in first row when we create new PO from Product relatedlist
	if($_REQUEST['return_module'] == 'Products')
	{
		$smarty->assign("PRODUCT_ID",vtlib_purify($_REQUEST['product_id']));
		$smarty->assign("PRODUCT_NAME",getProductName($_REQUEST['product_id']));
		$smarty->assign("UNIT_PRICE",vtlib_purify($_REQUEST['product_id']));
		$smarty->assign("QTY_IN_STOCK",$InventoryUtils->getPrdQtyInStck($_REQUEST['product_id'])); // crmv@42024
		$smarty->assign("VAT_TAX",$InventoryUtils->getProductTaxPercentage("VAT",$_REQUEST['product_id']));
		$smarty->assign("SALES_TAX",$InventoryUtils->getProductTaxPercentage("Sales",$_REQUEST['product_id']));
		$smarty->assign("SERVICE_TAX",$InventoryUtils->getProductTaxPercentage("Service",$_REQUEST['product_id']));
		$smarty->assign("row_no",1);	//crmv@47104
	}
}

if(isset($cust_fld))
{
	$smarty->assign("CUSTOMFIELD", $cust_fld);
}

if(isset($_REQUEST['return_module'])) $smarty->assign("RETURN_MODULE", vtlib_purify($_REQUEST['return_module']));
else $smarty->assign("RETURN_MODULE","PurchaseOrder");
if(isset($_REQUEST['return_action'])) $smarty->assign("RETURN_ACTION", vtlib_purify($_REQUEST['return_action']));
if(isset($_REQUEST['return_id'])) $smarty->assign("RETURN_ID", vtlib_purify($_REQUEST['return_id']));
if (isset($_REQUEST['return_viewname'])) $smarty->assign("RETURN_VIEWNAME", vtlib_purify($_REQUEST['return_viewname']));
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);
$smarty->assign("MODULE","PurchaseOrder");
$smarty->assign("PRINT_URL", "phprint.php?jt=".session_id().$GLOBALS['request_string']);
$smarty->assign("ID", $focus->id);

$smarty->assign("CALENDAR_LANG", $app_strings['LBL_JSCALENDAR_LANG']);
$smarty->assign("CALENDAR_DATEFORMAT", parse_calendardate($app_strings['NTC_DATE_FORMAT']));

//crmv@30721
if (empty($final_details)) {
	$final_details = $InventoryUtils->getFinalDetails($currentModule, $focus);
}
$smarty->assign("FINAL_DETAILS", $final_details);
//crmv@30721e

// crmv@83877 crmv@112297
// Field Validation Information
$tabid = getTabid($currentModule);
$otherInfo = array();
$validationData = getDBValidationData($focus->tab_name,$tabid,$otherInfo,$focus);	//crmv@96450
$validationArray = split_validationdataArray($validationData, $otherInfo);
$smarty->assign("VALIDATION_DATA_FIELDNAME",$validationArray['fieldname']);
$smarty->assign("VALIDATION_DATA_FIELDDATATYPE",$validationArray['datatype']);
$smarty->assign("VALIDATION_DATA_FIELDLABEL",$validationArray['fieldlabel']);
$smarty->assign("VALIDATION_DATA_FIELDUITYPE",$validationArray['fielduitype']);
$smarty->assign("VALIDATION_DATA_FIELDWSTYPE",$validationArray['fieldwstype']);
// crmv@83877e crmv@112297e

//crmv@45699 crmv@104568
if (method_exists($focus, 'getEditTabs')) {
	$smarty->assign("EDITTABS", $focus->getEditTabs());
}
//crmv@45699e crmv@104568e

$check_button = Button_Check($module);
$smarty->assign("CHECK", $check_button);
$smarty->assign("DUPLICATE",vtlib_purify($_REQUEST['isDuplicate']));

global $adb;
// Module Sequence Numbering
$mod_seq_field = getModuleSequenceField($currentModule);
if($focus->mode != 'edit' && $mod_seq_field != null) {
	$autostr = getTranslatedString('MSG_AUTO_GEN_ON_SAVE');
	$mod_seq_string = $adb->pquery("SELECT prefix, cur_id from ".$table_prefix."_modentity_num where semodule = ? and active=1",array($currentModule));
	$mod_seq_prefix = $adb->query_result($mod_seq_string,0,'prefix');
	$mod_seq_no = $adb->query_result($mod_seq_string,0,'cur_id');
	if($adb->num_rows($mod_seq_string) == 0 || $focus->checkModuleSeqNumber($focus->table_name, $mod_seq_field['column'], $mod_seq_prefix.$mod_seq_no))
		echo '<br><font color="#FF0000"><b>'.getTranslatedString('LBL_DUPLICATE').' '.getTranslatedString($mod_seq_field['label'],$currentModule).'. '
			.sprintf(getTranslatedString('LBL_CLICK_TO_CONFIGURE_MODENTITYNUM'),'index.php?module=Settings&action=CustomModEntityNo&parenttab=Settings&selmodule='.$currentModule,getTranslatedString('LBL_HERE'),getTranslatedString($mod_seq_field['label'],$currentModule)).'</b></font>';
	else
		$smarty->assign("MOD_SEQ_ID",$autostr);
} else {
	$smarty->assign("MOD_SEQ_ID", $focus->column_fields[$mod_seq_field['name']]);
}
// END

$smarty->assign("CURRENCIES_LIST", $InventoryUtils->getAllCurrencies());
//crmv@38845
if($focus->mode == 'edit') {
	$inventory_cur_info = $InventoryUtils->getInventoryCurrencyInfo('PurchaseOrder', $focus->id);
	$smarty->assign("INV_CURRENCY_ID", $inventory_cur_info['currency_id']);
} elseif($_REQUEST['isDuplicate'] == 'true') {
	$inventory_cur_info = $InventoryUtils->getInventoryCurrencyInfo('PurchaseOrder', $duplicate_from);
	$smarty->assign("INV_CURRENCY_ID", $inventory_cur_info['currency_id']);
} else {
	$smarty->assign("INV_CURRENCY_ID", $currencyid);
}
//crmv@38845e

// crmv@43864
if ($_REQUEST['hide_button_list'] == '1') {
	$smarty->assign('HIDE_BUTTON_LIST', '1');
}
// crmv@43864e

$smarty->assign('FIELDHELPINFO', vtlib_getFieldHelpInfo($currentModule));

//crmv@57221
$CU = CRMVUtils::getInstance();
$smarty->assign("OLD_STYLE", $CU->getConfigurationLayout('old_style'));
//crmv@57221e

//crmv@100495
require_once('modules/Settings/ProcessMaker/ProcessMakerUtils.php');
$PMUtils = ProcessMakerUtils::getInstance();
if ($PMUtils->showRunProcessesButton($currentModule, $focus->id)) $smarty->assign('SHOW_RUN_PROCESSES_BUTTON',true);
//crmv@100495e

//crmv@112297
$conditionalsFocus = CRMEntity::getInstance('Conditionals');
$smarty->assign('ENABLE_CONDITIONALS', $conditionalsFocus->existsConditionalPermissions($currentModule, $focus));
//crmv@112297e

$smarty->display('Inventory/InventoryEditView.tpl');
