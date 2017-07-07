{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr valign="top">
	<td width="50%">
		{if !empty($HISTORY)}
			<table border="0" cellpadding="5" cellspacing="0" width="100%" class="hdrNameBg">
				{foreach item=info from=$HISTORY}
					<tr>
						{foreach item=value from=$info}
							<td class="trackerList small">{$value}</td>
						{/foreach}
					</tr>
				{/foreach}
			</table>
		{/if}
	</td>
	<td width="50%">
		{if !empty($PENDING)}
			<table border="0" cellpadding="5" cellspacing="0" width="100%" class="hdrNameBg">
				{foreach item=info from=$PENDING}
					<tr>
						{foreach item=value from=$info}
							<td class="trackerList small">{$value}</td>
						{/foreach}
					</tr>
				{/foreach}
			</table>
		{/if}
	</td>
</tr>		
</table>