{*/*+*************************************************************************************
* The contents of this file are subject to the VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with the License
* The Original Code is: VTECRM
* The Initial Developer of the Original Code is VTECRM LTD.
* Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
* All Rights Reserved.
***************************************************************************************/*}

{* crmv@119414 *}

<aside id="leftPanel" data-minified="enabled">
	<ul class="moduleList">
		{foreach item=info from=$VisibleModuleList}
			{assign var="label" value=$info.name|@getTranslatedString:$info.name}
			{assign var="url" value="index.php?module="|cat:$info.name|cat:"&amp;action=index"}
			{assign var="moduleName" value=$info.name|strtolower}
			{assign var="moduleFirstLetter" value=$moduleName|substr:0:1|strtoupper}
			
			{assign var="class" value=""}
			{if $info.name eq $MODULE_NAME} 
				{assign var="class" value="active"}
			{/if}
			
			<li class="{$class}">
				<a href="{$url}">
					<div class="row">
						<div class="col-xs-2 vcenter">
							<i class="vteicon icon-module icon-{$moduleName}" data-first-letter="{$moduleFirstLetter}"></i>
						</div><!-- 
						 --><div class="col-xs-10 vcenter">
							<span class="moduleText">{$label}</span>
						</div>
					</div>
				</a>
			</li>
		{/foreach}
		
		{if !empty($LAST_MODULE_VISITED)}
			{assign var="moduleName" value=$LAST_MODULE_VISITED|strtolower}
			{assign var="moduleFirstLetter" value=$moduleName|substr:0:1|strtoupper}
			
			{assign var="class" value=""}
			{if $LAST_MODULE_VISITED eq $MODULE_NAME} 
				{assign var="class" value="active"}
			{/if}

			<li class="{$class}">
				<a href="index.php?module={$LAST_MODULE_VISITED}&amp;action=index">
					<div class="row">
						<div class="col-xs-2 vcenter">
							<i class="vteicon icon-module icon-{$moduleName}" data-first-letter="{$moduleFirstLetter}"></i>
						</div><!-- 
						 --><div class="col-xs-10 vcenter">
							<span class="moduleText">{$LAST_MODULE_VISITED|@getTranslatedString:$LAST_MODULE_VISITED}</span>
						</div>
					</div>
				</a>
			</li>
		{/if}
	</ul>
</aside>
	