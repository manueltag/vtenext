{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ***************************************************************************************}

{* crmv@3085m crmv@3086m crmv@57221 *}

<script language="JavaScript" type="text/javascript" src="{"include/js/RelatedList.js"|resourcever}"></script>

{* crmv@64719 *}
{if $OLD_STYLE eq true}
	{include file='CustomLinks.tpl' CUSTOM_LINK_TYPE="DETAILVIEWBASIC"}
	{include file='CustomLinks.tpl' CUSTOM_LINK_TYPE="DETAILVIEW"}
{/if}
{* crmv@64719e *}

<div id="turboLiftRelationsContainer">
	{if $OLD_STYLE eq true}
		{include file="TurboliftRelationsOldStyle.tpl"}
	{else}
		{include file="TurboliftRelations.tpl"}
	{/if}
</div>

{if $OLD_STYLE eq true}
	{include file="TurboliftUp.tpl"}
{/if}