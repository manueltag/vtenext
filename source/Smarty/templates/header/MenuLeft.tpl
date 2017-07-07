{*/*+*************************************************************************************
* The contents of this file are subject to the VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with the License
* The Original Code is: VTECRM
* The Initial Developer of the Original Code is VTECRM LTD.
* Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
* All Rights Reserved.
***************************************************************************************/*}

{* crmv@119414 *}

<script language="JavaScript" type="text/javascript" src="{$RELPATH}include/js/LateralMenu.js"></script>
<script language="JavaScript" type="text/javascript" src="{$RELPATH}include/js/FastPanel.js"></script>
<link rel="stylesheet" type="text/css" href="{$RELPATH}themes/{$THEME}/lateralmenu.css">
<link rel="stylesheet" type="text/css" href="{$RELPATH}themes/{$THEME}/{$THEME}.css">

<link href="include/js/jquery_plugins/mCustomScrollbar/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css" />
<script src="include/js/jquery_plugins/mCustomScrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
<script language="javascript" type="text/javascript" src="include/js/jquery_plugins/slimscroll/jquery.slimscroll.min.js"></script>
<link href="include/js/jquery_plugins/mCustomScrollbar/VTE.mCustomScrollbar.css" rel="stylesheet" type="text/css" />

{assign var="toggleState" value=$smarty.cookies.togglePin}
{if empty($toggleState)}
	{assign var="toggleState" value="disabled"}
{/if}

<div id="vteWrapper">

{if $HIDE_MENUS neq true}

{* HEADER BAR *}
<header id="vteHeader" data-minified="{$toggleState}" data-full="{$HIDE_MENUS}"></header>

<div id="status" style="display:none;">{include file="LoadingIndicator.tpl"}</div>

<div id="mainContainer" data-minified="{$toggleState}">

	{if $HIDE_MENUS neq true}
		<aside id="leftPanel" data-minified="{$toggleState}" style="display:none">
			<div class="vteLeftHeader">
				<div class="brandLogo">
					<div class="brandInnerLogo">
						<img class="img-responsive" src="{'header'|get_logo}" />
					</div>
				</div>
				
				<span class="toogleMenu">
					<img class="toggleImg" src="{'toggle'|get_logo}" />
					<i class="togglePin vteicon2 fa-thumb-tack md-link {if $toggleState eq 'disabled'}active{/if}"></i>
				</span>
			</div>
			{if $MODULE_NAME eq 'Settings' || $CATEGORY eq 'Settings' || $MODULE_NAME eq 'com_vtiger_workflow'}
				{include file="header/MenuLeftSettings.tpl"}
			{else}
				{include file="header/MenuLeftModules.tpl"}
			{/if}
		</aside>
	{/if}
	
	{if $HIDE_MENUS neq true}
		<aside id="rightPanel" style="display:none">
			<div class="vteRightHeader">
				<ul class="profileWrapper">
					<li class="profileInner">
						<a href="#" class="profile">
							<span>{$CURRENT_USER_ID|getUserAvatarImg}</span>
						</a>
						<ul class="profileMenu">
							{if $smarty.session.MorphsuitZombie eq false && $IS_ADMIN eq '1'}
								<li class="shrink">
									<a href="index.php?module=Settings&amp;action=index&amp;parenttab=Settings&amp;reset_session_menu_tab=true">
										<div class="row">
											<div class="col-xs-2 vcenter">
												<i class="vteicon">settings_applications</i>
											</div><!-- 
											 --><div class="col-xs-10 vcenter">
												{'Settings'|getTranslatedString:'Settings'}
											</div>
										</div>
									</a>
								</li>
							{/if}
							<li>
								<a href="index.php?module=Users&action=DetailView&record={$CURRENT_USER_ID}&modechk=prefview">
									<div class="row">
										<div class="col-xs-2 vcenter">
											<i class="vteicon">person</i>
										</div><!-- 
										 --><div class="col-xs-10 vcenter">
											{$APP.LBL_PREFERENCES}
										</div>
									</div>
								</a>
							</li>
							<li>
								<a href="index.php?module=Users&action=Logout">
									<div class="row">
										<div class="col-xs-2 vcenter">
											<i class="vteicon">power_settings_new</i>
										</div><!-- 
										 --><div class="col-xs-10 vcenter">
											{$APP.LBL_LOGOUT}
										</div>
									</div>
								</a>
							</li>
						</ul>	
					</li>
				</ul>
			</div>
			<ul class="menuList">
				<li><ul id="Buttons_List_Fixed" class="menuListSection"></ul></li>
			</ul>
		</aside>
		
		{literal}
			<!-- <script type="text/javascript">
				jQuery(document).ready(function() {
					setTimeout(function() {
						var leftMenuWidth = parseInt(jQuery('.menuList').width());
						var leftMenuHeight = parseInt(jQuery('body').height()) - parseInt(jQuery('#vteHeader').height());
				    	jQuery('.menuList').slimScroll({
							wheelStep: 10,
							height: leftMenuHeight + 'px',
							width: '100%',
							opacity: 0,
						    overflow: 'auto',
						});
						//jQuery('#rightPanel .slimScrollDiv').css('overflow', 'initial');
						//jQuery('#rightPanel .menuList').css('overflow', 'initial');
					}, 200);
		        });
			</script> -->
		{/literal}
		
		{include file="header/HideMenuJS.tpl"}
	{/if}
	
	<div id="mainContent" data-minified="{$toggleState}" data-full="{$HIDE_MENUS}">
	
	<aside id="fastPanel">
		<iframe id="fastIframe"></iframe>
		<div id="ajaxCont"></div>
		<div id="ajaxSearchCont"></div>
	</aside>
	
	<!-- Level 4 tabs starts -->
	<div id="Buttons_List_3" class="level4Bg" style="display:none;"></div>
	<!-- Level 4 tabs ends -->
	
{else}
	{if $smarty.request.fastmode neq 1}
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
	{/if}
	<div id="Buttons_List_3" class="level4Bg" style="display:none;"></div>	
{/if}

{literal}
<script type="text/javascript">
	var wrapperHeight = parseInt(visibleHeight(jQuery('#vteWrapper').get(0)));
	jQuery('#mainContent').css('min-height', wrapperHeight + 'px');
</script>
{/literal}