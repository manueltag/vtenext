{*/*+*************************************************************************************
* The contents of this file are subject to the VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with the License
* The Original Code is: VTECRM
* The Initial Developer of the Original Code is VTECRM LTD.
* Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
* All Rights Reserved.
***************************************************************************************/*}

{* crmv@120738 *}

<link rel="stylesheet" type="text/css" href="{$RELPATH}themes/{$THEME}/settings.css">

{php}
	include_once "include/utils/SettingsUtils.php";
	$SU = SettingsUtils::getInstance();
	
	$this->assign("BLOCKS", $SU->getSettingsBlocks());
	$this->assign("FIELDS", $SU->getSettingsFields());
	
	if ($_REQUEST['reset_session_menu']) {
		unset($_SESSION['settings_last_menu']);
	}
	
	if ($_REQUEST['reset_session_menu_tab']) {
		unset($_SESSION['settings_last_menu']);
		$_SESSION['settings_last_menu'] = 'LBL_USERS';
	}
	
	$lastPage = $_SESSION['settings_lastpage'];
	$this->assign("LAST_PAGE", $lastPage);
{/php}

{literal}
	<script type="text/javascript">
		jQuery('body').on('click', '[data-action]', function(e) {
	        e.preventDefault();
	        var $this = jQuery(this), action = jQuery(this).data('action');
	        switch (action) {
	        	case 'submenu-toggle':
	        		$this.next().slideToggle(300),
		            $this.parent().toggleClass('toggled');
	        		break;
	        }
        });
        
        jQuery(document).ready(function() {
			setTimeout(function() {
				var leftMenuWidth = parseInt(jQuery('.settingsList').width());
				var leftMenuHeight = parseInt(jQuery('body').height()) - parseInt(jQuery('#vteHeader').height());
		    	jQuery('.settingsList').slimScroll({
					wheelStep: 10,
					height: leftMenuHeight + 'px',
					width: leftMenuWidth + 'px',
				});
				
				jQuery('.settingsList').parent().find('.slimScrollBar').hide();
			}, 200);
			
			var offset = 250;
			var duration = 300;
			
			jQuery(window).scroll(function() {
				if (jQuery(this).scrollTop() > offset) {
					jQuery('#back-top').fadeIn(duration);
				} else {
					jQuery('#back-top').fadeOut(duration);
				}
			});
			
			jQuery('#back-top').click(function(e) {
				e.preventDefault();
				
				jQuery('html, body').animate({
					scrollTop: 0
				}, duration);
				
				return false;
			});
        });
	</script>
{/literal}

<ul class="settingsList">

	<li class="subMenu backButton">
		<a href="index.php">
			<div class="row">
				<div class="col-xs-2 vcenter subIcon">
					<i class="vteicon md-link nohover">arrow_back</i>
				</div><!-- 
				 --><div class="col-xs-10 vcenter subLabel">
					<span class="">{'LBL_GO_BACK'|getTranslatedString}</span>
				</div>
			</div>
		</a>
	</li>

	{foreach key=BLOCKID item=BLOCK from=$BLOCKS}
		{if $BLOCK.label neq 'LBL_MODULE_MANAGER'}
			{assign var=blocklabel value=$BLOCK.label|@getTranslatedString:'Settings'}
			{assign var=image value=$BLOCK.image}
			{assign var=imagetype value=$BLOCK.image_type}
							
			<li class="subMenu">
				<a href="#" data-action="submenu-toggle">
					<div class="row">
						<div class="col-xs-2 vcenter subIcon">
							{if !empty($image) && $imagetype eq 'icon'}
								<i class="vteicon nohover">{$image}</i>
							{elseif !empty($image) && $imagetype eq 'image'}
								<img src="{$image}" />
							{/if}
						</div><!-- 
						 --><div class="col-xs-9 vcenter subLabel">
							<span class="">{$blocklabel}</span>
						</div><!--
						--><div class="expandButton"></div>
					</div>
				</a>
				
				<ul style="display:none">
					{foreach item=data from=$FIELDS.$BLOCKID}
						{if $data.link neq ''}
							{assign var=label_original value=$data.name}
							{assign var=label value=$data.name|@getTranslatedString:'Settings'}
							{assign var='settingsTabClass' value=''}
							{assign var="labelFirstLetter" value=$label|substr:0:1|strtoupper}
							
							{if $smarty.request.module_settings eq 'true' && $smarty.request.formodule eq $data.formodule
								&& $smarty.request.action eq $data.action && $smarty.request.module eq $data.module}
								{assign var='settingsTabClass' value='active'}
								{php}$_SESSION['settings_last_menu'] = $this->_tpl_vars['label_original'];{/php}
							{elseif $smarty.request.module_settings eq '' && $data.formodule eq ''
								&& $smarty.request.action eq $data.action && $smarty.request.module eq $data.module}
								{assign var='settingsTabClass' value='active'}
								{php}$_SESSION['settings_last_menu'] = $this->_tpl_vars['label_original'];{/php}
							{elseif $smarty.session.settings_last_menu eq $data.name}
								{assign var='settingsTabClass' value='active'}
							{/if}
							
							<li class="{$settingsTabClass}">
								<a href="{$data.link}&reset_session_menu=true">
									<div class="row">
										<div class="col-xs-2 vcenter subIcon">
											{$labelFirstLetter}
										</div><!--
										--><div class="col-xs-10 vcenter subLabel">
											{$label}
										</div>
									</div>
								</a>
							</li>
						{/if}
					{/foreach}
				</ul>
				
			</li>
							
		{/if}
	{/foreach}
</ul>

<a id="back-top" class="btn btn-info btn-fab" href="#">
	<i class="vteicon nohover">arrow_upward</i>
</a>

{literal}
<script type="text/javascript">
	/* Open the active block */
	var activeItem = jQuery('.settingsList > li.subMenu > ul > li.active');
	if (activeItem.length > 0) {
		//activeItem.parent().prev().click();
		activeItem.parent().show();
        activeItem.parent().parent().toggleClass('toggled');
		activeItem.parent().parent().addClass('active');
	}
</script>
{/literal}
