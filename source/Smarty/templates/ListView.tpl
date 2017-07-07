{***************************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
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

<!-- module header -->
<script language="JavaScript" type="text/javascript" src="{"include/js/ListView.js"|resourcever}"></script>
<script language="JavaScript" type="text/javascript" src="include/js/search.js"></script>
<script language="JavaScript" type="text/javascript" src="include/js/Merge.js"></script> {* crmv@8719 *}
<script language="JavaScript" type="text/javascript" src="modules/{$MODULE}/{$MODULE}.js"></script>

{* crmv@43835 *}
<script type="text/javascript">
var gridsearch;
</script>
{* crmv@43835e *}

<input type="hidden" id="user_dateformat" name="user_dateformat" value="{$DATEFORMAT}">
<textarea name="select_ids" id="select_ids" style="display:none;"></textarea>

{include file='Buttons_List.tpl'}

<!-- Contents -->
<table border=0 cellspacing=0 cellpadding=0 width=100% align=center>	{*<!-- crmv@18592 -->*}
	<tr>
        <td valign=top></td>
		<td class="showPanelBg" valign="top" width=100% style="padding:0px;">
		
			{include file="Buttons_List3.tpl"}	{* crmv@102334 *}
		
			{include file="AdvancedSearch.tpl"}
		
			{* crmv@8719 *}
			<div id="mergeDup" class="menuSeparation" style="z-index:1;display:none;position:relative;padding:10px;">
				{include file="MergeColumns.tpl"}
			</div>
			{* crmv@8719e *}

			<!-- PUBLIC CONTENTS STARTS-->
		    <div id="ListViewContents" class="small" style="width:100%;position:relative;">
		    	{include file="ListViewEntries.tpl" MOD=$MOD} {* crmv@vte10usersFix *}
		    </div>
		</td>
		<td valign=top></td>
	</tr>
</table>

<form name="SendMail"><div id="sendmail_cont" style="z-index:100001;position:absolute;"></div></form>
<form name="SendFax"><div id="sendfax_cont"></div></form>
<form name="SendSms" id="SendSms" method="POST" action="index.php"><div id="sendsms_cont"></div></form>	{* crmv@16703 *}