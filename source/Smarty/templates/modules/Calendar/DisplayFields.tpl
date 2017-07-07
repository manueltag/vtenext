{*/*+*************************************************************************************
* The contents of this file are subject to the VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with the License
* The Original Code is: VTECRM
* The Initial Developer of the Original Code is VTECRM LTD.
* Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
* All Rights Reserved.
***************************************************************************************/*}

{* crmv@98866 *}

{* crmv@103922 *}
<script type="text/javascript" src="{"include/js/dtlviewajax.js"|resourcever}"></script>

<span id="crmspanid" style="display:none;position:absolute;" onmouseover="show('crmspanid');">
   <a class="edit" href="javascript:;">{$APP.LBL_EDIT_BUTTON}</a>
</span>

<input type="hidden" id="hdtxt_IsAdmin" value="{php}global $current_user; (is_admin($current_user))?$v='1':$v='0'; echo $v;{/php}">
{* crmv@103922e *}

{if empty($MODE) || $MODE eq 'edit'}
	{include file="modules/Calendar/EditViewBlock.tpl"}
{else}
	{include file="modules/Calendar/DetailViewBlock.tpl"}
{/if}

<script type="text/javascript">
	var cPopTitle1 = "{$SINGLE_MOD|@getTranslatedString:$MODULE}";
	var cPopTitle2 = {$JS_NAME};
</script>