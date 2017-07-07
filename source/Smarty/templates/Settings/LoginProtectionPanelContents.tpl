{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}

{* crmv@56023 *}

<table width="100%" border="0">
	<tr class="small">
		<td align="left" >
		{if $LIST_ENTRIES neq ''}
			{$RECORD_COUNTS}
		{/if}
		</td>
		{$NAVIGATION}
	</tr>
</table>

<table border=0 cellspacing=1 cellpadding=5 width=100% style="background-color:#cccccc;" class="small">
	<tr style="background-color:#efefef">
	{foreach item=header from=$LIST_HEADER}
  		<td class="lvtCol">{$header}</td>
	{/foreach}
	</tr>
	{foreach item=entity key=entity_id from=$LIST_ENTRIES}
		<tr bgcolor=white onMouseOver="this.className='lvtColDataHover'" onMouseOut="this.className='lvtColData'"  >
			 {foreach item=data from=$entity}	
				 <td>{$data}</td>
			 {/foreach}
		</tr>
	{foreachelse}
		<tr bgcolor=white>
			<td colspan="9" height="300px" align="center" class="genHeaderSmall">{$MOD.LBL_NO_DATA}</td>
		</tr>
	{/foreach}
</table>