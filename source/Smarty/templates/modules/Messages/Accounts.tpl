{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}

<table cellspacing="0" cellpadding="0" width="100%">
	<tr>
		<td colspan="2" class="folderMessageRow gray">
			<div style="padding-left: 10px; padding-right: 3px;">
				{'LBL_ACCOUNT_INBOXLIST'|getTranslatedString:'Messages'}
			</div>
		</td>
	</tr>
	{foreach item=entity from=$ACCOUNTS_INBOX}
		<tr class="lvtColDataMessage" onMouseOut="this.className='lvtColDataMessage'" onMouseOver="this.className='lvtColDataHoverMessage'">
			<td colspan="2" class="folderMessageRow listMessageFrom" style="cursor: pointer;" onClick="selectINBOXFolder('{$DIV_DIMENSION.Folders}','{$DIV_DIMENSION.ListViewContents}','{$entity.account}','{$entity.id}','{$entity.description}')">
				<div style="padding-left: 10px; padding-right: 3px;">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="100%">
							{if !empty($entity.img)}<img src="{$entity.img}" style="padding-right:10px;" />{/if}{$entity.description}
							{if $entity.count gt 0}
								<div style="margin-top:6px;float:right;">
									{include file="BubbleNotification.tpl" COUNT=$entity.count BN_BGCOLOR=$entity.bg_notification_color}
								</div>
							{/if}
						</td>
					</tr>
					</table>
				</div>
			</td>
		</tr>
	{/foreach}
	<tr>
		<td colspan="2" class="folderMessageRow gray">
			<div style="padding-left: 10px; padding-right: 3px;">
				{'LBL_ACCOUNTS'|getTranslatedString:'Messages'}
			</div>
		</td>
	</tr>
	{foreach item=entity from=$ACCOUNTS}
		<tr class="lvtColDataMessage" onMouseOut="this.className='lvtColDataMessage'" onMouseOver="this.className='lvtColDataHoverMessage'">
			<td colspan="2" class="folderMessageRow listMessageFrom" style="cursor: pointer;" onClick="selectAccount('folders','{$DIV_DIMENSION.Folders}','{$DIV_DIMENSION.ListViewContents}','{$entity.id}','{$entity.description}')">
				<div style="padding-left: 10px; padding-right: 3px;">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="100%">
							{if !empty($entity.img)}<img src="{$entity.img}" style="padding-right:10px;" />{/if}{$entity.description}
						</td>
					</tr>
					</table>
				</div>
			</td>
		</tr>
	{/foreach}
</table>