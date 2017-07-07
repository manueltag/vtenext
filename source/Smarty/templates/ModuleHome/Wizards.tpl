{* crmv@83340 crmv@96233 crmv@102379 *}

<div id="wizards_list_{$BLOCK.blockid}" class="wizards_list">
{if count($WIZARDS) > 0}
	{foreach item=WIZ from=$WIZARDS}
		<button class="btn btn-lg btn-primary" onclick="{$WIZ.handler}">{$WIZ.title}</button>
	{/foreach}
</div>
{else}
	<p>{$APP.LBL_NO_AVAILABLE_WIZARDS}</p>
{/if}
