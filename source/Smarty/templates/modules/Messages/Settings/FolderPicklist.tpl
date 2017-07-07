{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}
<script language="JavaScript" type="text/javascript" src="modules/Messages/Settings/Settings.js"></script>
{if !empty($FOLDER_LIST)}
	<select name="folder" class="small">
		{foreach key=value item=info from=$FOLDER_LIST}
    		<option value="{$value}" {if $SEL_FOLDER eq $value}selected{/if}>{$value}</option>
    	{/foreach}
	</select>
{/if}