{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@43942 *}

{* crmv@119414 *}
{assign var=overrides value=$THEME_CONFIG.tpl_overrides}
{if !empty($overrides[$smarty.template])}
	{include file=$overrides[$smarty.template]}
	{php}return;{/php}
{/if}
{* crmv@119414e *} 

{if !empty($smarty.request.ajax)}
	&#&#&#{$ERROR}&#&#&#
{else}
	<script language="JavaScript" type="text/javascript" src="{"include/js/ListView.js"|resourcever}"></script>
	<script language="JavaScript" type="text/javascript" src="{"modules/Popup/Popup.js"|resourcever}"></script>
	{include file='Buttons_List.tpl'}
	<div id="Buttons_List_3_Container" style="display:none;">
		<table id="bl3" border=0 cellspacing=0 cellpadding=2 width=100% class="small">
			<tr height="34">
				<td align="right" width="100%" style="padding-right:5px;">
					{* crmv@82419 *}
					<form id="basicSearch" name="basicSearch" method="post" action="index.php" onSubmit="return callSearch('Area');">
						<div class="form-group basicSearch">
							<input type="hidden" name="area" id="basic_search_area" value="{$AREAID}" />
							<input type="hidden" name="module" value="Area" />
							<input type="hidden" name="action" value="index" />
							<input type="hidden" name="ajax" value="true" />

							<input type="text" class="form-control searchBox" id="basic_search_text" name="search_text" value="{if $QUERY_SCRIPT neq ''}{$QUERY_SCRIPT}{else}{$APP.LBL_SEARCH_TITLE}{$AREALABEL}{/if}" onclick="clearText(this)" onblur="restoreDefaultText(this, '{$APP.LBL_SEARCH_TITLE}{$AREALABEL}')" />
							<span class="cancelIcon">
								<i class="vteicon md-sm md-link" id="basic_search_icn_canc" onclick="cancelAreaSearchText('{$APP.LBL_SEARCH_TITLE}{$AREALABEL}')" title="Reset" style="display:none">cancel</i>&nbsp;
							</span>
							<span class="searchIcon">
								<i class="vteicon md-link" id="basic_search_icn_go" onclick="jQuery('#basicSearch').submit();" title="{$APP.LBL_FIND}">search</i>
							</span>
						</div>
					</form>
					{* crmv@82419e *}
				</td>
			</tr>
		</table>
	</div>
	<script type="text/javascript">calculateButtonsList3();</script>
{/if}
{if empty($smarty.request.ajax)}
	<div id="ListViewContents">
{/if}
{if !empty($MODULES)}
	<table border="0" cellspacing="0" cellpadding="0" width="100%" class="small" align="center">
		<tr bgcolor="#FFFFFF" valign="top">
			{foreach name=areamodules item=module from=$MODULES}
				{math equation=x/y x=100 y=$smarty.foreach.areamodules.total format=%d assign=width}
				<td width="{$width}%">
					{include file='modules/Area/Module.tpl'}
				</td>
			{/foreach}
		</tr>
	</table>
{/if}
{if empty($smarty.request.ajax)}
	</div>
{/if}
{if $QUERY_SCRIPT neq ''}
	<script type="text/javascript">
		{if $AJAXCALL eq true}
			jQuery('#basicSearch').submit();
		{else}
			basic_search_submitted = true;
			jQuery('#basic_search_icn_canc').show();
		{/if}
	</script>
{/if}