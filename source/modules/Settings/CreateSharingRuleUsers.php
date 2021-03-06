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


require_once('include/database/PearDatabase.php');
require_once('include/utils/utils.php');
//Constructing the Role Array
$roleDetails=getAllRoleDetailsUser();
$output='';
$userid = $_REQUEST['userid'];

//Constructing the Group Array
$grpDetails=getAllGroupName();
$combovalues='';

global $mod_strings;
global $app_strings;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
global $adb;

$mode = $_REQUEST['mode'];
if(isset($_REQUEST['shareid']) && $_REQUEST['shareid'] != '')
{	
	$shareid=$_REQUEST['shareid'];
	$shareInfo=getSharingRuleInfo($shareid);
	$tabid=$shareInfo[1];
	$sharing_module=getTabModuleName($tabid);

}
else
{
	$sharing_module=$_REQUEST['sharing_module'];
	$tabid=getTabid($sharing_module);
}

eval(Users::m_de_cryption());
eval($hash_version[10]);

if($mode == 'create')
{
	foreach($roleDetails as $roleid=>$rolename)
	{
		if ($roleid != $userid) $combovalues .='<option value="user::'.$roleid.'">'.$rolename[0].'</option>';
	}
	$toComboValues='<option value="user::'.$userid.'">'.$roleDetails[$userid][0].'</option>';
	$fromComboValues=$combovalues;
}
elseif($mode == 'edit')
{


	//constructing the from combo values
	$fromtype=$shareInfo[3];
	$fromid=$shareInfo[5];
	$toComboValues='<option value="user::'.$userid.'">'.$roleDetails[$userid][0].'</option>';
	//constructing the to combo values
	$totype=$shareInfo[4];
	$toid=$shareInfo[6];	
	foreach($roleDetails as $roleid=>$rolename)
	{
		$selected='';

		if($totype == 'usr' && $roleid !=$userid)
		{
			if($roleid == $fromid)
			{
				$selected='selected';	
			}
		$fromComboValues .='<option value="user::'.$roleid.'" '.$selected.'>'.$rolename[0].'</option>';				
		}
	}	
}



$relatedmodule='';	
$relatedlistscombo='';
$relatedModuleSharingArr=getRelatedSharingModules($tabid);
$size=sizeof($relatedModuleSharingArr);
if($size > 0)
{
	if($mode=='edit')
	{
		$relatedModuleSharingPerrArr=getRelatedModuleSharingPermission($shareid);
	}
	foreach($relatedModuleSharingArr as $reltabid=>$relmode_shareid)
	{
		$rel_module=getTabModuleName($reltabid);
		$relatedmodule .=$rel_module.'###'; 						
	}
	foreach($relatedModuleSharingArr as $reltabid=>$relmode_shareid)
	{
		$ro_selected='';
		$rw_selected='';
		$rel_module=getTabModuleName($reltabid);
		if($mode=='create')
		{
			$ro_selected='selected';
		}
		elseif($mode=='edit')
		{
			$perr=$relatedModuleSharingPerrArr[$reltabid];
			if($perr == 0)
			{
				$ro_selected='selected';
			}
			elseif($perr == 1)
			{
				$rw_selected='selected';
			}
		}	

		$relatedlistscombo.='<tr><td align="right" nowrap style="padding-right:10px;"><b>'.$app_strings[$rel_module].' :</b></td>
			<td width="70%">';
		$relatedlistscombo.='<div class="dvtCellInfo"><select class="detailedViewTextBox" id="'.$rel_module.'_accessopt" name="'.$rel_module.'_accessopt" onChange="fnwriteRules(\''.$app_strings[$sharing_module].'\',\''.$relatedmodule.'\')">
			<option value="0" '.$ro_selected.' >'.$mod_strings["Read Only "].'</option>
			<option value="1" '.$rw_selected.' >'.$mod_strings["Read/Write"].'</option>
			</select></div></td></tr>';


	}
}


if($mode == 'create')
{
	$sharPerCombo = '<option value="0" selected>'.$mod_strings["Read Only "].'</option>';
        $sharPerCombo .= '<option value="1">'.$mod_strings["Read/Write"].'</option>';
}
elseif($mode == 'edit')
{
	$selected1='';
	$selected2='';
	if($shareInfo[7] == 0)
	{
		$selected1='selected';
	}
	elseif($shareInfo[7] == 1)
	{
		$selected2='selected';
	}

	$sharPerCombo = '<option value="0" '.$selected1.'>'.$mod_strings["Read Only "].'</option>';
        $sharPerCombo .= '<option value="1" '.$selected2.'>'.$mod_strings["Read/Write"].'</option>';	
}

	
$output.='<div id="sharingRule"><form name="newGroupForm" action="index.php" method="post">
<input type="hidden" name="module" value="Settings">
<input type="hidden" name="parenttab" value="Settings">	
<input type="hidden" name="action" value="SaveSharingRuleUsers">
<input type="hidden" name="sharing_module" value="'.$sharing_module.'">
<input type="hidden" name="shareId" value="'.$shareid.'">
<input type="hidden" name="mode" value="'.$mode.'">
<input type="hidden" name="record" value="'.$userid.'">
<input type="hidden" id="rel_module_lists" name="rel_module_lists" value="'.$relatedmodule.'">

