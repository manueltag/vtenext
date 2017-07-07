{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@3082m *}
<form name="SaveFolders" action="index.php">
	<input type="hidden" name="module" value="Messages">
	<input type="hidden" name="action" value="MessagesAjax">
	<input type="hidden" name="file" value="Settings/index">
	<input type="hidden" name="operation" value="SaveFolders">
	<input type="hidden" name="account" value="">
	<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center">
		{foreach name=messagesettings key=special item=folder from=$SPECIAL_FOLDERS}
			{assign var=i value=$smarty.foreach.messagesettings.iteration}
			{if $i is odd}
		    	<tr valign="top">
		    {/if}
		    	<td width="50%">
		    		<div class="cpanel_div">
			    		<div class="listMessageFrom" height="20px" style="font-weight:normal;vertical-align:middle;text-align:center;">
			    			<img src="modules/Messages/src/img/folder_{$special|strtolower}.png" border="0"/><span style="padding-left:10px;">{'LBL_Folder_'|cat:$special|getTranslatedString:'Messages'}</span>
			    		</div>
			    		<div style="padding-top:15px;" align="center">
			    			<select name="{$special}" class="small">
			    				<option value="">{'LBL_NONE'|getTranslatedString}</option>
			    				{foreach key=value item=info from=$FOLDER_LIST}
			    					<option value="{$value}" {if $value eq $folder}selected{/if}>{$value}</option>
			    				{/foreach}
			    			</select>
						</div>
			    	</div>
		    	</td>
			{if $i is even}
		    	</tr>
		    {/if}
	    {/foreach}
	</table>
</form>