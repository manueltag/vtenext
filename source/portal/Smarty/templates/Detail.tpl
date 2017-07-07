{*+*************************************************************************************
* The contents of this file are subject to the VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with
the License * The Original Code is: VTECRM * The Initial Developer of
the Original Code is VTECRM LTD. * Portions created by VTECRM LTD are
Copyright (C) VTECRM LTD. * All Rights Reserved.
***************************************************************************************}

<!-- <table> -->
<!-- 	<tr> -->
<!-- 		<td> -->
<!--			<input class="crmbutton small cancel" type="button" value="{'LBL_BACK_BUTTON'|getTranslatedString}" onclick="window.history.back();"/>-->
<!-- 		</td> -->
<!-- 	</tr> -->
<!-- </table> -->

{*$FIELDLIST*}
<div class="row" style="margin-top: 5px; margin-bottom: 30px;">
	<div class="col-md-2" style="float: left">
		<input align="left" class="btn btn-default" type="button"
			value="{'LBL_BACK_BUTTON'|getTranslatedString}"
			onclick="window.history.back();" />
	</div>

</div>

{*
<div class="row">
{foreach from=$FIELDLIST item=LIST key=BLOCK}
	<div class="col-md-12">
		<h3 class="value">
			<small>{$LIST.fieldlabel}</small>
		</h3>
	</div>
	<div class="col-md-12 linerow">
		<h3>&nbsp;{$LIST.fieldvalue}</h3>
	</div>
{/foreach}
</div>
*}

{* crmv@90004 *}
<div class="table col-md-12">
	{foreach from=$FIELDLIST item=LIST key=BLOCK}
		<div class="col-md-6  col-xs-12"" style="border: 0px">
			<h3 class="value" style="padding:5px 0px">
				{if $LIST.fieldname eq 'filename'}
					<small>{$LIST.fieldlabel|getTranslatedString}</small>
					<i class="material-icons-download icon_file_download"></i>
				{else}
					<small>{$LIST.fieldlabel|getTranslatedString}</small>
				{/if}
			</h3>
			<div class="linerow">
				<h3>&nbsp;{$LIST.fieldvalue|getTranslatedString}</h3>
			</div>
		</div>
	{/foreach}
</div>
{* crmv@90004e *}
{*
{assign var="N" value=0}
<table class="table col-md-12" id="potential">
	{foreach from=$FIELDLIST item=LIST key=BLOCK}
		{if $N == 0 && $N%2 == 0}
			<tr>
		{/if}

			<td class="col-md-6" style="border: 0px">
				<h3 class="value" style="padding:5px 0px">
					<small>{$LIST.fieldlabel}</small>
				</h3>
				<div class="linerow">
					<h3>&nbsp;{$LIST.fieldvalue}</h3>
				</div>
			</td>

		{if $N != 0 && $N%2 != 0}
			</tr>
		{/if}
		{assign var="N" value=$N+1}
	{/foreach}
</table>
*}

<table class="col-md-12">
<tr>
<td class="col-md-6">
</td>
</tr>
</table>

<!-- <tr><td colspan ="4"><table width="100%"> -->
<!-- </table></td></tr> -->
<!-- </table></td></tr></table></td></tr></table> -->

{if !empty($OTHERBLOCKS)} {*foreach item=other_blocks
from=$OTHERBLOCKS*} {*foreach item=_tab from=$EXTRADETAILTABS
name="extraDetailForeach"*}
<!--
<table class="table"> 
<tr>
	<td colspan="4">
		<table width="100%">
			<tr>
				<td align="left"><input class="crmbutton small cancel" type="button"
					value="{'LBL_BACK_BUTTON'|getTranslatedString}"
					onclick="window.history.back();" /></td>
				<td align="right"><input class="crmbutton small cancel"
					type="button"
					value="'{'LBL_RAISE_TICKET_BUTTON'|getTranslatedString}"
					onclick="location.href=\'index.php?module=HelpDesk&action=index&fun=newticket&projectid='.$projectid.'\'" />
				</td>
			</tr>
		</table>
	</td>
</tr>-->
{*/foreach*}
	<div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12 panel panel-default" style="padding:0px;">
{foreach from=$OTHERBLOCKS key=keyotherblocks item=itemotherblocks}
	<div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12 panel-heading ">
		{foreach from=$itemotherblocks.HEADER item=collotherblocks }
			<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">{$collotherblocks}</div>
		{/foreach}
	</div>
	<div class="row col-lg-12">
		{foreach from=$itemotherblocks.ENTRIES item=entriesotherblocks }
			{if $entriesotherblocks neq 'LBL_NOT_AVAILABLE'}
			{foreach from=$entriesotherblocks item=entryotherblock }
				<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">{$entryotherblock}</div>
			{/foreach}	
			{/if}
		{/foreach}
	</div>
{/foreach}
</div>
<!--
{foreach from=$OTHERBLOCKS key=label item=other_block}
<tr>
	<td class="detailedViewHeader" colspan="4">
		 <b>{$label}</b>
	</td>
</tr>
<tr>
	<td colspan="4">
		<table border="0" width="100%" cellspacing="0" cellpadding="5">
			'
		 				echo getblock_fieldlistview($result,"$projecttaskblock");	
			{$other_block}
			<br>
		</table>
	</td>
</tr>
-->
<tr>
	<td colspan="4">&nbsp;</td>
</tr>
</table>
{/foreach} {/if}
