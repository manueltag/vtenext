{if $sdk_mode eq 'detail'}
	{include file="DetailViewUI.tpl" keyid=15}
{elseif $sdk_mode eq 'edit'}
	{if $readonly eq 99}
		{include file='DisplayFieldsReadonly.tpl' uitype=15}
	{elseif $readonly eq 100}
		{include file="DisplayFieldsHidden.tpl" uitype=15}
	{else}
		{include file="EditViewUI.tpl" uitype=15}
	{/if}
{/if}