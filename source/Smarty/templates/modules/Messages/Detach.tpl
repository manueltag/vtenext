{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@44775 crmv@94525 *}
{include file="HTMLHeader.tpl" head_include="icons,jquery,jquery_plugins,jquery_ui,fancybox,prototype,sdk_headers"}

<body leftmargin=0 topmargin=0 marginheight=0 marginwidth=0 class=small>

{* extra script *}
<script language="javascript" type="text/javascript" src="modules/{$MODULE}/{$MODULE}.js"></script>

{include file='CachedValues.tpl'}
{include file='modules/SDK/src/Reference/Autocomplete.tpl'}

<div id="popupContainer" style="display:none;"></div>
<script language="JavaScript" type="text/javascript">
	var messageMode = '{$MESSAGE_MODE}';
	var current_account = '{$CURRENT_ACCOUNT}';
	var current_folder = '{$CURRENT_FOLDER}';
	var list_status = 'view';
	var ajax_enable = true;
</script>

{include file='modules/Messages/Move2Folder.tpl'}

<table cellpadding="0" cellspacing="0" border="0" class="level3Bg" width="100%" style="position:fixed;">
	<tr height="34">
		<td width="100%">
			<div class="closebutton" style="display: block; top:1px; left:0px;" onclick="window.close();"></div>
			<div style="float:right;padding-right:5px;" id="Button_List_Detail">
				{include file="modules/Messages/DetailViewButtons.tpl"}
			</div>
			<div id="status" style="float:right;padding:5px;;display:none;">{include file="LoadingIndicator.tpl"}</div>
		</td>
	</tr>
</table>
<div id="vte_menu" style="height:34px;"></div>

{include file="modules/Messages/DetailView.tpl"}

</body>
</html>