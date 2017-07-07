{* crmv@64542 *}

{* It would be nice to just include the field edit file, but that one is a mess, and the backend reads from the db *}
{* include file="Settings/LayoutBlockEntries.tpl" *}

{* So let's do it in javascript only *}

<div>
	<p>{$MOD.LBL_MMAKER_STEP2_INTRO}</p>
</div>

{* include blocks table *}
<div id="mmaker_div_allblocks">
{include file="Settings/ModuleMaker/Step2Fields.tpl"}
</div>