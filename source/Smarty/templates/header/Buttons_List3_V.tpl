{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@102334 crmv@128159 *}
<div id="Buttons_List_3">
	<table id="bl3" border=0 cellspacing=0 cellpadding=2 width=100% class="small">{*crmv@22259*}
		<tr>
			<!-- Buttons -->
			<td style="padding:5px" nowrap>

				{* crmv@vte10usersFix *}
				{if $MODULE eq 'Calendar'}
					<ul class="vteUlTable" style="padding-right:5px">
						<li>
						 	<button type="button" class="crmbutton small edit" onclick="listToCalendar('Today')">{$MOD.LBL_DAY}</button>
						 	<button type="button" class="crmbutton small edit" onclick="listToCalendar('This Week')">{$MOD.LBL_WEEK}</button>
						 	<button type="button" class="crmbutton small edit" onclick="listToCalendar('This Month')">{$MOD.LBL_MON}</button>
						 	<button type="button" class="crmbutton small edit">{$MOD.LBL_CAL_TO_FILTER}</button>
						 	{*
							<i data-toggle="tooltip" data-placement="bottom" class="vteicon md-link" title="{$MOD.LBL_DAY}" onclick="listToCalendar('Today')">view_day</i>
							<i data-toggle="tooltip" data-placement="bottom" class="vteicon md-link" title="{$MOD.LBL_WEEK}" onclick="listToCalendar('This Week')">view_week</i>
							<i data-toggle="tooltip" data-placement="bottom" class="vteicon md-link" title="{$MOD.LBL_MON}" onclick="listToCalendar('This Month')">view_module</i>
							<i data-toggle="tooltip" data-placement="bottom" class="vteicon md-link" title="{$MOD.LBL_CAL_TO_FILTER}">view_list</i>
							*}
						</li>
					</ul>
				{/if}
				{* crmv@vte10usersFix e *}
                
				{include file="Buttons_List_Contestual.tpl"}
			
				{assign var="FLOAT_TITLE" value=$APP.LBL_MASSEDIT_FORM_HEADER}
				{assign var="FLOAT_WIDTH" value="760px"}
				{capture assign="FLOAT_BUTTONS"}
					<button type="button" title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmbutton small save" onclick="jQuery('#massedit_form input[name=action]').val('MassEditSave'); if (massEditFormValidate()) jQuery('#massedit_form').submit();" name="button" style="min-width:70px" >{$APP.LBL_SAVE_BUTTON_LABEL}</button>
				{/capture}
				{capture assign="FLOAT_CONTENT"}
				<div id="massedit_form_div" style="overflow:auto"></div>	{* crmv@34588 *}
				{/capture}
				{include file="FloatingDiv.tpl" FLOAT_ID="massedit"}

			</td>
			{* crmv@22259e *}
		</tr>
	</table>
</div>

<div id="UnifiedSearchAreasUnifiedRow1_Cont" style="display:none;">
	<form id="basicSearch" name="basicSearch" method="post" action="index.php">
		<input type="hidden" name="searchtype" value="BasicSearch" />
		<input type="hidden" name="module" value="{$MODULE}" />
		<input type="hidden" name="parenttab" value="{$CATEGORY}" />
		<input type="hidden" name="action" value="index" />
		<input type="hidden" name="query" value="true" />
		<input type="hidden" id="basic_search_cnt" name="search_cnt" />
		<table cellspacing="0" cellpadding="0" width="100%">
		<tr>
			<td width="100%">
				<button class="crmbutton" onclick="jQuery('#basicSearch').submit();" style="width:100%">{$APP.LBL_SEARCH_TITLE}{$MODULE|getTranslatedString:$MODULE}</button>
			</td>
			<td nowrap>
				<span class="advSearchIcon" style="padding-left:10px">
					<a href="jajascript:;" onclick="advancedSearchOpenClose();updatefOptions(document.getElementById('Fields0'), 'Condition0');">
						{$APP.LNK_ADVANCED_SEARCH}
						<i id="adv_search_icn_go" class="vteicon" title="{$APP.LNK_ADVANCED_SEARCH}" style="vertical-align:middle">keyboard_arrow_down</i>
					</a>
				</span>
			</td>
		</tr>
		</table>
		{include file="AdvancedSearch.tpl"}
	</form>
	{literal}
	<script type="text/javascript">
		var folderId = parseInt("{/literal}{$FOLDERID}{literal}");
		jQuery('#basicSearch').on('submit', function(e) {
			e.preventDefault();
			callSearch('BasicGlobalSearch', folderId);
			return false;
		});
	</script>
	{/literal}
</div>
<script type="text/javascript">
	calculateButtonsList3();
	{if $smarty.request.query eq true && $smarty.request.searchtype eq 'BasicSearch' && !empty($smarty.request.search_text)}
		clearText(jQuery('#unifiedsearchnew_query_string'),'unified_search_icn_canc');
		jQuery('#unifiedsearchnew_query_string').data('restored', false); // crmv@104119
		jQuery('#unifiedsearchnew_query_string').val('{$smarty.request.search_text}');
		basic_search_submitted = true;
	{/if}
</script>
