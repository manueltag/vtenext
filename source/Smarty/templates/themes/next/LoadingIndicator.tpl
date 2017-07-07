{* crmv@104533 *}
{if $LIOLDMODE eq true}
<i class="dataloader" data-loader="circle" id="{$LIID}" style="vertical-align:middle;{$LIEXTRASTYLE}"></i>
{else}
<div class="wrap go" id="{$LIID}" style="{$LIEXTRASTYLE}">
	<div class="linearloader bar">
		<div class="hdrBg"></div>
	</div>
</div>
{/if}