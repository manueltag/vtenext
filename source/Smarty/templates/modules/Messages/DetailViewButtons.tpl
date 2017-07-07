{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@103863 *}
<div style="float:right;">
	{if $DETAILVIEWBUTTONSPERM.edit}
		<div style="float:left;">
			<button class="crmbutton small edit" type="button" onclick="javascript:OpenCompose('{$ID}','draft');">
				<table cellpadding="2" cellspacing="0" border="0">
					<tr height="20">
						<td style="padding-right:4px;">
							<i class="vteicon md-sm" title="{'LBL_EDIT_BUTTON'|@getTranslatedString:$MODULE}">create</i>
						</td>
						<td>{'LBL_EDIT_BUTTON'|@getTranslatedString:$MODULE}</td>
					</tr>
				</table>
			</button>
		</div>
	{/if}
	{if $DETAILVIEWBUTTONSPERM.reply}
		<div style="float:left;">
			<button class="crmbutton small edit" type="button" onclick="javascript:OpenCompose('{$ID}','reply');">
				<table cellpadding="2" cellspacing="0" border="0">
					<tr height="20">
						<td style="padding-right:4px;">
							<i class="vteicon md-sm" title="{'LBL_REPLY_ACTION'|@getTranslatedString:$MODULE}">reply</i>
						</td>
						<td>{'LBL_REPLY_ACTION'|@getTranslatedString:$MODULE}</td>
					</tr>
				</table>
			</button>
		</div>
	{/if}
	{if $DETAILVIEWBUTTONSPERM.reply_all}
		<div style="float:left;">
			<button class="crmbutton small edit" type="button" onclick="javascript:OpenCompose('{$ID}','reply_all');">
				<table cellpadding="2" cellspacing="0" border="0">
					<tr height="20">
						<td style="padding-right:4px;">
							<i class="vteicon md-sm" title="{'LBL_REPLY_ALL_ACTION'|@getTranslatedString:$MODULE}">reply_all</i>
						</td>
						<td>{'LBL_REPLY_ALL_ACTION'|@getTranslatedString:$MODULE}</td>
					</tr>
				</table>
			</button>
		</div>
	{/if}
	{if $DETAILVIEWBUTTONSPERM.forward}
		<div style="float:left;">
			<button class="crmbutton small edit" type="button" onclick="javascript:OpenCompose('{$ID}','forward');">
				<table cellpadding="2" cellspacing="0" border="0">
					<tr height="20">
						<td style="padding-right:4px;">
							<i class="vteicon md-sm" title="{'LBL_FORWARD_ACTION'|@getTranslatedString:$MODULE}">forward</i>
						</td>
						<td>{'LBL_FORWARD_ACTION'|@getTranslatedString:$MODULE}</td>
					</tr>
				</table>
			</button>
		</div>
	{/if}
	<div style="float:left;padding:5px">
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
				{if $DETAILVIEWBUTTONSPERM.seen}
					{if $FLAG_BLOCK.seen.value eq 'no'|@getTranslatedString:$MODULE}
						<a href="javascript:;" onClick="flag({$ID},'seen',1);"><i class="vteicon" title="{'LBL_UNSEEN_ACTION'|@getTranslatedString:$MODULE}">markunread</i></a>
					{else}
						<a href="javascript:;" onClick="flag({$ID},'seen',0);"><i class="vteicon" title="{'LBL_SEEN_ACTION'|@getTranslatedString:$MODULE}">drafts</i></a>
					{/if}
				{/if}
				</td>
				<td>
				{if $DETAILVIEWBUTTONSPERM.flagged}
					{if $FLAG_BLOCK.flagged.value eq 'no'|@getTranslatedString:$MODULE}
						<a href="javascript:;" onClick="flag({$ID},'flagged',1);"><i class="vteicon" title="{'LBL_UNFLAGGED_ACTION'|@getTranslatedString:$MODULE}">flag</i></a>
					{else}
						<a href="javascript:;" onClick="flag({$ID},'flagged',0);"><i class="vteicon" title="{'LBL_FLAGGED_ACTION'|@getTranslatedString:$MODULE}" style="color:red">flag</i></a>
					{/if}
				{/if}
				</td>
				<td>
				{if $DETAILVIEWBUTTONSPERM.move}
					<a href="javascript:;" onClick="MoveDisplay(this,'single','{$ID}');"><i class="vteicon" title="{'LBL_MOVE_ACTION'|@getTranslatedString:$MODULE}" border="0">move_to_inbox</i></a>
				{/if}
				</td>
				<td>
				{* crmv@46601 *}
				{if $DETAILVIEWBUTTONSPERM.spam}
					{if $DETAILVIEWBUTTONSPERM.spam_status eq 'configure'}
						<a href="javascript:;" onClick="if (confirm('{'LBL_CONFIGURE_SPAM'|getTranslatedString:'Messages'}')) openPopup('index.php?module=Messages&action=MessagesAjax&file=Settings/index&operation=Folders','','','auto',600,500);"><i class="vteicon" title="{'LBL_SPAM_ACTION'|@getTranslatedString:$MODULE}" border="0">whatshot</i></a>
					{elseif $DETAILVIEWBUTTONSPERM.spam_status eq 'off'}
						<a href="javascript:;" onClick="Move('{$SPECIAL_FOLDERS.Spam}',{$ID});"><i class="vteicon" title="{'LBL_SPAM_ACTION'|@getTranslatedString:$MODULE}" border="0">whatshot</i></a>
					{else if $DETAILVIEWBUTTONSPERM.spam_status eq 'on'}
						<a href="javascript:;" onClick="Move('{$SPECIAL_FOLDERS.INBOX}',{$ID});"><i class="vteicon" title="{'LBL_UNSPAM_ACTION'|@getTranslatedString:$MODULE}" style="color:red" border="0">whatshot</i></a>
					{/if}
				{/if}
				{* crmv@46601e *}
				</td>
				<td>
				{if $DETAILVIEWBUTTONSPERM.delete}
					<a href="javascript:;" onClick="flag({$ID},'delete');"><i class="vteicon" title="{'LBL_TRASH_ACTION'|@getTranslatedString:$MODULE}">delete</i></a>
				{/if}
				</td>
			</tr>
		</table>
	</div>
</div>