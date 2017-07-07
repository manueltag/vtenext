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
<!-- crmv@18549 crmv@19842 crmv@128159 -->
{if $MODULE neq ''}
	<script type="text/javascript" src="modules/{$MODULE}/{$MODULE}.js"></script>
{/if}
{* crmv@22622 *}
{if $smarty.cookies.crmvWinMaxStatus eq 'close'}
	{assign var="minImg" value="_min"}
	{assign var="minFontSize" value="font-size:14px;"}
	{assign var="minIcon" value=""}
{else}
	{assign var="minImg" value=""}
	{assign var="minFontSize" value=""}
	{assign var="minIcon" value="md-lg"}
{/if}
{* crmv@30356 *}
{if isMobile() neq true}
<div id="Buttons_List_SiteMap_Container" style="display:none;">
	<table border=0 cellspacing=0 cellpadding=5 class=small>
	<tr>
		{assign var="MODULELABEL" value=$MODULE|@getTranslatedString:$MODULE}
		{* crmv@20209 *}
		{if $smarty.request.module eq 'Users' || $smarty.request.module eq 'Administration'}
			{assign var=MODULE value=Users}
			{assign var="MODULELABEL" value=$MODULE|@getTranslatedString:$MODULE}
			{assign var=CATEGORY value=$smarty.request.parenttab}
			<td style="padding-left:10px;padding-right:50px" class="moduleName" nowrap><a class="hdrLink" style="{$minFontSize}" href="index.php?action=index&module=Administration&parenttab={$CATEGORY}">{$MODULELABEL}</a></td>
		{* crmv@30683 *}
		{elseif $smarty.request.module eq 'Settings' || $smarty.request.module eq 'PickList' || $smarty.request.module eq 'Picklistmulti' || $smarty.request.module eq 'com_vtiger_workflow' || $smarty.request.module eq 'Conditionals' || $smarty.request.module eq 'Transitions'}
			{assign var=MODULE value=Settings}
			{assign var="MODULELABEL" value=$MODULE|@getTranslatedString:$MODULE}
			<td style="padding-left:10px;padding-right:50px" class="moduleName" nowrap><a class="hdrLink" style="{$minFontSize}" href="index.php?module=Settings&action=index&parenttab=Settings&reset_session_menu_tab=true">{$MODULELABEL}</a></td>
		{* crmv@30683e *}
		{* crmv@20209e *}
		{elseif $CATEGORY eq 'Settings'}
			<!-- No List View in Settings - Action is index -->
			<td style="padding-left:10px;padding-right:50px" class="moduleName" nowrap><a class="hdrLink" style="{$minFontSize}" href="index.php?action=index&module={$MODULE}&parenttab={$CATEGORY}">{$MODULELABEL}</a></td>
		{elseif $smarty.request.module eq 'Home' && $REQUEST_ACTION eq 'UnifiedSearch'}	
			<td style="padding-left:10px;padding-right:50px" class="moduleName" nowrap><a class="hdrLink" style="{$minFontSize}" href="javascript:;">{'LBL_SEARCH'|@getTranslatedString:'Home'}</a></td>
		{* crmv@43942 *}
		{elseif $MODULE eq 'Area' && $REQUEST_ACTION eq 'index'}
			<td style="padding-left:10px;padding-right:50px" class="moduleName" nowrap><a class="hdrLink" style="{$minFontSize}" href="index.php?module=Area&action=index&area={$AREAID}">{$AREALABEL}</a></td>
		{* crmv@43942e *}
		{elseif $MENU_LAYOUT.type neq 'modules'}
			<td style="padding-left:10px;padding-right:50px" class="moduleName" nowrap>{$APP.$CATEGORY} > <a class="hdrLink" style="{$minFontSize}" href="index.php?action=index&module={$MODULE}&parenttab={$CATEGORY}">{$MODULELABEL}</a></td>	{* crmv@102334 *}
		{else}
			<td style="padding-left:10px;padding-right:50px" class="moduleName" nowrap><a class="hdrLink" style="{$minFontSize}" href="index.php?action=index&module={$MODULE}&parenttab={$CATEGORY}">{$MODULELABEL}</a></td>	{* crmv@102334 *}
		{/if}
	</tr>
	</table>
