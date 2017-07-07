{* /*+*************************************************************************************
* The contents of this file are subject to the VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with the License
* The Original Code is: VTECRM
* The Initial Developer of the Original Code is VTECRM LTD.
* Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
* All Rights Reserved.
***************************************************************************************/ *}

{* -------------------------------------
ID: @string the id of the panel
CLASS: @string the class of the panel
TYPE: @string the type of the panel (default/primary/success/warning/danger/info)
TITLE: @string the title of the panel
CONTENT: @string the description of the panel
FOOTER: @string the footer part of the panel
------------------------------------- *}

{if empty($TYPE)}
	{assign var=TYPE value="default"}
{/if}

<div {if !empty($ID)}id="{$ID}"{/if} class="panel panel-{$TYPE}{if !empty($CLASS)} {$CLASS}{/if}">

	{if !empty($TITLE)}
		<div class="panel-heading">
			<h3 class="panel-title">{$TITLE}</h3>
		</div>
	{/if}
	
	{if !empty($CONTENT)}
		<div class="panel-body">{$CONTENT}</div>
	{/if}
	
	{if !empty($FOOTER)}
		<div class="panel-footer">{$FOOTER}</div>
	{/if}
	
</div>
