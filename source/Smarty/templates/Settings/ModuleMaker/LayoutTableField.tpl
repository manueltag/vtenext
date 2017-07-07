{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@106857 *}
{* some extra translations *}

<script type="text/javascript">
	var ModuleMakerTrans = {ldelim}{rdelim};
	{if count($TRANS) > 0}
	{foreach key=lbl item=tr from=$TRANS}
	ModuleMakerTrans['{$lbl}'] = '{"'"|str_replace:"\'":$tr}';
	{/foreach}
	{/if}
</script>

{* javascript for the module maker *}
<script type="text/javascript" src="modules/Settings/ModuleMaker/ModuleMaker.js"></script>
<script type="text/javascript" src="include/js/layouteditor.js"></script>

{* some CSS *}
<style type="text/css">
{literal}
	.mmaker_step_field_cell {
		min-height: 40px;
		height: 40px;
	}
	.floatingDiv {
		display:none;
		position: fixed;
	}
	.floatingHandle {
		padding: 5px;
		cursor: move;
	}
	.newFieldMnu {
		text-decoration: none;
		color: black;
		display: block;
		padding-top: 5px;
		padding-bottom: 5px;
		padding-left: 5px;
		background-repeat: no-repeat;
		background-position: left;
	}
	.newFieldMnuSelected {
		background-color: #0099ff;
		color: white;
	}
	.newfieldprop {
		display:none;
	}
{/literal}
</style>

{* crmv@102879 - add table field *}
{include file="Settings/ModuleMaker/Step2TableField.tpl"}

{* enable dragging for every floating div *}
<div id="fieldinfo" style="display:none;">{$FIELDINFO}</div>
<script type="text/javascript">
{literal}
(function() {
	jQuery(document).ready(function(){
		setTimeout(function() {
			MlTableFieldConfig.initAddTableFieldPopup({/literal}{$BLOCKID},'{$FIELDID}',jQuery('#fieldinfo').text(){literal});
		}, 100);
	});
})();
{/literal}
</script>