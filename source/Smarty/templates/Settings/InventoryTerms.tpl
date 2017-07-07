<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->
<script language="JAVASCRIPT" type="text/javascript" src="include/js/smoothscroll.js"></script>
<script language="JavaScript" type="text/javascript" src="{"include/js/menu.js"|resourcever}"></script>

<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"> <!-- crmv@30683 -->
<tbody><tr>
        <td valign="top"></td>
        <td class="showPanelBg" style="padding: 5px;" valign="top" width="100%"> <!-- crmv@30683 -->
<form action="index.php" method="post" name="tandc" onsubmit="VtigerJS_DialogBox.block();">
<input type="hidden" name="module" value="Settings">
<input type="hidden" name="action">
<input type="hidden" name="inv_terms_mode">
<input type="hidden" name="parenttab" value="Settings">
	<div align=center>
				
			{include file="SetMenu.tpl"}
			{include file='Buttons_List.tpl'} {* crmv@30683 *} 
				<!-- DISPLAY -->
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
				<tr>
					<td width=50 rowspan=2 valign=top><img src="{$IMAGE_PATH}terms.gif" width="48" height="48" border=0></td>
					<td class=heading2 valign=bottom><b> {$MOD.LBL_SETTINGS} > {$MOD.INVENTORYTERMSANDCONDITIONS} </b></td> <!-- crmv@30683 -->
				</tr>
				<tr>
					<td valign=top class="small">{$MOD.LBL_INVEN_TANDC_DESC} </td>
				</tr>
				</table>
				
				<br>
				<table border=0 cellspacing=0 cellpadding=10 width=100% >
				<tr>
				<td>
				
					<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
					<tr>
						<td class="big"><strong>{$MOD.LBL_TANDC_TEXT} </strong></td>
						{if $INV_TERMS_MODE eq 'view'}
						<td class="small" align=right>
							<input class="crmButton small edit" title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" onclick="this.form.action.value='OrganizationTermsandConditions';this.form.inv_terms_mode.value='edit'" type="submit" name="Edit" value="{$APP.LBL_EDIT_BUTTON_LABEL}"> </td>

						{else}
							<td  class="small" align=right> 
							<input title="{$APP.LBL_SAVE_BUTTON_LABEL}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmButton small save" type="submit" name="button" value="{$APP.LBL_SAVE_BUTTON_LABEL}" onclick="this.form.action.value='savetermsandconditions';">
							<input title="{$APP.LBL_CANCEL_BUTTON_LABEL}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="crmButton small cancel" onclick="window.history.back()" type="button" name="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}">
							
						</td>
						{/if}
					</tr>
					</table>
					
					{if $INV_TERMS_MODE eq 'view'}
						<table border=0 cellspacing=0 cellpadding=5 width=100% >
						<tr>
						<td class="listRow small" valign=top style="padding:20px">
							{$INV_TERMSANDCONDITIONS} </td> 
					  </tr>
					</table>
						{else}
							<table border=0 cellspacing=0 cellpadding=5 width=100% class="listTable">
							<tr>
								<td class="colHeader small" valign=top>{$MOD.LBL_TYPE_TEXT_AND_SAVE}</td>
					  		</tr>
							<tr>
								<td class="listTableRow small" valign=top>
								<textarea class=small name="inventory_tandc" style="width:95%; height:200px;text-align:left;">{$INV_TERMSANDCONDITIONS}</textarea>

								</td>
							</tr>
							</table>	
						{/if}
					<table border=0 cellspacing=0 cellpadding=5 width=100% >
					<tr>
					  <td class="small" nowrap align=right><a href="#top">{$MOD.LBL_SCROLL}</a></td>
					</tr>
					</table>
				</td>
				</tr>
				</table>
			
			
			
			</td>
			</tr>
			</table>
		</td>
	</tr>
	</table>
		
	</div>
	</form>
</td>
        <td valign="top"></td>
   </tr>
</tbody>
</table>
