{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@92272 *}

<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"> <!-- crmv@30683 -->
<tr>
	<td valign="top"></td>
    <td class="showPanelBg" style="padding: 5px;" valign="top" width="100%"> <!-- crmv@30683 -->

	<div align=center>
		{include file='SetMenu.tpl'}
		{include file='Buttons_List.tpl'} {* crmv@30683 *}
		<table class="settingsSelUITopLine" border="0" cellpadding="5" cellspacing="0" width="100%">
		<tr>
			<td rowspan="2" valign="top" width="50"><img src="{'vtlib_modmng.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_PROCESS_MAKER}" title="{$MOD.LBL_PROCESS_MAKER}" border="0" height="48" width="48"></td>
			<td class="heading2" valign="bottom"><b> {$MOD.LBL_SETTINGS} &gt; {$MOD.LBL_PROCESS_MAKER}</b></td> <!-- crmv@30683 -->
		</tr>

		<tr>
			<td class="small" valign="top">{$MOD.LBL_PROCESS_MAKER_DESC}</td>
		</tr>
		</table>
				
		
		<table border="0" cellpadding="10" cellspacing="0" width="100%">
		<tr>
			<td>
				<div id="vtlib_processmaker_list">
                	{include file=$SUB_TEMPLATE}
                </div>	
			
				<table border="0" cellpadding="5" cellspacing="0" width="100%">
				<tr>
				  	<td class="small" align="right" nowrap="nowrap"><a href="#top">{$MOD.LBL_SCROLL}</a></td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
		<!-- End of Display -->
		
		</td>
        </tr>
        </table>
        </td>
        </tr>
        </table>
   </div>

        </td>
        <td valign="top"></td>
	</tr>
</table>
<br>