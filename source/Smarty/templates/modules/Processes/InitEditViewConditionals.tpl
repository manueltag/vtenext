{* crmv@112297 *}
{if $MODULE neq 'Processes' && $ENABLE_CONDITIONALS eq true}
	<input type="hidden" id="enable_conditionals" value="1">
	<script type="text/javascript">
		ProcessScript.initEditViewConditionals();
	</script>
{/if}