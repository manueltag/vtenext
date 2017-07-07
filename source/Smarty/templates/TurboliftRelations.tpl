{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@82419 crmv@104568 *}

{* crmv@119414 *}
{assign var=overrides value=$THEME_CONFIG.tpl_overrides}
{if !empty($overrides[$smarty.template])}
	{include file=$overrides[$smarty.template]}
	{php}return;{/php}
{/if}
{* crmv@119414e *}

<div class="rightMailMergeHeader">{'Relations'|getTranslatedString}</div>
{assign var="RELATION_LIMIT" value="8"} {* crmv@103926 *}
<ul class="nav nav-pills nav-stacked turbolift">
{foreach item=RELATION from=$RELATIONS name="tl_related_foreach"}
	{assign var=header value=' '|@str_replace:'':$RELATION.header}
	{assign var=related value=$MODULE|cat:"_"|cat:$header}
	{if $RELATION.type eq 'card'}
		{assign var=entity_id value=$RELATION.id}
		{assign var=info value=$RELATION.card}
		{if !empty($info.onclick)}
			{assign var=CARDONCLICK value=$info.onclick}
		{elseif $info.link neq ''}
			{assign var=CARDONCLICK value=$TURBOLIFT_HREF_TARGET_LOCATION|cat:"='"|cat:$info.link|cat:"';"}
		{/if}
		{include file='Card.tpl' CARDID="tl_"|cat:$related CARDMODULE=$info.module CARDMODULE_LBL=$info.modulelbl PREFIX=$info.prefix CARDNAME=$info.name CARDDETAILS=$info.details CARDONCLICK=$CARDONCLICK DISPLAY=$RELATION.display IMG=$info.img}
	{else}
		{if $RELATION.fixed}
			{assign var=fixedParam value="true"}
		{else}
			{assign var=fixedParam value="false"}
		{/if}
		{assign var=CARDONCLICK value="loadDynamicRelatedList(this,'`$RELATION.relationId`', '`$related`','module=`$MODULE`&action=`$MODULE`Ajax&file=DetailViewAjax&record=`$ID`&ajxaction=LOADRELATEDLIST&header=`$RELATION.header`&relation_id=`$RELATION.relationId`&actions=`$RELATION.actions`&load_header=yes', `$fixedParam`);"}
		{* crmv@43864	crmv@59091 *}
		<!--div relation_id="{$RELATION.relationId}" id="tl_{$related}" class="turboliftEntry turboliftEntryWithImage" onClick="{$CARDONCLICK}" style="{if $smarty.foreach.tl_related_foreach.index ge $RELATION_LIMIT}display:none{else}display:table{/if};width:100%;height:100%">	{* crmv@62415 *}
			<div></div>
			<div style="display:table-cell;padding:5px;box-sizing:border-box;width:70%;vertical-align:middle;">
				<div style="display:table;height:100%;width:100%;">
					{if $RELATION.header}
						<span style="display:table-cell;vertical-align:middle;">{$RELATION.header|getTranslatedString:$RELATION.module}</span>
					{else}
						<span style="display:table-cell;vertical-align:middle;">{$RELATION.module|getTranslatedString:$RELATION.module}</span>
					{/if}
				</div>
			</div>
			<div style="display:table-cell;padding:5px;box-sizing:border-box;width:30%;text-align:right;vertical-align:middle;">
				<div style="display:table;width:100%;height:100%">
					<span style="display:table-cell;vertical-align:middle;">{$RELATION.count}</span>
				</div>
			</div>
		</div-->
		<li class="turboliftEntry turboliftEntryWithImage {if $smarty.foreach.tl_related_foreach.index ge $RELATION_LIMIT}hidden{/if}" relation_id="{$RELATION.relationId}" id="tl_{$related}" onClick="{$CARDONCLICK}">
			{assign var="module" value=$RELATION.module}
			{assign var="module_lower" value=$module|strtolower}
			{assign var="translated_module" value=$RELATION.module|getTranslatedString:$RELATION.module}
			{assign var="module_first_letter" value=$translated_module|substr:0:1|strtoupper}
			
			{if $RELATION.header}
				{assign var=RELLABEL value=$RELATION.header|getTranslatedString:$RELATION.module}
			{else}
				{assign var=RELLABEL value=$translated_module}
			{/if}
			
			<div class="row no-gutter">
				<div class="col-sm-12">
					<div class="col-md-2 hidden-sm vcenter turboliftIcon">
						<div class="vcenter text-left">
							<i class="vteicon icon-module icon-{$module_lower}" data-first-letter="{$module_first_letter}"></i>
						</div>
					</div><!-- 
					--><div class="col-sm-{if $RELATION.count > 0}8{else}11{/if} col-md-{if $RELATION.count > 0}7{else}9{/if} vcenter turboliftLabel" title="{$RELLABEL}">
						<span>{$RELLABEL}</span>
					</div><!-- 
					 --><div class="col-sm-{if $RELATION.count > 0}4{else}1{/if} col-md-{if $RELATION.count > 0}3{else}1{/if} vcenter">
						<span class="badge pull-right">{$RELATION.count}</span>
					</div>
				</div>
			</div>
		</li>
		
		{* crmv@43864e	crmv@59091e *}
	{/if}
{/foreach}
{if $smarty.foreach.tl_related_foreach.total ge $RELATION_LIMIT}
	<li id="tl_MoreRelated" class="turboliftEntry turboliftEntryWithImage" onClick="Turbolift.showall(this);">
		<span>{$APP.LBL_MORE}</span>
	</li>
	{* TODO ad less*}
{/if}
</ul>


<script type="text/javascript">
{literal}

var Turbolift = {
	
	init: function() {
		var cont = jQuery('#turboLiftContainer > div');
		var turbotop = jQuery('#vte_menu').height();
		var turboh = jQuery(window).height() - turbotop - jQuery('#vte_footer').height() - 20;
		
		cont.css('top', turbotop);

		cont.mCustomScrollbar({
			set_height: turboh,
			axis: 'y',
			mouseWheelPixels: 100,
			theme: "dark-thick",
			autoHideScrollbar: true,
			scrollInertia: 0,
			advanced: {
				updateOnContentResize: true,
			}
		});
		cont.show();
		
	},
	
	showall: function(obj) {
		jQuery('#turboLiftRelationsContainer').find('.turboliftEntry').removeClass('hidden');
		jQuery(obj).hide();
		Turbolift.alignScroll();	
	},
	
	alignScroll: function() {
		var cont = jQuery('#turboLiftContainer > div');
		var sel = cont.find('.turboliftEntrySelected');
		
		var pos = 0;
		if (sel.length > 0) {
			pos = sel.position().top - 5;
		}
		
		var fakeid = cont.find('li.fakeLongDiv'),
			delay = 10;
		if (fakeid.length == 0) {
			cont.find('ul.turbolift').append('<li class="fakeLongDiv" style="height:600px"></li>');
			cont.mCustomScrollbar('update');
			delay = 100;
		}
		
		if (sel.length > 0) {
			setTimeout(function() {
				cont.mCustomScrollbar('scrollTo', pos, {
					scrollInertia: 600,		// like jquery slow
				});
			}, delay);
		}
	}
	
}

jQuery(document).ready(function() {
	Turbolift.init();
});

{/literal}

{* crmv@54375 *}
{if !empty($OPENRELATEDLIST)}
	if (jQuery("#{$OPENRELATEDLIST}").length > 0) jQuery("#{$OPENRELATEDLIST}").click();
{/if}
{* crmv@54375e *}
</script>
