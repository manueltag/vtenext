{* crmv@94525 crmv@94125 *}
{* Use this variable to choose what to include in the html header *}
{if $head_include eq 'all'}
	{assign var=INCLUDES value="all"}
{else}
	{assign var=INCLUDES value=","|explode:$head_include}
{/if}
{* if the called file is not in the VTE root, you should set this variable to correctly include the resources *}
{if $RELPATH eq ""}
	{if $PATH}
		{assign var="RELPATH" value=$PATH}
	{else}
		{assign var="RELPATH" value=""}
	{/if}
{/if}
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>

	{* Meta tags *}
	{if $APP.LBL_CHARSET}
	<meta http-equiv="Content-Type" content="text/html; charset={$APP.LBL_CHARSET}">
	{/if}
	
	{* crmv@25620 - Browser Title *}
	{if $BROWSER_TITLE eq ''}
		<title>{if $MODULE_NAME}{$MODULE_NAME|getTranslatedString:$MODULE_NAME} - {/if}{$APP.LBL_BROWSER_TITLE}</title>
		<script type="text/javascript">
			var browser_title = '{$APP.LBL_BROWSER_TITLE}';
		</script>
	{else}
		<title>{$BROWSER_TITLE}</title>
		<script type="text/javascript">
			var browser_title = '{$BROWSER_TITLE}';
		</script>
	{/if}
	{* crmv@25620e *}
	
	{* crmv@30356 - Icons *}
	{if $INCLUDES == 'all' || in_array('icons', $INCLUDES)}
	<link rel="shortcut icon" href="{$RELPATH}{'favicon'|get_logo}">
	
	<link rel="apple-touch-icon-precomposed" href="{$RELPATH}themes/images/ipad_icon_114x114.png" />
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="{$RELPATH}themes/images/ipad_icon_114x114.png" />
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="{$RELPATH}themes/images/ipad_icon_114x114.png" />
	{/if}
	{* crmv@30356 *}
	
	{* Base/compatibility scripts *}
	<script language="JavaScript" type="text/javascript" src="{$RELPATH}include/js/json2.js"></script>
	
	{* jQuery *}
	{if $INCLUDES == 'all' || in_array('jquery', $INCLUDES)}
	<script language="JavaScript" type="text/javascript" src="{$RELPATH}include/js/jquery.js"></script>
	
		{* jQuery plugins *}
		{if $INCLUDES == 'all' || in_array('jquery_plugins', $INCLUDES)}
			<script language="JavaScript" type="text/javascript" src="{$RELPATH}include/js/jquery_plugins/dimensions.min.js"></script>
			<link rel="stylesheet" href="{$RELPATH}include/js/jquery_plugins/css/scrollableFixedHeaderTable_style.css">
			<script language="JavaScript" type="text/javascript" src="{$RELPATH}include/js/jquery_plugins/scrollableFixedHeaderTable.js"></script>
			<script language="JavaScript" type="text/javascript" src="{$RELPATH}include/js/jquery_plugins/form.js"></script>
			<script language="JavaScript" type="text/javascript" src="{$RELPATH}include/js/jquery_plugins/jquery.debounce.min.js"></script>
			
			<!-- script language="JavaScript" type="text/javascript" src="{$RELPATH}include/js/jquery_plugins/timers.js"></script -->
		{/if}
		
		{* Fancybox *}
		{if $INCLUDES == 'all' || in_array('fancybox', $INCLUDES)}
			<script type="text/javascript" src="{$RELPATH}include/js/jquery_plugins/fancybox/jquery.mousewheel-3.0.6.pack.js"></script>
			<script type="text/javascript" src="{$RELPATH}include/js/jquery_plugins/fancybox/jquery.fancybox.pack.js"></script>
			<link rel="stylesheet" type="text/css" href="{$RELPATH}include/js/jquery_plugins/fancybox/jquery.fancybox.css" media="screen" />
		{/if}
		
		{* jQuery UI *}
		{if $INCLUDES == 'all' || in_array('jquery_ui', $INCLUDES)}
			<link rel="stylesheet" href="{$RELPATH}include/js/jquery_plugins/ui/jquery-ui.min.css">
			<link rel="stylesheet" href="{$RELPATH}include/js/jquery_plugins/ui/jquery-ui.theme.min.css">
			<script type="text/javascript" src="{$RELPATH}include/js/jquery_plugins/ui/jquery-ui.min.js"></script>
			<script type="text/javascript">
				// fix for some collision between bootstrap and jQuery UI
				jQuery.widget.bridge('uibutton', jQuery.ui.button);
				jQuery.widget.bridge('uitooltip', jQuery.ui.tooltip);
			</script>
		{/if}
	{/if}
	
	{* Prototype and scriptaculous *}
	{if $INCLUDES == 'all' || in_array('prototype', $INCLUDES)}
		<script language="javascript" type="text/javascript" src="{$RELPATH}include/scriptaculous/prototype.js"></script>
		<script language="javascript" type="text/javascript" src="{$RELPATH}include/scriptaculous/scriptaculous.js"></script>
		<script language="javascript" type="text/javascript" src="{$RELPATH}include/scriptaculous/dom-drag.js"></script>
	{/if}
	
	{* Theme *}
	{include file="Theme.tpl" THEME_MODE="head"}
	
	{* Language *}
	{if $CURRENT_LANGUAGE}
		<script language="JAVASCRIPT" type="text/javascript" src="{$RELPATH}include/js/{$CURRENT_LANGUAGE}.lang.js"></script>
	{else}
		<script language="JavaScript" type="text/javascript" src="{$RELPATH}include/js/{php} echo $_SESSION['authenticated_user_language'];{/php}.lang.js?{php} echo $_SESSION['vtiger_version'];{/php}"></script>
	{/if}

	{* VTE scripts *}
	<script language="JavaScript" type="text/javascript" src="{$RELPATH}{"include/js/vtlib.js"|resourcever}"></script>
	<script language="JavaScript" type="text/javascript" src="{$RELPATH}{"include/js/general.js"|resourcever}"></script>
	<script language="JavaScript" type="text/javascript" src="{$RELPATH}{"include/js/session.js"|resourcever}"></script> {* crmv@91082 *}
	<script language="JavaScript" type="text/javascript" src="{$RELPATH}{"include/js/QuickCreate.js"|resourcever}"></script>
	<script language="JavaScript" type="text/javascript" src="{$RELPATH}{"include/js/menu.js"|resourcever}"></script>
	<script language="JavaScript" type="text/javascript" src="{$RELPATH}{"include/calculator/calc.js"|resourcever}"></script>
	<script language="JavaScript" type="text/javascript" src="{$RELPATH}{"modules/Calendar/script.js"|resourcever}"></script>
	
	<script language="JavaScript" type="text/javascript" src="{$RELPATH}{"include/js/notificationPopup.js"|resourcever}"></script>
	<script language="JavaScript" type="text/javascript" src="{$RELPATH}{"modules/Popup/Popup.js"|resourcever}"></script> {* crmv@43864 *}
	<script language="JavaScript" type="text/javascript" src="{$RELPATH}{"modules/Area/Area.js"|resourcever}"></script>
	<script language="JavaScript" type="text/javascript" src="{$RELPATH}{"include/js/Color.js"|resourcever}"></script> {* crmv@98866 *}
	
	{* Charts *}
	{if $INCLUDES == 'all' || in_array('charts', $INCLUDES)}
		<script language="JavaScript" type="text/javascript" src="{$RELPATH}include/chartjs/Chart.min.js"></script> {* crmv@82770 *}
		<script language="JavaScript" type="text/javascript" src="{$RELPATH}include/chartjs/Chart.HorizontalBar.min.js"></script> {* crmv@82770 *}
		<script language="JavaScript" type="text/javascript" src="{$RELPATH}include/chartjs/Chart.Scatter.min.js"></script> {* crmv@82770 *}
		<script language="JavaScript" type="text/javascript" src="{$RELPATH}include/chartjs/VTEChart.js"></script> {* crmv@82770 *}
	{/if}
	
	{* JSCalendar - Obsolete! *}
	{if $INCLUDES == 'all' || in_array('jscalendar', $INCLUDES)}
		<link rel="stylesheet" type="text/css" media="all" href="{$RELPATH}jscalendar/calendar-win2k-cold-1.css">
		<script type="text/javascript" src="{$RELPATH}jscalendar/calendar.js"></script>
		<script type="text/javascript" src="{$RELPATH}jscalendar/calendar-setup.js"></script>
		{if $APP.LBL_JSCALENDAR_LANG}
		<script type="text/javascript" src="{$RELPATH}jscalendar/lang/calendar-{$APP.LBL_JSCALENDAR_LANG}.js"></script>
		{/if}
	{/if}
	{* crmv@82419e *}
	
	{* File uploads *}
	{if $INCLUDES == 'all' || in_array('file_upload', $INCLUDES)}
		<link rel="stylesheet" href="{$RELPATH}modules/Emails/plupload/jquery.plupload.queue/css/jquery.plupload.queue.css" type="text/css" media="screen" />
		<script type="text/javascript" src="{$RELPATH}modules/Emails/plupload/plupload.js"></script>
		<script type="text/javascript" src="{$RELPATH}modules/Emails/plupload/plupload.gears.js"></script>
		<script type="text/javascript" src="{$RELPATH}modules/Emails/plupload/plupload.silverlight.js"></script>
		<script type="text/javascript" src="{$RELPATH}modules/Emails/plupload/plupload.flash.js"></script>
		<script type="text/javascript" src="{$RELPATH}modules/Emails/plupload/plupload.browserplus.js"></script>
		<script type="text/javascript" src="{$RELPATH}modules/Emails/plupload/plupload.html4.js"></script>
		<script type="text/javascript" src="{$RELPATH}modules/Emails/plupload/plupload.html5.js"></script>
		<script type="text/javascript" src="{$RELPATH}modules/Emails/plupload/jquery.plupload.queue/jquery.plupload.queue.js"></script>
		<script type="text/javascript" src="{$RELPATH}modules/Emails/plupload/i18n/{php}echo get_short_language();{/php}.js"></script>	{* crmv@24568 *}
	{/if}
	
	{* crmv@42024 populate global JS variables *}
	<script type="text/javascript">setGlobalVars('{$JS_GLOBAL_VARS|replace:"'":"\'"}');</script> {* crmv@70731 *}
	{* crmv@42024e *}
	
	{* Asterisk Integration *}
	{if $USE_ASTERISK eq 'true'}
		<script type="text/javascript" src="{$RELPATH}include/js/asterisk.js"></script>
		<script type="text/javascript">
			if (typeof(use_asterisk) == 'undefined') use_asterisk = true;
		</script>
	{/if}
	
	{if $INCLUDES == 'all' || in_array('sdk_headers', $INCLUDES)}
	
		{* Inclusion of custom CSS *}
		{if $HEADERCSS}
			{foreach item=HDRCSS from=$HEADERCSS}
				<link rel="stylesheet" type="text/css" href="{$RELPATH}{$HDRCSS->linkurl}">
			{/foreach}
		{/if}
		
		{* Inclusion of custom javascript *}
		{if $HEADERSCRIPTS}
			{foreach item=HEADERSCRIPT from=$HEADERSCRIPTS}
				<script type="text/javascript" src="{$RELPATH}{$HEADERSCRIPT->linkurl}"></script>
			{/foreach}
		{/if}
	{/if}
	
</head>
