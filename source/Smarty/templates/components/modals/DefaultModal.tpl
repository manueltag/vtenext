{* /*+*************************************************************************************
* The contents of this file are subject to the VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with the License
* The Original Code is: VTECRM
* The Initial Developer of the Original Code is VTECRM LTD.
* Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
* All Rights Reserved.
***************************************************************************************/ *}

{* -------------------------------------
ID: @string the id of the modal
CLASS: @string the class of the modal
TITLE: @string the title of the modal
CONTENT: @string the description of the modal
CLOSE_FUNC: @string the close function of the modal
------------------------------------- *}

{literal}
	<style>
		.modal-dialog { margin: 0px; }
	</style>
{/literal}
                
<div {if !empty($ID)}id="{$ID}"{/if} class="modal-dialog{if !empty($CLASS)} {$CLASS}{/if}">
	
	<div class="modal-content">

		<div id="{"`$PARENT_ID`_Handle"}" class="modal-header">
			<button type="button" class="close" aria-hidden="true" onclick="{$CLOSE_FUNC}">×</button>
			
			{if !empty($TITLE)}
				<h4 class="modal-title">{$TITLE}</h4>
			{/if}
		</div>
		
		<div class="modal-body">
			{if !empty($CONTENT)}{$CONTENT}{/if}
		</div>
		
		<div class="modal-footer">
			<button type="button" class="crmbutton btn-default" onclick="{$CLOSE_FUNC}">Close</button>
			{if !empty($BUTTONS)}{$BUTTONS}{/if}
		</div>
	
	</div>

</div>
