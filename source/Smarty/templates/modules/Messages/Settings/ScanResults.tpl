{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@3082m *}
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	{* crmv@63113 *}
	{if !empty($FOLDERS_NOT_FOUND)}
		<tr height="20" valign="middle">
			<td align="center" colspan="2">
				<br /><b>{'LBL_FILTER_FOLDERS_NOT_FOUND'|getTranslatedString:'Messages'}<br /></b>
				{'<br />'|implode:$FOLDERS_NOT_FOUND}
			</td>
		</tr>
	{/if}
	{* crmv@63113e *}
	{foreach name=messagesettings key=folder item=messages from=$FILTERED}
		{assign var=found value=true}
		{assign var=i value=$smarty.foreach.messagesettings.iteration}
		{if $i is odd}
	    	<tr valign="top">
	    {/if}
	    	<td width="50%">
	    		<div class="cpanel_div">
		    		<div class="listMessageFrom" height="20px" style="font-weight:normal;vertical-align:middle;text-align:center;">
		    			<span style="padding-left:10px;">{$folder}</span>
		    		</div>
		    		<div style="padding-top:15px;" align="center">
		    			{$messages|@count}
					</div>
		    	</div>
	    	</td>
		{if $i is even}
	    	</tr>
	    {/if}
	{foreachelse}
		{assign var=found value=false}
		<tr height="300" valign="middle"><td align="center" colspan="2">{'LBL_NO_RECORD'|getTranslatedString}</td></tr>
	{/foreach}
	<tr><td align="center" colspan="2"><a href="javascript:;" onClick="{$FILTER_LINK}">{'LBL_GO_BACK'|getTranslatedString:'Messages'}</a></td></tr>
</table>
{if $found eq true}
	<script type="text/javascript">
	    jQuery(window).load(function(){ldelim}
	    	parent.VtigerJS_DialogBox.block();
	    	parent.$("status").style.display = "block";
	    	parent.getListViewEntries_js('Messages','start=1&folder='+parent.current_folder,false);
			parent.setmCustomScrollbar('#ListViewContents');
			parent.update_navigation_values(parent.location.href+'&folder='+parent.current_folder,'Messages',false);
			parent.reloadFolders();
			parent.VtigerJS_DialogBox.unblock();
			parent.$("status").style.display = "none";
	    {rdelim});
	</script>
{/if}