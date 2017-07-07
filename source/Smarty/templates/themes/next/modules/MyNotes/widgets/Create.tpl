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
{include file='modules/SDK/src/Reference/Autocomplete.tpl'}	{* crmv@29190 *}
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
	#noteSaveBtn {
		visibility: hidden;
	}
	textarea[name="description"] {
		background-color: #f5f5f5;
	}
	.dvtContentSpace .blocksContainer {
		border: 0px none;
		box-shadow: none;
		padding: 0px;
	}
	.dvtContentSpace .dvtContentSpaceInner {
		padding: 0px;
	}
	{/literal}
</style>

<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr valign="middle">
		<td width="50%" align="left">
			{include file="LoadingIndicator.tpl" LIID="vtbusy_info" LIEXTRASTYLE="display:none;"}
		</td>
		<td width="50%" align="right">
			<div style="float:right;">
				<input id="noteSaveBtn" type="button" value="{'LBL_SAVE_LABEL'|getTranslatedString}" title="{'LBL_SAVE_LABEL'|getTranslatedString}" class="crmbutton small save" onclick="MyNotesDVW.save('{$PARENT}')">
			</div>
		</td>
	</tr>
	<tr><td colspan="2">{$CODE}</td></tr>
</table>

{literal}
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('[name="subject"]').parent().parent().parent().hide();	// hide tr that contain subject field
	jQuery('[name="description"]').height(300-45);
	jQuery('#Buttons_List').remove();
	jQuery('#Buttons_List_white').remove();
	
	var description = jQuery('[name="description"]');
	var btnSave = jQuery('#noteSaveBtn');
	
	description.focus(function() {
		btnSave.css('visibility', 'visible');
	}).blur(function() {
		var descVal = description.val();
		if (descVal.length < 1) {
			btnSave.css('visibility', 'hidden');
		}
	});
});
</script>
{/literal}
