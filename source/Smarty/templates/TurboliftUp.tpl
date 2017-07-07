{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@26896 *}
{if $SinglePane_View eq 'true'}
	<div id="turbolift_anchor" height="1px"></div>
	<div id="turbolift_up" class="turboliftEntry turboliftEntryWithImage" style="display:none;" onClick="goToTopByScroll();">
		<div style="float:left;padding:5px;">
			<span style="padding:2px;" >{'LBL_BACK_TO_TOP'|@getTranslatedString}</span>
		</div>
		<div style="float:right;">
			<img style="height:20px;padding:2px;vertical-align:middle;" title="{'LBL_BACK_TO_TOP'|@getTranslatedString}" src="{'arrow_up.png'|@vtiger_imageurl:$THEME}" border="0" />
		</div>
	</div>
	<script type="text/javascript">
	{literal}
	jQuery(window).bind("scroll", function(){ moveTurboliftUp() });
	jQuery(document).ready(function() {
		moveTurboliftUp();
	});
	function moveTurboliftUp(){
		var eTop = jQuery('#turbolift_anchor').offset().top;
		jQuery('#turbolift_up').css('width',jQuery('#turbolift_up').parent().css('width'));
		jQuery(window).scroll(function() {
			if (eTop  - jQuery(window).scrollTop() < jQuery('#vte_menu_white').height()) {
				jQuery('#turbolift_up').show();
				jQuery('#turbolift_up').css('position','fixed');
				jQuery('#turbolift_up').css('top',jQuery('#vte_menu_white').height());
			} else {
				jQuery('#turbolift_up').hide();
				jQuery('#turbolift_up').css('position','absolute');
				jQuery('#turbolift_up').css('top',jQuery('#turbolift_anchor').offset().top + jQuery('#turbolift_anchor').height());
			}
		});
	}
	{/literal}
	function goToTopByScroll(){ldelim}
		jQuery('html,body').animate({ldelim}scrollTop: 0{rdelim},'slow');
	{rdelim}
	</script>
{/if}
{* crmv@26896e *}