{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@43611 *}

{if $RENDER_MODE neq 'ajax'}

<script type="text/javascript" src="include/js/SimpleListView.js"></script>
<style type="text/css">
	{literal}
	.popupLinkTitleRow {
	}
	.popupLinkListTitleRow {
	}
	.popupLinkListTitleCell {
		padding: 4px;
		font-weight: 700;
		border-bottom: 1px solid #b0b0b0;
		text-align: left;
	}
	.popupLinkListDataRow {
		cursor: pointer;
		text-align: left;
		height: 40px;
	}
	.popupLinkListDataRow0 {}
	.popupLinkListDataRow1 {}
	.popupLinkListDataRow:hover, .popupLinkListDataRow.hovered {}
	.popupLinkListDataExtraRow {
		padding: 6px;
	}
	.popupLinkListDataExtraCell {
		color:#606060;
		border-bottom:1px solid #d0d0d0;
		padding-bottom: 6px !important;
	}
	.popupLinkListDataCell {
		padding: 2px;
		border-bottom:1px solid #e0e0e0;
	}
	.popupLinkListCboxCell {
		width:20px;
	}
	.popupLinkTitle {
		font-weight: 700;
		padding: 4px;
	}
	.popupLinkList {
		overflow-y: auto;
		overflow-x: hidden;
	}
	.popupLinkListNoData {
		width: 90%;
		text-align: center;
		padding: 10px;
		font-style: italic;
	}
	.navigationBtn {
		cursor: pointer;
	}
	.SLVGreyLayer {
		background-color: rgba(250,250,250,0.5);
		left: 0px;
		top: 0px;
		z-index: 100;
	}
	form[name=basicSearch] {
		padding: 5px;
	}
	{/literal}
</style>

<div id="SLVContainer_{$LISTID}" style="position:relative">
{/if}

<div id="SLVGreyLayer" class="SLVGreyLayer" style="display:none;position:absolute;"></div>

<input type="hidden" name="mod" id="mod" value="{$MODULE}">
<input type="hidden" name="mod_label" id="mod_label" value="{$MODULE|getTranslatedString:$MODULE}">
<input type="hidden" name="mod_singlelabel" id="mod_singlelabel" value="{'SINGLE_'|cat:$MODULE|getTranslatedString:$MODULE}">

<div class="extraInputs">
	{if count($EXTRA_INPUTS) > 0}
	{foreach item=EINPUTVAL key=EINPUTKEY from=$EXTRA_INPUTS}
		<input type="hidden" name="{$EINPUTKEY}" value="{$EINPUTVAL}" />
	{/foreach}
	{/if}
</div>

<table border="0" class="small" width="100%" height="36px" style="border-bottom:1px solid #e0e0e0;"><tr>

	<td align="right" width="16px">

		{include file="LoadingIndicator.tpl" LIID="slv_busy_indicator" LIEXTRASTYLE="display:none;"}

	</td><td align="center">

		{if $SHOW_SEARCH eq true}

		{* basic search *}
		<form id="basicSearch" name="basicSearch" method="post" action="index.php" onSubmit="return SLV.search('{$LISTID}');">
			<input type="hidden" name="searchtype" value="BasicSearch" />
	        <input type="hidden" name="module" value="{$MODULE}" />
	        <input type="hidden" name="parenttab" value="{$CATEGORY}" />
			<input type="hidden" name="action" value="index" />
	        <input type="hidden" name="query" value="true" />
			<input type="hidden" id="basic_search_cnt" name="search_cnt" />
			{assign var=mod_label value=$MODULE|getTranslatedString:$MODULE}
			{assign var=search_placeholder value=$APP.LBL_SEARCH_TITLE|cat:$mod_label}
			<input type="hidden" id="search_placeholder" name="search_placeholder" value="{$search_placeholder}" />

			<table cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td>
						<div class="dvtCellInfo">
							<input type="text" id="basic_search_text" name="search_text" class="detailedViewTextBox" value="{if $LIST_SEARCH}{$LIST_SEARCH}{else}{$search_placeholder}{/if}" onclick="SLV.clear_search('{$LISTID}')" onblur="SLV.restore_search('{$LISTID}', '{$search_placeholder}')" />
						</div>
					</td>
					<td align="right" valign="bottom">
						<img id="basic_search_icn_canc" style="{if $LIST_SEARCH}{else}display:none{/if}" border="0" alt="Reset" title="Reset" style="cursor:pointer" onclick="SLV.cancel_search('{$LISTID}', '{$APP.LBL_SEARCH_TITLE}{$MODULE|getTranslatedString:$MODULE}')" src="{'close_little.png'|@vtiger_imageurl:$THEME}" />&nbsp;
					</td>
					<td>
						<img id="basic_search_icn_go" border="0" alt="{$APP.LBL_FIND}" title="{$APP.LBL_FIND}" style="cursor:pointer" onclick="jQuery(this).closest('form').submit();" src="{'UnifiedSearchButton.png'|@vtiger_imageurl:$THEME}" />
					</td>
				</tr>
			</table>
		</form>

		{/if}

	</td><td align="right" nowrap>
		{if $SHOW_CHECKBOXES}
			<input type="button" class="small crmbutton save" value="{$APP.LBL_ADD_SELECTED}" onclick="{$ADDSELECTED_FUNC}('{$LISTID}')" />
		{/if}
		{if $SHOW_CREATE}
			<input type="button" class="small crmbutton save" value="{$APP.LBL_CREATE}..." onclick="{$CREATE_FUNC}('{$LISTID}')" /> {* crmv@43050 *}
		{/if}
		{$EXTRA_BUTTONS_HTML}
	</td>

