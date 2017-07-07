{* crmv@101683 *}
{if $sdk_mode eq 'detail'}
	{include file="DetailViewUI.tpl" keyid=52}
{elseif $sdk_mode eq 'edit'}
	{if $readonly eq 99}
		{include file='DisplayFieldsReadonly.tpl' uitype=52}
	{elseif $readonly eq 100}
		{include file="DisplayFieldsHidden.tpl" uitype=52}
	{else}
		{include file="EditViewUI.tpl" uitype=52}
	{/if}
{/if}