<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
-->
{* crmv@55198 *}
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset={$APP.LBL_CHARSET}">
<title>{$MOD.TITLE_VTIGERCRM_FAX}</title>
<link REL="SHORTCUT ICON" HREF="{php}echo get_logo('favicon');{/php}">	
<link rel="stylesheet" href="themes/{$THEME}/style.css">
<script language="JavaScript" type="text/javascript" src="{"include/js/general.js"|resourcever}"></script>
<script language="javascript" type="text/javascript" src="include/scriptaculous/prototype.js"></script>
<script src="include/js/jquery.js" type="text/javascript"></script>
</head>
<body marginheight="0" marginwidth="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0">
<table class="small" border="0" cellpadding="0" cellspacing="0" width="100%">
<tbody>
   <tr>
	<td colspan="3">
		<table border=0 cellspacing=0 cellpadding=0 width=100% class="mailClientWriteEmailHeader level2Bg menuSeparation">
		<tr>
			<td>{$MOD.LBL_DETAILVIEW_FAX}</td>
		</tr>
		</table>
	</td>
   </tr> 
   {foreach item=row from=$BLOCKS}
   {foreach item=elements key=title from=$row}
   {if $elements.fldname eq 'subject'}	
	<tr>
	<td class="mailSubHeader" width="15%" style="padding: 5px;" align="right">{$MOD.LBL_TO}</td>
	<td class="dvtCellLabel" style="padding: 5px;">&nbsp;{$TO_FAX}</td>
	<td class="dvtCellLabel" width="20%" rowspan="4"><div id="attach_cont_fax" class="addEventInnerBox" style="overflow:auto;height:140px;width:100%;position:relative;left:0px;top:0px;"></td>
   </tr>
	<td class="mailSubHeader" style="padding: 5px;" align="right">{$MOD.LBL_SUBJECT}</td>
	<td class="dvtCellLabel" style="padding: 5px;">&nbsp;{$elements.value}</td>
   </tr>
   <tr>
	<td colspan="3" class="dvtCellLabel" style="padding: 10px;" align="center"><input type="button" name="forward" value=" {$MOD.LBL_FORWARD_BUTTON} " alt="{$MOD.LBL_FORWARD_BUTTON}" title="{$MOD.LBL_FORWARD_BUTTON}" class="crmbutton small edit" onClick="parent.OpenComposeFax('{$ID}','forward')">&nbsp;
	<input type="button" title="{$APP.LBL_EDIT}" alt="{$APP.LBL_EDIT}" name="edit" value=" {$APP.LBL_EDIT} " class="crmbutton small edit" onClick="parent.OpenComposeFax('{$ID}','edit')">&nbsp;
	&nbsp;</td>
   </tr>
   {elseif $elements.fldname eq 'description'}
   <tr>
	<td style="padding: 5px;" colspan="3" valign="top"><div style="overflow:auto;height:415px;width:100%;">{$elements.value}</div></td>

   </tr>
   {elseif $elements.fldname eq 'filename'}
   <tr><td colspan="3">
   	<div id="attach_temp_cont_fax" style="display:none;">
		<table class="small" width="100% ">
		{foreach item=attachments from=$elements.options}
			<tr><td width="90%">{$attachments}</td></tr>	
		{/foreach}	
		</table>	
	</div>	
   </td></tr>	
   {/if}	
   {/foreach}
   {/foreach}

</table>		
<script>
$('attach_cont_fax').innerHTML = $('attach_temp_cont_fax').innerHTML;
jQuery(window).load(loadedPopup());
</script>