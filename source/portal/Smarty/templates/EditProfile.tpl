{*+*************************************************************************************
* The contents of this file are subject to the VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with
the License * The Original Code is: VTECRM * The Initial Developer of
the Original Code is VTECRM LTD. * Portions created by VTECRM LTD are
Copyright (C) VTECRM LTD. * All Rights Reserved.
***************************************************************************************}
<!-- crmv@57342 -->

<!-- Javascrip -->
<script src="js/jquery-1.11.0.js"></script>
<script src="js/bootstrap.min.js"></script>

<!--<div class="row" style="margin-top: 5px; margin-bottom: 30px;">
	<div class="col-md-2" style="float: left">
		<input align="left" class="btn btn-default" type="button" value="{'LBL_BACK_BUTTON'|getTranslatedString}" onclick="window.history.back();" />
 	</div> 
 </div> -->

<form action="index.php" method="post">
<input type="hidden" name="module" value="Contacts">
<input type="hidden" name="action" value="Save">
<input type="hidden" name="id" value="{$CUSTOMERID}">
<input type="hidden" name="ajax" value="true">

<div class="row" style="margin-top: 5px; margin-bottom: 30px;">
	<div class="col-md-10  col-sm-9 col-xs-6">
		<button align="left" class="btn btn-default" type="button" value="{'LBL_BACK_BUTTON'|getTranslatedString}" onclick="location.href='?module=Contacts&action=index&id={$CUSTOMERID}&profile=yes'"/>{'LBL_BACK_BUTTON'|getTranslatedString}</button>
	</div>	

	<div class="col-md-2 col-sm-2 col-xs-4" style="float:right">
		<button class="btn btn-success" type="submit" value="{'LBL_SAVE'|getTranslatedString}">{'LBL_SAVE'|getTranslatedString}</button>
	</div>
</div>

<h1 class="page-header" style="margin-bottom: 0px">{'UPDATE_PROFILE'|getTranslatedString}</h1>

<div class="row">
	{foreach from=$FIELDLIST item=FIELD}
		 {foreach from=$FIELD item=VALUE}
			<div class="col-md-12">
				<h3>
					<small>{$VALUE.0|getTranslatedString}</small>
				</h3>
			</div>
			<div class="col-md-12">
			{if in_array($VALUE.2,$SELECTFIELDS)}
				<select name="{$VALUE.2}" class="form-control">
					{if $VALUE.1 eq "Yes"}
						<option value="1" selected>{"LBL_YES"|getTranslatedString}</option>
						<option value="0">{"LBL_NO"|getTranslatedString}</option>
					{else}
						<option value="1">{"LBL_YES"|getTranslatedString}</option>
						<option value="0" selected>{"LBL_NO"|getTranslatedString}</option>
					{/if}
				</select>
			{else}
				{if $VALUE.2 eq 'cf_906'}
					<select name="{$VALUE.2}" class="form-control">
					{if $VALUE.1 eq "M"}
						<option value="M" selected>M</option>
						<option value="F">F</option>
					{else}
						<option value="M">M</option>
						<option value="F" selected>F</option>
					{/if}
					</select>
				{elseif $VALUE.2 eq 'cf_825'}
					<select name="{$VALUE.2}" class="form-control">
						{foreach from=$PROVINCIA item=prov}
							<option value="{$prov}" {if $prov eq $VALUE.1}selected{/if}>{$prov}</option>
						{/foreach}
					</select>
				{elseif $VALUE.2 eq 'cf_826'}
					<select name="{$VALUE.2}" class="form-control">
						{foreach from=$REGIONE item=regio}
							<option value="{$regio}" {if $regio eq $VALUE.1}selected{/if}>{$regio}</option>
						{/foreach}
					</select>
				{elseif $VALUE.2 eq 'cf_827'}
					<select name="{$VALUE.2}" class="form-control">
						{foreach from=$STATOUSA item=stato}
							<option value="{$stato}" {if $stato eq $VALUE.1}selected{/if}>{$stato}</option>
						{/foreach}
					</select>
				{elseif $VALUE.2 eq 'cf_828'}
					<select name="{$VALUE.2}" class="form-control">
						{foreach from=$NAZIONE item=nazio}
							<option value="{$nazio}" {if $nazio eq $VALUE.1}selected{/if}>{$nazio}</option>
						{/foreach}
					</select>
				{else}
					{if $VALUE.2 eq 'birthday'}
						<input name="{$VALUE.2}" type="date" class="form-control" value="{$VALUE.1}">
					{elseif $VALUE.2 eq 'mobile'}
						<input name="{$VALUE.2}" type="number" class="form-control" value="{$VALUE.1}">
					{else}
						<input name="{$VALUE.2}" type="text" class="form-control" value="{$VALUE.1}">
					{/if}
				{/if}
			{/if}
		</div>
	{/foreach}
{/foreach}
</div>
</form>

<!-- crmv@57342e -->