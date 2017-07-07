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


global $mod_strings;
global $app_strings;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

require_once('include/home.php');
require_once('Smarty_setup.php');
require_once('include/freetag/freetag.class.php');

$homeObj=new Homestuff;
$smarty=new vtigerCRM_Smarty;
$smarty->assign("MOD",$mod_strings);
$smarty->assign("APP",$app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH",$image_path);

if(!empty($_REQUEST['homestuffid'])){
	$stuffid = $_REQUEST['homestuffid'];
}
if(!empty($_REQUEST['blockstufftype'])){
	$stufftype = $_REQUEST['blockstufftype'];
}

if($stufftype=='Tag Cloud'){
	$freetag = new freetag();
	$smarty->assign("ALL_TAG",$freetag->get_tag_cloud_html("",$current_user->id));
	$smarty->display("Home/TagCloud.tpl");
}elseif($stufftype == 'URL'){
	$url = $homeObj->getWidgetURL($stuffid);
	if(strpos($url, "://") === false){
		$url = "http://".trim($url);
	}
	$smarty->assign("URL",$url);
	$smarty->assign("WIDGETID", $stuffid);
	$smarty->display("Home/HomeWidgetURL.tpl");
//crmv@25466
} elseif ($stufftype == 'SDKIframe') {
	$sdkiframe = SDK::getHomeIframe($stuffid);
	$url = $sdkiframe['url'];
	$size = $sdkiframe['size'];
	if ($sdkiframe['iframe']) {
		$smarty->assign("URL",$url);
		$smarty->assign("SIZE",$size);
		$smarty->assign("WIDGETID", $stuffid);
		$smarty->assign("STUFFTYPE", $stufftype); //crmv@3079m
		$smarty->display("Home/HomeWidgetURL.tpl");
	} else {
		// check if there is a protocol specified
		if (strpos($url, "://") === false) {
			require($url);
		} else {
			echo file_get_contents($url);
		}
	}
//crmv@25466e
//crmv@25314	//crmv@29079
}elseif($stufftype == 'Iframe'){
	$url = $homeObj->getIframeURL($stuffid);
	if (strpos($url, "index.php") == 0) {
		//continue
	} elseif (strpos($url, "://") === false) {
		$url = "http://".trim($url);
	}
	$smarty->assign("URL",$url);
	$smarty->assign("WIDGETID", $stuffid);
	$smarty->assign("STUFFTYPE", $stufftype); //crmv@3079m
	$smarty->display("Home/HomeWidgetURL.tpl");
//crmv@25314e	//crmv@29079e
// crmv@30014
}elseif($stufftype == 'Charts' && vtlib_isModuleActive('Charts')){
	$chartted = $homeObj->getChartDetails($stuffid);
	$chid = $chartted['chartid'];
	if (!empty($chid)) {
		$chartInst = CRMEntity::getInstance('Charts');
		$chartInst->setCacheField('chart_file_home');
		$chartInst->retrieve_entity_info($chid, 'Charts');
		$chartInst->homestuffid = $stuffid;

		$chartInst->homestuffsize = empty($chartted['size']) ? 1 : $chartted['size'];
		$chartInst->reloadReport(); // when clicking on reload, reload report data
		echo $chartInst->renderHomeBlock();
	}
// crmv@30014e
}else{
	$homestuff_values=$homeObj->getHomePageStuff($stuffid,$stufftype);
	if($stufftype=="DashBoard"){
		$homeObj->getDashDetails($stuffid,'type');
		$dashdet=$homeObj->dashdetails;
	}
}

$smarty->assign("DASHDETAILS",$dashdet);
$smarty->assign("HOME_STUFFTYPE",$stufftype);
$smarty->assign("HOME_STUFFID",$stuffid);
$smarty->assign("HOME_STUFF",$homestuff_values);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);

$smarty->display("Home/HomeBlock.tpl");
?>