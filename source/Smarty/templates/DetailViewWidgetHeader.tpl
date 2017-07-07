{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}
 
{* crmv@119414 *}
{assign var=overrides value=$THEME_CONFIG.tpl_overrides}
{if !empty($overrides[$smarty.template])}
	{include file=$overrides[$smarty.template]}
	{php}return;{/php}
{/if}
{* crmv@119414e *} 

<table class="small" border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td class="dvInnerHeader" style="font-weight:bold;">
			{assign var=WIDGET_TITLE value=$CUSTOM_LINK_DETAILVIEWWIDGET|vtlib_widget_title}
			{if empty($WIDGET_TITLE)}
				{assign var=WIDGET_TITLE value=$CUSTOM_LINK_DETAILVIEWWIDGET->linklabel|getTranslatedString:$MODULE}
			{/if}
			{$WIDGET_TITLE}
		</td>
	</tr>
</table>
