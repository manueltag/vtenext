<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

global $mod_strings, $app_strings, $theme, $currentModule;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

if(isset($_REQUEST['return_module']) && $_REQUEST['return_module']=="PriceBooks")
{
	$pricebook_id = vtlib_purify($_REQUEST['pricebook_id']);
	$product_id = vtlib_purify($_REQUEST['record']);
	$listprice = vtlib_purify($_REQUEST['listprice']);
	$return_action = "CallRelatedList";
	$return_id = vtlib_purify($_REQUEST['pricebook_id']);
}
else
{
	$product_id = vtlib_purify($_REQUEST['record']);
	$pricebook_id = vtlib_purify($_REQUEST['pricebook_id']);
	$listprice = getListPrice($product_id,$pricebook_id);
	$return_action = "CallRelatedList";
	$return_id = vtlib_purify($_REQUEST['pricebook_id']);
}
$output='';
$output ='<div id="roleLay" style="display:block;" class="crmvDiv">
	<table border=0 cellspacing=0 cellpadding=5 width=100%>
	<tr height="34">
		<td class="level3Bg" align="left" style="padding:5px"><b>'.getTranslatedString('LBL_EDITLISTPRICE','Products').'</b></td>
	</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=5 width=95% align=center> 
<tr>
	<td class="small">
	<table border=0 celspacing=0 cellpadding=5 width=100% align=center>
	<tr>
		<td width="100%" class="cellText small" align="center"><input class="dataInput" type="text" id="list_price" name="list_price" value="'.$listprice.'" /></td>
	</tr>
	</table>
	</td>
</tr>
</table>
<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
<tr>
	<td colspan="3" align="center" class="small">
	<input title="'.$app_strings["LBL_SAVE_BUTTON_LABEL"].'" class="crmbutton small save" onClick="if(verify_data(\''.getTranslatedString('LBL_PB_LIST_PRICE','PriceBooks').'\') == true) gotoUpdateListPrice('.$return_id.','.$pricebook_id.','.$product_id.',\''.$currentModule.'\'); else document.getElementById(\'roleLay\').style.display=\'inline\'; return false;" type="button" name="button" value="'.$app_strings["LBL_SAVE_BUTTON_LABEL"].'">
	</td>
</tr>
</table>
<div class="closebutton" onClick="fninvsh(\'editlistprice\');"></div>
</div>';

echo $output;
?>