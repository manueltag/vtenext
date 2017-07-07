{*********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ********************************************************************************}

{* crmv@97692 *}
{* crmv@104853 *}

{include file='modules/SDK/src/Reference/Autocomplete.tpl'}	{* crmv@29190 *}

{if $smarty.request.mode eq 'DetailViewMyNotesWidget'}
	{include file="SmallHeader.tpl"}
	{include file='CachedValues.tpl'}
	<script type="text/javascript" src="{"include/js/dtlviewajax.js"|resourcever}"></script>
	<script type="text/javascript" src="modules/SDK/SDK.js"></script>
	<script type="text/javascript" src="modules/{$MODULE}/{$MODULE}.js"></script>
	<style type="text/css">
		{literal}
		.detailBlockHeader, .dvtTabCache, .dvtSelectedCell {
			display: none;
		}
		.dvtContentSpace {
			border: 0px;
		}
		.myNotesContainer {
			overflow-y: auto;
			height: 300px;
			width: 100%;
		}
		{/literal}
	</style>
	
	<table cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>
			<td width="30%" valign="middle">
				<table cellspacing="0" cellpadding="0" border="0" width="100%">
					<tr>
						<td>
							{if $CHECK.EditView eq 'yes' && $HIDE_BUTTON_CREATE neq true}
								<a href="javascript:;" onClick="MyNotesDVW.create('{$NOTEPARENTID}')"><i class="vteicon" title="{'LBL_CREATE'|getTranslatedString}">add</i></a>
							{/if}
						</td>
						<td>
							{include file="LoadingIndicator.tpl" LIID="vtbusy_info" LIEXTRASTYLE="display:none;"}
							{include file="LoadingIndicator.tpl" LIID="status" LIEXTRASTYLE="display:none;"}
						</td>
					</tr>
				</table>
			</td>
			
			<td valign="middle" align="right">
				{* crmv@44609 *}
				{if $SHOW_TURBOLIFT_CONVERT_BUTTON}
				<div style="">
					<button type="button" class="crmbutton small edit messageRightButton" onclick="javascript:top.LPOP.openPopup('{$MODULE}', '{$ID}', 'onlycreate', {ldelim}'callback_create':'LPOP.convert'{rdelim});" title="{'LBL_CONVERT_ACTION'|@getTranslatedString}">
						<i class="vteicon md-sm md-text" title="{'LBL_CONVERT_ACTION'|@getTranslatedString}">launch</i>
						<span style="padding:2px;" >{'LBL_CONVERT_ACTION'|@getTranslatedString}</span>
					</button>
				</div>
				{/if}
				{* crmv@44609e *}
			</td>
			
			<td valign="middle" align="right">
				<div style="">
					{if $DELETE}
						<input id="deleteNoteButton" type="button" value="{'LBL_DELETE'|getTranslatedString}" title="{'LBL_DELETE'|getTranslatedString}" class="crmbutton small delete" onclick="MyNotesDVW.delete('{$ID}','{$NOTEPARENTID}','{'NTC_DELETE_CONFIRMATION'|getTranslatedString}')">
					{/if}					
				</div>
			</td>
			
			<td valign="middle" align="right">
				<div style="">
					{if !empty($NAVIGATION.1)}
						<i class="vteicon md-link valign-bottom" style="width:30px" title="{$APP.LNK_LIST_PREVIOUS}" onclick="MyNotesDVW.load('{$NAVIGATION.1}','{$NOTEPARENTID}');" name="prevrecord">arrow_backward</i>
					{else}
						<i class="vteicon disabled valign-bottom" style="width:30px" title="{$APP.LNK_LIST_PREVIOUS}">arrow_backward</i>
					{/if}
					<span>{$NAVIGATION.0}</span>
					{if !empty($NAVIGATION.2)}
						<i class="vteicon md-link valign-bottom" title="{$APP.LNK_LIST_NEXT}" onclick="MyNotesDVW.load('{$NAVIGATION.2}','{$NOTEPARENTID}');" name="nextrecord">arrow_forward</i>
					{else}
						<i class="vteicon disabled valign-bottom" title="{$APP.LNK_LIST_NEXT}">arrow_forward</i>
					{/if}
				</div>
			</td>
		</tr>
	</table>
{/if}

<link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="jscalendar/calendar.js"></script>
<script type="text/javascript" src="jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="jscalendar/calendar-setup.js"></script>

<script type="text/javascript">
var gVTModule = '{$smarty.request.module|@vtlib_purify}';
var fieldname = new Array({$VALIDATION_DATA_FIELDNAME});
var fieldlabel = new Array({$VALIDATION_DATA_FIELDLABEL});
var fielddatatype = new Array({$VALIDATION_DATA_FIELDDATATYPE});
var fielduitype = new Array({$VALIDATION_DATA_FIELDUITYPE}); // crmv@83877
</script>

<span id="crmspanid" style="display:none;position:absolute;" onmouseover="show('crmspanid');">
   <a class="edit" href="javascript:;">{$APP.LBL_EDIT_BUTTON}</a>
</span>

<div class="myNotesContainer">
<table border=0 cellspacing=0 cellpadding=0 width=100% align=center>
<tr>
	<td valign="top" width=100%>
		<table border=0 cellspacing=0 cellpadding=0 width=100% align=center>
			<tr>
				<td valign="top">                
					<table border=0 cellspacing=0 cellpadding=0 width=100%>
						<tr>
							{* MAIN COLUMN (fields and related) *}
							<td align=left valign="top">
								<div style="padding:5px;">
									<form action="index.php" method="post" name="DetailView" id="form">
										{include file='DetailViewHidden.tpl'}
										<div id="DetailViewBlocks">
											{include file="DetailViewBlocks.tpl" SHOW_DETAILS_BUTTON=false}
										</div>
									</form>
								</div>
								{include file='RelatedListsHidden.tpl'}	{* crmv@54245 *}
								<div id="RelatedLists" {if empty($RELATEDLISTS)}style="display:none;"{/if}>
									{include file='RelatedListNew.tpl' PIN=true}
								</div>
								<div id="DynamicRelatedList" style="display:none;"></div>
								</form>	{* crmv@54245 close form opened in RelatedListsHidden.tpl *}
								{* vtlib Customization: Embed DetailViewWidget block:// type if any *}
								{include file='DetailViewWidgets.tpl'}
								{* END *}
							</td>
							{* RIGHT COLUMN (buttons, widget, turbolift, ...) *}
							{if $SHOW_TURBOLIFT neq 'no'}
								<td width="22%" valign="top" style="padding:5px 5px 0px 0px;" id="turboLiftContainer"> {* crmv@43864 *}
									{include file="DetailViewActions.tpl"}
									{include file='Turbolift.tpl'}
								</td>
							{/if}
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</td>
</tr>
</table>
</div>
{* crmv@55694 *}
{if $smarty.request.mode eq 'DetailViewMyNotesWidget'}
	<script type="text/javascript">
		var fieldtitle = jQuery('#DetailViewBlocks tr:first-child').height();
		jQuery('[name=description]').parent().parent().height(300-45-34-10-fieldtitle-10-14-15); //tab-img_raccoglitore-buttons-paddingtop-fieldtitle-tablepadding-fieldheaderdescription-variouspadding
		jQuery('[name=description]').parent().parent().css('overflow-y','auto');
		jQuery('[name=description]').height(jQuery('#dtlview_Nota').parent().height());
		jQuery('[name=description]').parent().parent().width('100%');
		jQuery('#Buttons_List').remove();
		jQuery('#Buttons_List_white').remove();
	</script>
	</body></html>
{/if}
{* crmv@55694e *}
