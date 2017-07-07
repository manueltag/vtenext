{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@16265 crmv@34885 crmv@2281m crmv@22952 crmv@43942 crmv@44723 crmv@54707 crmv@82419 crmv@126984 *}
{if $MENU_LAYOUT.type eq 'modules'}
    <div id="vte_main_menu" class="navbar-collapse collapse navbar-responsive-collapse">
		<ul class="nav navbar-nav">
			<li>
			{php}
			// define this function (SDK::setUtil) to override the logo with anything
			if (function_exists('get_logo_override')) echo get_logo_override('project'); else { global $enterprise_project; if (!empty($enterprise_project)) echo '<img src="'.get_logo('project').'" border="0">'; }
			{/php}
			</li>
		{foreach item=info from=$VisibleModuleList}
			{assign var="label" value=$info.name|@getTranslatedString:$info.name}
			{assign var="url" value="index.php?module="|cat:$info.name|cat:"&amp;action=index"}
			{if $info.name eq $MODULE_NAME}
				<li class="active"><a href="{$url}">{$label}</a></li>
			{else}
				<li class=""><a href="{$url}">{$label}</a></li>
			{/if}
		{/foreach}
		{if $LAST_MODULE_VISITED neq ''}
			{if $LAST_MODULE_VISITED eq $MODULE_NAME}
				<li class="active"><a href="index.php?module={$LAST_MODULE_VISITED}&amp;action=index">{$LAST_MODULE_VISITED|@getTranslatedString:$LAST_MODULE_VISITED}</a></li>
			{else}
				<li class=""><a href="index.php?module={$LAST_MODULE_VISITED}&amp;action=index">{$LAST_MODULE_VISITED|@getTranslatedString:$LAST_MODULE_VISITED}</a></li>
			{/if}
		{/if}
		{if $ENABLE_AREAS eq '1'}
			<li class="dropdown" onClick="UnifiedSearchAreasObj.show(this,'list');"><a href="javascript:void(0);">{$APP.LBL_AREAS} <b class="caret"></b></a></li>
		{/if}
		{if isset($OtherModuleList)}
			<li class="dropdown" onClick="AllMenuObj.showAllMenu(this);"><a href="javascript:void(0);">{$APP.LBL_MODULES} <b class="caret"></b></a></li>
		{/if}
		</ul>
		<ul class="nav navbar-nav navbar-right">
			{if $smarty.session.MorphsuitZombie eq false && $IS_ADMIN eq '1'}
				{* crmv@75301 *}
				{if $HEADER_OVERRIDE.settings_icon}
					{$HEADER_OVERRIDE.settings_icon}
				{else}
					<li class="shrink">
						<a href="index.php?module=Settings&amp;action=index&amp;parenttab=Settings&amp;reset_session_menu_tab=true">
							<img src="{'settingsBtn.png'|@vtiger_imageurl:$THEME}" title="{'Settings'|getTranslatedString:'Settings'}" border=0>
						</a>
					</li>
				{/if}
				{* crmv@75301e *}
			{/if}
			
			{if !$ISVTEDESKTOP}
			 	{* crmv@75301 *}
				{if $HEADER_OVERRIDE.user_icon}
					{$HEADER_OVERRIDE.user_icon}
				{else}
					<li class="shrink" onclick="showOverAll(this,'Preferences_sub');">
						<a>{$CURRENT_USER_ID|getUserAvatarImg:"style='cursor:pointer;'":'menu'}</a>
					</li>
				{/if}
				{* crmv@75301e *}
			{/if}
			<li class="shrink">
				<a><img id="logo" src="{php}echo get_logo('header');{/php}" alt="{$APP.LBL_BROWSER_TITLE}" title="{$APP.LBL_BROWSER_TITLE} - {php}if (function_exists('getVTENumberUserLabel')) echo getVTENumberUserLabel();{/php}" border=0></a>
			</li>
			<li>
			</li>
		</ul>
    </div>
    
	<div class="drop_mnu_all" id="OtherModuleList_sub">
		{include file="header/HeaderAllModules.tpl" }
	</div>
	{* scripts *}
	<script type="text/javascript" src="include/js/AllMenu.js"></script>
	<script type="text/javascript">
		AllMenuObj.initialize();
	</script>
	
