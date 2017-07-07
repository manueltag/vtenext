{* /*+*************************************************************************************
* The contents of this file are subject to the VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with the License
* The Original Code is: VTECRM
* The Initial Developer of the Original Code is VTECRM LTD.
* Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
* All Rights Reserved.
***************************************************************************************/ *}

{* -------------------------------------
ID: @string the id of the button
CLASS: @string the class of the button
TYPE: @string the type of the button (default/primary/success/warning/danger/info)
ICON: @string the icon of the button
MINI: @boolean true/false
------------------------------------- *}

{if empty($TYPE)}
	{assign var=TYPE value="default"}
{/if}

{assign var=button_class value="btn btn-fab"}

{if $MINI eq true}
	{assign var=button_class value="$button_class-mini"}
{/if}

{literal}
	<style>
		.btn-fab {
			position: relative;		
		}
		
		.btn-fab i.vteicon {
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			-webkit-transform: translate(-50%, -50%);
			-moz-transform: translate(-50%, -50%);
			-o-transform: translate(-50%, -50%);
			-ms-transform: translate(-50%, -50%);
		}
	</style>
{/literal}

<a href="javascript:void(0)" {if !empty($ID)}id="{$ID}"{/if} class="{$button_class} btn-{$TYPE}{if !empty($CLASS)} {$CLASS}{/if}">
	
	<i class="vteicon">{$ICON}</i>
	
</a>