</tr></table>


{if count($LIST_ENTRIES) > 0}

	<table class="small" width="100%" cellspacing="0">
	
		{assign var=FIRSTCRMID value=""}
		{foreach name=listrowfor item=listrow key=crmid from=$LIST_ENTRIES}
			<tr id="row_{$crmid}" class="popupLinkListDataRow popupLinkListDataRow{cycle name=popupLinkCycle1 values='0,1'}{if $smarty.session.mynote_selected eq $crmid} lvtColDataHoverMessage{/if}" onclick="{$SELECT_FUNC}('{$LISTID}', '{$MODULE}', '{$crmid}', '')" {if $hasextrarow eq true}onmouseover="jQuery(this).next().addClass('hovered')" onmouseout="jQuery(this).next().removeClass('hovered')"{/if}>
				{if $SHOW_CHECKBOXES}
					<td class="popupLinkListDataCell popupLinkListCboxCell linkNoPropagate" onclick="jQuery(this).find('input').prop('checked', !jQuery('#list_cbox_{$crmid}').prop('checked'))"><input class="linkNoPropagate" type="checkbox" name="list_cbox_{$crmid}" id="list_cbox_{$crmid}" /></td>
				{/if}
				<td class="popupLinkListDataCell listMessageSubject">
					<div style="float:right">
						<span class="gray" style="font-size:11px;">{$listrow.1}</span>
					</div>
					<div style="float:left;overflow:hidden;">
						{$listrow.0}
					</div>
				</td>
			</tr>
			{if $hasextrarow eq true}
				<tr class="popupLinkListDataRow popupLinkListDataExtraRow popupLinkListDataRow{cycle name=popupLinkCycle2 values='0,1'}" onmouseover="jQuery(this).prev().addClass('hovered')" onmouseout="jQuery(this).prev().removeClass('hovered')" onclick="{$SELECT_FUNC}('{$LISTID}', '{$MODULE}', '{$crmid}', '')">
					<td colspan="{$LIST_MAXROWFIELDS}" class="popupLinkListDataCell popupLinkListDataExtraCell" >
						{foreach name=listrowforextra item=listcell from=$listrow}
							{if $smarty.foreach.listrowforextra.iteration > $LIST_MAXROWFIELDS}
								{$listcell}{if $smarty.foreach.listrowforextra.last eq false},{/if}
							{/if}
						{/foreach}
					</td>
				</tr>
			{/if}
		{/foreach}
	
	</table>
	
	<script type="text/javascript">
	{literal}
	// handler for details link
	jQuery('#linkMsgListCont .linkNoPropagate').click(function(e) {
		e.stopPropagation();
		e.cancelBubble = true;	// IE specific
		return true;
	});
	{/literal}
	</script>

{else}
	<div class="popupLinkListNoData">
	<p>{$APP.LBL_NO_DATA}</p>
	</div>
{/if}

<table border="0" class="small" width="100%" height="36px"><tr>

	<td align="left">

		{* filters *}
		{if $SHOW_FILTERS eq true}
		{$APP.LBL_VIEW}&nbsp;<SELECT NAME="viewname" id="viewname" class="small" onchange="SLV.change_filter('{$LISTID}')">{$CUSTOMVIEW_OPTION}</SELECT>
		{/if}

	</td><td align="center">

		{* navigation *}
		{if $LIST_TOT_PAGES > 1 && $SHOW_NAVIGATION eq true}
		<form id="paginationForm" name="paginationForm" method="POST" action="index.php" onsubmit="return SLV.go_to_page('{$LISTID}')">
			<input type="hidden" name="navigationPageOrig" id="navigationPageOrig" value="{$LIST_PAGE}">
			<input type="hidden" name="navigationPageTotal" id="navigationPageTotal" value="{$LIST_TOT_PAGES}">
			<table border="0" cellpadding="1" cellspacing="1"><tr>
				<td><img class="navigationBtn" src="{'start.gif'|@vtiger_imageurl:$THEME}" border="0" onclick="SLV.go_to_page('{$LISTID}', 1)"></td>
				<td><img class="navigationBtn" src="{'previous.gif'|@vtiger_imageurl:$THEME}" border="0" onclick="SLV.go_to_prev_page('{$LISTID}')"></td>
				<td>{$APP.Page} <input class="small" size="4" maxlength="4" type="text" name="navigationPage" id="navigationPage" value="{$LIST_PAGE}"> {$APP.LBL_LIST_OF} {$LIST_TOT_PAGES}</td>
				<td><img class="navigationBtn" src="{'next.gif'|@vtiger_imageurl:$THEME}" border="0" onclick="SLV.go_to_next_page('{$LISTID}')"></td>
				<td><img class="navigationBtn" src="{'end.gif'|@vtiger_imageurl:$THEME}" border="0" onclick="SLV.go_to_page('{$LISTID}', '{$LIST_TOT_PAGES}')"></td>
			</tr></table>
		</form>
		{/if}
		
	</td>
	
</tr></table>

{if $RENDER_MODE neq 'ajax'}
</div>
{/if}