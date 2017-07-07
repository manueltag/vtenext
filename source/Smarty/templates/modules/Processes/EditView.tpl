{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@99316 crmv@106857 *}

{* crmv@93990 *}
{if $smarty.request.ajxaction eq 'DYNAFORMPOPUP'}
	<script type="text/javascript">
		jQuery('#vte_menu .mailClientWriteEmailHeader').append('<h5 style="float:left;">&gt; <a href="index.php?module=Processes&action=DetailView&record={$ID}" target="_blank">{$PROCESS_NAME}</a></h5>');
	</script>
	<div style="padding:10px">
		{$REQUESTED_ACTION}
	</div>
{/if}
{* crmv@93990e *}

{include file=$TEMPLATE}

{if $ENABLE_DFCONDITIONALS eq true}
	<input type="hidden" id="enable_dfconditionals" value="1">
	<div id="df_fields" style="display:none">{$DFFIELDS}</div>
	<script type="text/javascript">
		DynaFormScript.initEditViewConditionals('{$ID}','{$DFFIELDS|addslashes}',true);
	</script>
{/if}