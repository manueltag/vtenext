{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@57221 *}
<div id="buttons_{$label}" style="float:right;display:none;">
	{if $ajaxEditablePerm}
		<a class="simpleSave" href="javascript:;" onclick="{if !empty($AJAXSAVEFUNCTION)}{$AJAXSAVEFUNCTION}{else}dtlViewAjaxSave{/if}('{$label}','{$MODULE}',{$uitype},'{$keytblname}','{$keyfldname}','{$ID}');fnhide('crmspanid');">{$APP.LBL_SAVE_LABEL}</a> -
		<a class="simpleCancel" href="javascript:;" onclick="hndCancel('dtlview_{$label}','editarea_{$label}','{$label}')">{$APP.LBL_CANCEL_BUTTON_LABEL}</a>
	{/if}
</div>