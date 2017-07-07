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
require_once('include/utils/CommonUtils.php');
$category = getParentTab();
global $theme,$app_strings,$mod_strings,$currentModule,$current_user;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

//crmv@18549
require_once('Smarty_setup.php');
$smarty = new vtigerCRM_Smarty;
$smarty->assign("MODULE",$currentModule);
$smarty->assign("CATEGORY",$category);
$smarty->assign("APP",$app_strings);
$smarty->assign("THEME",$theme);
$smarty->assign("CHECK",Button_Check($currentModule)); // crmv@81193
$smarty->assign("CURRENT_USER_ID",$current_user->id);
$smarty->display("Buttons_List.tpl");
//crmv@18549e

/* crmv@128159 */

//crmv@vte10usersFix // crmv@98866
$html_string .= '
		<style>
		.showPanelBg, .showPanelBg * {
			padding: 0px;
		}
		.showPanelBg {
			padding-top: 5px;
		}
	</style>
	<table border=0 cellspacing=0 cellpadding=0 width=100% align=center>	<!-- crmv@18592 -->
	     <tr>
	     	<td valign="top" width="100%" > <!-- crmv@20210 -->
			<!-- Calendar Tabs starts -->
			<div class="small"> <!-- crmv@18592 -->
			<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"> <!-- crmv@25128 -->';
$html_string .= '
				<tr>
					<td>
						<div id="Buttons_List_3">
			                <table id="bl3" border=0 cellspacing=0 cellpadding=2 width=100% class="small">
				                <tr>';

if ($_REQUEST['action'] == 'index') {
	$html_string .= '
					                <td style="padding:5px" nowrap>
										<ul class="vteUlTable" style="padding-right:5px">
											<li>
												<button class="crmbutton small edit" onclick="jClickCalendar(\'showdaybtn\')">'.$mod_strings['LBL_DAY'].'</button>
												<button class="crmbutton small edit" onclick="jClickCalendar(\'showweekbtn\')">'.$mod_strings['LBL_WEEK'].'</button>
												<button class="crmbutton small edit" onclick="jClickCalendar(\'showmonthbtn\')">'.$mod_strings['LBL_MONTH'].'</button>
												<button class="crmbutton small edit" onclick="location.href = \'index.php?action=ListView&module=Calendar&parenttab='.$category.'\'">'.$mod_strings['LBL_CAL_TO_FILTER'].'</button>';
	//crmv@39473
	if (is_dir('modules/SDK/src/Geolocalization') || vtlib_isModuleActive('Geolocalization') !== false) {
		$html_string .= '&nbsp;<button class="crmbutton small edit" onclick="window.wdCalendar.GeoCalendar();">'.getTranslatedString('Geolocalization','Geolocalization').'</button>';
	}
	//crmv@39473e
	//crmv@43117 crmv@97209
	$html_string .= '</li></ul>';
	$html_string .= $smarty->fetch("Buttons_List_Contestual.tpl");
	$html_string .= '</td>
				                	<td width=100% align="right">
				                		<table>
				                			<tr>
				                				<td><div id="errorpannel_new" style="display: none; color: red;">'.$mod_strings['LBL_CAL_INTERRUPTED'].'</div></td>
				                				<td><button class="crmbutton small edit" onclick="wdCalendar.jQuery(\'#BBIT-DP-TODAY\').click();">'.$app_strings['LBL_TODAY'].'</button></td>
				                				<td><div id="filterCalendar_new" style="float: right;"></div></td>
				                				<td><i class="vteicon md-link" onclick="jClickCalendar(\'sfprevbtn\')">arrow_back</i></td>
				                				<td><button id="txtdatetimeshow_new" class="crmbutton small" onclick="wdCalendar.jQuery(\'#BBIT_DP_CONTAINER\').toggle()"></button></td>
				                				<td><i class="vteicon md-link" onclick="jClickCalendar(\'sfnextbtn\')">arrow_forward</i></td>
				                			</tr>
				                		</table>
				                	</td>';
	//crmv@43117e crmv@97209e
} else {
	$html_string .= '
					                <td style="padding:5px" nowrap>
										<button class="crmbutton small edit" onclick="listToCalendar(\'Today\')">'.$mod_strings['LBL_DAY'].'</button>
										<button class="crmbutton small edit" onclick="listToCalendar(\'This Week\')">'.$mod_strings['LBL_WEEK'].'</button>
										<button class="crmbutton small edit" onclick="listToCalendar(\'This Month\')">'.$mod_strings['LBL_MONTH'].'</button>
										<button class="crmbutton small edit" onclick="location.href = \'index.php?action=ListView&module=Calendar&parenttab='.$category.'\'">'.$mod_strings['LBL_CAL_TO_FILTER'].'</button>
				                	</td>';
}
$html_string .= '
				                </tr>
				            </table>
			            </div>
	            	</td>
	            </tr>';
$html_string .= '
				<tr>
					<td align="left" valign="top">
						<table border="0" cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td align="left">
								<!-- content cache -->
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td>
	';

$html_string .= '<script>calculateButtonsList3();</script>';
echo $html_string;
//crmv@vte10usersFix e
?>