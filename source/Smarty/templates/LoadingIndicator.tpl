{* crmv@119414 *}
{assign var=overrides value=$THEME_CONFIG.tpl_overrides}
{if !empty($overrides[$smarty.template])}
	{include file=$overrides[$smarty.template]}
	{php}return;{/php}
{/if}
{* crmv@119414e *}

{* crmv@104533 *}
<i class="dataloader" data-loader="circle" id="{$LIID}" style="vertical-align:middle;{$LIEXTRASTYLE}"></i>