{* crmv@82419e *}
{elseif $MENU_LAYOUT.type eq 'tabs'}

	<div id="vte_main_menu" class="navbar-collapse collapse navbar-responsive-collapse">
		<ul class="nav navbar-nav">
			<li>
			{php}
			// define this function (SDK::setUtil) to override the logo with anything
			if (function_exists('get_logo_override')) echo get_logo_override('project'); else { global $enterprise_project; if (!empty($enterprise_project)) echo '<img src="'.get_logo('project').'" border="0">'; }
			{/php}
			</li>
			{foreach key=maintabs item=detail from=$HEADERS}
				{if $maintabs ne $CATEGORY}
					<li class="dropdown" onmouseover="fnDropDown(this,'{$maintabs}_sub');" onmouseout="fnHideDrop('{$maintabs}_sub');"><a href="index.php?module={$detail[0]}&amp;action=index&amp;parenttab={$maintabs}">{$APP[$maintabs]} <b class="caret"></b></a></li>
				{else}
					<li class="dropdown active" onmouseover="fnDropDown(this,'{$maintabs}_sub');" onmouseout="fnHideDrop('{$maintabs}_sub');"><a href="index.php?module={$detail[0]}&amp;action=index&amp;parenttab={$maintabs}">{$APP[$maintabs]} <b class="caret"></b></a></li>
				{/if}
			{/foreach}
			{if $ENABLE_AREAS eq '1'}
				<li class="dropdown" onClick="UnifiedSearchAreasObj.show(this,'list');"><a href="javascript:void(0);">{$APP.LBL_AREAS} <b class="caret"></b></a></li>
			{/if}
		</ul>
		<ul class="nav navbar-nav navbar-right">
			{if $smarty.session.MorphsuitZombie eq false && $IS_ADMIN eq '1'}
				{* crmv@75301 *}
				{if $HEADER_OVERRIDE.settings_icon}
					{$HEADER_OVERRIDE.settings_icon}
				{else}
					<li class="shrink">
						<a href="index.php?module=Settings&amp;action=index&amp;parenttab=Settings&amp;reset_session_menu_tab=true">
							<img src="{'settingsBtn.png'|@vtiger_imageurl:$THEME}" title="{'Settings'|getTranslatedString:'Settings'}" border=0>
						</a>
					</li>
				{/if}
				{* crmv@75301e *}
			{/if}
			
			{if !$ISVTEDESKTOP}
			 	{* crmv@75301 *}
				{if $HEADER_OVERRIDE.user_icon}
					{$HEADER_OVERRIDE.user_icon}
				{else}
					<li class="shrink" onclick="showOverAll(this,'Preferences_sub');">
						<a>{$CURRENT_USER_ID|getUserAvatarImg:"style='cursor:pointer;'":'menu'}</a>
					</li>
				{/if}
				{* crmv@75301e *}
			{/if}
			<li class="shrink">
				<a><img id="logo" src="{php}echo get_logo('header');{/php}" alt="{$APP.LBL_BROWSER_TITLE}" title="{$APP.LBL_BROWSER_TITLE} - {php}if (function_exists('getVTENumberUserLabel')) echo getVTENumberUserLabel();{/php}" border=0></a>
			</li>
			<li>
			</li>
		</ul>
	</div>
	
	<TABLE border=0 cellspacing=0 cellpadding=2 width=100% class="level2Bg">
	<tr>
		<td>
			<table border=0 cellspacing=0 cellpadding=0>
			<tr>
				{foreach key=number item=modules from=$QUICKACCESS.$CATEGORY}
					{assign var="modulelabel" value=$modules[1]|@getTranslatedString:$modules[0]}
   					{* Use Custom module action if specified *}
					{assign var="moduleaction" value="index"}
   					{if isset($modules[2])}
   						{assign var="moduleaction" value=$modules[2]}
   					{/if}
					{if $modules.0 eq $MODULE_NAME}
						<td class="level2SelTab" nowrap><a href="index.php?module={$modules.0}&amp;action={$moduleaction}&amp;parenttab={$CATEGORY}">{$modulelabel}</a></td>
					{else}
						<td class="level2UnSelTab" nowrap> <a href="index.php?module={$modules.0}&amp;action={$moduleaction}&amp;parenttab={$CATEGORY}">{$modulelabel}</a> </td>
					{/if}
				{/foreach}
			</tr>
			</table>
		</td>
	</tr>
	</TABLE>
	
	{foreach name=parenttablist key=parenttab item=details from=$QUICKACCESS}
		<div class="drop_mnu" id="{$parenttab}_sub" onmouseout="fnHideDrop('{$parenttab}_sub')" onmouseover="fnShowDrop('{$parenttab}_sub')" style="width:150px;">
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				{foreach name=modulelist item=modules from=$details}
					{assign var="modulelabel" value=$modules[1]|@getTranslatedString:$modules[0]}
					{* Use Custom module action if specified *}
					{assign var="moduleaction" value="index"}
				   	{if isset($modules[2])}
				   		{assign var="moduleaction" value=$modules[2]}
				   	{/if}
					<tr><td><a href="index.php?module={$modules.0}&amp;action={$moduleaction}&amp;parenttab={$parenttab}" class="drop_down">{$modulelabel}</a></td></tr>
				{/foreach}
			</table>
		</div>
	{/foreach}
{/if}