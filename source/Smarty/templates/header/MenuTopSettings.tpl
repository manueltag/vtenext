<!--

/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/

-->

{* crmv@120738 *}
{assign var=overrides value=$THEME_CONFIG.tpl_overrides}
{if !empty($overrides[$smarty.template])}
	{include file=$overrides[$smarty.template]}
	{php}return;{/php}
{/if}
{* crmv@120738 *}

{php}
	//add the settings page values
	$this->assign("BLOCKS",getSettingsBlocks());
	$this->assign("FIELDS",getSettingsFields());
	// crmv@30683
	global $theme;
	$this->assign("THEME", $theme);  
	if ($_REQUEST['reset_session_menu']) {
		unset($_SESSION['settings_last_menu']);
	}
	if ($_REQUEST['reset_session_menu_tab']) {
		unset($_SESSION['settings_last_menu']);
		$_SESSION['settings_last_menu'] = 'LBL_USERS';
	}

	// crmv@30683e
	{/php}

<table border=0 cellspacing=0 cellpadding=5 width="100%" class="settingsUI">
	<tr>
		<td valign=top>
			<table border=0 cellspacing=0 cellpadding=0 width=100%>
				<tr>
					<td valign=top id="settingsSideMenu" width="10%" >
						<!--Left Side Navigation Table-->
						<table border=0 cellspacing=0 cellpadding=0 width="100%">
{assign var=test value=''}
{foreach key=BLOCKID item=BLOCKLABEL from=$BLOCKS}
	{if $BLOCKLABEL neq 'LBL_MODULE_MANAGER'}
	{assign var=blocklabel value=$BLOCKLABEL|@getTranslatedString:'Settings'}
										<tr>
								<td class="settingsTabHeader" nowrap>
									{$blocklabel}
								</td>
							</tr>
							
		{foreach item=data from=$FIELDS.$BLOCKID}
			{if $data.link neq ''}
				{assign var=label_original value=$data.name} {* crmv@30683 *}
				{assign var=label value=$data.name|@getTranslatedString:'Settings'}
				{* crmv@22660 *}
				{assign var='settingsTabClass' value='settingsTabList'}
				{if $smarty.request.module_settings eq 'true' && $smarty.request.formodule eq $data.formodule
					&& $smarty.request.action eq $data.action && $smarty.request.module eq $data.module}
					{assign var='settingsTabClass' value='settingsTabSelected'}
					{php}$_SESSION['settings_last_menu'] = $this->_tpl_vars['label_original'];{/php} {* crmv@30683 *}
				{elseif $smarty.request.module_settings eq '' && $data.formodule eq ''
					&& $smarty.request.action eq $data.action && $smarty.request.module eq $data.module}
					{assign var='settingsTabClass' value='settingsTabSelected'}
					{php}$_SESSION['settings_last_menu'] = $this->_tpl_vars['label_original'];{/php} {* crmv@30683 *}
				{* crmv@30683 *}	
				{elseif $smarty.session.settings_last_menu eq $data.name}
					{assign var='settingsTabClass' value='settingsTabSelected'}
				{* crmv@30683e  *}
				{/if}
				<tr>
					<td class="{$settingsTabClass}" nowrap>
						{*//crmv@31817*}
						<a href="{$data.link}&reset_session_menu=true">
							{if $data.icon|strpos:".png" !== false}
								{assign var=icon value=$data.icon|@replace:'.png':'_small.png'}
							{else}	
								{assign var=icon value=$data.icon|@replace:'.gif':'_small.png'}
							{/if}
							<img border="0" src="{$icon|@vtiger_imageurl:$THEME}" align="top">
						</a>			
						<a href="{$data.link}&reset_session_menu=true">
						<!-- crmv@30683  -->
							{$label} 
						<!-- crmv@30683e  -->
						</a>
						{*//crmv@31817e*}
					</td>
				</tr>
				{* crmv@22660e *}
			{/if}
		{/foreach}
	{/if}
{/foreach}
						</table>
						<!-- Left side navigation table ends -->
		
					</td>
					<td width="8px" valign="top"> 
						<i class="vteicon" title="Hide Menu" id="hideImage" style="display:inline;cursor:pointer;" onclick="toggleShowHide_panel('showImage','settingsSideMenu'); toggleShowHide_panel('showImage','hideImage');">arrow_back</i>
						<i class="vteicon" title="Show Menu" id="showImage" style="display:none;cursor:pointer;" onclick="toggleShowHide_panel('settingsSideMenu','showImage'); toggleShowHide_panel('hideImage','showImage');">arrow_forward</i>
					</td>
					<td class="small settingsSelectedUI" valign=top align=left>
						<script type="text/javascript">
{literal}
							function toggleShowHide_panel(showid, hideid){
								var show_ele = document.getElementById(showid);
								var hide_ele = document.getElementById(hideid);
								if(show_ele != null){ 
									show_ele.style.display = "";
									}
								if(hide_ele != null) 
									hide_ele.style.display = "none";
							}
{/literal}
						</script>