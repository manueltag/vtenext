{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@104566 *}
<div class="history_line">
	<div class="history_line_img">
		{if $line.log.img.element eq 'i'}
			<i class="{$line.log.img.class}" {if !empty($line.log.img.data_first_letter)}data-first-letter="{$line.log.img.data_first_letter}"{/if}>{$line.log.img.html}</i>
		{/if}
	</div>
	<div class="history_line_info">
		<div class="history_line_title">
			<div>
				<div class="history_line_user_img">
					<img src="{$line.user.img}" alt="" title="{$line.user.full_name}" class="userAvatar">
				</div>
				<div class="history_line_user_name">
					{$line.user.full_name}
				</div>
				<div class="history_line_text">
					{$line.log.text}
				</div>
			</div>
			<div class="history_line_details">
				{include file="modules/ChangeLog/HistoryDetails.tpl"}
			</div>
		</div>
		<div class="history_line_date">
			<a href="javascript:;" title="{$line.date.formatted}" style="color: gray; text-decoration: none;">{$line.date.friendly}</a>
		</div>
	</div>
</div>