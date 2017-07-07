{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@27020 crmv@38592 *}

{if $HEAD_INCLUDE}
{include file="HTMLHeader.tpl" head_include=$HEAD_INCLUDE}
{else}
{include file="HTMLHeader.tpl" head_include="icons,jquery,jquery_plugins,jquery_ui,fancybox,prototype,jscalendar"}
{/if}

<body leftmargin=0 topmargin=0 marginheight=0 marginwidth=0 class=small>

{include file="Theme.tpl" THEME_MODE="body"}

<div id="popupContainer" style="display:none;"></div>
{* crmv@21048m-e crmv@82419e *}

{* crmv@62447 *}
{if $PAGE_TITLE neq 'SKIP_TITLE'}
	{if $CAL_MODE eq 'on'}
		<table id="vte_menu_head" width="100%" cellspacing="0" cellpadding="0" border="0" style="position: fixed; z-index: {if $HEADER_Z_INDEX > 0}{$HEADER_Z_INDEX}{else}0{/if};{if $PAGE_TITLE eq ''}padding: 5px;{/if}"> {* crmv@42752 *}
		<tr>
			<td width="100%" class="mailClientWriteEmailHeader level2Bg menuSeparation">
				<h4 style="float:left;"><a href="javascript:;" onclick="{$PAGE_TITLE_LINK}">{$PAGE_TITLE}</a></h4>
				{if !empty($PAGE_SUB_TITLE)}
					<h5 style="float:left;">&gt; <a href="javascript:;" onclick="{$PAGE_SUB_TITLE_LINK}" style="font-size:14px;" class="hdrLink">{$PAGE_SUB_TITLE}</a></h5>
				{/if}
				<h5 style="float:right;">{$PAGE_RIGHT_TITLE}</h5>
			</td>
		</tr>
		</table>
		<div id="vte_menu_white"></div>
	{else}
		<table id="vte_menu" width="100%" cellspacing="0" cellpadding="0" border="0" style="position: fixed; z-index: {if $HEADER_Z_INDEX > 0}{$HEADER_Z_INDEX}{else}0{/if};{if $PAGE_TITLE eq ''}padding: 5px;{/if}"> {* crmv@42752 *}
		<tr>
			<td width="100%" class="mailClientWriteEmailHeader level2Bg menuSeparation">
				<h4 style="float:left;"><a href="javascript:;" onclick="{$PAGE_TITLE_LINK}">{$PAGE_TITLE}</a></h4>
				{if !empty($PAGE_SUB_TITLE)}
					<h5 style="float:left;">&gt; <a href="javascript:;" onclick="{$PAGE_SUB_TITLE_LINK}" style="font-size:14px;" class="hdrLink">{$PAGE_SUB_TITLE}</a></h5>
				{/if}
				<h5 style="float:right;">{$PAGE_RIGHT_TITLE}</h5>
			</td>
		</tr>
		</table>
		<div id="vte_menu_white"></div>
	{/if}
{/if}
{* crmv@62447e *}

{if !empty($BUTTON_LIST)}
<div id="Buttons_List" class="level3Bg {$BUTTON_LIST_CLASS}" style="position:fixed; width: 100%; {if $HEADER_Z_INDEX > 0}z-index:{$HEADER_Z_INDEX};{else}z-index:0;{/if};">{$BUTTON_LIST}</div>	{* crmv@92272 *}
<div id="Buttons_List_white"></div>
{/if}

<script type="text/javascript">
	jQuery(window).ready(function(){ldelim}
		setTimeout(function(){ldelim}
			{if $PAGE_TITLE neq 'SKIP_TITLE'}
				{if $CAL_MODE eq 'on'}
		    		jQuery('#vte_menu_white').height(jQuery('#vte_menu_head').outerHeight());
		    	{else}
		    		jQuery('#vte_menu_white').height(jQuery('#vte_menu').outerHeight());
				{/if}
			{/if}
	    	jQuery('#Buttons_List_white').height(jQuery('#Buttons_List').outerHeight());
	    {rdelim},0);
	{rdelim});
    jQuery(window).load(function(){ldelim}
		loadedPopup();
		bindButtons(window.top);	//crmv@59626
    {rdelim});
</script>
