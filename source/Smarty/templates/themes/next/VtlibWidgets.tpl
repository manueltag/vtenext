{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}
 
{* crmv@115268 *}
{if $CUSTOM_LINKS && !empty($CUSTOM_LINKS.$WIDGETTYPE)}
	<table border=0 cellspacing=0 cellpadding=5 width=100% id="DetailViewWidgets"> {* crmv@104566 *}
	{assign var="widgetcount" value=0}
	{assign var="widgettotal" value=0}
	{foreach name=detailviewwidget item=CUSTOM_LINK_DETAILVIEWWIDGET from=$CUSTOM_LINKS.$WIDGETTYPE}
		{if preg_match("/^block:\/\/.*/", $CUSTOM_LINK_DETAILVIEWWIDGET->linkurl)}
			{php}
				$widgetLinkInfo_tmp = $this->_tpl_vars['CUSTOM_LINK_DETAILVIEWWIDGET'];
				if (preg_match("/^block:\/\/(.*)/", $widgetLinkInfo_tmp->linkurl, $matches)) {
					list($widgetControllerClass_tmp, $widgetControllerClassFile_tmp) = explode(':', $matches[1]);
					if (vtlib_isModuleActive($widgetControllerClass_tmp) || $widgetControllerClassFile_tmp == 'include/utils/DetailViewWidgets.php') {
			{/php}
			{assign var="widgettotal" value=$widgettotal+1}
			{if $CUSTOM_LINK_DETAILVIEWWIDGET->size eq 2}
				<tr class="detailWidgetCont">
					<td colspan="2" class="detailWidget" id="detailviewwidget{$smarty.foreach.detailviewwidget.iteration}">
						{include file='DetailViewWidgetHeader.tpl'}
						<div class="detailWidgetInner">
						{php}
							echo vtlib_process_widget($this->_tpl_vars['CUSTOM_LINK_DETAILVIEWWIDGET'], $this->_tpl_vars);
						{/php}
						</div>
					</td>
				</tr>
				<tr class="detailWidgetDivider"></tr>
			{elseif $CUSTOM_LINK_DETAILVIEWWIDGET->size eq 1}
				{if $widgetcount eq 0}	
			   		<tr valign="top" class="detailWidgetCont">
				{/if}
				{assign var="widgetcount" value=$widgetcount+1}
				<td width="50%" class="detailWidget" id="detailviewwidget{$smarty.foreach.detailviewwidget.iteration}">
					{include file='DetailViewWidgetHeader.tpl'}
					<div class="detailWidgetInner">
					{php}
						echo vtlib_process_widget($this->_tpl_vars['CUSTOM_LINK_DETAILVIEWWIDGET'], $this->_tpl_vars);
					{/php}
					</div>
				</td>
				{if $widgetcount eq 2}
					</tr>
					{assign var="widgetcount" value=0}
				{/if}
			{/if}
			{php}}}{/php}
		{/if}
	{/foreach}
	{if $widgettotal is not even}
		<td width="50%" id="detailviewwidget0"></td>
		</tr>
	{/if}
	</table>
{/if}