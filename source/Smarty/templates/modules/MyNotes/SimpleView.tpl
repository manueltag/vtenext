{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}

{include file="SmallHeader.tpl" HEADER_Z_INDEX="1"}
{include file='CachedValues.tpl'}
{include file="modules/SDK/src/Reference/Autocomplete.tpl"}

<link href="include/js/jquery_plugins/mCustomScrollbar/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css" />
<script src="include/js/jquery_plugins/mCustomScrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
<script language="javascript" type="text/javascript" src="include/js/jquery_plugins/slimscroll/jquery.slimscroll.min.js"></script>
<link href="include/js/jquery_plugins/mCustomScrollbar/VTE.mCustomScrollbar.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="{"include/js/ListView.js"|resourcever}"></script>
<script type="text/javascript" src="{"include/js/dtlviewajax.js"|resourcever}"></script>
<script type="text/javascript" src="modules/SDK/src/Notifications/NotificationsCommon.js"></script>
<script type="text/javascript" src="modules/{$MODULE}/{$MODULE}.js"></script>
<script type="text/javascript" src="modules/SDK/SDK.js"></script>

{literal}
<style>
	html, body {
		height: 100%;
	}
</style>
{/literal}

<table class="small" width="100%" cellspacing="0" id="PageContents">
	<tr valign="top">
		<td width="30%">
			<div id="ListViewContents">{$LIST}</div>
		</td>
		<td width="70%"><div id="DetailViewContents" style="overflow-y:auto;"></div></td>	{* crmv@55694 *}
	</tr>
</table>

<script type="text/javascript">
{literal}
jQuery(document).ready(function() {
	setTimeout(function() {
		var vteMenu = jQuery('#vte_menu').outerHeight(true);
		var buttonsList = jQuery('#Buttons_List').outerHeight(true);
		var emptyHeight = jQuery('body').height() - vteMenu - buttonsList - 5;

		jQuery('#ListViewContents').slimScroll({
			wheelStep: 10,
			height: emptyHeight,
			width:'100%'
		});
		//crmv@55694
		/* jQuery('#DetailViewContents').slimScroll({
			wheelStep: 10,
			height: emptyHeight+'px',
			width:'100%'
		}); */
		jQuery('#DetailViewContents').height(emptyHeight+'px');
		//crmv@55694e
		if (jQuery('.slimScrollDiv').length > 0) {
			jQuery('#ListViewContents').parent().css('outline','1px solid #E0E0E0');
		}
	}, 100);
});
{/literal}

{if !empty($MYNOTE_SELECTED)}
	jQuery('#row_{$MYNOTE_SELECTED}').click();
{/if}
</script>

</body>
</html>