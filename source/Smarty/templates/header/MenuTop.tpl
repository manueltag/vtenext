{*/*+*************************************************************************************
* The contents of this file are subject to the VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with the License
* The Original Code is: VTECRM
* The Initial Developer of the Original Code is VTECRM LTD.
* Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
* All Rights Reserved.
***************************************************************************************/*}

{* crmv@119414 *}

{* crmv@82419 *}
{if $HIDE_MENUS neq true}	{* crmv@62447 *}
	<div id="vte_menu" class="navbar navbar-default" {if isMobile() neq true}style="position:fixed;"{/if}>	{* crmv@30356 *}
	    
	    {* crmv@23715 crmv@75301 *}
	    {if $smarty.session.menubar neq 'no' && $MENU_TPL}
			{include file=$MENU_TPL}
		{/if}
		{* crmv@23715e crmv@75301e *}
		
		<div class="drop_mnu" id="Preferences_sub" style="width:150px;">
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr><td><a class="drop_down" href="index.php?module=Users&action=DetailView&record={$CURRENT_USER_ID}&modechk=prefview">{$APP.LBL_PREFERENCES}</a></td></tr>
				<tr><td><a class="drop_down" href="index.php?module=Users&action=Logout">{$APP.LBL_LOGOUT}</a></td></tr>
				{if $HEADERLINKS}
					{foreach item=HEADERLINK from=$HEADERLINKS}
						{assign var="headerlink_href" value=$HEADERLINK->linkurl}
						{assign var="headerlink_label" value=$HEADERLINK->linklabel}
						{if $headerlink_label eq ''}
							{assign var="headerlink_label" value=$headerlink_href}
						{else}
							{assign var="headerlink_label" value=$headerlink_label|@getTranslatedString:$HEADERLINK->module()}
						{/if}
						<tr><td><a href="{$headerlink_href}" class="drop_down">{$headerlink_label}</a></td></tr>
					{/foreach}
				{/if}
			</table>
		</div>

		{* crmv@75301 *}
		{if $HEADER_OVERRIDE.post_menu_bar}
			{$HEADER_OVERRIDE.post_menu_bar}
		{/if}
		{* crmv@75301e *}
		
		<!-- Level 3 tabs starts -->
		{* crmv@22622 crmv@29079 crmv@37362 *}
		{if $smarty.cookies.crmvWinMaxStatus eq ''}
			{php}setWinMaxStatus();{/php}
			<script>setCookie('crmvWinMaxStatus','{$smarty.cookies.crmvWinMaxStatus}');</script>
		{/if}
		{if $smarty.cookies.crmvWinMaxStatus eq 'close'}
			{assign var="minImg" value="_min"}
			{assign var="minIcon" value=""}
		{else}
			{assign var="minImg" value=""}
			{assign var="minIcon" value="md-lg"}
		{/if}
		{if $smarty.cookies.crmvWinMaxStatus eq 'close'}
			{assign var="orangeTableHeight" value="38"}
		{else}
			{assign var="orangeTableHeight" value="57"}
		{/if}
		<div id="orange" class="winMaxAnimate">{* crmv@21996 crmv@30356 crmv@98866 *}
		<table id="orangeTable" border=0 cellspacing=0 cellpadding=0 class="level2Bg" width="100%">
		<tr>
			<td>
				<table border=0 cellspacing=0 cellpadding=0 width="100%" height="{$orangeTableHeight}px">
					<tr>
						<td><div id="Buttons_List_SiteMap" class="winMaxWait"></div></td>
						<td><div id="Buttons_List_Fixed"></div></td>
						<td>
							<div id="Buttons_List_QuickCreate" style="display: none">
								<a href="javascript:;">
									<i data-toggle="tooltip" data-placement="top" class="vteicon {$minIcon}" title="{$APP.LBL_QUICK_CREATE}" onclick="showFloatingDiv('Create_sub', this);">flash_on</i>
								</a>	{* crmv@31197 *} {* crmv@82419 *}
							</div>
						</td>
						<td><div id="Buttons_List_Contestual" style="display:none;" {if $smarty.cookies.crmvWinMaxStatus eq 'close'}class="ButtonsListContestualSmall"{else}class="ButtonsListContestualLarge"{/if}></div></td>	{* crmv@2963m *}
						<td width="100%" align="right">
							{* crmv@82419 *}
							<div class="globalSearch">
								<form name="UnifiedSearchNew" onSubmit="UnifiedSearchAreasObj.show(document.getElementById('orange'),'search');return false;">
								<input type="text" id="unifiedsearchnew_query_string" name="query_string" value="{$QUERY_STRING}" class="form-control searchBox" onFocus="this.value='';" onBlur="if(this.value=='')this.value='{"LBL_GLOBAL_SEARCH_STRING"|getTranslatedString}';">	{* crmv@31197 *}
								<span class="searchIcon">
									<i class="vteicon" onClick="UnifiedSearchAreasObj.show(document.getElementById('orange'),'search');">search</i>
								<span>
								</form>
							</div>
							{* crmv@82419e *}
							<div style="float:right;padding:4px 8px 0px 0px;">
								<div id="status" style="display:none;">{include file="LoadingIndicator.tpl"}</div>
							</div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		</table>
		</div>{* crmv@21996 *}
		{* crmv@22622e crmv@29079e crmv@37362e *}
		<!-- Level 3 tabs ends -->

		{* crmv@75301 *}
		{if $HEADER_OVERRIDE.post_primary_bar}
			{$HEADER_OVERRIDE.post_primary_bar}
		{/if}
		{* crmv@75301e *}
		
		<!-- Level 4 tabs starts -->
		<div id="Buttons_List_3" class="level4Bg" style="display:none;"></div>
		<!-- Level 4 tabs ends -->

		{* crmv@75301 *}
		{if $HEADER_OVERRIDE.post_secondary_bar}
			{$HEADER_OVERRIDE.post_secondary_bar}
		{/if}
		{* crmv@75301e *}
	</div>
	
	{if isMobile() neq true}
		<div id="vte_menu_white" class="winMaxAnimate"></div>
		<script>jQuery('#vte_menu_white').height(jQuery('#vte_menu').height());</script>
	{/if}
{else}
	{if isset($smarty.request.page_title)}
		{assign var="PAGE_TITLE" value=$smarty.request.page_title|@getTranslatedString:$MODULE}
		{assign var="OP_MODE" value=$smarty.request.op_mode}
	{* crmv@68357 *}
	{elseif $smarty.request.useical eq 'true'}
		{assign var="PAGE_TITLE" value='LBL_PREVIEW_INVITATION'|@getTranslatedString:$MODULE} 
		{assign var="CAL_MODE" value='on'}
		{assign var="OP_MODE" value='calendar_preview_buttons'}
	{else}
		{if $smarty.request.activity_mode eq 'Events'}
			{assign var="PAGE_TITLE" value='LBL_ADD'|@getTranslatedString:$MODULE}
		{else}
			{assign var="PAGE_TITLE" value='LBL_ADD_TODO'|@getTranslatedString:$MODULE}
		{/if}
		{assign var="CAL_MODE" value='on'}
		{assign var="OP_MODE" value='calendar_buttons'}
    {/if}
    {* crmv@68357e *}
	{include file='SmallHeader.tpl'}
	{include file='Buttons_List4.tpl'}
	<div id="Buttons_List_3" class="level4Bg" style="display:none;"></div>
{/if}
{* crmv@62447e *}
{* crmv@82419e *}