</div>
{/if}
{* crmv@30356e crmv@82419 *}
<ul id="Buttons_List_Fixed_Container" style="display:none;">
	<li>
		<a href="javascript:;">
			<i data-toggle="tooltip" data-placement="top" class="vteicon" title="{$APP.LBL_SEARCH}" data-module="GlobalSearch" data-fastpanel="half">search</i>
		</a>
	</li>
	{* crmv@29079 *}
	{if 'Processes'|vtlib_isModuleActive}
		<li>
			<a href="javascript:;">
				<i data-toggle="tooltip" data-placement="top" id="ProcessesCheckChangesImg" class="vteicon" title="{'Processes'|getTranslatedString:'Processes'}" data-module="Processes" data-fastpanel="full">call_split</i>
			</a>
			<span class="badge vteBadge" id="ProcessesCheckChangesDivCount"></span>
		</li>
	{/if}
	{* crmv@29079e *}
	{if $CALENDAR_DISPLAY eq 'true' && $CHECK.Calendar eq 'yes'}
		{if $CATEGORY eq 'Settings' || $CATEGORY eq 'Tools' || $CATEGORY eq 'Analytics'}
			<li><a href="javascript:;" onClick='fnvshobj(this,"miniCal");getMiniCal("parenttab=My Home Page");'><i class="vteicon" title="{$APP.LBL_CALENDAR_TITLE}">event</i></a></li>
		{else}
			<li><a href="javascript:;" onClick='fnvshobj(this,"miniCal");getMiniCal("parenttab={$CATEGORY}");'><i class="vteicon" title="{$APP.LBL_CALENDAR_TITLE}">event</i></a></li>
		{/if}
	{/if}
	{if $WORLD_CLOCK_DISPLAY eq 'true'}
		<li><a href="javascript:;"><i class="vteicon" data-toggle="tooltip" data-placement="top" title="{$APP.LBL_CLOCK_TITLE}" onClick="fnvshobj(this,'wclock');">access_time</i></a></li> {* crmv@82419 *}
	{/if}
	{if $CALCULATOR_DISPLAY eq 'true'}
		<li><a href="javascript:;"><i class="vteicon2 fa-calculator" data-toggle="tooltip" data-placement="top" title="{$APP.LBL_CALCULATOR_TITLE}" onClick="fnvshobj(this,'calculator_cont');fetch_calc();"></i></a></li> {* crmv@82419 *}
	{/if}
	<!-- All Menu -->
	<li><a href="javascript:;">
		<i data-toggle="tooltip" data-placement="top" class="vteicon" title="{$APP.LBL_LAST_VIEWED}" data-module="LastViewed" data-fastpanel="half">list</i>
	</a>
	</li>	{* crmv@32429 crmv@82419 *}
	<li>
		<a href="javascript:;">
			<i data-toggle="tooltip" data-placement="top" class="vteicon md-lg" title="{'Calendar'|getTranslatedString:'Calendar'}" data-module="Calendar" data-fastpanel="full" data-hover-module="EventList" data-hover-fastpanel="custom" data-hover-size="220px">event</i>
		</a>
	</li>
	{* crmv@2963m *}
	{if $MODULE neq 'Messages' && 'Messages'|vtlib_isModuleActive}
		<li>
			<a href="javascript:;">
				<i data-toggle="tooltip" data-placement="top" id="MessagesCheckChangesImg" class="vteicon" title="{'Messages'|getTranslatedString:'Messages'}" data-module="Messages" data-fastpanel="full">email</i>
			</a>
			<span class="badge vteBadge" id="MessagesCheckChangesDivCount"></span>
		</li>
	{/if}
	{* crmv@2963me *}
	{* crmv@29079 *}
	{if 'ModComments'|vtlib_isModuleActive}
		<li>
			<a href="javascript:;">
				<i data-toggle="tooltip" data-placement="top" id="ModCommentsCheckChangesImg" class="vteicon" title="{'LBL_MODCOMMENTS_COMMUNICATIONS'|getTranslatedString:'ModComments'}" data-module="ModComments" data-fastpanel="half">chat</i>
			</a>
			<span class="badge vteBadge" id="ModCommentsCheckChangesDivCount"></span>
		</li>
	{/if}
	{* crmv@29079e *}
	{* crmv@29617 *}
	<li>
		<a href="javascript:;">
			<i data-toggle="tooltip" data-placement="top" class="vteicon" id="ModNotificationsCheckChangesImg" title="{'ModNotifications'|getTranslatedString:'ModNotifications'}" data-module="ModNotifications" data-fastpanel="half">language</i>
		</a>
		<span class="badge vteBadge" id="ModNotificationsCheckChangesDivCount"></span>
	</li>
	{* crmv@29617e *}
	{* crmv@28295 *}
	<li>
		<a href="javascript:;">
			<i data-toggle="tooltip" data-placement="top" id="TodosCheckChangesImg" class="vteicon" title="{'Todos'|getTranslatedString:'ModComments'}" data-module="TodoList" data-fastpanel="half">assignment_turned_in</i>
		</a>
		<span class="badge vteBadge" id="TodosCheckChangesDivCount"></span>
	</li>
	{* crmv@28295e *}
	<li>
		<a href="javascript:;">
			<i data-toggle="tooltip" data-placement="top" class="vteicon" title="{$APP.LBL_QUICK_CREATE}" data-module="QuickCreate" data-fastpanel="half">flash_on</i>
		</a>
	</li>
	{$SDK->getMenuButton('fixed')}	{* crmv@24189 *}