<table border=0 cellspacing=0 cellpadding=5 width=95% align=center> 
<tr>
	<td class="small">
	<table border=0 celspacing=0 cellpadding=5 width=100% align=center bgcolor=white>
	<tr>
		<td><b>'.$mod_strings[LBL_STEP].' 1 : '.$display_module.' '.$app_strings[LBL_LIST_OF].' </b>('.$mod_strings[LBL_SELECT_ENTITY].')</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>';
//combovalues

		$output .= '<div class="dvtCellInfo">';
		$output.='<select class="detailedViewTextBox" id="'.$app_strings[$sharing_module].'_share" name="'.$sharing_module.'_share" onChange="fnwriteRules(\''.$app_strings[$sharing_module].'\',\''.$relatedmodule.'\')";>'.$fromComboValues.'</select>';	
		$output .= '</div>';
		$output.='</td>

		<td>&nbsp;</td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>

		<td style="text-align:left;"><b>'.$mod_strings[LBL_STEP].' 2 : '.$mod_strings[LBL_CAN_BE_ACCESSED_BY].' </b>('.$mod_strings[LBL_SELECT_ENTITY].')</td>
	</tr>
	<tr>
		<td>

			<div class="dvtCellInfo">
				<select class="detailedViewTextBox" id="'.$app_strings[$sharing_module].'_access" name="'.$sharing_module.'_access" onChange="fnwriteRules(\''.$app_strings[$sharing_module].'\',\''.$relatedmodule.'\')";>';

		$output.=$toComboValues.'</select></div>

		</td>
		<td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
		<td align="left"><b>'.$mod_strings[LBL_PERMISSIONS].'</b></td>
	</tr>
	<tr>
		<td>
		<div class="dvtCellInfo">
				<div class="dvtCellInfo">
			<select	class="detailedViewTextBox" id="share_memberType" name="share_memberType" onChange="fnwriteRules(\''.$app_strings[$sharing_module].'\',\''.$relatedmodule.'\')";>';
		$output .= $sharPerCombo;
		$output .= '</select></div>

		</td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
		<td style="text-align:left;"><b>'.$mod_strings[LBL_STEP].' 3 : '.$mod_strings[LBL_ACCESS_RIGHTS_FOR_MODULES].' </b></td>
		<td>&nbsp;</td>

	</tr>
	<tr>
		<td style="padding-left:20px;text-align:left;">
		<table width="75%"  border="0" cellspacing="0" cellpadding="0">';

		$output .=$relatedlistscombo.'</table>
		</td>
		<td>&nbsp;</td>
	</tr>
	<tr><td colspan="2" align="left">&nbsp;</td></tr>
	<tr>
		<td colspan="2" class="dvInnerHeader"><b>'.$mod_strings[LBL_RULE_CONSTRUCTION].'</b></td>

	</tr>
	<tr>
		<td  style="white-space:normal;padding:10px 0px" colspan="2" id="rules">&nbsp;
	</td>
	</tr>
	<tr>
		<td style="white-space:normal;" colspan="2" id="relrules">&nbsp;
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
	<tr>
		<td colspan="2" align="center">
		<input type="submit" class="crmButton small save" name="add" value="'.$mod_strings[LBL_ADD_RULE].'">&nbsp;&nbsp;
	</td>
	</tr>
</table>';

$output.='</form></div>';

// crmv@104853
if ($sharing_module == 'Accounts') {
	$display_module = $app_strings['Accounts'] . ' & ' . $app_strings['Contacts'];
} else {
	$display_module = $app_strings[$sharing_module];
}
$display_module .= ' - ';
if ($mode == 'edit') {
	$display_module .= $mod_strings['LBL_EDIT_CUSTOM_RULE'];
} else {
	$display_module .= $mod_strings['LBL_ADD_CUSTOM_RULE'];
}

$display_module = Zend_Json::encode($display_module);

$output .= '<script type="text/javascript">
	var titleObj = jQuery("#tempdiv2_Handle_Title");
	titleObj.html("<strong>"+'.$display_module.'+"</strong>");
	titleObj.addClass("layerPopupHeading");
</script>';
// crmv@104853e

echo $output;
?>