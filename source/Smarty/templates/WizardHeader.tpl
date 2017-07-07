{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}

{* crmv@OPER6317 crmv@96233 crmv@98866 *}

{include file="SmallHeader.tpl" HEAD_INCLUDE="all"}
{include file='CachedValues.tpl'}

<script language="JavaScript" type="text/javascript" src="{"include/js/Wizard.js"|resourcever}"></script>
<script type="text/javascript" src="include/ckeditor/ckeditor.js"></script>
<script language="JavaScript" type="text/javascript" src="modules/SDK/SDK.js"></script>

<link href="themes/{$THEME}/wizard.css" rel="stylesheet" type="text/css" />

<input type="hidden" name="wizardid" id="wizardid" value="{$WIZARD_ID}" />
<input type="hidden" name="module" id="module" value="{$MODULE}" />
<input type="hidden" name="wizard_parent_module" id="wizard_parent_module" value="{$PARENT_MODULE}"/>
<input type="hidden" name="wizard_parent_id" id="wizard_parent_id" value="{$PARENT_ID}"/>

{* popup status *}
<div id="status" name="status" style="display:none;position:fixed;right:20px;top:16px;z-index:100">
	{include file="LoadingIndicator.tpl"}
</div>
