{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@3082m *}
{foreach item=list from=$LIST}
	<div class="cpanel_div" style="min-height: 0px;">
		<table border="0" cellpadding="0" cellspacing="5" width="100%">
			<tr>
				{if $list.edit eq true}
					<td width="16"><a href="index.php?module=Messages&action=MessagesAjax&file=Settings/index&operation=EditPop3&id={$list.id}"><img src="{'small_edit.png'|@vtiger_imageurl:$THEME}" title="{'LBL_EDIT'|getTranslatedString}" alt="{'LBL_EDIT'|getTranslatedString}" border="0"></a></td>
				{/if}
				{if $list.delete eq true}
					<td width="16"><a href="index.php?module=Messages&action=MessagesAjax&file=Settings/index&operation=DeletePop3&id={$list.id}"><img src="{'small_delete.png'|@vtiger_imageurl:$THEME}" title="{'LBL_DELETE'|getTranslatedString}" alt="{'LBL_DELETE'|getTranslatedString}" border="0"></a></td>
				{/if}
				<td align="left" style="padding-left:10px;">
					{'LBL_POP3_FETCH_FROM'|getTranslatedString:'Messages'} <b>{$list.username}</b> {'LBL_POP3_FETCH_IN'|getTranslatedString:'Messages'} <b>{$list.folder}</b>
				</td>
			</tr>
		</table>
	</div>
{/foreach}