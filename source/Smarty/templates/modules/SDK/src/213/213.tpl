{* crmv@98570 *}
{if $sdk_mode eq 'detail'}
	<button title="{$label}" type="button" name="{$keyfldname}" class="crmbutton small" onclick="{$keyval.onclick}">{$label}</button>
	<script type="text/javascript">
	{$keyval.code}
	</script>
{elseif $sdk_mode eq 'edit'}
	<button title="{$fldlabel}" type="button" name="{$fldname}" class="crmbutton small" onclick="{$fldvalue}">{$fldlabel}</button>
	<script type="text/javascript">
	{$secondvalue}
	</script>
{/if}