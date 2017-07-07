{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}
 
{* crmv@104853 *}

{include file="SmallHeader.tpl"}
{include file='CachedValues.tpl'}
<script type="text/javascript" src="modules/SDK/SDK.js"></script>
<script type="text/javascript" src="modules/{$MODULE}/{$MODULE}.js"></script>
<style type="text/css">
	{literal}
	.detailedViewHeader, .dvtTabCache, .dvtSelectedCell, .dvtCellLabel {
		display: none;
	}
	.dvtContentSpace {
		border: 0px;
	}
	.dvtCellInfoM {
		background-color: #ffffff;
	}
	.dvtCellInfoM:hover {
		background-color: #f5f9fc;
	}
	{/literal}
</style>

<table cellspacing="0" cellpadding="0" border="0" width="100%" style="padding-top:10px;">
	<tr>
		{* crmv@97209 *}
		<td width="7" style="padding:0px"><img src="{'MyNotesWidgetSx.png'|@vtiger_imageurl:$THEME}" /></td>
		<td width="100%" style="background-image:url({'MyNotesWidgetCenter.png'|@vtiger_imageurl:$THEME});background-repeat:repeat-x;">&nbsp;</td>
		<td width="7" style="padding:0px"><img src="{'MyNotesWidgetDx.png'|@vtiger_imageurl:$THEME}" /></td>
		{* crmv@97209e *}
	</tr>
</table>

<table cellspacing="0" cellpadding="0" border="0" width="100%" class="rightMailMerge" style="border-top:0px;padding:14px 4px 0px 4px;">
	<tr valign="middle" height="34">
		<td width="50%" align="left" style="padding-left:10px">
			{include file="LoadingIndicator.tpl" LIID="vtbusy_info" LIEXTRASTYLE="display:none;"}
		</td>
		<td width="50%" align="right">
			<div style="float:right;">
				<input type="button" value="{'LBL_SAVE_LABEL'|getTranslatedString}" title="{'LBL_SAVE_LABEL'|getTranslatedString}" class="crmbutton small save" onclick="MyNotesDVW.save('{$PARENT}')">
			</div>
		</td>
	</tr>
	<tr><td colspan="2">{$CODE}</td></tr>
</table>

{literal}
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('[name="subject"]').parent().parent().parent().hide();	// hide tr that contain subject field
	jQuery('[name="description"]').height(300-45-34-10-30);
});
</script>
{/literal}