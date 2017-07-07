{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@43942 *}

{if !empty($smarty.request.ajax)}
	&#&#&#{$ERROR}&#&#&#
{else}
	<script language="JavaScript" type="text/javascript" src="{"include/js/ListView.js"|resourcever}"></script>
	<script language="JavaScript" type="text/javascript" src="{"modules/Popup/Popup.js"|resourcever}"></script>
	{include file='Buttons_List.tpl'}
	{* crmv@82419 *}
	<div id="UnifiedSearchAreasUnifiedRow1_Cont" style="display:none;">
		<input type="hidden" id="basic_search_area" value="{$AREAID}" />
		<button class="crmbutton" onclick="callSearch('AreaGlobalSearch');" style="width:100%">{$APP.LBL_SEARCH_TITLE}{$MODULE|getTranslatedString:$MODULE}</button>
	</div>
	{* crmv@82419e *}
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
			jQuery('#unifiedsearchnew_query_string').val('{$QUERY_SCRIPT}');
			callSearch('AreaGlobalSearch');
		{else}
			basic_search_submitted = true;
			//jQuery('#unified_search_icn_canc').show();
		{/if}
	</script>
{/if}