</ul>
{* code of Buttons_List_Contestual moved in Buttons_List_Detail_V.tpl *}
{* crmv@22622e crmv@82419e *}

{include file="header/HideMenuJS.tpl"}

<script type="text/javascript">
jQuery('#Buttons_List_SiteMap').html(jQuery('#Buttons_List_SiteMap_Container').html());jQuery('#Buttons_List_SiteMap_Container').html('');
{if $MENU_LAYOUT.type eq 'modules'}
	//crmv@30356
	{if isMobile()}
		jQuery('#Buttons_List_SiteMap').width(10);
	{else}
		jQuery('#Buttons_List_SiteMap').width(200);
	{/if}
{else}
	{if isMobile()}
		jQuery('#Buttons_List_SiteMap').width(10);
	{else}
		jQuery('#Buttons_List_SiteMap').width(280);
	{/if}
	//crmv@30356e
{/if}

{* crmv@22622 *}
{* crmv@30356 *}
jQuery('#Buttons_List_Fixed').html(jQuery('#Buttons_List_Fixed_Container').html());
jQuery('#Buttons_List_Fixed_Container').html('');
jQuery('#Buttons_List_QuickCreate').show();
{* crmv@30356e *}
//jQuery('#vte_menu_white').height(jQuery('#vte_menu').height());

{* crmv@22622 e *}
var menubar = '{php}echo $_SESSION['menubar'];{/php}';
{literal}
jQuery('.level2Bg img').on('mouseover mouseout', null, function(event) { // crmv@82419
	if (getCookie('crmvWinMaxStatus') != 'close' || menubar != 'no') {	//crmv@23715
		if (event.type == 'mouseover') {
			if (jQuery(this).attr('title') != '')
		    	var title = jQuery(this).attr('title');
		    else
		    	var title = jQuery(this).attr('title1');
		    if (title == '' || title == undefined) return false;

		    jQuery('#menu_tooltip_text').html(title);
		    jQuery(this).attr('title1',title);
		    jQuery(this).attr('title','');

			jQuery('#menu_tooltip').width('10');
		    var position = jQuery(this).offset();
		    jQuery('#menu_tooltip').width(jQuery('#menu_tooltip_text').width()+2);
		    //jQuery('#menu_tooltip').css('left',position.left+(jQuery(this).width()/2)-(jQuery('#menu_tooltip').width()/2));
		    jQuery('#menu_tooltip').css('left',position.left);
		    //crmv@23715
		    if (menubar == 'no') {
		    	jQuery('#menu_tooltip').css('top',8);
			}
			//crmv@23715e
		    jQuery('#menu_tooltip').show();
		} else {
			jQuery('#menu_tooltip').hide();
		}
	}
});
{/literal}
{* crmv@29079 crmv@29617 crmv@28295 crmv@35676 crmv@2963m crmv@OPER5904 *}
{if $MODULE neq 'Messages'}
	{assign var="NOTIFICATION_MODULES" value="Messages,ModComments,ModNotifications,Todos"}
{else}
	{assign var="NOTIFICATION_MODULES" value="ModComments,ModNotifications,Todos"}
{/if}
jQuery('#Buttons_List_Fixed_Container').ready(function(){ldelim}
	NotificationsCommon.showChangesFirst('CheckChangesDiv','CheckChangesImg','{$NOTIFICATION_MODULES}','{$PERFORMANCE_CONFIG.NOTIFICATION_INTERVAL_TIME}'); {*crmv@82948*}
	NotificationsCommon.showChangesInterval('CheckChangesDiv','CheckChangesImg','{$NOTIFICATION_MODULES}','{$PERFORMANCE_CONFIG.NOTIFICATION_INTERVAL_TIME}');
{rdelim});
{* end tags *}
</script>
<!-- crmv@18549e crmv@19842e -->
