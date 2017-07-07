{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}

{if empty($BN_COLOR)}
	{assign var="BN_COLOR" value="#FFF"}
{/if}
{if empty($BN_BGCOLOR)}
	{assign var="BN_BGCOLOR" value="#7B7E84"}
{/if}
<span {if !empty($BN_ID)}id="{$BN_ID}"{/if} {if !empty($BN_ONCLICK)}onClick="{$BN_ONCLICK}"{/if} style="font-weight:normal;font-size:11px;padding:2px 6px;border-radius:1em;color:{$BN_COLOR};background:{$BN_BGCOLOR};">{$COUNT}</span>