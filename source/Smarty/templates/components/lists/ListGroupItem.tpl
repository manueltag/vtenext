{* /*+*************************************************************************************
* The contents of this file are subject to the VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with the License
* The Original Code is: VTECRM
* The Initial Developer of the Original Code is VTECRM LTD.
* Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
* All Rights Reserved.
***************************************************************************************/ *}

{* -------------------------------------
ID: @string the id of the item
CLASS: @string the class of the item
TYPE: @string icon/image
TITLE: @string the title of the item
CONTENT: @string the description of the item
SRC: @string the source of the image
ICON: @string the name of the icon
------------------------------------- *}

<div {if !empty($ID)}id="{$ID}"{/if} class="list-group-item{if !empty($CLASS)} {$CLASS}{/if}">

	{if $TYPE eq 'image'}
		<div class="row-picture">
			<img class="circle" src="{$SRC}" alt="icon">
		</div>
	{elseif $TYPE eq 'icon'}
		<div class="row-action-primary">
			<i class="vteicon">{$ICON}</i>
		</div>
	{elseif $TYPE eq 'check'}
		<div class="row-action-primary checkbox">
			<label>
				<input type="checkbox">
				<span class="checkbox-material">
					<span class="check"></span>
				</span>
			</label>
		</div>
	{/if}

	<div class="row-content">
		<h4 class="list-group-item-heading">{$TITLE}</h4>
		
		{if !empty($CONTENT)}
			<p class="list-group-item-text">{$CONTENT}</p>
		{/if}
	</div>
	
</div>
